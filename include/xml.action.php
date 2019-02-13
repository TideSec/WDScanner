<?php
ini_set('max_execution_time',100);
function get_xml($url) {
	global $db;
    #$url = 'http://127.0.0.1/file.php?p=28c69769c6ceca0c20a4efd73d989ca6';
    #print $url;
	$xml_str = file_get_contents($url);
	#print $xml_str;
	if (strlen($xml_str) > 300){
		$xml = xml2array($xml_str);
		$tmp_arr = explode("=",$url);
		$hash = $tmp_arr[1];

		$site = $xml['ScanGroup']['Scan']['StartURL'];
		$FinishTime = $xml['ScanGroup']['Scan']['FinishTime'];
		$ScanTime = $xml['ScanGroup']['Scan']['ScanTime'];
		$Banner = $xml['ScanGroup']['Scan']['Banner'];
		$Responsive = $xml['ScanGroup']['Scan']['Responsive'];
		$Os = $xml['ScanGroup']['Scan']['Os'];
		$Technologies = $xml['ScanGroup']['Scan']['Technologies'];

		$in_target_info_arr['url'] = $site;
		$in_target_info_arr['FinishTime'] = $FinishTime;
		$in_target_info_arr['ScanTime'] = $ScanTime;
		$in_target_info_arr['Banner'] = $Banner;
		$in_target_info_arr['Responsive'] = $Responsive;
		$in_target_info_arr['Os'] = $Os;
		$in_target_info_arr['Technologies'] = $Technologies;
		$in_target_info_arr['hash'] = $hash;

		$insert = $db->insert_into("target_info",$in_target_info_arr);

		$ReportItems = $xml['ScanGroup']['Scan']['ReportItems']['ReportItem'];

		for ($i = 1; $i <= count($ReportItems); $i++) {
			######## ©������ #########
			$ld_Name = $ReportItems[$i]['Name'];
			if ( !empty($ld_Name) ){
				$ld_ModuleName = $ReportItems[$i]['ModuleName'];
				$ld_Details = $ReportItems[$i]['Details'];
				//$ld_Details = "";
				$ld_Affects = $ReportItems[$i]['Affects'];
				$ld_Parameter = $ReportItems[$i]['Parameter'];
				$ld_Severity = $ReportItems[$i]['Severity'];
				$ld_Request = str_replace("\n","<br>",$ReportItems[$i]['TechnicalDetails']['Request']);
				$ld_Response = str_replace("\n","<br>",$ReportItems[$i]['TechnicalDetails']['Response']);
				###########################

				$in_target_vul_arr['Name'] = $ld_Name;
				$in_target_vul_arr['ModuleName'] = $ld_ModuleName;
				$in_target_vul_arr['Details'] = str_replace(array("Acunetix", "acunetix","wvs"), "wdscan", addslashes($ld_Details));
				$in_target_vul_arr['Affects'] = $ld_Affects;
				$in_target_vul_arr['Parameter'] = $ld_Parameter;
				$in_target_vul_arr['Severity'] = $ld_Severity;
				$Request = addslashes($ReportItems[$i]['TechnicalDetails']['Request']);
                $in_target_vul_arr['Request'] = str_replace(array("Acunetix", "acunetix","wvs"), "wdscan", $Request);
                $Response = addslashes($ReportItems[$i]['TechnicalDetails']['Response'].$i);
                $in_target_vul_arr['Response'] = str_replace(array("Acunetix", "acunetix","wvs"), "wdscan", $Response);
				$in_target_vul_arr['hash'] = $hash;
				$in_target_vul_arr['unique'] = MD5($in_target_vul_arr['Request'].$i.$hash);
                $in_target_vul_arr['vul_cn_id'] = get_vul_cn_id($ld_Name);


				if ($ld_Severity != 'info'){
                #if ($ld_Name == 'Blind SQL Injection'){
					$info = "$site <br> $FinishTime <br> $ScanTime <br> $Responsive <br> $Banner <br> $Os <br> $Technologies <br> $ld_Name <br> $ld_ModuleName <br> $ld_Details <br> $ld_Affects <br> $ld_Parameter <br> $ld_Severity <p> $ld_Request <p> $ld_Response";
//                    $in_target_vul_arr['Name'] = "111";
//                   $in_target_vul_arr['ModuleName'] = "222";
                    #$in_target_vul_arr['Details'] = "333";
//                    $in_target_vul_arr['Affects'] = "444";
//                    $in_target_vul_arr['Parameter'] = "555";
//                    $in_target_vul_arr['Severity'] = "666";
                    #$in_target_vul_arr['Request'] = "777";
                    #$in_target_vul_arr['Response'] = "888";
                    #$in_target_vul_arr['hash'] = "999";
                    #$in_target_vul_arr['unique'] = $i;

					$insert = $db->insert_into("target_vul",$in_target_vul_arr);
				}
			}
		}

        $up_arr['high'] = get_severity($hash,'high');
        $up_arr['medium'] = get_severity($hash,'medium');
        $up_arr['low'] = get_severity($hash,'low');
		$up_arr['status'] = 'ok';
        $up_arr['finishtime'] = $FinishTime;
		$update = $db->update('scan_list',$up_arr,"status='ing' and hash='{$hash}'");

		$sql = "SELECT point_server.hash,point_server.level FROM `scan_list` LEFT JOIN `point_server` ON scan_list.pointserver = point_server.pointip where scan_list.hash='{$hash}'";
		$results = $db->fetch_assoc($sql);
		$iphash = $results['hash'];

		$up_arr1['level'] = $results['level'] - 1;
		if ( $up_arr1['level'] > 0 ){
			$update = $db->update("point_server",$up_arr1,"hash='{$iphash}'");
		}
	}
}
function get_spider($url) {
    global $db;
    #$url = 'http://127.0.0.1/file.php?p=28c69769c6ceca0c20a4efd73d989ca6';
    #print $url;
    $xml_str = file_get_contents($url);
    #print $xml_str;
    if (strlen($xml_str) > 300) {
        $xml = xml2array($xml_str);
        $tmp_arr = explode("=", $url);
        $hash = $tmp_arr[1];

        $SiteFile = $xml['ScanGroup']['Scan']['Crawler']['SiteFiles']['SiteFile'];
        $site = $xml['ScanGroup']['Scan']['StartURL'];

        $url_all_num = 1;
        $spider_url = array();
        for ($i = 0; $i <= count($SiteFile) - 2; $i++) {
            $FullURL = $SiteFile[$i]['FullURL'];
            $spider_url[] = $FullURL;
            $Variation = $SiteFile[$i]['Variations']['Variation'];

            if (count($Variation) > 2) {
                for ($x = 0; $x <= count($Variation); $x++) {
                    $spider_url[] = $Variation[$x]['URL'];
                }
            } elseif (count($Variation) == 2) {
                $spider_url[] = $Variation['URL'];
            }
        }
        $spider_url = array_unique($spider_url);
        $act_num = 0;
        $all_url = '';
        $active_url='';
        //echo "spider_url_num:" . count($spider_url);
        $web_type = ['aspx', 'asp', 'jsp', 'php', 'perl', 'cgi', 'do', 'action', '?'];
        foreach ($spider_url as $url) {
            if ($url != '') {
//                echo $url . "<br />";
                $all_url = $all_url.$url.'<br>';
                foreach ($web_type as $type) {
                    if (strstr($url, $type) != '') {
                        $active_url = $active_url.$url.'<br>';
                        $act_num = $act_num+1;
                    }
                }
            }
        }
        $up_arr['url_num'] = count($spider_url);
        $up_arr['act_num'] = $act_num;
        $up_arr['status'] = 'ok';
        $up_arr['finishtime'] = date('Y-m-d');
        $up_arr['url_all'] = $all_url;
        $up_arr['act_all'] = $active_url;
		$up_arr['check_status'] = 'new';

        $update = $db->update('spider',$up_arr,"hash='{$hash}'");
    }
}

