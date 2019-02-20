# coding:utf-8

# 从代理ip网站上总共要爬取的ip页数。一般每页20条，小项目(20-30个代理ip即可完成的)可以设置为1-2页。
page_num = 3

# 对已经检测成功的ip测试轮次。
examine_round = 3

# 超时时间。代理ip在测试过程中的超时时间。
timeout = 5

# 数据库链接地址
host = '127.0.0.1'

# 数据库链接端口
port = 3306

# 数据库链接用户名
user = 'root'

# 数据库密码
passwd = '123456'

# 数据库名
DB_NAME = 'wdscan'

# 表名
TABLE_NAME = 'valid_ip'

# 数据库字符
charset = 'utf8'

# 1个代理ip最大容忍失败次数，超过则从db中删去。
USELESS_TIME = 4

# 1个代理ip最小容忍成功率
SUCCESS_RATE = 0.8

# 超时惩罚时间
TIME_OUT_PENALTY = 10

# 每隔多久检测一次
CHECK_TIME_INTERVAL = 24*3600
