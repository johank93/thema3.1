__author__ = 'johan'

from django.shortcuts import render
from django.http import HttpResponseRedirect, HttpResponse
import urllib
import http.client
import json
import os
import base64
from nrgweb.util import read_json_file
from nrgweb.models import GoogleAuth
from django.core.urlresolvers import reverse
from nrgweb.forms import RegistrationForm

CLIENT_SECRETS = os.path.join(os.path.dirname(__file__), 'google_secrets.json')


def get_authorization_url():
    """Return the authorization url"""
    auth_params = read_json_file(CLIENT_SECRETS)
    params = {
        "client_id": auth_params["client_id"],
        "response_type": "code",
        "scope": "openid email",
        "redirect_uri": auth_params["redirect_uri"],
    }
    return "https://accounts.google.com/o/oauth2/auth?" + urllib.parse.urlencode(params)

def google_auth(request):
    """View handling the google oauth callback"""
    if 'code' not in request.GET:
        return HttpResponse('invalid google response', content_type="text/plain")

    # Store the google data in the session.
    google_data = do_google_auth(request.GET)
    # Retrieve the information from the id token.
    token_info = get_token_info(google_data['id_token'])
    # Check if the user exists already.
    new_registration = False
    try:
        local_user = GoogleAuth.objects.get(user_id=token_info['sub'])
        # As no DoesNotExist exception has been raised, update the local entry.
        local_user.token_expires = token_info['exp']
        local_user.time_issued = token_info['iat']
        local_user.email = token_info['email']
        local_user.save()
    except GoogleAuth.DoesNotExist:
        #  The user (read: huishouden) doesn't exist, so it needs to be created.
        local_user = GoogleAuth()
        local_user.user_id = token_info['sub']
        local_user.token_expires = token_info['exp']
        local_user.time_issued = token_info['iat']
        local_user.email = token_info['email']
        local_user.save()
        # This is a new registration. As such, a new huishouden needs to be registered.
        new_registration = True

    # Set essential session data
    request.session['auth_provider'] = 'google'
    request.session['google_user'] = local_user

    # If the local user doesn't have a huishouden linked to it. Redirect to the google
    # register view.
    if local_user.huishouden is None or new_registration:
        return HttpResponseRedirect(reverse('nrgweb:google_register'))

    # The user is logged in.
    request.session['logged_in'] = True
    request.session['huishouden'] = local_user.huishouden

    # Go to the default view.
    return HttpResponseRedirect(reverse('nrgweb:input'))

def google_register(request):
    """"Google huishouden registration view"""
    # Sanity check.
    if not 'google_user' in request.session:
        return HttpResponse('Google user entry not set in session data.', content_type="text/plain")
    google_user = request.session['google_user']

    # Check if the form was posted.
    if request.method == 'POST':
        form = RegistrationForm(request.POST)
        if form.is_valid():
            huishouden = form.save()
            # Link the google user to the newly created huishouden
            google_user.huishouden = huishouden
            google_user.save()
            # Set essential session data.
            request.session['logged_in'] = True
            request.session['auth_provider'] = 'google'
            request.session['huishouden'] = huishouden
            return HttpResponseRedirect(reverse('nrgweb:device'))
    else:
        # Create a new registration form with the google email address as default
        # mail address
        form = RegistrationForm(initial={"email": google_user.email})
    return render(request, 'register.html', {'form': form, 'session': request.session})

def do_google_auth(get_params):
    """Do google authentication"""
    conn = httplib.HTTPSConnection("accounts.google.com")
    # Read the google secrets as auth parameters.
    auth_params = read_json_file(CLIENT_SECRETS)
    # Set the authentication code.
    params = {
        "code": get_params["code"],
        "client_id": auth_params["client_id"],
        "client_secret": auth_params["client_secret"],
        "redirect_uri": auth_params["redirect_uri"],
        "grant_type": auth_params["grant_type"],
    }
    # Prepare the verification request.
    params = urllib.urlencode(params)
    headers = {"Content-type": "application/x-www-form-urlencoded"}
    conn.request("POST", "/o/oauth2/token", params, headers)
    response = conn.getresponse()
    # Read the data and serialize it.
    data = response.read()
    google_data = json.loads(data)
    if 'error' in google_data:
        print('Error: ' + google_data['error'])
        return None
    return google_data

def get_token_info(id_token):
    """Get token information. The id_token is a set of 3 strings separated by a ".". All
    3 strings are base64 encoded. For now, only the second string is relevant as it contains
    the google user information."""
    elems = id_token.split(".")
    return json.loads(base64.decodestring(elems[1] + "=="))
