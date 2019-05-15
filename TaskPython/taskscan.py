# coding=utf-8
import urllib, urllib2, time, os, base64, json
import _winreg

wvs_path = ""


def get_html(url):
    try:
        url = url.strip()
        req = urllib2.Request(url)
        html = urllib2.urlopen(req).read()
        return html
    except urllib2.URLError as e:
        if 'error' in str(e):
            print e.reason
            print "Restarting Apache2a now..."
            cmd = 'net start Apache2a'
            os.system(cmd)
            cmd2 = 'net start MySQLa'
            os.system(cmd2)
            time.sleep(3)
        return ''


def writefile(logname, cmd):
    try:
        fp = open(logname, 'a')
        fp.write(cmd + "\n")
        fp.close()
    except:
        return False


def regedit(re_root, re_path, re_key):
    try:
        key = _winreg.OpenKey(_winreg.HKEY_LOCAL_MACHINE, re_path)
        value, type = _winreg.QueryValueEx(key, re_key)
        return value
    except:
        return False


def get_console(url):
    now = time.strftime('%Y-%m-%d %X', time.localtime(time.time()))
    date = time.strftime('%Y-%m-%d', time.localtime(time.time()))
    try:
        # if 1 == 1:
        a = get_html(url)
        # print a
        if len(a) > 50:
            base = base64.b64decode(a)
            print base
            json_arr = json.loads(base)
            target_url = json_arr['target_url']
            user = json_arr['siteuser']
            pwd = json_arr['sitepwd']
            scan_rule = json_arr['scan_rule']
            hash = json_arr['hash']
            print json_arr
            console = '"%s\\wvs_console.exe" /Scan %s --HtmlAuthUser=%s --HtmlAuthPass=%s  /Verbose /ExportXML /SaveLogs /SaveFolder C:\\WDScanner\\WWW\\report\\%s\\' % (
            wvs_path, target_url, user, pwd, hash)
            # console = console + '\ndel %0'
            scantime = time.strftime('%Y-%m-%d %X', time.localtime(time.time()))
            print "%s\n%s\n" % (scantime, console)
            writefile('bat\\%s.bat' % hash, console)
            cmd = 'cmd.exe /c bat\\\%s.bat' % hash
            print "%s\n%s\n%s\n" % (now, target_url, cmd)
            os.system(cmd)
        else:
            print "Nothing To Do"
    except Exception, e:
        info = '%s\nError: %s' % (now, e)
        writefile('logs\\%s-Error.log' % date, info)
        print info


wvs_path = regedit(0, "SOFTWARE\Wow6432Node\Acunetix\WVS10", "Path")
print wvs_path
# exit()
url = 'http://127.0.0.1/taskscan.php'
i = 0
while 1:
    now = time.strftime('%Y-%m-%d %X', time.localtime(time.time()))
    # print now
    try:
        # if 1==1:
        a = get_console(url)
        i += 1
        time.sleep(5)
    except Exception, e:
        info = '%s\nError: %s' % (now, e)
        writefile('Error.log', info)
        print info
        time.sleep(1)
