#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os

import logging
import traceback

import requests


class GetBanner(object):
    def __init__(self, raw_domain='180.97.33.108'):
        self.raw_domain = raw_domain
        self.result = dict()

    def execute_old(self):
        cmd = 'curl -I {0} 2>&1'.format(self.raw_domain)
        try:
            tmp = os.popen(cmd).readlines()
            for i in tmp:
                i=i.replace('\r\n','')
                if ':' in i:
                    j = i.split(":")
                    self.result[j[0]] = j[1]
                else:
                    if i:
                        self.result["status"] = i
        except:
            pass
        return self.result

    def execute(self):
        headers = {
            'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36'
        }
        response_header_dict = {}
        try:
            response_header_dict = dict(requests.head('http://' + self.raw_domain, headers=headers, timeout=(5, 5)).headers)
        except Exception as e:
            logging.error('get http header error.')
            logging.error(traceback.format_exc())
            logging.error(str(e))
        return response_header_dict


if __name__ == '__main__':
    a = GetBanner('google.com')
    result = a.execute()
    for key in result.keys():
        print key.ljust(30) + result[key]
