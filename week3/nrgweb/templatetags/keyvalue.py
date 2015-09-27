__author__ = 'johan'

from django import template

register = template.Library()

@register.filter
def measurement_value(dict, key):
    """Usage in template:
    {{ dictionary|keyvalue:keyname }}
    """
    if key in dict and 'value' in dict[key]:
        return dict[key]['value']
    return "*unknown key*"

@register.filter
def measurement_sum_value(dict, key):
    """Usage in template:
    {{ dictionary|keyvalue:keyname }}
    """
    if key in dict and 'sum' in dict[key]:
        return dict[key]['sum']
    return "*unknown key*"
