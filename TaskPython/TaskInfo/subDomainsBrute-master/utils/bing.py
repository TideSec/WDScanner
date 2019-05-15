#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import time
from lxml import etree
from urlparse import urlparse

import requests

from config import headers

reload(sys)
sys.setdefaultencoding('utf-8')


def get_html(url):
    for i in range(3):
        try:
            head=headers
            head['Referer']='https://www.bing.com'
            rsp = requests.get(url, headers=head, timeout=5)
        except requests.ConnectionError, requests.ConnectTimeout:
            # print e.message, 'bing搜索网络出错，暂停5秒'
            time.sleep(5)
            continue
        if rsp.status_code == 200:
            root = etree.HTML(rsp.text)
            return root
        else:
            time.sleep(5)


def geturl(url):
    parse = urlparse(url)
    url = parse.netloc
    return url


class Bing(object):
    def __init__(self, domain, limit_page=30):
        self.domain = domain
        self.limit_page = limit_page
        self.result = set()

    def search_by_bing(self, url, index):  # bing搜索
        root = None
        # print 'bing', GetNowTime()
        if url is None:
            url = r'https://www.bing.com/search?q=site%3A' + self.domain
        for i in range(3):
            root = self.getroot(url, index)
            if root is not None:
                break
            else:
                time.sleep(10)
                continue
        if root is None:
            return
        else:
            urls1 = root.xpath(r'//*[@id="b_results"]/li[*]/h2/a/@href')
            urls2 = root.xpath(r'//*[@id="b_results"]/li[*]/div[1]/div/h2/a/@href')
            next_page = root.xpath(r'//*[@class="sb_pagN"]/@href')
            for i in urls1:
                url = geturl(i)
                if url:
                    self.result.add(url)
            for i in urls2:
                url = geturl(i)
                if url:
                    self.result.add(url)
            if len(next_page) > 0 and index < self.limit_page:
                url = 'https://www.bing.com' + next_page[0]
                if index > 30:
                    time.sleep(1)
                index += 1
                return url, index
            else:
                # print 'bing搜索结束，共搜索', index + 1, '页'
                return None

    def getroot(self, url, index):  # bing优化
        root = get_html(url)
        if root is None:
            return root
        else:
            error_page = root.xpath(r'//*[@id="b_results"]/li/h1/strong')
            if len(error_page) > 0:
                # print error_page[0].text
                if not error_page[0].text.find(self.domain) == -1 and index > 30:
                    # print '可能触发放爬虫机制'
                    # print url
                    return None
                else:
                    # print 'bing无结果'
                    return root
            else:
                return root

    def execute(self):
        bing_index = 0
        bing_url = None
        try:
            while True:
                bing_result = self.search_by_bing(bing_url, bing_index)
                if bing_result is None:
                    break
                else:
                    bing_url = bing_result[0]
                    bing_index = bing_result[1]
        except KeyboardInterrupt:
            print '手动退出'
        return list(self.result)


if __name__ == '__main__':
    try:
        target = sys.argv[1] if len(sys.argv) > 1 else 'cugb.edu.cn'
        a = Bing(target)
        print a.execute()
        # print a.result
    except KeyboardInterrupt:
        pass
