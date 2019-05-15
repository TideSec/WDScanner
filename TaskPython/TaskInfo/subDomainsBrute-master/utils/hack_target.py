#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import requests
import json
import time

reload(sys)
sys.setdefaultencoding('utf-8')

'''
通过调取hacktarget的api获取子域名
http://api.hackertarget.com/hostsearch/?q=目标网页
'''


class HackTarget(object):
    def __init__(self, domain):
        self.domain = domain
        self.result_dict = {}
        self.result = []

    def execute(self):
        url = 'http://api.hackertarget.com/hostsearch/?q={0}'.format(self.domain)
        try:
            rsp = requests.get(url=url, timeout=5)
            for i in rsp.text.split('\n'):
                j = i.split(",")
                self.result_dict[j[0]] = j[1]
        except KeyboardInterrupt:
            pass
        except requests.exceptions.ConnectionError, requests.exceptions.ConnectTimeout:
            pass
        except Exception, e:
            pass
        for dist in self.result_dict:
            self.result.append(dist)
        self.result = list(set(self.result))
        return self.result
        #return self.result_dict


if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'cugb.edu.cn'
    a = HackTarget(target)
    print a.execute()
