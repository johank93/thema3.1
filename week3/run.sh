#!/bin/bash

kill_stunnel() 
{
	kill -9 $stunnel_pid
}

trap kill_stunnel exit
stunnel ssl/dev_https &> /dev/null &
stunnel_pid=$!
python manage.py runserver
