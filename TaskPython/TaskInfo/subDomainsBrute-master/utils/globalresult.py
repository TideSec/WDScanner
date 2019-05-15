#!/usr/bin/env python
# -*- coding: utf-8 -*-

'''
放全局结果字典，格式：
'domain': 'ip, ip, ip, ...'
'''

import sys
import threading
import utils


g_lock = threading.Lock()
g_result_dict = {}


def update_result_dict(result_dict):
    '''
    更新结果字典
    :param result_dict: 结果字典
    '''
    g_lock.acquire()
    for domain in result_dict.keys():
        need_print = False
        if domain not in g_result_dict:
            g_result_dict[domain] = result_dict[domain]
            need_print = True
        else:
            dest_ips = g_result_dict[domain].split(', ')
            src_ips = result_dict[domain].split(', ')
            for src_ip in src_ips:
                if src_ip not in dest_ips:
                    g_result_dict[domain] += ', ' + src_ip
                    need_print = True
        if need_print:
            sys.stdout.write(domain.ljust(30) + g_result_dict[domain] + '\n')
            sys.stdout.flush()
    g_lock.release()


def add_list(result_list):
    to_dict = {}
    for i in result_list:
        to_dict[i] = utils.get_ip(i)
    update_result_dict(to_dict)


if __name__ == '__main__':
    g_result_dict['www.baidu.com'] = '1.2.3.4'

    # test case 1
    # result_dict = {'www.baidu.com': '2.3.4.5'}
    # update_result_dict(result_dict)

    # test case 2
    # result_dict = {'admin.baidu.com': '3.4.5.6'}
    # update_result_dict(result_dict)

    # test case 3
    # result_dict = {'www.baidu.com': '1.2.3.4'}
    # update_result_dict(result_dict)

    # test case 4
    # result_dict = {'www.baidu.com': '1.2.3.4, 2.3.4.5'}
    # update_result_dict(result_dict)

    # test case 5
    # result_dict = {'www.baidu.com': '3.4.5.6, 2.3.4.5'}
    # update_result_dict(result_dict)

    # test case 6
    # result_list = ['www.baidu.com', 'fanyi.baidu.com']
    # add_list(result_list)

    print g_result_dict
