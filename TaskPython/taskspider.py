#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Time    : 17/8/24 上午11:07
# @Author  : 重剑无锋
# @Site    : www.tidesec.net
# @Email   : 6295259@qq.com

#依赖于wvs的结果，将wvs返回的每个Url打开查找链接

import Queue
import random
import threading
import time
import urllib, time, os, base64, json

import re, sys
import urllib2
from copy import deepcopy
from sgmllib import SGMLParser

import requests

reload(sys)
sys.setdefaultencoding('utf8')

global hash



header = {"Accept": "text/html,application/xhtml+xml,application/xml;",
                  "Accept-Encoding": "gzip",
                  "Accept-Language": "zh-CN,zh;q=0.8",
                  "Referer": "http://www.baidu.com/link?url=www.so.com&url=www.soso.com&&url=www.sogou.com",
                  "User-Agent": "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36"
                  }

def url_protocol(url):
    domain = re.findall(r'.*(?=://)', url)
    if domain:
        return domain[0]
    else:
        return url

def same_url(urlprotocol,url):
    url = url.replace(urlprotocol + '://', '')
    if re.findall(r'^www', url) == []:
        sameurl = 'www.' + url
        if sameurl.find('/') != -1:
            sameurl = re.findall(r'(?<=www.).*?(?=/)', sameurl)[0]
        else:
            sameurl = sameurl + '/'
            sameurl = re.findall(r'(?<=www.).*?(?=/)', sameurl)[0]
    else:
        if url.find('/') != -1:
            sameurl = 'www.' + re.findall(r'(?<=www.).*?(?=/)', url)[0]
        else:
            sameurl = url + '/'
            sameurl = 'www.' + re.findall(r'(?<=www.).*?(?=/)', sameurl)[0]
    print('the domain is：' + sameurl)
    return sameurl


class linkQuence:
    def __init__(self):
        self.visited = []    #已访问过的url初始化列表
        self.unvisited = []  #未访问过的url初始化列表
        self.external_url=[] #外部链接

    def getVisitedUrl(self):  #获取已访问过的url
        return self.visited
    def getUnvisitedUrl(self):  #获取未访问过的url
        return self.unvisited
    def getExternal_link(self):
        return self.external_url   #获取外部链接地址
    def addVisitedUrl(self,url):  #添加已访问过的url
        return self.visited.append(url)
    def addUnvisitedUrl(self,url):   #添加未访问过的url
        if url != '' and url not in self.visited and url not in self.unvisited:
            return self.unvisited.insert(0,url)
    def addExternalUrl(self,url):   #添加外部链接列表
        if url!='' and url not in self.external_url:
            return self.external_url.insert(0,url)

    def removeVisited(self,url):
        return self.visited.remove(url)
    def popUnvisitedUrl(self):    #从未访问过的url中取出一个url
        try:                      #pop动作会报错终止操作，所以需要使用try进行异常处理
            return self.unvisited.pop()
        except:
            return None
    def unvisitedUrlEmpty(self):   #判断未访问过列表是不是为空
        return len(self.unvisited) == 0