function check_url($url,$hash) {
    global $db;
    #$url = 'http://127.0.0.1/file.php?p=28c69769c6ceca0c20a4efd73d989ca6';
    #print $url;
    $check_str = file_get_contents($url);
    $bad_url = '';
    $bad_url_num = 0;
    $evil_url = '';
    $evil_url_num = 0;
    $key_url = '';
    $key_url_num = 0;
    $new_url_alls = '';
    $new_act_alls = '';
//    $new_act_all_num ='';
//    $new_url_all_num='';
    //print $check_str;
    if (strlen($check_str) > 50){
        $urls = explode('+++',$check_str);
        $urls = str_replace("\n", '', $urls);
        foreach ($urls as $url_a){
            //print $url."+++"."<br>";
            $flag = explode('##',$url_a);
			$flag[0]=trim($flag[0]);
//            print $flag[0].$flag[1]."<br>";
            if ($flag[0] == "bad"){
                //print $flag[1]."<br>";
                $bad_url = $bad_url.$flag[1]."<br>";
                $bad_url_num = $bad_url_num+1;
            }
            if ($flag[0] == "evil"){
                $evil_url = $evil_url.$flag[1]."<br>";
                $evil_url_num = $evil_url_num+1;
            }
            if ($flag[0] == "key"){
                $key_url = $key_url.$flag[1]."<br>";
                $key_url_num = $key_url_num+1;
            }
        }
        //从urlall.txt中读取所有url
        $url_all_url = $url.'&c=urlall';
        $url_all_content = file_get_contents($url_all_url);
        $url_all_content = str_replace(array("\r\n", "\r","\n"),'###',$url_all_content);
        //echo $url_all_content;

        if (strlen($url_all_content) > 50) {
            $all_urls = explode('###', $url_all_content);
            //从数据库中读取之前的url纪录，url_all和act_all
            $sql1 = "select url_all,act_all from spider where hash = '$hash'";
            $results1 = $db->fetch_assoc($sql1);
            $aaa = $results1['url_all'];
            $old_url_all = explode('<br>', $aaa);
            $old_act_all = explode('<br>', $results1['act_all']);

            //$new_url_all为urlall.txt读取后的数组和数据库中读取的url_all数组合并后去重得到的新数组
            $new_url_all = array_unique(array_merge($old_url_all, $all_urls));
            $new_url_all_num = count($new_url_all);
            echo $new_url_all_num;
            if ($new_url_all_num < 150000) {
                //$new_act_all为$old_act_all，加上$new_url_all中重新获取到的动态页url
                $new_act_all = $old_act_all;
                $web_type = ['aspx', 'asp', 'jsp', 'php', 'perl', 'cgi', 'do', 'action', '?'];
                foreach ($new_url_all as $url2) {
                    if ($url2 != '') {
                        foreach ($web_type as $type) {
                            if (strstr($url2, $type) != '') {
                                array_push($new_act_all, $url2);
                            }
                        }
                    }
                }
                $new_act_all = array_unique($new_act_all);
                $new_act_all_num = count($new_act_all);


                foreach ($new_url_all as $new_url) {
                    $new_url_alls = $new_url_alls . $new_url . '<br>';
                }

                foreach ($new_act_all as $new_url) {
                    $new_act_alls = $new_act_alls . $new_url . '<br>';
                }
            }
        }


       // print $bad_url;
        $str3 = addslashes($bad_url);
        $encode3 = mb_detect_encoding($str3, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
        $up_arr['bad_page'] = mb_convert_encoding($str3, 'UTF-8', $encode3);

        $str4 = addslashes($evil_url);
        $encode4 = mb_detect_encoding($str4, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
        $up_arr['evil_page'] = mb_convert_encoding($str4, 'UTF-8', $encode4);

//        $up_arr['bad_page'] = addslashes($bad_url);
//        $up_arr['evil_page'] =addslashes($evil_url);

		$str0 = addslashes($key_url);
        $encode0 = mb_detect_encoding($str0, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
        $up_arr['key_page'] = mb_convert_encoding($str0, 'UTF-8', $encode0);
		$up_arr['bad_num'] = $bad_url_num;
        $up_arr['evil_num'] = $evil_url_num;
        $up_arr['key_num'] = $key_url_num;
        $up_arr['snap_num'] = $key_url_num+$evil_url_num;
        $up_arr['check_status'] = 'ok';
        $up_arr['snap_file'] = dirname(dirname(__FILE__)).'/report/logspider/'.$hash;

        if (isset($new_url_alls)){
            $str1 = addslashes($new_url_alls);
            $encode1 = mb_detect_encoding($str1, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
            $up_arr['url_all'] = mb_convert_encoding($str1, 'UTF-8', $encode1);
//    $up_arr['url_all'] = addslashes($new_url_alls);
        }

        if (isset($new_act_alls)) {
            $str2 = addslashes($new_act_alls);
            $encode2 = mb_detect_encoding($str2, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
            $up_arr['act_all'] = mb_convert_encoding($str2, 'UTF-8', $encode2);
//        $up_arr['act_all'] = addslashes($new_act_alls);
        }


        if (isset($new_url_all_num)){
            $up_arr['url_num'] = $new_url_all_num;
        }
        if (isset($new_act_all_num)) {
            $up_arr['act_num'] = $new_act_all_num;
        }
        $update = $db->update('spider',$up_arr,"hash='{$hash}'");
}
}
?>