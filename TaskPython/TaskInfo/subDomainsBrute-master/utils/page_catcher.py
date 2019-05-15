#!/usr/bin/env python
# -*- coding: utf-8 -*-

import re
import sys
from urlparse import urlparse
import utils

import requests
import globalresult

reload(sys)
sys.setdefaultencoding('utf-8')

"""
直接爬取域名对应的页面，从 html 中查找子域名。
从0开始，每加1多一层
"""

urls = []
ym3s = []
ym4s = []
control = {}
add = []


def get_root_domain(target_domain):
    return '.'.join(target_domain.split( '.')[-2:])


class PageCatcher(object):
    def __init__(self, target_domain, depth=0, proxy=None):
        self.target_domain = target_domain
        # if key.startswith('www.'):
        #     key = key[4:]
        self.key = get_root_domain(target_domain)
        self.depth = depth

    def execute(self, *args, **kwargs):
        """
        爬虫执行方法

        :param target_domain: 目标域名
        :param depth: 爬取深度
        :param proxy: 代理
        :return: 子域名列表

        url爬取出新的url存入control[0]中，
        从control[0]中提取url进行爬取存入control[1]中，遍历control中所有
        url提取域名并保存。

        主要问题：重复量较多，处理速度慢

        """
        domains = self.get_ym(self.target_domain)
        # utils.print_result_list(domains)
        '''
        打印结果
        '''
        # globalresult.add_list(domains)
        return domains

    def get_html(self, url):
        TIMEOUT = 5
        # headers = {'User-Agent':'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 \
        #            (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11',
        #     'Accept':'text/html;q=0.9, */*;q=0.8',
        #     'Accept-Charset':'ISO-8859-1, utf-8;q=0.7, *;q=0.3',
        #     'Accept-Encoding':'gzip',
        #     'Connection':'close',
        #     'Referer':None
        # }

        if url:
            if url.startswith('http'):
                url = url
            elif len(url.split('.')) == 2:
                url = 'http://www.' + url
            else:
                url = 'http://' + url
            try:
                r = requests.get(url, timeout=TIMEOUT)
                r.text.encode(encoding='utf-8')
                if r.status_code == 200:
                    return r.text
                return []
            # except KeyboardInterrupt:
            #     print '退出了哦'
            except:
                return []

    def get_url(self, url):
        key = self.key
        u = []
        if url in add:
            # print '这个页面爬取过了'
            return []
        else:
            add.append(url)
            # print len(add)
            html = self.get_html(url)  # 获取页面源码
        if not html:
            return []

        res = '(\'|")?((http|ftp)s?://.*?)(\'|")'  # 正则表达式匹配('http://')
        ret = re.findall(res, html)

        # if 'news' in url:
        #     return []
        # if 'tieba' in url:
        #     return []
        # if 'timg' in url :
        #     return []
        # if 'music' in url:
        #     return []
        # if 'wenku' in url:
        #     return []
        for r in ret:
            r = r[1]
            # print r, '###'
            if r.startswith('this') or r.startswith('sogou'):
                continue
            if r.endswith('.jpg') or r.endswith('.png') or r.endswith('.gif') or \
                    r.endswith('.pdf') or r.endswith('.exe') or r.endswith('.apk'):
                continue
            if r.endswith('js') or r.endswith('css'):
                continue
            # 以上这些内容的排除掉
            if r not in u:
                if key in r:
                    if self.similar(r, u):
                        u.append(r)

        # print u,'-----------',url
        return u

    def similar(self, url, urls):
        # 定义方法判断相似度
        url = url.split('/')
        if not urls:
            return True
        for u in urls:
            u = u.split('/')
            if len(url) >= 4 and len(u) >= 4:
                if url[0] == u[0] and url[1] == u[1] and url[2] == u[2]:
                    if url[3] == u[3]:
                        return False
                    if url[3][:3] == u[3][:3]:
                        return False
            elif len(u) == 3 and len(url) == 3:
                if url[0] == u[0] and url[1] == u[1] and url[2] == u[2]:
                    return False

        return True

    def controller(self, url, ):
        key = self.key
        limit = self.depth
        # define level, 0: root
        count = 0
        # 创建空dict存储domian
        for i in range(int(limit) + 2):
            control[i] = []

        u = self.get_url(url)
        control[count] = u
        while count < int(limit):
            try:
                for c in control[count]:
                    uu = self.get_url(c)
                    if uu:
                        for uuu in uu:
                            if self.similar(uuu, control[count + 1]):
                                control[count + 1].append(uuu)
                                # print uuu, '***'
            except KeyboardInterrupt:
                print '退出了哦'
                sys.exit(0)
            except Exception, e:
                print e
                return []
            count = count + 1
            # 数据存储在control中，contrl= {count1：['http:./']....}
            # whiletrue控制循环，当count大于limit之后退出循环
        return control
        # 推出循环后返回获得的所有内容

    def get_ym(self, url):
        limit = self.depth
        key = self.key
        cc = self.controller(url)

        for i in range(int(limit) + 2):
            for ii in cc[i]:
                if ii:
                    urls.append(ii)

        for i in urls:
            parse = urlparse(i)
            n = parse.netloc
            # 取出其中的二级域名，如果不存在便保存起来

            if key in n:
                if '=' not in n and ';' not in n and '?' not in n and ':' not in n and \
                                '@' not in n and '&' not in n and '>' not in n and '<' not in n and r'\\' not in n:
                    if n not in ym3s:
                        ym3s.append(n)
        return ym3s


class RecursivePageCatcher(object):
    """递归爬取"""

    def __init__(self, target_domains, depth=0, proxy=None):
        self.target_domains = target_domains
        self.key = get_root_domain(target_domains[0])
        self.depth = depth

    def execute(self, *args, **kwargs):
        """
        爬虫执行方法

        :param target_domains: 域名列表
        :param depth: 爬取深度
        :param proxy: 代理
        :return: 子域名列表
        """

        domain = []
        for i in self.target_domains:
            ym = PageCatcher(self.key, i, self.depth).execute()
            for y in ym:
                if y not in domain:
                    domain.append(y)
        return domain
        # domains = get_ym(target_domain, target_domain, depth)
        # return domains


if __name__ == '__main__':
    url = sys.argv[1]
    key = sys.argv[1]
    limit = sys.argv[2]
    print '站点：', url, '层级', limit
    yms = PageCatcher(key, url, limit).execute()
    print url, '中有', len(yms), '域名'
    # for y in yms:
    #     print y
    # ym = RecursivePageCatcher(key, yms, limit).execute()
    # for i in ym:
    #     print i
