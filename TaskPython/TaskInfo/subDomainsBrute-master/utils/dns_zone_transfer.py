#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import socket
import dns
import re
from dns import resolver, query, exception

reload(sys)
sys.setdefaultencoding('utf-8')


class DnsZoneTransfer(object):

    def __init__(self, domain):
        self.domain = domain
        self.nameservers = []
        try:
            nss = resolver.query(domain, 'NS')
            self.nameservers = [str(ns) for ns in nss]
        except:
            pass

    def transfer(self):
        # f = open('result.txt', 'a')
        result_dict = {}
        for ns in self.nameservers:
            # print >> sys.stderr, "Querying %s" % (ns,)
            # print >> sys.stderr, "-" * 50
            z = self.query(ns)
            # print z
            # if z is not None:
            #     f.write(str(self.domain)+':  '+str(ns)+'\n')
                # print self.domain ,ns
            # print >> sys.stderr, "%s\n" % ("-" * 50,)
            if z is not None:
                result_dict.update(z)
        return result_dict

    def query(self, ns):
        nsaddr = self.resolve_a(ns)
        try:
            z = self.pull_zone(nsaddr)
        # except (exception.FormError, socket.error, EOFError):
        except Exception:
            # print >> sys.stderr, "AXFR failed\n"
            return None
        else:
            return z

    def resolve_a(self, name):
        """Pulls down an A record for a name"""
        nsres = resolver.query(name, 'A')
        return str(nsres[0])

    def pull_zone(self, nameserver):
        """Sends the domain transfer request"""
        try:
            q = query.xfr(nameserver, self.domain, relativize=False, timeout=2, lifetime=5)
        except dns.exception.Timeout:
            raise EOFError
        zone = {}

        for m in q:
            for rrset in m.answer:
                for rd in rrset:
                    # result_string = str(rrset.name).ljust(30) + str(dns.rdatatype.to_text(rrset.rdtype)).ljust(10) + rd.to_text(origin=None, relativize=True)
                    # zone += result_string + '\
                    parse_type = dns.rdatatype.to_text(rrset.rdtype)
                    if parse_type == 'A':
                        sub_domain = str(rrset.name)
                        if re.match(r'^.*\.$', sub_domain):
                            sub_domain = sub_domain[:-1]
                        zone[sub_domain] = rd.to_text(origin=None, relativize=True)
        if not zone:
            raise EOFError
        return zone

    def execute(self):
        return self.transfer()


if __name__ == '__main__':
    target = sys.argv[1] if len(sys.argv) > 1 else 'cugb.edu.cn'
    transfer = DnsZoneTransfer(target)
    result_dict = transfer.transfer()
    for key in result_dict.keys():
        print '%s: %s' % (key, result_dict[key])
