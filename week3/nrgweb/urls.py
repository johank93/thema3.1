'''
Created on 24 sep. 2013

@author: johan
'''

from django.conf.urls import patterns, url
import nrgweb.views
import nrgweb.google
import nrgweb.facebook

urlpatterns = patterns('',
    url(r'^$', nrgweb.views.index, name='index'),
    url(r'^login', nrgweb.views.login, name='login'),
    url(r'^logout', nrgweb.views.logout, name='logout'),
    url(r'^report', nrgweb.views.report, name='report'),
    url(r'^input', nrgweb.views.invoer, name='input'),
    url(r'^device$', nrgweb.views.invoerApparaat, name='device'),
    url(r'^measurements(/(?P<measurement_date>\d{4}-\d{2}-\d{2}))?', nrgweb.views.measurements, name='measurements'),
    url(r'^registered', nrgweb.views.registered, name='registered'),
    url(r'^register', nrgweb.views.register, name='register'),
    url(r'^avg/device/(?P<device_id>\d+)/?(?P<postcode>\d+)?', nrgweb.views.avg_per_device, name='avg_dev'),
    url(r'^avg/category/(?P<category>[a-zA-Z]+)/?(?P<postcode>\d+)?', nrgweb.views.avg_per_category, name='avg_cat'),
    url(r'^device/measurements/(?P<device_id>\d+)(/(?P<measurement_date>\d{4}-\d{2}-\d{2}))?', nrgweb.views.get_device_measurements, name='dev_get'),
    url(r'^googleauth', nrgweb.google.google_auth, name='google_auth'),
    url(r'^googleregister', nrgweb.google.google_register, name='google_register'),
    url(r'^facebookauth', nrgweb.facebook.facebook_auth, name='facebook_auth'),
    url(r'^facebookregister', nrgweb.facebook.facebook_register, name='facebook_register'),
)
