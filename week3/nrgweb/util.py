__author__ = 'johan'

from django.db import connection
import datetime
from nrgweb.models import Meting, HuishoudenApparaat
import json
import http.client
import urllib.request

def get_time_values(values, as_tuple = True, format = '%H:%M'):
    """Get the time values from a list of datetime tuples"""
    list = []
    for value in values:
        time = value[0]
        if as_tuple:
            list.append((time.strftime('%H:%M:%S'), time.strftime(format)))
        else:
            list.append(time.strftime(format))
    return list

def get_measurements_dates(huishouden_id):
    """Get all measurement dates of a given huishouden_id"""
    dates = []
    cursor = connection.cursor()
    cursor.execute("SELECT DISTINCT(datum) AS datum FROM nrgweb_meting ORDER BY datum")
    for row in cursor.fetchall():
        dates.append(row[0].strftime('%Y-%m-%d'))
    return dates

def get_measurement_table(_huishouden_id, measurement_date):
    """Generate a measurement table for the given huishouden_id and date"""
    from nrgweb.models import MetingTijden, Meting
    # Initialize the measurements table.
    times = get_time_values(MetingTijden.objects.values_list())
    measurements = {
        'times': times,
        'devices': {}
    }

    # Get the measurements.
    metingen = Meting.objects.filter(huishouden_id=_huishouden_id, datum=measurement_date)
    # Return nothing if no measurements found
    if len(metingen) == 0:
        return None

    for meting in metingen:
        # Initialize a new measurement entry if necessary
        device = meting.apparaat.apparaat
        device_id = device.apparaat_id
        if not device_id in measurements['devices']:
            measurement = {
                'name': "{0} {1}".format(device.merk, device.typenummer),
                'values': {}
            }
            # Initialize a value for all measurable times.
            for time in times:
                print(time[1])
                measurement['values'][time[1]] = {'value': 0.0, 'sum': 0.0}
            # And store it in the main measurements dictionary.
            measurements['devices'][device_id] = measurement
        # Add the value.
        measurements['devices'][device_id]['values'][meting.tijd.strftime('%H:%M')] = {'value': float(meting.waarde), 'sum': 0.0}
    # Create the summarized values.
    for device_id in measurements['devices']:
        sum = None
        for time in times:
            time = time[1]
            if sum == None:
                sum = float(measurements['devices'][device_id]['values'][time]['value'])
            else:
                sum += float(measurements['devices'][device_id]['values'][time]['value'])
            measurements['devices'][device_id]['values'][time]['sum'] = sum
    return measurements

def get_averages_for_device(device_id, postcode = None):
    """Get all average values for a device id, optionally for a given postcode area (only the first number
    of the postcode will be used"""
    query = """select
    m.tijd as time,
    avg(m.waarde)  as avg_value
from nrgweb_meting m, nrgweb_huishoudenapparaat ha, nrgweb_apparaat a
where
"""
    if postcode is not None:
        query += " m.huishouden_id in (select huishouden_id from nrgweb_huishouden where "
        query += "substr(postcode, 1, 1) = '" + postcode[0] + "') and "

    query += "m.apparaat_id = ha.id and "
    query += "ha.apparaat_id = a.apparaat_id and "
    query += "a.apparaat_id = %s "
    query += "group by ha.apparaat_id, m.tijd order by m.tijd"

    # Get the real device id.
    device_id = HuishoudenApparaat.objects.get(pk=device_id).apparaat_id

    entries = []
    cursor = connection.cursor()
    cursor.execute(query, [device_id])
    for row in cursor.fetchall():
        entries.append({'time': row[0].strftime('%H:%M'), 'value': row[1]})
    return entries


def get_averages_for_category(category, postcode = None):
    """Get averages measurement values for devices in the given category"""
    query = """select
    m.tijd as time,
    round(avg(m.waarde), 2) as avg_value
from nrgweb_meting m, nrgweb_huishoudenapparaat ha, nrgweb_apparaat a
where
"""

    if postcode is not None:
        query += " m.huishouden_id in (select huishouden_id from nrgweb_huishouden where "
        query += "substr(postcode, 1, 1) = '" + postcode[0] + "') and "

    query += "m.apparaat_id = ha.id and "
    query += "ha.apparaat_id = a.apparaat_id and "
    query += "a.categorie_id = %s "
    query += "group by a.categorie_id, m.tijd order by m.tijd"

    entries = []
    cursor = connection.cursor()
    cursor.execute(query, [category])
    for row in cursor.fetchall():
        entries.append({'time': row[0].strftime('%H:%M'), 'value': row[1]})
    return entries

def get_device_list(huishouden_id):
    """Get a list of devices for the given huishouden_id"""
    from nrgweb.models import HuishoudenApparaat, Apparaat
    devices = HuishoudenApparaat.objects.filter(huishouden = huishouden_id).values_list()
    deviceList = []
    device = None
    if len(devices) > 0:
        deviceList = []
        for device in devices:
            # get columnvalues for the device
            items = list(Apparaat.objects.filter(apparaat_id = device[2]).values())
            # add unique device id to list of columnvalues
            items.append(device[0])
            # add device items to list
            deviceList.append(items)
        return (device, deviceList)
    return None

def get_measurements_for_device(device_id, date = None):
    """Get all measurements for the given device id and optionally for the date (default "now")"""
    if date is None:
        date = datetime.date.today().isoformat()

    entries = []
    for measurement in Meting.objects.filter(apparaat=device_id, datum=date):
        entries.append({'time': measurement.tijd.strftime('%H:%M'), 'value': float(measurement.waarde)})

    return entries

def read_json_file(input_fn):
    """Read the contents from a json file and return it."""
    in_fd = open(input_fn)
    data = json.load(in_fd)
    in_fd.close()
    return data

def url_get_data(url, params = '', method = "GET"):
    url_info = urllib.parse.urlparse(url)
    if url_info.scheme == "https":
        conn = http.client.HTTPSConnection(url_info.netloc)
    else:
        conn = http.client.HTTPConnection(url_info.netloc)

    if isinstance(params, dict):
        # If the params are a dictionary, convert it to a query string
        path = url_info.path + "?" + urllib.parse.urlencode(params)
    else:
        # Otherwise, just assume it's a string.
        path = url_info.path + "?" + params
    # Do the request.
    conn.request(method, path)
    response = conn.getresponse()
    # Read the response.
    data = response.read()
    conn.close()
    return data

def url_get_json(url, params = ''):
    return json.loads(url_get_data(url, params))

def url_get_query_string(url, params = ''):
    return urllib.parse.parse_qs(url_get_data(url, params))