#!/usr/bin/env python
# -*- coding: utf-8 -*-

# import sys
# sys.path.append("../")

import logging
import re
import subprocess
import time

import sys

from common import http_request_get, is_domain


class Netcraft(object):
    """docstring for Netcraft"""
    def __init__(self, domain):
        super(Netcraft, self).__init__()
        self.cookie = ''
        self.subset = []
        self.domain = domain
        self.site = 'http://searchdns.netcraft.com'

    def run(self):
        try:
            self.cookie = self.get_cookie().get('cookie')
            url = '{0}/?restriction=site+contains&position=limited&host=.{1}'.format(
                self.site, self.domain)
            r = http_request_get(url, custom_cookie=self.cookie)
            self.parser(r.text)
            return list(set(self.subset))
        except Exception, e:
            logging.info(str(e))
            return self.subset

    def parser(self, response):
        npage = re.search('<A href="(.*?)"><b>Next page</b></a>', response)
        if npage:
            for item in self.get_subdomains(response):
                if is_domain(item):
                    self.subset.append(item)
            nurl = '{0}{1}'.format(self.site, npage.group(1))
            r = http_request_get(nurl, custom_cookie=self.cookie)
            time.sleep(3)
            self.parser(r.text)
        else:
            for item in self.get_subdomains(response):
                if is_domain(item):
                    self.subset.append(item)

    def get_subdomains(self, response):
        _regex = re.compile(r'(?<=<a href\="http://).*?(?=/" rel\="nofollow")')
        domains = _regex.findall(response)
        for sub in domains:
            yield sub

    def get_cookie(self):
        try:
            cmdline = 'phantomjs ph_cookie.js'
            run_proc = subprocess.Popen(cmdline,shell=True,stdout=subprocess.PIPE,stderr=subprocess.PIPE)
            (stdoutput,erroutput) = run_proc.communicate()
            response = {
                'cookie': stdoutput.rstrip(),
                'error': erroutput.rstrip(),
            }
            return response
        except Exception, e:
            logging.info(str(e))
            return {'cookie':'', 'error': str(e)}

    def execute(self):
        return self.run()


if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'cugb.edu.cn'
    netcraft = Netcraft(domain=target)
    print netcraft.execute()
