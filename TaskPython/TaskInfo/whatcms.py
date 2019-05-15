#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Time    : 17/8/16 上午12:01
# @Author  : SecPlus
# @Site    : www.SecPlus.org
# @Email   : miacey@163.com

import Queue,sys

reload(sys)
sys.setdefaultencoding('utf8')

import requests
import json, hashlib, sys
import gevent
from gevent.queue import Queue
import time


class whatcms(object):
    def __init__(self, url,file):
        self.tasks = Queue()
        self.url = url.rstrip("/")
        self.out = open(file,'w')
        # print file
        fp = open('data.json')
        webdata = json.load(fp, encoding="utf-8")
        for i in webdata:
            self.tasks.put(i)
        fp.close()
        print("webdata total:%d" % len(webdata))

    def _GetMd5(self, body):
        m2 = hashlib.md5()
        m2.update(body)
        return m2.hexdigest()

    def _clearQueue(self):
        while not self.tasks.empty():
            self.tasks.get()

    def _worker(self):
        data = self.tasks.get()
        test_url = self.url + data["url"]
        # print test_url
        rtext = ''
        try:
            r = requests.get(test_url, timeout=0.5)
            if (r.status_code != 200):
                return
            rtext = r.text
            if rtext is None:
                return
        except:
            rtext = ''

        if data["re"]:
            if (rtext.find(data["re"]) != -1):
                result = data["name"]
                print("CMS:%s Judge:%s re:%s" % (result, test_url, data["re"]))
                self.out.write(result)
                self._clearQueue()
                return True
        else:
            md5 = self._GetMd5(rtext)
            if (md5 == data["md5"]):
                result = data["name"]
                print("CMS:%s Judge:%s md5:%s" % (result, test_url, data["md5"]))
                self.out.write(result)
                self._clearQueue()
                return True

    def _boss(self):
        while not self.tasks.empty():
            self._worker()

    def whatweb(self, maxsize=100):
        start = time.clock()
        allr = [gevent.spawn(self._boss) for i in range(maxsize)]
        gevent.joinall(allr)
        end = time.clock()
        print ("cost: %f s" % (end - start))


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("usag:python whatcms.py http://www.xxx.com")
    else:
        url = sys.argv[1]
        out  = sys.argv[2]
        g = whatcms(url,out)
        g.whatweb(1000)
