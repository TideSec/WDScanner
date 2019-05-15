# coding=utf-8
import shutil
import urllib, time, os, base64, json

import re


def get_html(url):
    url = url.strip()
    html = urllib.urlopen(url).read()
    return html


def writefile(logname, cmd):
    try:
        fp = open(logname, 'a')
        fp.write(cmd + "\n")
        fp.close()
    except:
        return False


def geturl(url):
    now = time.strftime('%Y-%m-%d %X', time.localtime(time.time()))
    date = time.strftime('%Y-%m-%d', time.localtime(time.time()))
    try:
        a = get_html(url)
        if len(a) > 50:
            base = base64.b64decode(a)
            print base
            json_arr = json.loads(base)
            target_url = json_arr['target_url'].strip('\r')
            hash = json_arr['hash']
            # target_url = 'http://www.bzta.gov.cn'
            # hash = '853c54149cf4a2ce600e24b03696ae64'
            task(target_url, hash)
            readreport(target_url, hash)
        else:
            print "Nothing To Do"
    except Exception, e:
        info = '%s\nError: %s' % (now, e)
        # writefile('%s-Error.log' % date, info)
        print info


def readreport(target, hash):
    path = 'C:\WDScanner\WWW\TaskPython\TaskInfo\loginfo\\' + hash + '\\' + hash
    result = open(path + '.txt', 'w')
    # --------------domian---------------
    domain_text = open(path + '-domain.txt', 'r').read().replace(" ", '').replace("\n", '').replace('"', '')[
                  1:-1].split(',')
    domain_all = []
    for domain in domain_text:
        domain = domain.strip('\r')
        domain_all.append(domain)
    # print domain_all
    # domain_num =0
    # print domain_text
    # print len(domain_text)
    subdomain_text = open(path + '-subdomain.txt', 'r')
    for x in subdomain_text.readlines():
        x = x.strip('\n').strip('\r')
        domain_all.append(x)

    domain = list(set(domain_all))
    domain_num = len(domain)
    domains = ''
    for x in domain:
        domains = x + '<br>' + domains
    # print domains

    # print type(domain_all)
    # domain_data = {'domain_num':len(domain_text),'domain':domain_text}
    # json.dump(domain_data, result, ensure_ascii=False)

    # --------------port--------------
    nmap_text = open(path + '-nmap.txt', 'r')
    nmap_text = nmap_text.readlines()
    count = len(nmap_text)
    port_num = 0
    port_info = ''
    ip = ""
    os = ""
    os_info = 'Running OS:<br>'
    num_tmp = 0
    if count > 2:
        for x in range(count):
            # print nmap_text[x]
            if nmap_text[x].startswith('Nmap scan'):
                if nmap_text[x][-2:-1] == ')':
                    ip = nmap_text[x][0:-2].split('(')[1]
                else:
                    ip = nmap_text[x][0:-1].replace('Nmap scan report for ', '')

            if nmap_text[x].startswith('PORT'):
                for a in range(count - x):
                    if nmap_text[x + a][0:2].isdigit():
                        port_info = port_info + nmap_text[x + a] + '<br>'
                        num_tmp = num_tmp + 1
            port_num = num_tmp

            if nmap_text[x].startswith('Running'):
                os_tmp = nmap_text[x].split(':')[1][0:-2].split(',')
                os = os_tmp[0]
                for a in os_tmp:
                    os_info = os_info + a + '<br>'
            else:
                if nmap_text[x].startswith('Aggressive'):
                    os = nmap_text[x].split(':')[1].split(',')[0].split('(')[0]
                    os_info = os_info + os + '<br>'

            if nmap_text[x].startswith('Aggressive') or nmap_text[x].startswith('OS details'):
                os_info_tmp = nmap_text[x].split(',')
                for b in os_info_tmp:
                    os_info = os_info + b + '<br>'


    else:
        os_info = "Error"
        ip = "Error"
        os = "Error"
        port_num = "Error"
        port_info = "Error<br>"

    os_info = os_info.replace('\r\n', '').replace('Aggressive OS guesses:', '<br>OS Details:<br>').replace(
        'OS details:', '<br>OS Details:<br>')[0:-4]

    # print nmap_text[port_num+2].split(':')[0]
    # print str(nmap_text[port_num+2:])
    # print port_info
    # nmap_data = {'ip':nmap_text[0],'port_num':port_num,'port_info':port_info,'os':nmap_text[port_num+2].split(':')[0],'os_info':str(nmap_text[port_num+2:])}
    # json.dump(nmap_data, result, ensure_ascii=False)

    # --------------whatweb--------------
    whatweb_text = open(path + '-whatweb.txt', 'r').read()
    # print whatweb_text
    pattern1 = re.compile('.*?HTTPServer\[(.*?)\]')
    httpserver = re.findall(pattern1, whatweb_text)
    xx = ''
    for x in httpserver:
        xx = x + xx
    httpserver = xx.replace('][', ',')
    # print httpserver

    pattern2 = re.compile('.*?Title\[(.*?)\]')
    title = re.findall(pattern2, whatweb_text)
    xx = ''
    for x in title:
        xx = x + xx
    title = xx
    # print title
    pattern3 = re.compile('.*?X-Powered-By\[(.*?)\]')
    xpb = re.findall(pattern3, whatweb_text)
    xx = ''
    for x in xpb:
        xx = x + xx
    xpb = xx
    # print xpb

    # --------------waf-------------
    waf_text = open(path + '-waf.txt', 'r').read()
    # print waf_text
    pattern1 = re.compile('is behind a (.*)')
    waf1 = re.findall(pattern1, waf_text)
    waf = 'UnDetect'
    if waf1:
        waf = waf1[0]

    pattern2 = re.compile('.*?seems to be behind a WAF.*?')
    waf2 = re.findall(pattern2, waf_text)
    if waf2:
        waf = 'Unknown'

    pattern3 = re.compile('.*?No WAF detected by.*?')
    waf3 = re.findall(pattern3, waf_text)
    if waf3:
        waf = 'NoWaf'

    # --------------whatcms-------------
    whatcms_text = open(path + '-whatcms.txt', 'r').read()
    if whatcms_text == '':
        bugscancms_text = open(path + '-bugscancms.txt', 'r').read()
        whatcms_text = bugscancms_text
    # print whatcms_text

    # --------------weakfile-------------
    bbscan_url = open(path + '-bbscan.txt', 'r')
    url = []
    for x in bbscan_url.readlines():
        url.append(x)
    wyspider_url = open(path + '-wyspider.txt', 'r')
    url = []
    for y in wyspider_url.readlines():
        url.append(y)
    url = list(set(url))
    weakfile_num = len(url)
    urls = ''
    for x in url:
        urls = x + '<br>' + urls
    print urls

    # print whatcms_text


    report_data = {'domain_num': domain_num, 'domain_info': domains,
                   'ip': ip, 'port_num': port_num, 'port_info': port_info,
                   'os': os, 'os_info': os_info,
                   'httpserver': httpserver, 'title': title, 'xpb': xpb, 'whatweb_text': whatweb_text,
                   'waf': waf, 'whatcms_text': whatcms_text, 'weakfile_num': weakfile_num, 'weakfile': urls}

    json.dump(report_data, result, ensure_ascii=False)


