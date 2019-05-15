#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import json
import sqlite3
import os

reload(sys)
sys.setdefaultencoding('utf-8')
env = os.getenv("sub_domain_env")
if env not in('devel','local','binbin','prod'):
    env = 'local'

try:
    with open('config/%s_config.json'%env, 'r') as config_file:
        config_json_text = config_file.read()
        config_json = json.loads(config_json_text)
except Exception as e:
    print 'failed to load config file'
    sys.exit(-1)


db = sqlite3.connect(config_json['db_name'] + '.db', check_same_thread=False)
db_cursor = db.cursor()

db_cursor.execute('''
    CREATE TABLE IF NOT EXISTS `root_domain` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `domain` VARCHAR(64) UNIQUE
    )
''')
db.execute('''
    CREATE TABLE IF NOT EXISTS `result_domain` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `root_domain_id` INTEGER,
        `domain` VARCHAR(255) UNIQUE,
        `ip` VARCHAR(255)
    )
''')
