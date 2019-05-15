#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
通过娟萝网http://www.juanluo.com的搜索引擎api接口查询子域名
"""

import sys
import requests
import time
from urlparse import urlparse
from lxml import etree
from common import headers

reload(sys)
sys.setdefaultencoding('utf-8')


def check_contain_chinese(check_str):  # 中文检测
    for ch in check_str.decode('utf-8'):
        if u'\u4e00' <= ch <= u'\u9fff':
            return True
    return False


def geturl(url):
    parse = urlparse(url)
    url = parse.netloc
    return url


class Juanluo(object):

    def __init__(self, domain, limit_page=30, proxy=None):
        self.domain = domain
        self.limit_page = limit_page
        self.proxy = proxy
        self.result = set()
        self.TIMEOUT = 10
        self.sleep = 1

    def filtr(self, unfiltered_str):  # 检测是否为空是否包含中文过滤协议
        if unfiltered_str is None:
            return None
        else:
            if not check_contain_chinese(unfiltered_str):
                if not unfiltered_str.find(self.domain) == -1:
                    s = unfiltered_str.split(r'//')[0]
                    if s == "http:" or s == "https:":
                        url = geturl(unfiltered_str)
                    else:
                        url = unfiltered_str.split(r'/')[0]
                    return url.encode("utf-8")
                else:
                    # print "抓取错误"
                    return None
            else:
                # print '包含中文，抓取错误'
                return None

    def get_html(self, url):
        root = None
        for i in range(3):
            try:
                rsp = requests.get(url, headers=headers, timeout=self.TIMEOUT)
            except Exception, e:
                # print e.message, 'juanluo搜索网络出错，暂停5秒'
                time.sleep(5)
                continue

            if rsp.status_code == 200:
                root = etree.HTML(rsp.text)
                break
            else:
                time.sleep(5)

        return root

    def execute(self):
        index = 0
        net_fail = 0
        juanluo_url = None

        while True:
            result = self.search_by_juanluo(juanluo_url, index)
            if result is None:
                break
            elif juanluo_url == result[0]:
                net_fail += 1
                # print "juanluo搜索出错%d次" % net_fail
                if net_fail >= 5:
                    # print 'juanluo搜索出错满五次,请检查网络'
                    break
            else:
                net_fail = 0
                juanluo_url = result[0]
                index = result[1]
        return list(self.result)

    def search_by_juanluo(self, url, index):
        if url is None:
            url = r'http://www.juanluo.com/?q=site%3A' + self.domain
        try:
            rsp = requests.get(url=url, headers=headers, timeout=self.TIMEOUT)
        except Exception, e:
            return url, index
        root = etree.HTML(rsp.text)
        urls = root.xpath(r'//*[@id="result"]/div[@class="g"]/span')
        # urls2 = root.xpath(r'//*[@id]/div[1]/div[2]/div[2]/a[1]')
        for i in urls:
            url = self.filtr(i.text)
            if url is None:
                continue
            else:
                self.result.add(url)
        next_page = root.xpath(r'//*[@class="n"]/@href')

        if len(next_page) > 0 and index < self.limit_page:
            if len(next_page) == 1:
                next_url = next_page[0]
            else:
                next_url = next_page[1]
            url = next_url
            if index > 100:
                time.sleep(self.sleep)
            index += 1
            return url, index
        else:
            return None


if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'cugb.edu.cn'
    a = Juanluo(target)
    a.execute()
    print a.result
