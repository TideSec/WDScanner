#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import requests
from lxml import etree
import time
from urlparse import urlparse

reload(sys)
sys.setdefaultencoding('utf-8')

'''抓取站长帮手网的信息'''


def geturl(url):
    parse = urlparse(url)
    url = parse.netloc
    return url


class ILinks(object):
    def __init__(self, domain):
        self.domain = domain

    def execute(self):
        subdomain_set = set()
        url = 'http://i.links.cn/subdomain/'
        data = {'domain': self.domain,
                'b2': '1',
                'b3': '1',
                'b4': '1'}
        try:
            rsp = requests.post(url=url, data=data, timeout=5)
            root = etree.HTML(rsp.text)
            urls = root.xpath('//*[@class="domain"]/a')
            for i in urls:
                '''结果打印和集合添加'''
                url = geturl(i.text)
                if len(url) == 0:
                    url = i.text
                # print url
                subdomain_set.add(url)
        except requests.exceptions.ConnectionError, requests.exceptions.ConnectTimeout:
            print '网络问题退出'
        except Exception, e:
            print "其它问题退出:", e.message

        return list(subdomain_set)


if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'cugb.edu.cn'
    a = ILinks(target)
    a.execute()
