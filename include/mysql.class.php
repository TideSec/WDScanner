<?php
class mysql{
	private $db_host;     //数据库主机
	private $db_user;     //数据库用户名
	private $db_pass;     //数据库密码
	private $db_database; //数据库名字
	private $db_charset;  //数据库编码
	private $conn;        //数据库连接标识
	private $result;      //执行query命令的结果资源标识
	private $db_pre;     //表前缀

	function __construct($db_host,$db_user,$db_pass,$db_databbse,$db_charset,$conn,$db_pre)
	{
		$this->db_host = $db_host;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;
		$this->db_database = $db_databbse;
		$this->conn = $conn;
		$this->db_charset = $db_charset;
		$this->connect();
		$this->db_pre = $db_pre;
	}

	//数据库连接
	private function connect()
	{
		$this->conn = mysql_connect($this->db_host,$this->db_user,$this->db_pass) or die("数据库连接错误");
		MySQL_query("SET NAMES 'UTF8'");
		mysql_select_db($this->db_database,$this->conn) or die("没有找到".$this->db_database."这个数据库");

	}

	//数据库执行语句，可执行查询添加修改删除等任何SQL语句
	function query($sql)
	{
		$sql = str_replace("##_",$this->db_pre,$sql);
		$result = mysql_query($sql,$this->conn);
		if (!$result) {
			//调用中使用SQL语句出错时，会自动打印出来
			//echo "<font color=red>SQL语句错误：$sql</font><br>";
			$k=fopen(LDINC."/data/mysqllog.txt","a+");
			fwrite($k,date("Y-m-d H:i:s")."执行{$sql}出错,来源于".$_SERVER['REQUEST_URI']."\r\n");
			fclose($k);
			//			echo "<font color=red>SQL语句错误</font><br>";
		}
		return $result;
	}

	function fetch_array($result = null)
	{
		$result = $result == null ? $this->result : $result;
		return mysql_fetch_array($result);
	}
	
	function fetch_row($result = null)
	{
		$result = $result == null ? $this->result : $result;
		return mysql_fetch_row($result);//mysql_fetch_array($result);
	}
	/**
	 *根据select查询结果计算结果集条数
	 */
	function db_num_rows($sql)
	{
		$result=$this->query($sql);
		if(empty($result)) $result=0;
		return mysql_num_rows($result);
	}

	//查询一个表下所有的字段
	function findall($table)
	{
		$result = $this->query("select * from $table");
		return $result;
	}

	//添加数据到数据库
	function insert_into($table,$array_value)
	{
		foreach ($array_value as $key=>$value)
		{
			$filed .= "`$key`,";
			$val .= "'$value',";
		}

		$filed = substr($filed,0,(strlen($filed)-1));    //替换最后一个逗号
		$val= substr($val,0,(strlen($val)-1));			 //替换最后一个逗号

        $sql="INSERT INTO ".$table." ($filed) VALUES ($val)";//拼成SQL语句
//        print $sql;
		$this->query($sql);
		return mysql_insert_id();
	}

	/**
	 *函数从结果集中取得一行作为关联数组。返回根据从结果集取得的行生成的关联数组，如果没有更多行，则返回 false。
	 */
	function fetch_assoc($sql)
	{
		$res = $this->query ( $sql );
		if ($res !== false) {
			return mysql_fetch_assoc ( $res );
		} else {
			return false;
		}
	}
	
	function fetch_assoc1($sql)
	{
		$res = $this->query ( $sql );
		if ($res !== false) {
			return mysql_fetch_assoc ( $res );
		} else {
			return false;
		}
	}


	/**
	 *更新数据库,$table代表着更新的表,$array_value更新的数组,$where条件
	 */
	function update($table,$array_value,$where)
	{
		foreach ($array_value as $key=>$value)
		{
			$upvalue .= "`$key`='$value',";
		}
		$upvalue = substr($upvalue,0,(strlen($upvalue)-1));    //替换最后一个逗号
		$sql="update $table set $upvalue where $where";		   //拼成SQL语句
        //echo $sql;
		return $this->query($sql);
	}

    function createtable($table)
    {
        $checksql = "drop table if exists $table";
//        print $checksql;
        $this->query($checksql);
        $sql = "CREATE TABLE $table (id int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,spider_site varchar(255),site_hash varchar(255),spider_url varchar(255),active_url int DEFAULT 0,evil_url int DEFAULT 0,key_url int DEFAULT 0,bad_url int DEFAULT 0,key_value varchar(255),snap varchar(255))";		   //拼成SQL语句
//        echo $sql;
        return $this->query($sql);
    }

    //获得错误描述
    function GetError()
    {
        $str = mysql_error();
        return $str;
    }
	function free_result($query) {
		return @mysql_free_result($query);
	}
	function escape_string($str){
		return mysql_escape_string($str);
	}
	//获取字段数
	function num_fields($query) {
		return mysql_num_fields($query);
	}
	//获取数据库版本
	function version() {
		return mysql_get_server_info($this->conn);
	}
	//删除数据库
	function delete($where)
	{
		$sql = "DELETE from $where";
		return $this->query($sql);
	}
	//判断此条数据库语句是否存在记录
	function checknumsql($sql)
	{
		if($this->db_num_rows($sql) > 0)
		return true;
		else
		return false;
	}
	//查询一个表返回的值
	function listtablezd($table,$zd)
	{
		//echo "select $zd from $table <br />";
		$arr = $this->fetch_array($this->query("select $zd from $table"));
		$str = $arr[$zd];
		return $str;
	}

	//获取受影响的行数
	function Getaffected($sql)
	{
		$this->query($sql);
		$rc = mysql_affected_rows();
		return $rc;
	}
	/**
	 * 获取设置表某个字段
	 */
	function GetConfig($field)
	{
		return $this->listtablezd("##_config where id=1",$field);
	}
}


?>