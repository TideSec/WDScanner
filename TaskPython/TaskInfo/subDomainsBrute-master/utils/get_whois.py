#!/usr/bin/env python
# -*- coding: utf-8 -*-

from whois import whois
import sys


class GetWhois(object):
    def __init__(self, domain):
        self.domain = domain

    def execute(self):
        w = whois(self.domain)
        return w

    def run(self):
        return self.execute()


if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'qq.com'
    print GetWhois(target).execute()