class Spider():
    '''
    真正的爬取程序
    '''
    def __init__(self,url,domain_url,urlprotocol):
        self.linkQuence = linkQuence()   #引入linkQuence类
        self.linkQuence.addUnvisitedUrl(url)   #并将需要爬取的url添加进linkQuence对列中
        self.current_deepth = 1    #设置爬取的深度
        self.domain_url = domain_url
        self.urlprotocol = urlprotocol

    def getPageLinks(self,url):
        '''
        获取页面中的所有链接
        '''
        try:

            # pageSource=urllib2.urlopen(url).read()
            time.sleep(0.5)
            pageSource = requests.get(url, timeout=5, headers=header).text.encode('utf-8')
            pageLinks = re.findall(r'(?<=href=\").*?(?=\")|(?<=href=\').*?(?=\')', pageSource)
            # print pageLinks

        except:
            # print ('open url error')
            return []
        return pageLinks

    def processUrl(self,url):
        '''
        判断正确的链接及处理相对路径为正确的完整url
        :return:
        '''
        true_url = []
        in_link = []
        excludeext = ['.zip', '.rar', '.pdf', '.doc', '.xls', '.jpg','.mp3','.mp4','.mpg','.wmv','.wma']
        for suburl in self.getPageLinks(url):
            exit_flag = 0
            for ext in excludeext:
                if ext in suburl:
                    print "break:" + suburl
                    exit_flag = 1
                    break
            if exit_flag == 0:
                if re.findall(r'/', suburl):
                    if re.findall(r':', suburl):
                        true_url.append(suburl)
                    else:
                        true_url.append(self.urlprotocol + '://' + self.domain_url + '/' + suburl)
                else:
                    true_url.append(self.urlprotocol + '://' + self.domain_url + '/' + suburl)

        for suburl in true_url:
            print('from:' + url + ' get suburl：' + suburl)

        return true_url

    def sameTargetUrl(self,url):
        same_target_url = []
        for suburl in self.processUrl(url):
            if re.findall(self.domain_url,suburl):
                same_target_url.append(suburl)
            else:
                self.linkQuence.addExternalUrl(suburl)
        return same_target_url

    def unrepectUrl(self,url):
        '''
        删除重复url
        '''
        unrepect_url = []
        for suburl in self.sameTargetUrl(url):
            if suburl not in unrepect_url:
                unrepect_url.append(suburl)
        return unrepect_url

    def crawler(self,crawl_deepth=1):

        self.current_deepth=0
        while self.current_deepth < crawl_deepth:
            if self.linkQuence.unvisitedUrlEmpty():break
            links=[]
            while not self.linkQuence.unvisitedUrlEmpty():
                visitedUrl = self.linkQuence.popUnvisitedUrl()
                if visitedUrl is None or visitedUrl == '':
                    continue
                print("#"*30 + visitedUrl +" :begin"+"#"*30)
                for sublurl in self.unrepectUrl(visitedUrl):
                    links.append(sublurl)
                # links = self.unrepectUrl(visitedUrl)
                self.linkQuence.addVisitedUrl(visitedUrl)
                print("#"*30 + visitedUrl +" :end"+"#"*30 +'\n')
            for link in links:
                self.linkQuence.addUnvisitedUrl(link)
            self.current_deepth += 1
        # print(self.linkQuence.visited)
        # print (self.linkQuence.unvisited)
        urllist=[]
        for suburl in self.linkQuence.getVisitedUrl():
            urllist.append(suburl)
        # urllist.append("#"*30 + ' UnVisitedUrl '+ "#"*30)
        for suburl in self.linkQuence.getUnvisitedUrl():
            urllist.append(suburl)
        return urllist
def writelog(domain_url,urllist):
    filename=domain_url + '.txt'
    outfile=open(filename,'w')
    for suburl in urllist:
        outfile.write(suburl+'\n')
    outfile.close()
def urlspider(url,crawl_deepth=66):
    # ext_link = []
    urlprotocol = url_protocol(url)
    domain_url = same_url(urlprotocol,url)
    print "domain_url:"+domain_url
    spider = Spider(url,domain_url,urlprotocol)
    urllist=spider.crawler(crawl_deepth)
    return urllist


class Worker(threading.Thread):  # 处理工作请求
    def __init__(self, workQueue, resultQueue, **kwds):
        threading.Thread.__init__(self, **kwds)
        self.setDaemon(True)
        self.workQueue = workQueue
        self.resultQueue = resultQueue

    def run(self):
        while 1:
            try:
                callable, args, kwds = self.workQueue.get(False)  # get task
                res = callable(*args, **kwds)
                self.resultQueue.put(res)  # put result
            except Queue.Empty:
                break


class WorkManager:  # 线程池管理,创建
    def __init__(self, num_of_workers=10):
        self.workQueue = Queue.Queue()  # 请求队列
        self.resultQueue = Queue.Queue()  # 输出结果的队列
        self.workers = []
        self._recruitThreads(num_of_workers)

    def _recruitThreads(self, num_of_workers):
        for i in range(num_of_workers):
            worker = Worker(self.workQueue, self.resultQueue)  # 创建工作线程
            self.workers.append(worker)  # 加入到线程队列

    def start(self):
        for w in self.workers:
            w.start()

    def wait_for_complete(self):
        while len(self.workers):
            worker = self.workers.pop()  # 从池中取出一个线程处理请求
            worker.join()
            if worker.isAlive() and not self.workQueue.empty():
                self.workers.append(worker)  # 重新加入线程池中
                # logging.info('All jobs were complete.')

    def add_job(self, callable, *args, **kwds):
        self.workQueue.put((callable, args, kwds))  # 向工作队列中加入请求

    def get_result(self, *args, **kwds):
        return self.resultQueue.get(*args, **kwds)


