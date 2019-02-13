<?php
require(dirname(__FILE__).'/include/config.inc.php');

/**
 * Created by PhpStorm.
 * User: xysoul
 * Date: 17/7/23
 * Time: 下午9:19
 */
#echo "1111";
$cfg['db_host'] = 'localhost';       // 数据库服务器
$cfg['db_name'] = 'proxy';       // 数据库名
$cfg['db_user'] = 'root';       // 数据库用户名
$cfg['db_pass'] = 'sdxc6295259';       // 数据库密码
$cfg['db_charset'] = 'utf-8';      //数据库编码
$cfg['db_pre'] = '';      //表前缀

$mode = $_GET['m'];


$db = new Mysql($cfg['db_host'],$cfg['db_user'],$cfg['db_pass'],$cfg['db_name'],$cfg['db_charset'],$cfg['db_charset'],$cfg['db_pre']);

    $sql = "SELECT * FROM valid_ip order by score desc";
    $results = $db->query($sql);

    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {

            echo $fs["0"];
            echo '<br>';
            $i ++;
        }}
        #print $html_str;
    else{
        print "False";}

?>


