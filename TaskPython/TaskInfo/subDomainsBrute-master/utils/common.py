#!/usr/bin/env python
# -*- coding: utf-8 -*-

import re
from config import *

import json
import subprocess

import logging

import requests as requests
import requests as __requests__

# from tldextract import extract, TLDExtract

from fileutils import FileUtils

import requests.packages.urllib3
requests.packages.urllib3.disable_warnings()

if allow_http_session:
    requests = requests.Session()

def is_domain(domain):
    domain_regex = re.compile(
        r'(?:[A-Z0-9_](?:[A-Z0-9-_]{0,247}[A-Z0-9])?\.)+(?:[A-Z]{2,6}|[A-Z0-9-]{2,}(?<!-))\Z', 
        re.IGNORECASE)
    return True if domain_regex.match(domain) else False

def http_request_get(url, body_content_workflow=False, allow_redirects=allow_redirects, custom_cookie=""):
    try:
        if custom_cookie:
            headers['Cookie']=custom_cookie
        result = requests.get(url, 
            stream=body_content_workflow, 
            headers=headers, 
            timeout=timeout, 
            proxies=proxies,
            allow_redirects=allow_redirects,
            verify=allow_ssl_verify)
        return result
    except Exception, e:
        # return empty requests object
        return __requests__.models.Response()

def http_request_post(url, payload, body_content_workflow=False, allow_redirects=allow_redirects, custom_cookie=""):
    """ payload = {'key1': 'value1', 'key2': 'value2'} """
    try:
        if custom_cookie:
            headers['Cookie']=custom_cookie
        result = requests.post(url, 
            data=payload, 
            headers=headers, 
            stream=body_content_workflow, 
            timeout=timeout, 
            proxies=proxies,
            allow_redirects=allow_redirects,
            verify=allow_ssl_verify)
        return result
    except Exception, e:
        # return empty requests object
        return __requests__.models.Response()

def curl_get_content(url):
    try:
        cmdline = 'curl "{url}"'.format(url=url)
        logging.info("subprocess call curl: {}".format(url))
        run_proc = subprocess.Popen(
            cmdline,
            shell=True,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE)
        (stdoutput,erroutput) = run_proc.communicate()
        response = {
            'resp': stdoutput.rstrip(),
            'err': erroutput.rstrip(),
        }
        return response
    except Exception as e:
        pass

def save_result(filename, args):
    try:
        fd = open(filename, 'w')
        json.dump(args, fd, indent=4)
    finally:
        fd.close()

def read_json(filename):
    if FileUtils.exists(filename):
        try:
            fd = open(filename, 'r')
            args = json.load(fd)
            return args
        finally:
            fd.close()
    else:
        return []





