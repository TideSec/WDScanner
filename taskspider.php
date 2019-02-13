<?php
require(dirname(__FILE__).'/include/config.inc.php');
#print "ok1";
global $db;
$sql = "select * from spider where check_status='new' or check_status='ing'";

$results = $db->query($sql);
if (mysql_num_rows($results) > 0){
    $i = 1;
    while ($fs = $db->fetch_array($results))
    {
        $url = $fs['url'];
        $siteuser = $fs['siteuser'];
        $sitepwd = $fs['sitepwd'];
        $hash = $fs['hash'];
        $check_status = $fs['check_status'];

        if ( $check_status =='ing' ) {
            $get_hash = $hash;
            if (!empty($get_hash)) {
                $url = "http://127.0.0.1/filespider.php?p=$get_hash";
//                echo $url;
                //check_url($url, $get_hash);
                $spider_data = file_get_contents($url);
                if ($spider_data != 'null'){
                    check_url($url, $get_hash);
                }else if ($spider_data == 'null'){
                    //print "aa";
                    continue;
                }
            }
        }else if ( $check_status =='new' ){
            $up_arr['check_status'] = 'ing';
            //$hash = '4fd615f4a8c0eb8e7889a003587ae222';
            $sql1 = "select url_all,url from spider where hash = '$hash'";
            $results1 = $db->query($sql1);
            echo $hash."<br>";
            if (mysql_num_rows($results1) > 0){
                while ($fs1 = $db->fetch_array($results1)){
                    echo $fs1[1]."<br>".$fs1[0];
                    if ($fs1[0] = '<br>'){
                        echo $fs1[1];
                    }
                }
            }
            $update = $db->update('spider',$up_arr,"hash='{$hash}'");
            exit(0);
        }
    }
}

?>