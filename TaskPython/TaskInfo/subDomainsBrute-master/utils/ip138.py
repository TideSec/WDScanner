#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""从ip138中的相关网页获取子域名,模仿wydomain的写法"""

import sys
import logging
import re

from common import http_request_get, is_domain


class Ip138(object):
    """docstring for IP138"""

    def __init__(self, domain):
        super(Ip138, self).__init__()
        self.domain = domain
        self.subset = []

    def run(self):
        try:
            self.fetch_ip138()
            return list(set(self.subset))
        except Exception as e:
            logging.info(str(e))
            return self.subset

    def fetch_ip138(self):
        """get subdomains from ip138.com"""

        url = 'http://site.ip138.com/{0}/domain.htm'.format(self.domain)
        r = http_request_get(url).content
        regx = r'<a.*>(.*\.%s)</a>' % self.domain
        # subs = re.compile(r'(?<="\>\r\n<li>).*?(?=</li>)')
        result = re.findall(regx, r)
        for sub in result:
            # print sub
            if is_domain(sub):
                self.subset.append(sub)

    def execute(self):
        return self.run()

if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'cugb.edu.cn'
    a = Ip138(target)
    print a.execute()