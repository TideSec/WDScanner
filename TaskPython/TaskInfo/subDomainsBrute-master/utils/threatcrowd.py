#!/usr/bin/env python
# -*- coding: utf-8 -*-

# import sys
# sys.path.append("../")

import json
import logging

import sys

from common import http_request_get, is_domain


class Threatcrowd(object):
    """docstring for Threatcrowd"""
    def __init__(self, domain):
        super(Threatcrowd, self).__init__()
        self.domain = domain
        self.subset = []
        self.website = "https://www.threatcrowd.org"

    def run(self):
        try:
            url = "{0}/searchApi/v2/domain/report/?domain={1}".format(self.website, self.domain)
            # content = curl_get_content(url).get('resp')
            content = http_request_get(url).content

            for sub in json.loads(content).get('subdomains'):
                if is_domain(sub):
                    self.subset.append(sub)

            return list(set(self.subset))
        except Exception as e:
            logging.info(str(e))
            return self.subset

    def execute(self):
        return self.run()


if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'cugb.edu.cn'
    threat = Threatcrowd(target)
    print threat.execute()