class MyParser(SGMLParser):
    def __init__(self):
        self.data = ""
        self.links = []
        self.TAG_BEG = False
        self.TAG_END = False
        SGMLParser.__init__(self, 0)

    def handle_data(self, data):
        if (self.TAG_BEG is True) and (self.TAG_END is False):
            self.data += data
        pass

    def start_title(self, attrs):
        self.link = ""
        self.data = ""

        self.TAG_BEG = True
        self.TAG_END = False
        for (key, val) in attrs:
            if key == "href":
                self.link = val

    def end_title(self):
        self.TAG_BEG = False
        self.TAG_END = True

        self.title = self.data.strip()

    def flush(self):
        pass

    def handle_comment(self, data):
        pass

    def start_a(self, attrs):
        self.data = ""

        self.TAG_BEG = True
        self.TAG_END = False
        for (key, val) in attrs:
            if key == "href":
                self.link = val

    def end_a(self):
        self.TAG_BEG = False
        self.TAG_END = True
        tmp = {}
        tmp["name"] = self.data
        tmp["link"] = self.link
        self.links.append(deepcopy(tmp))

    def unknown_starttag(self, tag, attrs):
        pass

    def unknown_endtag(self, tag):
        pass

    def unknown_entityref(self, ref):
        pass

    def unknown_charref(self, ref):
        pass

    def unknown_decl(self, data):
        pass

    def close(self):
        SGMLParser.close(self)
        self.flush()


def lst2str(lst):
    string = ""
    for item in lst:
        string += item.strip() + "\n"
    return string


def downURL(url, filename):
    global hash
    print "Download %s, save as %s" % (url, filename)
    try:
        fp = urllib2.urlopen(url)
    except:
        print "download exception"
        print sys.exc_info()
        return 0
    op = open('./logspider/' + hash + '/' + filename, "wb")
    while 1:
        s = fp.read()
        if not s:
            break
        op.write(s)
    fp.close()
    op.close()
    return 1


def get_filters(path):
    if path is None:
        return

    filters = []
    with open(path, 'r') as f:
        for line in f.readlines():
            if "\n" in line:
                filters.append(line[:-1])
            else:
                filters.append(line)
    return filters


def check_key(html):
    # 敏感字检测
    try:

        filters = get_filters("filters.txt")
        content = html
        if re.search('gb2312', content):
            content = content.decode('gbk', 'replace').encode('utf-8')

        for filter_word in filters:
            if filter_word in content:
                return "1####" + filter_word
    except Exception as e:
        print str(e)
        return "0"



def check(item, base_url, urllog):
    try:
        if item[0:4] == 'http':
            proto, rest = urllib.splittype(item)
            host, rest = urllib.splithost(rest)
            local_file = (host + rest).replace('/', '_')
            if host[0:3] == 'www':
                host = host[4:]
        elif item[0:3] == 'www':
            host = item.split('/')[0]
            local_file = item.replace('/', '_')
        else:
            host = item
            local_file = item.replace('/', '_')

        
        html = get_html(item)
        print "+++++++++++++++check_bad_url:"+item+"+++++++++++++++++++++"
        if not html:
            badurl = "bad##" + item + " --- Parent_Page:" + base_url + '+++' + '\n'
            urllog.write(badurl)
        else:
            print "+++++++++++++++check_key_url:"+item+"+++++++++++++++++++++"
            key = str(check_key(html))
            if len(key) > 4:
                key = key.split("####")
                # print key
                if len(key) == 2:
                    keyurl = "key##" + item + " --- Parent_Page:" + base_url + " --- KeyWord:" + key[
                        1] + '+++' + '\n'
                else:
                    keyurl = "key##" + item + " --- Parent_Page:" + base_url + " --- KeyWord:赌博||彩票||百家乐||博彩||太阳城" + '+++' + '\n'
                print keyurl
                urllog.write(keyurl)
                downURL(item, local_file)
            else:
                pass

    except Exception as e:
        print str(e)
        return 0


