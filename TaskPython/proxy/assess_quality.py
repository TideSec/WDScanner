#!/user/bin/env python
# -*- coding:utf-8 -*-


import requests
import time
import datetime
import logging
import pymysql as mdb
import config as cfg

log_file = 'assess_logger.log'
logging.basicConfig(filename=log_file, level=logging.WARNING)

TEST_ROUND_COUNT = 0


def modify_score(ip, success, response_time):

    conn = mdb.connect(cfg.host, cfg.user, cfg.passwd, cfg.DB_NAME)
    cursor = conn.cursor()


    if success == 0:
        logging.warning(datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")+": " + ip + \
                        " out of time")
        try:
            cursor.execute('SELECT * FROM %s WHERE content= "%s"' % (cfg.TABLE_NAME, ip))
            q_result = cursor.fetchall()
            for r in q_result:
                test_times = r[1] + 1
                failure_times = r[2]
                success_rate = r[3]
                avg_response_time = r[4]

                # 超时达到4次且成功率低于标准
                if failure_times > 4 and success_rate < cfg.SUCCESS_RATE:
                    cursor.execute('DELETE FROM %s WHERE content= "%s"' % (cfg.TABLE_NAME, ip))
                    conn.commit()
                    logging.warning(datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")+": " + ip + \
                                    " was deleted.")
                else:
                    # not too bad
                    failure_times += 1
                    success_rate = 1 - float(failure_times) / test_times
                    avg_response_time = (avg_response_time * (test_times - 1) + cfg.TIME_OUT_PENALTY) / test_times
                    score = (success_rate + float(test_times) / 500) / avg_response_time
                    n = cursor.execute('UPDATE %s SET test_times = %d, failure_times = %d, success_rate = %.2f, avg_response_time = %.2f, score = %.2f WHERE content = "%s"' % (TABLE_NAME, test_times, failure_times, success_rate, avg_response_time, score, ip))
                    conn.commit()
                    if n:
                        logging.error(datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")+": " + \
                                      ip + ' has been modify successfully!')
                break
        except Exception as e:
            logging.error(datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")+": " + \
                                      'Error when try to delete ' + ip + str(e))
        finally:
            cursor.close()
            conn.close()
    elif success == 1:
 
        try:
            cursor.execute('SELECT * FROM %s WHERE content= "%s"' % (cfg.TABLE_NAME, ip))
            q_result = cursor.fetchall()
            for r in q_result:
                test_times = r[1] + 1
                failure_times = r[2]
                avg_response_time = r[4]
                success_rate = 1 - float(failure_times) / test_times
                avg_response_time = (avg_response_time * (test_times - 1) + response_time) / test_times
                score = (success_rate + float(test_times) / 500) / avg_response_time
                n = cursor.execute('UPDATE %s SET test_times = %d, success_rate = %.2f, avg_response_time = %.2f, score = %.2f WHERE content = "%s"' %(cfg.TABLE_NAME, test_times, success_rate, avg_response_time, score, ip))
                conn.commit()
                if n:
                    logging.error(datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")+": " + \
                                  ip + 'has been modify successfully!')
                break
        except Exception as e:
            logging.error(datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")+": " + \
                                      'Error when try to modify ' + ip + str(e))
        finally:
            cursor.close()
            conn.close()


def ip_test(proxies, timeout):


    url = 'https://www.baidu.com'

    for p in proxies:
        proxy = {'http': 'http://'+p}
        try:
            
            start = time.time()
            r = requests.get(url, proxies=proxy, timeout=timeout)
           
            end = time.time()
            
            if r.text is not None:
                logging.warning(datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")+": " + p + \
                                " out of time")
                resp_time = end -start
                modify_score(p, 1, resp_time)
                print 'Database test succeed: '+p+'\t'+str(resp_time)
        except OSError:
            modify_score(p, 0, 0)


def assess():
    global TEST_ROUND_COUNT
    TEST_ROUND_COUNT += 1
    logging.warning(datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")+": " + ">>>>\t" + str(TEST_ROUND_COUNT) + " round!\t<<<<")
    
    conn = mdb.connect(cfg.host, cfg.user, cfg.passwd, cfg.DB_NAME)
    cursor = conn.cursor()

    try:
        cursor.execute('SELECT content FROM %s' % cfg.TABLE_NAME)
        result = cursor.fetchall()
        ip_list = []
        for i in result:
            ip_list.append(i[0])
        if len(ip_list) == 0:
            return
        ip_test(ip_list, cfg.timeout)
    except Exception as e:
        logging.warning(datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")+": " + str(e))
    finally:
        cursor.close()
        conn.close()


def main():
    while True:
        assess()
        
        time.sleep(cfg.CHECK_TIME_INTERVAL)

if __name__ == '__main__':
    main()