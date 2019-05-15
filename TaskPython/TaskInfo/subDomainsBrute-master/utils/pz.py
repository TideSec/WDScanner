#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import requests
import lxml.etree as etree
import socket
from time import ctime, time
from urlparse import urlparse

reload(sys)
sys.setdefaultencoding('utf-8')

TIMEOUT = 5

'''
实现功能：根据给出的域名，首先socket域名的ip地址，然后更具bing高级搜索ip:，使用xpath，爬取包含该ip的链接，返回url及title
参数一：目标域名
'''


def gethtml(url):  # 获取必应的搜索结果
    try:
        headers = {
            'Host': 'www.bing.com',
            'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/55.0.2883.87 Chrome/55.0.2883.87 Safari/537.36'}
        r = requests.get(url, headers=headers, timeout=TIMEOUT)
        if r.status_code == 200:
            return r.text
        else:
            return None
    except KeyboardInterrupt:
        sys.exit()
    except Exception, e:
        print e
        return 'error'


end = []
check = []


def getsearchresult_url(ip, url):  # 使用搜索的地址，获取搜索结果
    try:
        html = gethtml(url)
        if not html:
            print '页没有内容，url:%s' % url
            return 'error'
        if html == 'error':  # 搜索必应失败
            return 'error'
        root = etree.HTML(html)
        lines = root.xpath('//*[@id="b_results"]')  # 结果集合
        if not lines:
            return 'error'
        else:
            lines = lines[0]
        urlandtitle = []
        for l in lines:
            url = l.xpath('.//h2/a/@href')
            title = l.xpath('.//h2/a/text()')
            if url and title:
                url = url[0]
                parser = urlparse(url)
                netloc = parser.netloc
                if netloc == 'ip.chinaz.com':
                    pass
                else:
                    title = title[0]
                    urlandtitle.append({'url': url, 'title': title})
            else:
                pass
        urls_temp = {}
        urls_temp = urlandtitle

        for u in urls_temp:
            title = u['title']
            parser = urlparse(u['url'])
            url = parser.scheme + '://' + parser.netloc + '/'
            if url not in check:
                end.append({'url': url, 'title': title})
                check.append(url)
        next_page = lines.xpath('.//*[@class="sb_pagN"]/@href')
        # time.sleep(1)

        if len(next_page) > 0:
            url = 'https://www.bing.com'+next_page[0]
            return 0,url,end
        else:
            return 1,None,end
    except Exception, e:
        print e
        return 'error'


def getbing(ip):  # 获取ip地址的域名等信息
    i = []
    searchurl = 'https://www.bing.com/search?q=ip%3a' + ip + '&qs=HS&pq=ip%3a'
    uat = getsearchresult_url(ip, searchurl)
    if not uat:
        return None
    if uat == 'error':
        return None
    while True:
        if uat[0] == 0:
            for u in uat[2]:
                if not (u['url'],u['title']) in i:
                    i.append((u['url'],u['title']))
            uat = getsearchresult_url(ip,uat[1])
        elif uat[0] == 1:
            for u in uat[2]:
                if not (u['url'],u['title']) in i:
                    i.append((u['url'],u['title']))
            break
        else:
            break
    return i


if __name__ == '__main__':
    ip = sys.argv[1]
    i = getbing(ip)
    for ii in i:
        print ii