def reptile(base_url):
    try:
        urlall_list = []
        page_list = []
        global hash
        file = './logspider/' + hash + '/urllog.txt'
        urllog = open(file, 'a+')
        urlall = './logspider/' + hash + '/urlall.txt'
        temp = open(urlall, 'a+')
        temp.close()
        urls = open(urlall, 'r+')
        for url in urls.readlines():
            urlall_list.append(url.strip('\n'))

        if not len(base_url):
            print "No page to reptile!"
            sys.exit(1)

        parser = MyParser()

        if base_url.startswith("http"):
            myopen = urllib2.urlopen
        else:
            myopen = open

        try:
            content = myopen(base_url).read()
        except:
            print "Failed to read from %s." % base_url
            print sys.exc_info()
            return 0
        # print content

        for item in content:
            parser.feed(item)

        for tmp in parser.links:
            page_list.append(tmp.get("link"))

        # global title
        # title = parser.title
        parser.close()
        item_list = list(set(page_list))

        proto, rest = urllib.splittype(base_url)
        host, rest = urllib.splithost(rest)
        if base_url[0:4] == 'http':
            base_domain = proto + '://' + host
        elif base_url[0:3] == 'www':
            base_domain = base_url.split('/')[0]
        else:
            base_domain = base_url

        wm = WorkManager(20)
        for item in item_list:
            pos = item.find('#')
            if pos != -1:
                item = item[:pos]

            if not item.startswith("http"):
                item = base_domain + '/' + item
                pass

            # print urlall_list
            if item not in urlall_list:
                urls.write(item + '\n')
                urlall_list.append(item)
            else:
                continue
            print item
            wm.add_job(check, item, base_url, urllog)
        wm.start()
        wm.wait_for_complete()

        urllog.close()
        urls.close()
    except:
        return False


def get_html(url):
    try:
        url = url.strip()
        header = {"Accept": "text/html,application/xhtml+xml,application/xml;",
                  "Accept-Encoding": "gzip",
                  "Accept-Language": "zh-CN,zh;q=0.8",
                  "Referer": "http://www.baidu.com/link?url=www.so.com&url=www.soso.com&&url=www.sogou.com",
                  "User-Agent": "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36"
                  }

        # html = urllib.urlopen(url).read()
        html = requests.get(url, timeout=3, verify=False, headers=header).content
        # print html
        return html
    except:
        return 'False'


def writefile(logname, cmd):
    try:
        fp = open(logname, 'a')
        fp.write(cmd + "\n")
        fp.close()
    except:
        return False

def geturl(url):
    global hash
    now = time.strftime('%Y-%m-%d %X', time.localtime(time.time()))
    date = time.strftime('%Y-%m-%d', time.localtime(time.time()))
    try:
        a = get_html(url)
        # print a
        if len(a) > 50:
            # print a
            target_urls = a.split('<br>')
            
            hash = target_urls[0][-32:]
            print os.getcwd()
            if not os.path.exists('./logspider/' + hash):
                os.mkdir('./logspider/' + hash)
            print hash
            site_url = target_urls[1]
            print "site_url:"+site_url
            spider_urls = urlspider(site_url, 5)
            target_url = target_urls[1:]
            num = 0
            for spider_url in spider_urls:
                target_url.append(spider_url)
            target_url = list(set(target_url))
            num_all = len(target_url)
            for url in target_url:
                # pass
                print str(num_all)+'---'+str(num)
                print url
                num = num + 1
                reptile(url)
            # reptile_simple(url)
            done = open('./logspider/' + hash + '/done.txt', 'w+')
            done.close()
            # exit(0)
        else:
            print "Nothing To Do"
    except Exception, e:
        info = '%s\nError: %s' % (now, e)
        writefile('%s-Error.log' % date, info)
        # print info



url = 'http://127.0.0.1/taskspider.php'
i = 0
while 1:
    now = time.strftime('%Y-%m-%d %X', time.localtime(time.time()))
    # print now
    try:
        # if 1==1:
        a = geturl(url)
        i += 1
        time.sleep(5)
    except Exception, e:
        info = '%s\nError: %s' % (now, e)
        writefile('Error.log', info)
        print info
        time.sleep(1)