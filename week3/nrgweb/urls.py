'''
Created on 24 sep. 2013

@author: johan
'''

from django.conf.urls import patterns, url
import views
import google
import facebook

urlpatterns = patterns('',
    url(r'^$', views.index, name='index'),
    url(r'^login', views.login, name='login'),
    url(r'^logout', views.logout, name='logout'),
    url(r'^report', views.report, name='report'),
    url(r'^input', views.invoer, name='input'),
    url(r'^device$', views.invoerApparaat, name='device'),
    url(r'^measurements(/(?P<measurement_date>\d{4}-\d{2}-\d{2}))?', views.measurements, name='measurements'),
    url(r'^registered', views.registered, name='registered'),
    url(r'^register', views.register, name='register'),
    url(r'^avg/device/(?P<device_id>\d+)/?(?P<postcode>\d+)?', views.avg_per_device, name='avg_dev'),
    url(r'^avg/category/(?P<category>[a-zA-Z]+)/?(?P<postcode>\d+)?', views.avg_per_category, name='avg_cat'),
    url(r'^device/measurements/(?P<device_id>\d+)(/(?P<measurement_date>\d{4}-\d{2}-\d{2}))?', views.get_device_measurements, name='dev_get'),
    url(r'^googleauth', google.google_auth, name='google_auth'),
    url(r'^googleregister', google.google_register, name='google_register'),
    url(r'^facebookauth', facebook.facebook_auth, name='facebook_auth'),
    url(r'^facebookregister', facebook.facebook_register, name='facebook_register'),
)
