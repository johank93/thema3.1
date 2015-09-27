'''
Created on 24 sep. 2013

@author: johan
'''

from django.db import models
import re

class Huishouden(models.Model):
    huishouden_id = models.AutoField(primary_key=True)
    straat = models.CharField(max_length=40)
    huisnummer = models.IntegerField()
    huisnummer_toevoeging = models.CharField(max_length=5, null=True)
    woonplaats = models.CharField(max_length=40)
    postcode = models.CharField(max_length=6)
    aantal_personen = models.IntegerField()
    email = models.CharField(max_length=40)
    telefoonnummer = models.CharField(max_length=10)
    password = models.CharField(max_length=40)

    def __unicode__(self):
        return self.huishouden_id

    
class ApparaatCategorie(models.Model):
    def __unicode__(self):
        return self.categorie
    
    categorie = models.CharField(max_length=20, primary_key=True)
    
class Apparaat(models.Model):
    apparaat_id = models.AutoField(primary_key=True)
    categorie = models.ForeignKey(ApparaatCategorie)
    merk = models.CharField(max_length=40)
    typenummer = models.CharField(max_length=40)

    def __unicode__(self):
        return "{0} - {1} ({2})".format(self.merk, self.typenummer, self.categorie)

class HuishoudenApparaat(models.Model):
    def __unicode__(self):
        return self.apparaat.typenummer
        
    huishouden = models.ForeignKey(Huishouden)
    apparaat = models.ForeignKey(Apparaat)
    
class MetingTijden(models.Model):
    tijd = models.TimeField(primary_key=True)
    
class Meting(models.Model):
    meting_id = models.AutoField(primary_key=True)
    apparaat = models.ForeignKey(HuishoudenApparaat)
    datum = models.DateField()
    tijd = models.TimeField()
    waarde = models.DecimalField(decimal_places=2, max_digits=4)
    huishouden = models.ForeignKey(Huishouden)

class GoogleAuth(models.Model):
    user_id = models.CharField(primary_key=True, max_length=30)
    token_expires = models.BigIntegerField()
    time_issued = models.BigIntegerField()
    email = models.CharField(max_length=40)
    huishouden = models.ForeignKey(Huishouden, blank=True, null=True)

class FacebookAuth(models.Model):
    user_id = models.CharField(primary_key=True, max_length=30)
    token_expires = models.BigIntegerField()
    huishouden = models.ForeignKey(Huishouden, blank=True, null=True)