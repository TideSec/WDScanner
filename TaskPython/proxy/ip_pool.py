# coding:utf-8
import time
import config as cfg
import requests
from lxml import etree
import pymysql as mdb
import datetime


class IPFactory:

    def __init__(self):
        self.page_num = cfg.page_num
        self.round = cfg.examine_round
        self.timeout = cfg.timeout
        self.all_ip = set()

       
        #self.create_db()

        # # 抓取全部ip
        # current_ips = self.get_all_ip()
        # # 获取有效ip
        # valid_ip = self.get_the_best(current_ips, self.timeout, self.round)
        # print valid_ip

    def create_db(self):

        drop_db_str = 'drop database if exists ' + cfg.DB_NAME + ' ;'
        create_db_str = 'create database ' + cfg.DB_NAME + ' ;'
        # 选择该数据库
        use_db_str = 'use ' + cfg.DB_NAME + ' ;'
        # 创建表格
        create_table_str = "CREATE TABLE " + cfg.TABLE_NAME + """(
          `content` varchar(30) NOT NULL,
          `test_times` int(5) NOT NULL DEFAULT '0',
          `failure_times` int(5) NOT NULL DEFAULT '0',
          `success_rate` float(5,2) NOT NULL DEFAULT '0.00',
          `avg_response_time` float NOT NULL DEFAULT '0',
          `score` float(5,2) NOT NULL DEFAULT '0.00'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"""

        
        conn = mdb.connect(cfg.host, cfg.user, cfg.passwd)
        cursor = conn.cursor()
        try:
            cursor.execute(drop_db_str)
            cursor.execute(create_db_str)
            cursor.execute(use_db_str)
            cursor.execute(create_table_str)
            conn.commit()
        except OSError:
            print "无法创建数据库！"
        finally:
            cursor.close()
            conn.close()

    def get_content(self, url, url_xpath, port_xpath):

        # 返回列表
        ip_list = []

        try:
            # 设置请求头信息
            headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko'}

            # 获取页面数据
            results = requests.get(url, headers=headers, timeout=4)
            tree = etree.HTML(results.text)

            # 提取ip:port
            url_results = tree.xpath(url_xpath)
            port_results = tree.xpath(port_xpath)
            urls = [line.strip() for line in url_results]
            ports = [line.strip() for line in port_results]

            if len(urls) == len(ports):
                for i in range(len(urls)):
                    # 匹配ip:port对
                    full_ip = urls[i]+":"+ports[i]
                    # 此处利用all_ip对过往爬取的ip做了记录，下次再爬时如果发现
                    # 已经爬过，就不再加入ip列表。
                    if full_ip in self.all_ip:
                        continue
                    # 存储
                    ip_list.append(full_ip)
        except Exception as e:
            print 'get proxies error: ', e

        return ip_list

    def get_all_ip(self):
        """
        各大网站抓取的ip聚合。
        """
        # 有2个概念：all_ip和current_all_ip。前者保存了历次抓取的ip，后者只保存本次的抓取。
        current_all_ip = set()

        ##################################
        # 66ip网
        ###################################
        url_xpath_66 = '/html/body/div[last()]//table//tr[position()>1]/td[1]/text()'
        port_xpath_66 = '/html/body/div[last()]//table//tr[position()>1]/td[2]/text()'
        for i in xrange(self.page_num):
            url_66 = 'http://www.66ip.cn/' + str(i+1) + '.html'
            results = self.get_content(url_66, url_xpath_66, port_xpath_66)
            self.all_ip.update(results)
            current_all_ip.update(results)
            # 停0.5s再抓取
            time.sleep(0.5)

        ##################################
        # xici代理
        ###################################
        url_xpath_xici = '//table[@id="ip_list"]//tr[position()>1]/td[position()=2]/text()'
        port_xpath_xici = '//table[@id="ip_list"]//tr[position()>1]/td[position()=3]/text()'
        for i in xrange(self.page_num):
            url_xici = 'http://www.xicidaili.com/nn/' + str(i+1)
            results = self.get_content(url_xici, url_xpath_xici, port_xpath_xici)
            self.all_ip.update(results)
            current_all_ip.update(results)
            time.sleep(0.5)

        ##################################
        # mimiip网
        ###################################
        url_xpath_mimi = '//table[@class="list"]//tr[position()>1]/td[1]/text()'
        port_xpath_mimi = '//table[@class="list"]//tr[position()>1]/td[2]/text()'
        for i in xrange(self.page_num):
            url_mimi = 'http://www.mimiip.com/gngao/' + str(i+1)
            results = self.get_content(url_mimi, url_xpath_mimi, port_xpath_mimi)
            self.all_ip.update(results)
            current_all_ip.update(results)
            time.sleep(0.5)

        ##################################
        # kuaidaili网
        ###################################
        url_xpath_kuaidaili = '//td[@data-title="IP"]/text()'
        port_xpath_kuaidaili = '//td[@data-title="PORT"]/text()'
        for i in xrange(self.page_num):
            url_kuaidaili = 'http://www.kuaidaili.com/free/inha/' + str(i+1) + '/'
            results = self.get_content(url_kuaidaili, url_xpath_kuaidaili, port_xpath_kuaidaili)
            self.all_ip.update(results)
            current_all_ip.update(results)
            time.sleep(0.5)

        return current_all_ip

    def get_valid_ip(self, ip_set, timeout):
        """
        代理ip可用性测试
        """
        # 设置请求地址
        url = 'https://www.baidu.com'

        # 可用代理结果
        results = set()

        # 挨个检查代理是否可用
        for p in ip_set:
            proxy = {'http': 'http://'+p}
            try:
                # 请求开始时间
                start = time.time()
                r = requests.get(url, proxies=proxy, timeout=timeout)
                # 请求结束时间
                end = time.time()
                # 判断是否可用
                if r.text is not None:
                    print 'succeed: ' + p + '\t' + " in " + format(end-start, '0.2f') + 's'
                    # 追加代理ip到返回的set中
                    results.add(p)
            except OSError:
                print 'timeout:', p

        return results

    def get_the_best(self, valid_ip, timeout, round):
        """
        N轮检测ip列表，避免"辉煌的15分钟"
        """
        # 循环检查次数
        for i in range(round):
            print "\n>>>>>>>\tRound\t"+str(i+1)+"\t<<<<<<<<<<"
            # 检查代理是否可用
            valid_ip = self.get_valid_ip(valid_ip, timeout)
            # 停一下
            if i < round-1:
                time.sleep(30)

        # 返回可用数据
        return valid_ip

    def save_to_db(self, valid_ips):
        """
        将可用的ip存储进mysql数据库
        """
        if len(valid_ips) == 0:
            print "本次没有抓到可用ip。"
            return
        # 连接数据库
        print "\n>>>>>>>>>>>>>>>>>>>> 代理数据入库处理 Start  <<<<<<<<<<<<<<<<<<<<<<\n"
        conn = mdb.connect(cfg.host, cfg.user, cfg.passwd, cfg.DB_NAME)
        cursor = conn.cursor()
        try:
            for item in valid_ips:
                # 检查表中是否存在数据
                item_exist = cursor.execute('SELECT * FROM %s WHERE content="%s"' %(cfg.TABLE_NAME, item))

                # 新增代理数据入库
                if item_exist == 0:
                    # 插入数据
                    n = cursor.execute('INSERT INTO %s VALUES("%s", 1, 0, 0, 1.0, 2.5)' %(cfg.TABLE_NAME, item))
                    conn.commit()

                    # 输出入库状态
                    if n:
                        print datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")+" "+item+" 插入成功。\n"
                    else:
                        print datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")+" "+item+" 插入失败。\n"

                else:
                    print datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")+" "+ item +" 已存在。\n"
        except Exception as e:
            print "入库失败：" + str(e)
        finally:
            cursor.close()
            conn.close()
        print "\n>>>>>>>>>>>>>>>>>>>> 代理数据入库处理 End  <<<<<<<<<<<<<<<<<<<<<<\n"

    def get_proxies(self):
        ip_list = []

        # 连接数据库
        conn = mdb.connect(cfg.host, cfg.user, cfg.passwd, cfg.DB_NAME)
        cursor = conn.cursor()

        # 检查数据表中是否有数据
        try:
            ip_exist = cursor.execute('SELECT * FROM %s ' % cfg.TABLE_NAME)

            # 提取数据
            result = cursor.fetchall()

            # 若表里有数据　直接返回，没有则抓取再返回
            if len(result):
                for item in result:
                    ip_list.append(item[0])
            else:
                # 获取代理数据
                current_ips = self.get_all_ip()
                valid_ips = self.get_the_best(current_ips, self.timeout, self.round)
                self.save_to_db(valid_ips)
                ip_list.extend(valid_ips)
        except Exception as e:
            print "从数据库获取ip失败！"
        finally:
            cursor.close()
            conn.close()

        return ip_list


def main():
    ip_pool = IPFactory()
    while True:
        current_ips = ip_pool.get_all_ip()
        # 获取有效ip
        valid_ip = ip_pool.get_the_best(current_ips, cfg.timeout, cfg.examine_round)
        print valid_ip
        ip_pool.save_to_db(valid_ip)
        time.sleep(cfg.CHECK_TIME_INTERVAL)

if __name__ == '__main__':
    main()