<?php
require(dirname(__FILE__).'/include/config.inc.php');
#print "ok1";
$sql = "select * from info where status='new' or status='ing'";
#$sql = "select * from info where hash = '83c8d07b99c516c1f05abd2e408984f0'";

$results = $db->query($sql);
if (mysql_num_rows($results) > 0){
    $i = 1;
    while ($fs = $db->fetch_array($results))
    {
        $url = $fs['url'];
        $hash = $fs['hash'];
        $status = $fs['status'];
        //echo '----';
        //echo $url;

        if ( $status =='ing' ){
            //echo $hash;
            //echo '22';
//    $sql = "select * from `info` where status='ing'";
//    $sf = $db->fetch_assoc($sql);
            $get_hash = $hash;
            //$get_hash = '55984d6dd7d1496e7a347a7ec56eb623';
//    echo  $get_hash;
            if (!empty($get_hash)) {
                $url = "http://127.0.0.1/fileinfo.php?p=$get_hash";
                //echo $url;
                $info_data = file_get_contents($url);
                //echo $info_data;

                if ($info_data != 'null'){
                    $up_arr = array();
//        $info_data = iconv("gb2312","utf-8//IGNORE",$info_data);
                    //echo $info_data;
                    if ($json_data = json_decode($info_data, true)) {
                    } else {
                        $info_data = iconv("gb2312", "utf-8//IGNORE", $info_data);
                        $json_data = json_decode($info_data, true);
                    }
                    // echo $json_data['domain_info'];
                    //$up_arr['hash'] = $get_hash;
                    $up_arr['ip'] = $json_data['ip'];
//        echo $up_arr['ip'];
                    $up_arr['port_num'] = $json_data['port_num'];
                    $up_arr['port'] = addslashes($json_data['port_info']);
                    $up_arr['sub_num'] = $json_data['domain_num'];
                    $up_arr['sub'] = $json_data['domain_info'];
                    $up_arr['cms'] = $json_data['whatcms_text'];
                    $up_arr['waf'] = $json_data['waf'];
                    $up_arr['os'] = $json_data['os'];
                    $up_arr['os_info'] = addslashes($json_data['os_info']);
                    $up_arr['whatweb_info'] = addslashes($json_data['whatweb_text']);
                    $up_arr['language'] = $json_data['xpb'];
                    $up_arr['middleware'] = $json_data['httpserver'];
                    $up_arr['weakfile_num'] = $json_data['weakfile_num'];
                    $up_arr['weakfile'] = addslashes($json_data['weakfile']);
//        $up_arr['other'] = implode('#',$json_data);
                    $up_arr['status'] = 'ok';
                    $up_arr['title'] = $json_data['title'];
//        print $json_data['title'];
                    $insert = $db->update("info", $up_arr, "hash='{$get_hash}'");
                }else if ($info_data == 'null'){
                    //print "aa";
                    continue;
                }
            }
        }else if ( $status =='new' ){
            //echo '11';
            $up_arr1 = array();
            $up_arr1['status'] = 'ing';
            $scan_arr['target_url'] = $url;
            $scan_arr['hash'] = $hash;
            //echo json_encode($scan_arr);

            echo base64_encode(json_encode($scan_arr));

            $update = $db->update('info',$up_arr1,"status='new' and hash='{$hash}'");
            exit(0);
        }
    }
}


?>