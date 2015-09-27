'''
Created on 24 sep. 2013

@author: johan & vincent
'''

from django.shortcuts import render
from forms import RegistrationForm, LoginForm, NrgInputForm, DeviceInputForm, DeviceLinkForm
from django.http import HttpResponseRedirect, HttpResponse
from django.core.urlresolvers import reverse
from models import Huishouden, HuishoudenApparaat, Apparaat, MetingTijden, Meting
from util import *
import json
import google
import facebook

def index(request):
    """Main view"""
    return render(request, 'index.html', {'session': request.session})

def register(request):
    """"User registration view"""

    # Check if user registration needs to be handled elsewhere.
    if "auth_provider" in request.session:
        if request.session["auth_provider"] == "google":
            return HttpResponseRedirect(reverse('nrgweb:google_register'))
        elif request.session["auth_provider"] == "facebook":
            pass

    if request.method == 'POST':
        form = RegistrationForm(request.POST)
        if form.is_valid():
            form.save()
            return HttpResponseRedirect(reverse('nrgweb:registered'))
    else:
        form = RegistrationForm()
    return render(request, 'register.html', {'form': form})

def login(request):
    """Login view"""
    # Check if the user has been logged in already.
    if is_logged_in(request):
        return HttpResponseRedirect(reverse('nrgweb:input'))

    if request.method == 'POST':
        form = LoginForm(request.POST)
        if form.is_valid():
            # Authenticate the user.
            huishouden = Huishouden.objects.get(email=request.POST['email'])
            if huishouden.password == request.POST['password']:
                request.session['logged_in'] = True
                request.session['huishouden'] = huishouden
                # Check if redirection after logging in is requested by some view.
                if 'redirect_after_login' in request.session:
                    redirect_to = request.session['redirect_after_login']
                    del request.session['redirect_after_login']
                else:
                    # Default redirection destination
                    redirect_to = 'nrgweb:measurements'
                return HttpResponseRedirect(reverse(redirect_to))
    else:
        form = LoginForm()
        template_data = {'form': form, 'session': request.session}
        #template_data['google_auth_url'] = google.get_authorization_url()
        #template_data['facebook_auth_url'] = facebook.get_authorization_url()
    return render(request, 'login.html', template_data)

def logout(request):
    # Delete the 'user logged in' key if it exists.
    if 'logged_in' in request.session:
        request.session.flush()
    return HttpResponseRedirect(reverse('nrgweb:index'))

def report(request):
    """Reporting view"""
    if not is_logged_in(request):
        request.session['redirect_after_login'] = 'nrgweb:report'
        return HttpResponseRedirect(reverse('nrgweb:login'))
    
    # Initialize the template data.
    template_data = {'session': request.session}
    # Get the huishouden object.
    huishouden = Huishouden.objects.get(huishouden_id=request.session['huishouden'].huishouden_id)

    # Generate the device list.    
    dev_info = get_device_list(huishouden.huishouden_id)
    template_data['devices'] = dev_info[0]
    if len(template_data['devices']) > 0:
        template_data['deviceList'] = dev_info[1]
        print(dev_info[1])

    return render(request, 'report.html', template_data)

def invoer(request):
    """Measurement input view"""
    if not is_logged_in(request):
        request.session['redirect_after_login'] = 'nrgweb:input'
        return HttpResponseRedirect(reverse('nrgweb:login'))

    # Initialize the template data.
    template_data = {'session': request.session}
    # Get the huishouden object.
    huishouden = Huishouden.objects.get(huishouden_id=request.session['huishouden'].huishouden_id)

    # Generate the device list.
    dev_info = get_device_list(huishouden.huishouden_id)
    if dev_info is None:
        request.session["statusmsg"] = "Er is nog geen apparaat ingevoerd. Voert u aub eerst een apparaat in"
        return HttpResponseRedirect(reverse('nrgweb:device'))

    # Get the device list.
    template_data['devices'] = dev_info[0]
    if len(template_data['devices']) > 0:
        template_data['deviceList'] = dev_info[1]

    # Check if data was posted.
    if request.method == 'POST':
        nrgForm = NrgInputForm(request.POST)
        if nrgForm.is_valid():
            # Get the "meting" object from the form.
            meting = nrgForm.save(commit=False)
            # Set the huishouden (mandatory foreign key).
            meting.huishouden = huishouden
            # Save it.
            meting.save()
            # Redirect to the measurements page
            return HttpResponseRedirect(reverse('nrgweb:measurements'))
    else:
        nrgForm = NrgInputForm()
        nrgForm.fields['apparaat'].queryset = HuishoudenApparaat.objects.filter(huishouden_id=huishouden.huishouden_id)

    template_data['nrgForm'] = nrgForm
    return render(request, 'input.html', template_data)

