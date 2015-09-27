'''
Created on 25 sep. 2013

@author: johan
'''

from django import forms
from nrgweb.models import MetingTijden, Meting, Huishouden, Apparaat, HuishoudenApparaat
from datetime import date
from nrgweb.util import get_time_values

class RegistrationForm(forms.ModelForm):
    password = forms.PasswordInput()
    huisnummer_toevoeging = forms.CharField(required=False)
    class Meta:
        model = Huishouden
        fields = [ 'straat', 'huisnummer', 'huisnummer_toevoeging', 'woonplaats', 'postcode',
                   'aantal_personen', 'email', 'telefoonnummer', 'password' ]

class LoginForm(forms.Form):
    email = forms.CharField()
    password = forms.CharField(widget=forms.PasswordInput())

class NrgInputForm(forms.ModelForm):
    datum = forms.CharField(initial=date.today().isoformat())
    tijd = forms.ChoiceField(choices=get_time_values(MetingTijden.objects.values_list()))

    class Meta:
        model = Meting
        fields = ['datum', 'tijd', 'waarde', 'apparaat' ]
        
class DeviceInputForm(forms.ModelForm):
    merk = forms.CharField()
    typenummer = forms.CharField()
    
    class Meta:
        model = Apparaat
        fields = [ 'merk', 'typenummer', 'categorie' ]

class DeviceLinkForm(forms.ModelForm):
    class Meta:
        model = HuishoudenApparaat
        fields = ['apparaat']