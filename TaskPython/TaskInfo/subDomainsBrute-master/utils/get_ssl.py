#!/usr/bin/env python
# -*- coding: utf-8 -*-


import os
import re
import requests
import sys

'''
通过证书查找子域名
'''


class GetSsl(object):

    def __init__(self, raw_domain):
        self.raw_domain = raw_domain
        self.x = set()
        self.PREFIX_URL1 = 'https://www.'
        self.PREFIX_URL2 = 'https://'

    def is_https1(self):
        url = self.PREFIX_URL2 + self.raw_domain
        try:
            requests.get(url, timeout=4)
        except requests.ConnectionError, requests.ConnectTimeout:
            return False
        return True

    def is_https2(self):
        url = self.PREFIX_URL1 + self.raw_domain
        try:
            requests.get(url, timeout=4)
        except requests.ConnectionError, requests.ConnectTimeout:
            return False
        return True

    def get_domains_from_openssl(self):  # 使用 OpenSSL 的 SAN 获得域名，有命令注入风险
        domains = set()
        cmd = 'openssl s_client -showcerts -connect %s:443 < /dev/null 2>/dev/null \
        | openssl x509 -text | grep -A 1 "Subject Alternative Name"' % self.raw_domain
        try:
            tmp = os.popen(cmd).readlines()
            data = re.split(r'DNS:', tmp[1].strip())
            for i in data:
                if i:
                    domains.add(i.replace(', ', '').replace('*.', ''))
            if self.raw_domain in domains:
                domains.remove(self.raw_domain)
        except KeyboardInterrupt:
            pass
        return list(domains)

    def execute(self):
        if self.is_https1() or self.is_https2():
            openssl_domains = self.get_domains_from_openssl()
            return openssl_domains
        return []


if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'qq.com'
    print GetSsl(target).execute()
