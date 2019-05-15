#!/usr/bin/env python
# -*- coding: utf-8 -*-

import json
import sys
import socket

import datetime


def get_ip(domain):
    try:
        ip_list = socket.gethostbyname_ex(domain)[2]
        ip = ', '.join(ip_list) if 1 == len(ip_list) else ip_list[0]
        # for i in ip_list:
        #     ip += (i+', ')
    except Exception as e:
        ip = 'None'
    return ip


def out(msg):
    sys.stdout.write(msg)
    sys.stdout.flush()


def print_result_list(result_list):
    for i in result_list:
        msg = i.ljust(30) + get_ip(i)
        out(msg + '\n')