def invoerApparaat(request):
    """View handling the addition of devices"""
    if not is_logged_in(request):
        request.session['redirect_after_login'] = 'nrgweb:device'
        return HttpResponseRedirect(reverse('nrgweb:login'))

    if 'statusmsg' in request.session:
        statusmsg = request.session['statusmsg']
        del request.session['statusmsg']
    else:
        statusmsg = None

    if request.method == 'POST':
        _huishouden_id = request.session['huishouden'].huishouden_id
        if not 'apparaat' in request.POST:
            form = DeviceInputForm(request.POST)
            if request.POST.get('deviceId') in ['none']:   # Add a device since there is no ID
                if form.is_valid():
                    newDevice = form.save() # get the device object just added to Apparaat
                    newHuishoudenApparaat = HuishoudenApparaat(huishouden_id = _huishouden_id, apparaat_id = newDevice.pk)
                    newHuishoudenApparaat.save()
            else:
               device = HuishoudenApparaat.objects.filter(pk = request.POST.get('deviceId'))
               # remove apparaat record
               Apparaat.objects.filter(pk = device[0].apparaat_id).delete()
               # remove Huishouden apparaat record
               device.delete()
        else:
            form = DeviceLinkForm(request.POST)
            if form.is_valid():
                newLink = form.save(commit=False)
                newLink.huishouden = Huishouden.objects.get(huishouden_id=_huishouden_id)
                newLink.save()

        return HttpResponseRedirect(reverse('nrgweb:device'))
    else:
        deviceForm = DeviceInputForm()
        devices = HuishoudenApparaat.objects.filter(huishouden = request.session['huishouden'].huishouden_id).values_list()
        linkForm = DeviceLinkForm()
        deviceList = []
        for device in devices:
            items = list(Apparaat.objects.filter(apparaat_id = device[2]).values()) # get columnvalues for the device
            items.append (device[0]) # add unique device id to list of columnvalues
            deviceList.append(items) # add device items to list
        return render(request, 'inputDevice.html', {'session' : request.session,
                                                    'deviceForm': deviceForm,
                                                    'deviceList' : deviceList,
                                                    'linkForm': linkForm,
                                                    'statusmsg': statusmsg})

def registered(request):
    """View rendered when a user has registered"""
    return render(request, 'registered.html', {'session': request.session})

def is_logged_in(request):
    """Determine if the user is logged in """
    return 'logged_in' in request.session and request.session['logged_in']

def measurements(request, measurement_date=None):
    # Redirect if not logged in.
    if not is_logged_in(request):
        request.session['redirect_after_login'] = 'nrgweb:measurements'
        return HttpResponseRedirect(reverse('nrgweb:login'))

    # Set the measurement date to today if no date is given.
    if measurement_date is None:
        from datetime import date
        measurement_date = date.today().isoformat()

    # Get the measurement dates.
    dates = get_measurements_dates(request.session['huishouden'].huishouden_id)

    # Initialize template data.
    template_data = {'session': request.session, 'dates': dates, 'measurement_date': measurement_date}

    # Find all measurements for the given date.
    if len(dates) > 0:
        from util import get_measurement_table
        measurements = get_measurement_table(request.session['huishouden'].huishouden_id,
                                                              measurement_date)
        if measurements is not None:
            template_data['measurements'] = measurements

    return render(request, 'measurements.html', template_data)


def avg_per_device(request, device_id, postcode = None):
    """Average per device view"""
    return HttpResponse(json.dumps(get_averages_for_device(device_id, postcode)), content_type="application/json")

def avg_per_category(request, category, postcode = None):
    """Average per category view"""
    return HttpResponse(json.dumps(get_averages_for_category(category, postcode)), content_type="application/json")

def get_device_measurements(request, device_id, measurement_date = None):
    """Get a JSON list of device measurements"""
    return HttpResponse(json.dumps(get_measurements_for_device(device_id, measurement_date)), content_type="application/json")

