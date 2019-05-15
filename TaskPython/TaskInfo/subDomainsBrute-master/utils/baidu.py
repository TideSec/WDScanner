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


def geturl(url):
    parse = urlparse(url)
    url = parse.netloc
    return url


def check_contain_chinese(check_str):  # 中文检测
    for ch in check_str.decode('utf-8'):
        if u'\u4e00' <= ch <= u'\u9fff':
            return True
    return False


class Baidu(object):
    def __init__(self, domain, limit_page=40):
        self.domain = domain
        self.limit_page = limit_page
        self.result = set()

    def execute(self):
        baidu_index = 0
        net_fail = 0
        baidu_url = None

        while True:
            baidu_result = self.search_by_baidu(baidu_url, baidu_index)
            if baidu_result is None:
                break
            elif baidu_url == baidu_result[0]:
                net_fail += 1
                # print "百度搜索出错%d次" % net_fail
                if net_fail >= 5:
                    # print '百度搜索出错满五次,请检查网络'
                    break
            else:
                net_fail = 0
                baidu_url = baidu_result[0]
                baidu_index = baidu_result[1]
        return list(self.result)

    def search_by_baidu(self, url, index):
        # print 'baidu', GetNowTime()
        if url is None:
            url = r'https://www.baidu.com/s?&wd=site%3A' + self.domain
        try:
            baidu_rsp = requests.get(url=url, headers=headers, timeout=5)
        except requests.ConnectTimeout, requests.ConnectionError:
            # print e.message, "百度搜索网络错误"
            return url, index
        root = etree.HTML(baidu_rsp.text)
        urls1 = root.xpath(r'//*[@id]/div[2]/a[1]')
        urls2 = root.xpath(r'//*[@id]/div[1]/div[2]/div[2]/a[1]')
        for i in urls1:
            url = self.filtr(i.text)
            if url is None:
                continue
            else:
                self.result.add(url)
        for i in urls2:
            url = self.filtr(i.text)
            if url is None:
                continue
            else:
                self.result.add(url)
        '''
        '//*[@id="page"]/a[10]'
        '//*[@class="n"]/@href'
        百度的样式可能会变化
        '''
        next_page = root.xpath(r'//*[@class="n"]/@href')

        if len(next_page) > 0 and index < self.limit_page:
            if len(next_page) == 1:
                next_url = next_page[0]
            else:
                next_url = next_page[1]
            url = 'https://www.baidu.com' + next_url
            if index > 1000:
                time.sleep(1)
            index += 1
            return url, index
        else:
            # print '百度搜索结束，共搜索', index + 1, '页'
            return None

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


if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'cugb.edu.cn'
    a = Baidu(target)
    print a.execute()
    # print a.result
