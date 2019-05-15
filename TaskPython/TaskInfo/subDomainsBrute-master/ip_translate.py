#!/usr/bin/env python
# -*- coding:utf-8 -*-
import json
import time
import datetime
import pexpect
from lib.threadpool import ThreadPool as Pool

def interpret(results,domain):

	iplist = []
	ipcount = []

	for x in results:
	    iplist.extend(str(x['ip']))

	#获取ip范围值
	#192.168.1
	ip={'.'.join(r.split(".")[0:3]) for r in iplist}
	ip = list(ip)

	#如果取值c段在范围内
	for i in range(0,len(ip)):
	    for ipaddr in list(iplist):
	    	#会出现192.168.1 与192.168.11的问题.增加.
	        if ip[i]+"." in ipaddr:
	        	#采用id来区分
	            res = {"id":i,"ip":ipaddr,"ipc":ip[i]}
	            if res not in ipcount:
	                ipcount.append(res)

	def is_intranet(ip):
	    """
	    匹配内网ip地址
	    """
	    ret = ip.split('.')
	    if not len(ret) == 4:
	        return True
	    if ret[0] == '10':
	        return True
	    if ret[0] == '127' and ret[1] == '0':
	        return True
	    if ret[0] == '172' and 16 <= int(ret[1]) <= 32:
	        return True
	    if ret[0] == '192' and ret[1] == '168':
	        return True
	    return False

	def getnum(a):
		#经典的排序法
		for i in range(0,len(a)):
		    for j in range(i+1,len(a)):
		        first=int(a[i])
		        second=int(a[j])
		        if first<second:
		            a[i]=a[j]
		            a[j]=first
		return a

	def getip(item):
		result = []
		ipslist = []
		for addr in ipcount:
			if id(addr['id']) == id(item):
				ipc = addr['ipc']
				#去掉c段的前缀，用于排序以及比较大小
				result.append(addr['ip'].replace(ipc+".",''))
		if len(result)==1:
			ipslist = ipc+"."+str(result[0])
		else:
			resu = getnum(list(set(result)))
			try:
				maxnum = resu[0]
				minnum = resu[len(resu)-1]
				for x in range(int(minnum),int(maxnum)+1):
					ip = ipc+"."+str(x)
					if is_intranet(ip) is False:
						ipslist.append(ipc+"."+str(x))
			except Exception as e:
				print str(e)
			
		return ipslist


	mylist = []
	for ifc in ipcount:
	    mylist.append(ifc['id'])

	myset = list(set(mylist))

	iplists = []
	ipclist = []
	for item in myset:
		if mylist.count(item)==1:
			iplists.append(getip(item))
		else:
			iplists.extend(getip(item))

	for ilist in iplists:
		if ilist not in iplist:
			ipclist.append(ilist)
	ipclist = list(set(ipclist))
	if len(ipclist)>0:
		ret = {"domain":"get_crpret."+domain,"ip":ipclist}
		results.append(ret)
	return results

def run_comand32(runcmd):
    scanner = ''
    try:
        child1 = pexpect.spawn(runcmd,timeout=2400)
        out = child1.readlines()
        for out_item in out:
            if len(out_item) == 0:
                return scanner
            else:
                scanner = out_item.strip()
            return scanner
    except Exception as e:
        print runcmd,str(e)
        pass
	

def trs(args):
    import os
    iplist,domain = args
    tp = Pool(3)
    path = os.path.split(os.path.realpath(__file__))[0]
    for ip in iplist:
        command = "python {path}/../whatcms/portscan.py --host {ip} --domain {domain}"
        if domain.find('get_crpret')==-1:
            runcmd = command.format(path=path,ip=ip,domain=domain)
        else:
            runcmd = command.format(path=path,ip=ip,domain=ip)
        tp.push(run_comand32, runcmd)
    tp.wait()
    tp.busy()