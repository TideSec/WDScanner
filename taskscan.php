<?php
require(dirname(__FILE__).'/include/config.inc.php');
date_default_timezone_set('Asia/Shanghai');
function sub_days($a,$b){
    $a_dt=getdate($a);
    $b_dt=getdate($b);
    $a_new=mktime(12,0,0,$a_dt['mon'],$a_dt['mday'],$a_dt['year']);
    $b_new=mktime(12,0,0,$b_dt['mon'],$b_dt['mday'],$b_dt['year']);
    return round(abs($a_new-$b_new)/86400);
}
global $db;

$sql = "select * from scan_list LEFT JOIN target_info ON scan_list.hash = target_info.hash";
$results = $db->query($sql);
if (mysql_num_rows($results) > 0){
    $i = 1;
    while ($fs = $db->fetch_array($results))
    {
        $url = $fs['1'];
		$url = str_replace(array("\r\n", "\r","\n"), "", $url);
        $pointserver = $fs['pointserver'];
        $rule = $fs['rule'];
        $siteuser = $fs['siteuser'];
        $sitepwd = $fs['sitepwd'];
        $cookie = $fs['cookie'];
        $hash = $fs['11'];
        $delay = $fs['delay'];
        $status = $fs['status'];
        $nextscan = $fs['nextscan'];
        $customer = $fs['customer'];

        if ($delay == '2'){
            $delay_new='3';
        }else if ($delay == '3'){
            $delay_new='6';
        }else if ($delay == '4') {
            $delay_new = '12';
        }else{
            $delay_new = '1';
        }

        $finishtime = $fs['finishtime'];
        $finishtime = explode(',',$finishtime);
        list($day, $month, $year) = split ('[/.-]', $finishtime[0]);
        $finishtime = $year.'-'.$month.'-'.$day;
        $finishtime=strtotime($finishtime);
        $time_now=strtotime(date("Y-m-d"));
        $time_sub=sub_days($time_now,$finishtime);
        $time_sub= floor($time_sub/30);

        if ($status == 'new'){

            $up_arr['status'] = 'ing';
            //echo "$url|$pointserver|$rule|$siteuser|$sitepwd|$cookie|$hash";
            $scan_arr['target_url'] = $url;
			
            $scan_arr['scan_rule'] = $rule;
            $scan_arr['siteuser'] = $siteuser;
            $scan_arr['sitepwd'] = $sitepwd;
            $scan_arr['sitecookie'] = $cookie;
            $scan_arr['hash'] = $hash;
            //echo '***'.json_encode($scan_arr).'***';

            echo base64_encode(json_encode($scan_arr));

            $update = $db->update('scan_list',$up_arr,"status='new' and hash='{$hash}'");
            exit(0);
        }else if (($status == 'ok') and ($nextscan =='')){
            if ($time_sub == $delay_new){

//                echo '---'.$url.'---';

                $in_arr['url'] = $url;
                $in_arr['createtime'] = date('Y-m-d');
                $in_arr['user'] = $_SESSION['username'];//当前session用户
                $in_arr['pointserver'] = specify_server();//分配节点服务器ip
                $in_arr['group'] = "";//项目组名称
                $in_arr['siteuser'] = $siteuser;
                $in_arr['sitepwd'] = $sitepwd;
                $in_arr['cookie'] = $cookie;
                $in_arr['rule'] = $rule;
                $in_arr['status'] = 'new';
                $in_arr['customer'] = $customer;
                $in_arr['delay'] = $delay;

                $in_arr['hash'] = md5($in_arr['url'].time().authkey);
                $up_arr['nextscan'] = $in_arr['hash'];

                //$insert = $db->insert_into("scan_list",$in_arr);
                //$update = $db->update('scan_list',$up_arr,"hash='{$hash}'");

                $in_spider_arr['url'] = $url;
                $in_spider_arr['createtime'] = date('Y-m-d');
                $in_spider_arr['siteuser'] = $_POST['user'];
                $in_spider_arr['sitepwd'] = $_POST['pwd'];
                $in_spider_arr['status'] = 'new';
                $in_spider_arr['check_status'] = 'new';
                $in_spider_arr['customer'] = $_POST['customer'];
                $in_spider_arr['delay'] = $_POST['delay'];
                $in_spider_arr['hash'] = $in_arr['hash'];
//                $insert = $db->insert_into("spider", $in_spider_arr);
            }
        }else if ($status == 'ing'){
//            $sql = "select * from `scan_list` where status='ing'";
//            $sf = $db->fetch_assoc($sql);
            $get_hash = $fs['11'];
            if (!empty($get_hash)){
                $url = "http://127.0.0.1/file.php?p=$get_hash";
//                echo '+++'.$url.'+++';
                $scan_data = file_get_contents($url);
                if ($scan_data != 'null'){
                    get_xml($url);
                    get_spider($url);
                }else if ($scan_data == 'null'){
                    //print "aa";
                    continue;
                }
            }
        }
    }
    }

//$get_hash = '181bc7b15ab682b2753afa23011e3084';
  //          if (!empty($get_hash)){
    //            $url = "http://127.0.0.1/file.php?p=$get_hash";
////                echo '+++'.$url.'+++';
////                get_xml($url);
//               get_spider($url);
//            }
//function spider_read_hash(){
//    global $db;
//    $sql = "select * from spider where status = 'new'";
//    $fs = $db->fetch_assoc($sql);
////    if (mysql_num_rows($results) > 0){
////        while ($fs = $db->fetch_array($results)){
//            $hash = $fs['hash'];
//            $hash = 'e27c1ac042e4aae07293edc569eaa69d';
//            $up_arr['status'] = 'ok';
////            echo $hash;
////            $update = $db->update('spider',$up_arr,"status='new' and hash='{$hash}'");
//            if (!empty($hash)){
//                $url = "http://127.0.0.1/WDScanner/file.php?p=$hash";
////                echo '+++'.$url.'+++';
////                get_xml($url);
//                get_spider($url);
//        }
////    }
////}
//}
//spider_read_hash();

?>