'''
Created on 24 sep. 2013

@author: johan
'''

from nrgweb.models import Apparaat
from nrgweb.models import ApparaatCategorie
from django.contrib import admin

class ApparaatCategorieAdmin(admin.ModelAdmin):
    model = ApparaatCategorie
    fieldsets = [
        ('Categorie', {'fields': ['categorie']}),
    ]
    list_display = ('categorie', )
    list_filter = ['categorie']
    search_fields = ['categorie']
    
class ApparaatAdmin(admin.ModelAdmin):
    model = Apparaat
    fieldsets = [
        (None, {'fields': ['merk', 'typenummer', 'categorie']}),
    ]
    list_display = ('merk', 'typenummer')
    list_filter = ['merk', 'typenummer', ]
    search_fields = ['merk', 'typenummer']
    
admin.site.register(Apparaat, ApparaatAdmin)
admin.site.register(ApparaatCategorie, ApparaatCategorieAdmin)
    