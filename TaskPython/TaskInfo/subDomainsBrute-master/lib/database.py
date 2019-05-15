# -*- coding: UTF-8 -*-  
'''
Created on 2017年5月15日
@author: Saline
'''
import pymysql
from DBUtils.PooledDB import PooledDB

mysqldb_conn = {
	#数据库信息
    'host' : 'localhost',
    'user' : 'root',
    'port' : '3306',
    'password' : 'mysqlroot',
    'db' : 'wyproxy',
    #数据库连接编码
    'charset' : 'utf8',
    #mincached : 启动时开启的闲置连接数量(缺省值 0 以为着开始时不创建连接)
    "DB_MIN_CACHED":"10",
    #maxcached : 连接池中允许的闲置的最多连接数量(缺省值 0 代表不闲置连接池大小)
    "DB_MAX_CACHED":"10",
    #maxshared : 共享连接数允许的最大数量(缺省值 0 代表所有连接都是专用的)如果达到了最大数量,被请求为共享的连接将会被共享使用
    "DB_MAX_SHARED":"20",
    #maxconnecyions : 创建连接池的最大数量(缺省值 0 代表不限制)
    "DB_MAX_CONNECYIONS":"100",
    #blocking : 设置在连接池达到最大数量时的行为(缺省值 0 或 False 代表返回一个错误<toMany......>; 其他代表阻塞直到连接数减少,连接被分配)
    "DB_BLOCKING":True,
    #maxusage : 单个连接的最大允许复用次数(缺省值 0 或 False 代表不限制的复用).当达到最大数时,连接会自动重新连接(关闭和重新打开)
    "DB_MAX_USAGE":"0",
    #setsession : 一个可选的SQL命令列表用于准备每个会话，如["set datestyle to german", ...]
    "DB_SET_SESSION":None
}
'''
@功能：数据库连接池
'''
class PTConnectionPool(object):
    __pool = None
    def __enter__(self):
        self.conn = self.getConn()
        self.cursor = self.conn.cursor()
        return self

    def getConn(self):
        if self.__pool is None:
            self.__pool = PooledDB(
            	creator=pymysql, cursorclass= pymysql.cursors.DictCursor, 
            	mincached=int(mysqldb_conn.get('DB_MIN_CACHED')),
            	maxcached=int(mysqldb_conn.get('DB_MAX_CACHED')),
            	maxshared=int(mysqldb_conn.get('DB_MAX_SHARED')),
            	maxconnections=int(mysqldb_conn.get('DB_MAX_CONNECYIONS')),
            	blocking=mysqldb_conn.get('DB_BLOCKING'),
            	setsession=mysqldb_conn.get('DB_SET_SESSION'),
                maxusage=int(mysqldb_conn.get('DB_MAX_USAGE')),
                host=mysqldb_conn.get('host'),
                port=int(mysqldb_conn.get('port')),
                user=mysqldb_conn.get('user'),
                passwd=mysqldb_conn.get('password'),
                db=mysqldb_conn.get('db') , use_unicode=False,
                charset=mysqldb_conn.get('charset')
            )

        return self.__pool.connection()

    """
    @summary: 释放连接池资源
    """
    def __exit__(self, type, value, trace):
        self.cursor.close()
        self.conn.close()

'''
@功能：获取数据库连接
'''
def getPTConnection():
    return PTConnectionPool() 


def query(sql,args= None):
    with getPTConnection() as db:
        try:
            cur = db.cursor
            cur.execute(sql,args)
            return db.cursor.fetchall()
        except Exception, e:
            print str(e)
            db.conn.rollback()
            raise Exception(e)


def execute(sql, args=None):
    with getPTConnection() as db:
        try:
            cur = db.cursor
            result = cur.execute(sql, args)
            db.conn.commit()
            return result
        except Exception, e:
            print str(e)
            db.conn.rollback()
            raise Exception(e)

def executmany(sql, args=None):
    with getPTConnection() as db:
        try:
            cur = db.cursor
            result = cur.executemany(sql, args)
            db.conn.commit()
            return result
        except Exception, e:
            print e
            db.conn.rollback()
            raise Exception(e)

if __name__ == "__main__":  
	res = execute('select count(*) from movies')  
	print str(res)

	res = query('select * from movies limit 10')  
	print str(res)