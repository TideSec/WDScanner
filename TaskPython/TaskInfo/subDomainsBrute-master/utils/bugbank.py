#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import requests
import json
import time

reload(sys)
sys.setdefaultencoding('utf-8')

'''
通过调取bugbank的api获取子域名
http://www.bugbank.cn/api/subdomain/collect?domain=目标网页&page=页数
'''


class Bugbank(object):
    def __init__(self, domain):
        self.domain = domain
        self.result_dict = {}

    def execute(self):
        page = 1
        max_page = 1
        subdomain_set = set()
        while True:
            url = 'http://www.bugbank.cn/api/subdomain/collect?domain=%s&page=%d' % (self.domain, page)
            try:
                rsp = requests.get(url=url, timeout=5)
                date_dict = json.loads(rsp.text)
                if page == 1:
                    total = date_dict["page"]["total"]
                    max_page = int((total + 9) / 10)
                    # print max_page  # 最大页数
                    # time.sleep(2)
                current_page = date_dict["page"]["current"]
                # print "当前第", current_page, '页'
                # time.sleep(1)
                for i in date_dict["data"]:
                    '''结果打印和集合添加'''
                    # print i['domain']
                    # subdomain_set.add(i['domain'])
                    self.result_dict[i['domain']] = i['ips']
                page += 1
                if page > max_page:
                    break
            except KeyboardInterrupt:
                # print '手动停止退出'
                break
            except requests.exceptions.ConnectionError, requests.exceptions.ConnectTimeout:
                # print '网络问题退出'
                break
            except Exception, e:
                # print "其它问题退出:", e.message
                break
        return self.result_dict


if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'cugb.edu.cn'
    a = Bugbank(target)
    print a.execute()