def task(target, hash):
    path = 'C:\WDScanner\WWW\TaskPython\TaskInfo\\'
    logpath = 'C:\WDScanner\WWW\TaskPython\TaskInfo\loginfo\\' + hash + '\\'
    print logpath
    if not os.path.exists(logpath):
        os.mkdir(logpath)
    pwd = os.getcwd()
    open(logpath + hash + '-domain.txt', 'w').close()
    open(logpath + hash + '-subdomain.txt', 'w').close()
    open(logpath + hash + '-nmap.txt', 'w').close()
    open(logpath + hash + '-wyspider.txt', 'w').close()
    open(logpath + hash + '-whatweb.txt', 'w').close()
    open(logpath + hash + '-whatcms.txt', 'w').close()
    open(logpath + hash + '-waf.txt', 'w').close()
    open(logpath + hash + '-bugscancms.txt', 'w').close()
    open(logpath + hash + '-bbscan.txt', 'w').close()

    try:
        print target

        os.chdir(path)

        print "-------------domain_start-------------"
        url = target
        if url[0:4] == 'http':
            proto, rest = urllib.splittype(url)
            host, rest = urllib.splithost(rest)
            if host[0:3] == 'www':
                host = host[4:]
        elif url[0:3] == 'www':
            host = url[4:]
        else:
            host = url

        if ':' in host:
            host = host.split(':')[0]

        #从本地目录中检索目标子域名是否已经枚举过，如果枚举过则直接使用该文件。
        domain_flag = 0
        file_list = []
        for r, d, f in os.walk(path+'loginfo\\'):
            for files in f:
                file = "%s\%s" % (r, files)
                if 'domain.txt' in file:
                    file_list.append(file)
        for domain_file in file_list:
            domain_file_content = open(domain_file, 'r').read()
            # print domain_file
            if host in domain_file_content:
                print domain_file
                domain_flag = 1
                if 'domain' in domain_file:
                    shutil.copy(domain_file, logpath + hash + '-domain.txt')
                if 'subdomain' in domain_file:
                    shutil.copy(domain_file, logpath + hash + '-subdomain.txt')

        if (domain_flag == 0):
            domain = 'python ' + path + 'wydomain\wydomain.py -d ' + host + ' -o ' + logpath + hash + '-domain.txt'
            print domain
            os.system(domain)
            print "+++++++++++++domain_ok+++++++++++++"


        #print "-------------whatcms_start-------------"

        #whatcms = 'python ' + path + 'whatcms.py ' + target + ' ' + logpath + hash + '-whatcms.txt '
        #print whatcms
        #os.system(whatcms)
        #print "+++++++++++++whatcms_ok+++++++++++++"

        #print "-------------bugscancms_start-------------"
        # print os.getcwd()
        #bugscancms = 'python ' + path + 'bugscan-cms.py ' + target + ' ' + logpath + hash + '-bugscancms.txt '
        #print bugscancms
        #os.system(bugscancms)
        #print "+++++++++++++bugscancms_ok+++++++++++++"

        print "-------------nmap_start-------------"
        url1 = target
        if url1[0:4] == 'http':
            proto, rest = urllib.splittype(url1)
            host1, rest = urllib.splithost(rest)
        else:
            host1 = url1
        if ':' in host1:
            host1 = host1.split(':')[0]

        nmap = 'nmap.exe -oN ' + logpath + hash + '-nmap.txt  -sT -sV -O --script=banner --top-port 200  ' + host1
        # nmap = 'nmap.exe -oN ' + logpath + hash + '-nmap.txt  -sT -P0 -sV -O --script=banner -p T:80,3306,22,3389 '+ host1

        print nmap
        os.system(nmap)
        print "+++++++++++++nmap_ok+++++++++++++"

        print "-------------whatweb_start-------------"
        whatweb = 'ruby ' + path + 'whatweb\whatweb --log-brief=' + logpath + hash + '-whatweb.txt ' + target
        print whatweb
        os.system(whatweb)
        print "+++++++++++++whatweb_ok+++++++++++++"

        print "-------------waf_start-------------"
        waf = 'python ' + path + 'wafw00f\wafw00f\\bin\wafw00f ' + target + ' >> ' + logpath + hash + '-waf.txt '
        print waf
        os.system(waf)
        print "+++++++++++++waf_ok+++++++++++++"

        try:
            if (domain_flag == 0):
                print "-------------subdomain_start-------------"
                os.chdir(path + '\subDomainsBrute-master\\')
                subdomain = 'python ' + path + 'subDomainsBrute-master\subDomainsBrute.py  ' + host + ' --out ' + logpath + hash + '-subdomain.txt'
                print subdomain
                os.system(subdomain)
                print "+++++++++++++subdomain_ok+++++++++++++"
        except:
            pass

        try:
            print "-------------weakfile_start-------------"
            os.chdir(path + '\BBScan\\')
            bbscan = 'python ' + path + 'BBScan\BBScan.py --host ' + target + ' --no-browser --out ' + logpath + hash + '-bbscan.txt '
            print bbscan
            #os.system(bbscan)

            os.chdir(path + '\weakfilescan\\')
            wyspider = 'python ' + path + 'weakfilescan\wyspider.py ' + target + ' ' + logpath + hash + '-wyspider.txt '
            print wyspider
            os.system(wyspider)

            print "+++++++++++++weakfile_ok+++++++++++++"
        except:
            pass

    except Exception, e:
        print e
        pass


# exit()
url = 'http://127.0.0.1/taskinfo.php'
i = 0
while 1:
    now = time.strftime('%Y-%m-%d %X', time.localtime(time.time()))
    # print now
    try:
        # if 1==1:
        a = geturl(url)
        i += 1
        # exit(0)
        time.sleep(5)
    except Exception, e:
        info = '%s\nError: %s' % (now, e)
        # writefile('Error.log', info)
        print info
        time.sleep(1)
