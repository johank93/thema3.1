from nrgweb.util import url_get_json

__author__ = 'johan'

import os
from nrgweb.util import read_json_file, url_get_json, url_get_query_string
import urllib
from django.http import HttpResponse, HttpResponseRedirect
from django.core.urlresolvers import reverse
from django.shortcuts import render
from nrgweb.models import Huishouden, FacebookAuth
from nrgweb.forms import RegistrationForm

# File containing the facebook secrets.
CLIENT_SECRETS = os.path.join(os.path.dirname(__file__), 'facebook_secrets.json')

def get_authorization_url():
    auth_params = read_json_file(CLIENT_SECRETS)
    params = {
        "client_id": auth_params["app_id"],
        "redirect_uri": auth_params["redirect_uri"]
    }
    return "https://www.facebook.com/dialog/oauth?" + urllib.parse.urlencode(params)

def facebook_auth(request):
    """View handling the google oauth callback"""
    if 'error_code' in request.GET and 'error_message' in request.GET:
        return HttpResponse('Facebook authentication cancelled: ' + request.GET['error_message'],
                            content_type="text/plain")
    elif 'code' in request.GET:
        # Get the access token for the code.
        auth_params = read_json_file(CLIENT_SECRETS)
        facebook_data = code_to_token(request.GET["code"], auth_params)
        access_token = facebook_data["access_token"][0]
        token_expires = facebook_data["expires"][0]
        # Check if we got an access token
        if not 'access_token' in facebook_data:
            return HttpResponse('access_token not found in facebook response data ({0})'.format(facebook_data))
        # Get the user information of the token.
        facebook_user_id = get_user_id(access_token)
        # Check if the user actually exists.
        new_registration = False
        try:
            fb_entry = FacebookAuth.objects.get(user_id=facebook_user_id)
            # Update the entry.
            fb_entry.token_expires = token_expires
            fb_entry.save()
        except FacebookAuth.DoesNotExist:
            new_registration = True
            # Create a new entry.
            fb_entry = FacebookAuth()
            fb_entry.token_expires = token_expires
            fb_entry.user_id = facebook_user_id
            fb_entry.save()

        # Set essential session data.
        request.session["auth_provider"] = "facebook"
        request.session["facebook_user"] = fb_entry

        # If the facebook user doesn't have a huishouden linked to it, redirect to the facebook
        # user registration.
        if fb_entry.huishouden is None or new_registration:
            return HttpResponseRedirect(reverse('nrgweb:facebook_register'))

        # The user is logged in.
        request.session['logged_in'] = True
        request.session['huishouden'] = fb_entry.huishouden
        # Go to the default view.
        return HttpResponseRedirect(reverse('nrgweb:input'))

    return HttpResponse('invalid facebook response (no error and no code)', content_type="text/plain")

def facebook_register(request):
    # Sanity check.
    if not 'facebook_user' in request.session:
        return HttpResponse('Facebook user entry not set in session data.', content_type="text/plain")
    facebook_user = request.session['facebook_user']

    # Check if the form was posted.
    if request.method == 'POST':
        form = RegistrationForm(request.POST)
        if form.is_valid():
            huishouden = form.save()
            # Link the facebook user to the newly created huishouden
            facebook_user.huishouden = huishouden
            facebook_user.save()
            # Set essential session data.
            request.session['logged_in'] = True
            request.session['auth_provider'] = 'facebook'
            request.session['huishouden'] = huishouden
            return HttpResponseRedirect(reverse('nrgweb:device'))
    else:
        # Create a new registration form.
        form = RegistrationForm()
    return render(request, 'register.html', {'form': form, 'session': request.session})

def get_user_id(access_token):
    params = {
        "fields": "id",
        "access_token": access_token
    }
    user_info = url_get_json("https://graph.facebook.com/me?", params)
    return user_info["id"]


def code_to_token(code, auth_params=None):
    if auth_params is None:
        auth_params = read_json_file(CLIENT_SECRETS)
    params = {
        "client_id": auth_params["app_id"],
        "redirect_uri": auth_params["redirect_uri"],
        "client_secret": auth_params["app_secret"],
        "code": code
    }
    return url_get_query_string("https://graph.facebook.com/oauth/access_token", params)


def get_app_token(auth_params=None):
    if auth_params is None:
        auth_params = read_json_file(CLIENT_SECRETS)
    params = {
        "client_id": auth_params["app_id"],
        "client_secret": auth_params["app_secret"],
        "grant_type": "client_credentials",
    }
    return url_get_query_string("https://graph.facebook.com/oauth/access_token", params)
