<?php
#require(dirname(__FILE__).'/include/config.inc.php');

/**
 * Created by PhpStorm.
 * User: xysoul
 * Date: 17/7/23
 * Time: 下午9:19
 */
#echo "1111";
function proxy()
{
    $cfg['db_host'] = 'localhost';       // 数据库服务器
    $cfg['db_name'] = 'wdscan';       // 数据库名
    $cfg['db_user'] = 'root';       // 数据库用户名
    $cfg['db_pass'] = '123456';       // 数据库密码
    $cfg['db_charset'] = 'utf-8';      //数据库编码
    $cfg['db_pre'] = '';      //表前缀

    $mode = $_GET['m'];


    $db = new Mysql($cfg['db_host'], $cfg['db_user'], $cfg['db_pass'], $cfg['db_name'], $cfg['db_charset'], $cfg['db_charset'], $cfg['db_pre']);

    $sql = "SELECT * FROM valid_ip order by score desc";
    $results = $db->query($sql);

    if (mysql_num_rows($results) > 0) {
        $i = 1;
        while ($fs = $db->fetch_array($results)) {

            $id = $i;
            $ip = $fs["0"];
            $test_times = $fs["1"];
            $failure_times = $fs["2"];
            $response_time = $fs["4"];
            $score = $fs["5"];

            $html_str .= "
									<tr class=\"$class\">
										<td>
											$id
										</td>
										<td style=\"word-break:break-all; word-wrap:break-word;\">
											<a href=\"http://$ip\" target=\"_blank\">$ip</a>
										</td>
										<td>
											$test_times
										</td>
										<td>
											$failure_times
										</td>
										<td>
											$response_time
										</td>
										<td>
											$score
										</td>
										
										<td>
											<a href=\"?m=reflush\">刷新</a>|<a href=\"javascript:alert('请以管理员身份登录')\">删除</a>
										</td>
									</tr>\r\n";
            $i++;
        }
        return $html_str;
    }
    else {
        print "False";
    }
    #include("html/proxy.html");
}
?>


