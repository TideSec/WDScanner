#!/usr/bin/env python
# -*- coding: utf-8 -*-

import json
import time
import requests

class Captcha(object):
    """docstring for Captcha"""
    def __init__(self):
        super(Captcha, self).__init__()
        self.url = 'http://api.ysdm.net/create.json'
        self.username = 'a61323636'
        self.password = '123456'
        self.timeout = 90
        self.softid = 1
        self.softkey = 'b40ffbee5c1cf4e38028c197eb2fc751'
        self.typeid = 3000

    def verification(self, filename):
        (cnt,retry) = (0, 3)
        while True:
            try:
                if cnt >= retry:
                    break # over max_retry_cnt
                payload = {
                    'username': self.username,
                    'password': self.password,
                    'timeout': self.timeout,
                    'softid': self.softid,
                    'softkey': self.softkey,
                    'typeid': self.typeid,
                }
                multiple_files = [('image', ('captcha.gif', open(filename, 'rb'), 'image/gif')),]
                r = requests.post(self.url, data=payload, files=multiple_files)
                return json.loads(r.text)
            except Exception, e:
                cnt += 1
                print('{0} [INFO] {1}'.format(
                    time.strftime('%Y-%m-%d %H:%M:%S'), str(e)))
            else:
                cnt = 0


# captcha = Captcha()
# imgurl = 'http://ce.wooyun.org/captcha.php'
# print captcha.verification(imgurl)


