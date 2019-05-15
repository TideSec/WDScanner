#! /usr/bin/env python
# -*- coding: utf-8 -*-
import socket
from utils.alexa import Alexa
from utils.threatcrowd import Threatcrowd
from utils.threatminer import Threatminer
from utils.crt import Crt
from utils.i_links import ILinks
from utils.ip138 import Ip138
from utils.netcraft import Netcraft
from utils.bugbank import Bugbank
from utils.bing import Bing
from utils.baidu import Baidu
from utils.hack_target import HackTarget

'''
- alexa
- page catcher
- ssl crt
- ilinks
- ip138
'''
def get_subd(domain):
	subdomains = []
	realdomains = []
	subdomains.extend(Ip138(domain).execute())
	subdomains.extend(Alexa(domain).execute())
	subdomains.extend(Crt(domain).execute())
	subdomains.extend(ILinks(domain).execute())
	subdomains.extend(Threatcrowd(domain).execute())
	subdomains.extend(Threatminer(domain).execute())
	subdomains.extend(Netcraft(domain).execute())
	subdomains.extend(Bugbank(domain).execute())
	subdomains.extend(Bing(domain).execute())
	subdomains.extend(Baidu(domain).execute())
	subdomains.extend(HackTarget(domain).execute())

	subdomains = list(set(subdomains))

	for target in subdomains:
		try:
			mainHost, mainHost, C_ip = socket.gethostbyname_ex('wildcardfake.' + target)
		except:
			if target.endswith(domain):
				realdomains.append(str(target))
			pass
	
	realdomains = list(set(realdomains))
	return realdomains

if __name__ == '__main__':
	import sys,time
	target = sys.argv[1] if len(sys.argv) > 1 else 'gznu.edu.cn'
	start = time.time()
	print get_subd(target)
	print (time.time()-start)/60