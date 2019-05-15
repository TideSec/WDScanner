#!/usr/bin/env python
# -*- coding: utf-8 -*-


import os
import re
import json
import requests
import argparse
from lxml import etree
import utils
import globalresult

'''
通过证书和
crt.sh
查找子域名

谷歌由于防火墙和验证码排除
'''


class GetBySsl(object):
    # Todo: 对于 CDN 和 WWW 域名未处理

    def __init__(self, raw_domain, verbose=False):
        self.raw_domain = raw_domain
        self.TIMEOUT = 20
        self.x = []
        self.verbose = verbose
        self.PREFIX_URL1 = 'https://www.'
        self.PREFIX_URL2 = 'https://'

    def is_https1(self):
        url = self.PREFIX_URL2 + self.raw_domain
        try:
            rsp = requests.get(url, timeout=4)
        except Exception, e:
            return False
        return True

    def is_https2(self):
        url = self.PREFIX_URL1 + self.raw_domain
        try:
            rsp = requests.get(url, timeout=4)
        except Exception, e:
            return False
        return True

    def get_json(self, x, token=''):  # 爬取下一页需要上一个请求中的 nextPageToken
        url = 'https://www.google.com/transparencyreport/jsonp/ct/search?domain=%s&incl_exp=true&incl_sub=true&c=data&token=%s' % (
            self.raw_domain, token)
        try:
            r = requests.get(url, timeout=self.TIMEOUT)
            if r.status_code == 200:
                data = r.text[23:-3]
                j = json.loads(data)
                x += j['results']

                if 'nextPageToken' in j:
                    self.get_json(x, j['nextPageToken'])  # 递归
                    return x

        except Exception, e:
            print e

    def get_domains_from_google(self):  # 从 Google 获取域名  Todo: 多线程
        domains = set()
        results = self.get_json(self.x, '')
        if results is None:
            print('[!] There is no HTTPS domain for %s.' % self.raw_domain)
        else:
            for d in results:
                if 'firstDnsName' in d:
                    domains.add(d['firstDnsName'].replace("*.", ""))
                domains.add(d['subject'].replace("*.", ""))

        if self.raw_domain in domains:
            domains.remove(self.raw_domain)  # 排除用户输入的（根）域名
            if domains:
                print('[+] Number of Domains: %s\n%s' % (len(domains), domains))
            else:
                print('[!] It uses Wildcard Certificates for root domain.')

        return domains

    def get_domains_from_crt(self):  # 从 crt.sh 获取域名
        domains = set()
        url = "https://crt.sh/?Identity=%%.%s" % self.raw_domain
        # print url
        head = {'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Encoding': 'gzip, deflate, sdch, br',
                'Accept-Language': 'zh-CN,zh;q=0.8',
                'Cache-Control': 'max-age=0',
                'Connection': 'keep-alive',
                'Host': 'crt.sh'}
        try:
            r = requests.get(url, headers=head, timeout=self.TIMEOUT)
            if r.status_code == 200:
                root = etree.HTML(r.text.encode("utf-8"))
                td_info = root.xpath(r'.//td[@class="outer"]/table/tr/td[4]')
                for td in td_info:
                    if td.text:  # 排除空的情况
                        if "@" not in td.text and 'SingleDomain' not in td.text:  # 排除邮箱等情况
                            domain = td.text.split("=")[-1].replace("*.", "")
                            domains.add(domain)
        except Exception, e:
            pass
            # print e
        return domains

        # if self.raw_domain in domains:
        #     domains.remove(self.raw_domain)
        #     if domains:
        #         print('[+] Number of Domains: %s\n%s' % (len(domains), domains))
        #         print '22222:',domains
        #         return domains
        #     else:
        #         print(
        #             '[!] There is no HTTPS domain for %s or it uses Wildcard Certificates for root domain.' % self.raw_domain)

    def get_domains_from_openssl(self):  # 使用 OpenSSL 的 SAN 获得域名，有命令注入风险
        domains = set()
        cmd = 'openssl s_client -showcerts -connect %s:443 < /dev/null 2>/dev/null | openssl x509 -text | grep -A 1 "Subject Alternative Name"' % self.raw_domain
        try:
            tmp = os.popen(cmd).readlines()
            data = re.split(r'DNS:', tmp[1].strip())
            for i in data:
                if i:
                    domains.add(i.replace(', ', '').replace('*.', ''))

            if self.raw_domain in domains:
                domains.remove(self.raw_domain)
                if domains and self.verbose:
                    print('[+] Number of Domains: %s\n%s' % (len(domains), domains))
                elif self.verbose:
                    print('[!] Its Subject Alternative Name is exactly itself.')

        except Exception, e:
            if self.verbose:
                print('[!] You have no OpenSSL or there is no HTTPS domain for %s.' % self.raw_domain)

        return domains

    def get_domains(self):
        # print('[-] Get Domains from OpenSSL ...')
        openssl_domains = set()
        crt_domains = set()
        if self.is_https1() or self.is_https2():
            openssl_domains = self.get_domains_from_openssl()
        # print('[-] Get Domains from crt.sh ...')
        crt_domains = self.get_domains_from_crt()
        # print('[-] Get Domains from Google, it will be a little slow ...')
        # google_domains = self.get_domains_from_google()

        # if crt_domains or google_domains or openssl_domains:
        #     total_domains = (crt_domains | google_domains | openssl_domains)
        #     print('[√] Total Domains: %s\n%s' % (len(total_domains), total_domains))
        # else:
        #     print('[X] Oops, there is nothing!')
        total_domains = set()
        if openssl_domains or crt_domains:
            total_domains = (openssl_domains | crt_domains)
            # utils.print_result_list(list(total_domains))
            # print('[√] Total Domains: %s\n%s' % (len(total_domains), total_domains))
        else:
            # print('[X] Oops, there is nothing!')
            pass
        return list(total_domains)

    def execute(self, *args, **kwargs):
        result_list = self.get_domains()
        globalresult.add_list(result_list)
        return result_list


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description="Get Domains by SSL")
    parser.add_argument("domain", help="The domain that you want to test. e.g. jd.com")
    raw_domain = parser.parse_args().domain
    # GetBySsl(raw_domain).get_domains()
    print GetBySsl(raw_domain).execute()