# coding=utf-8
import urllib,time,os,base64,json


def get_html(url):
	url=url.strip()
	html=urllib.urlopen(url).read()
	return html
	
def writefile(logname,cmd):
	try:
		fp = open(logname,'a')
		fp.write(cmd+"\n")
		fp.close()
	except:
		return False
		
		
def geturl(url):
	now = time.strftime('%Y-%m-%d %X', time.localtime(time.time()))
	date = time.strftime('%Y-%m-%d', time.localtime(time.time()))
	try:
		a = get_html(url)
		print a
		if len(a) > 50:
			base = base64.b64decode(a)
			print base
			json_arr = json.loads(base)
			target_url = json_arr['target_url']
			hash = json_arr['hash']
			task(target_url )
			
	except Exception , e:
		info = '%s\nError: %s' %(now,e)
		writefile('logs\\%s-Error.log'%date,info)
		print info
		

def task(target):
	print target


#exit()
url = 'http://127.0.0.1/taskinfo.php'
i = 0
while 1:
	now = time.strftime('%Y-%m-%d %X', time.localtime(time.time()))
	#print now
	try:
	#if 1==1:
		a = geturl(url)
		i +=1
		time.sleep(5)
	except Exception , e:
		info = '%s\nError: %s' %(now,e)
		writefile('Error.log',info)
		print info
		time.sleep(1)
