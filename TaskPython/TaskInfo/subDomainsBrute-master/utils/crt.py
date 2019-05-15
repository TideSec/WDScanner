#!/usr/bin/env python
# -*- coding: utf-8 -*-

from common import http_request_get
import sys
from lxml import etree

'''
通过
crt.sh
查找子域名
'''


class Crt(object):

    def __init__(self, domain='baidu.com', proxy=None):
        self.domain = domain
        self.result = set()
        self.proxy = proxy

    def execute(self):  # 从 crt.sh 获取域名
        url = "https://crt.sh/?Identity=%%.%s" % self.domain
        try:
            r = http_request_get(url)
            #print r
            if r.status_code == 200:
                root = etree.HTML(r.text)
                td_info = root.xpath(r'.//td[@class="outer"]/table/tr/td[4]')
                for td in td_info:
                    if td.text:  # 排除空的情况
                        if "@" not in td.text and 'SingleDomain' not in td.text:  # 排除邮箱等情况
                            domain = td.text.split("=")[-1].replace("*.", "")
                            self.result.add(domain)
        except Exception, e:
            print e.message
            # print e
        return list(self.result)


if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'qq.com'
    try:
        print Crt(target).execute()
    except KeyboardInterrupt:
        print '{0}'.format("手动退出")