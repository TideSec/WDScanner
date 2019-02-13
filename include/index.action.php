<?php

function scan_index() {
    global $db;

    //print_r($_POST);
    $sql = "SELECT * FROM scan_list INNER JOIN  customer ON scan_list.customer = customer.id  INNER JOIN info ON  scan_list.hash = info.hash  order by rand() DESC LIMIT 5";
    $results = $db->query($sql);
//    print $sql;
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $i;
			$url = substr($fs["1"],0,35);
            $link_url = $fs["1"];
            $customer = $fs["name"];
            $pointserver = $fs["4"];
            $status = $fs["10"];
            $hash = $fs["11"];
            $finishtime = $fs["finishtime"];
            $finishtime = explode(',',$finishtime)[0];

            $banner = substr($fs["cms"],0,18);
            //$responsive = $fs["responsive"];
            //$technologies = $fs["technologies"];
            $os = substr(str_replace("Microsoft ","",$fs["os"]),0,13);
            $high = $fs["high"];
            $medium = $fs["medium"];
            $low =  $fs["low"];
            $delay = $fs["13"];
            $title = mb_substr($fs["title"],0,8,"utf-8");


            if ($status == 'ok'){
                $class = 'success';
                $responsive = "已完成";
            }else if ($status == 'ing'){
                $class = 'warning';
                $responsive = "扫描中";
            }else if ($status == 'new'){
                $class = 'info';
                $responsive = "队列中";
            }else{
                $class = 'error';
            }

            if ($delay == '1'){
                $scan_delay = "每月一次";
            }else if ($delay == '2'){
                $scan_delay = "每季度一次";
            }else if ($delay == '3'){
                $scan_delay = "每半年一次";
            }else if ($delay == '4'){
                $scan_delay = "仅一次";
            }else{
                $scan_delay = "仅一次";
            }


            $html_str .= "
									<tr class=\"$class\">
										<td style=\"text-align:center\">
											$id
										</td>
										<td style=\"word-break:break-all; word-wrap:break-word;text-align:center\">
											<a href=$link_url target=\"_blank\">$url</a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=vul&p={$hash}\">$title</a>
										</td>
										<td style=\"text-align:center\">
											$responsive
										</td>
										<td style=\"text-align:center\">
											$pointserver
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=vul&p={$hash}&c=high\"><font color=\"red\">$high</font></a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=vul&p={$hash}&c=medium\"><font color=\"orange\">$medium</font></a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=vul&p={$hash}&c=low\"><font color=\"green\">$low</font></a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=siteinfo&p={$hash}\">$banner</a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=siteinfo&p={$hash}\">$os</a>
										</td>
										<td style=\"text-align:center\">
											$scan_delay
										</td>
										<td style=\"text-align:center\">
											$finishtime
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=vul&p={$hash}\">详情</a>|<a href=\"javascript:del('{$hash}')\">删除</a>|<a href=\"javascript:exportreport('{$hash}')\">报告</a>
										</td>
									</tr>\r\n";
            $i ++;
        }

        return $html_str;
    }else{
        return "";

    }
}

function spider_index() {
    global $db;

    $sql = "SELECT * FROM spider INNER JOIN  customer ON spider.customer = customer.id order by rand() LIMIT 5";
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $i;
			$url = substr($fs["1"],0,40);
            $link_url = $fs["1"];
            $customer = $fs["name"];
            $status = $fs["status"];
            $hash = $fs["hash"];
            $url_num= $fs["url_num"];
            $act_num = $fs["act_num"];
            $key_num = $fs["key_num"];
            $bad_num = $fs["bad_num"];
            $snap_num = $fs["snap_num"];
            $evil_num = $fs["evil_num"];
            $delay = $fs["12"];
            $finishtime = $fs["finishtime"];


            if ($status == 'ok'){
                $class = 'success';
                $responsive = "已完成";
            }else if ($status == 'ing'){
                $class = 'warning';
                $responsive = "爬取中";
            }else if ($status == 'new'){
                $class = 'info';
                $responsive = "队列中";
            }else{
                $class = 'error';
                $responsive = "队列中";
            }


            if ($delay == '1'){
                $scan_delay = "每月一次";
            }else if ($delay == '2'){
                $scan_delay = "每季度一次";
            }else if ($delay == '3'){
                $scan_delay = "每半年一次";
            }else if ($delay == '4'){
                $scan_delay = "仅一次";
            }else{
                $scan_delay = "仅一次";
            }


            $html_str .= "
									<tr class=\"$class\">
										<td style=\"text-align:center\">
											$id
										</td>
										<td style=\"word-break:break-all; word-wrap:break-word;text-align:center\">
											<a href=$link_url target=\"_blank\">$url</a>
										</td>
										<td style=\"text-align:center\">
											$customer
										</td>
										<td style=\"text-align:center\">
											$responsive
										</td>
										<td style=\"text-align:center\">
											$url_num
										</td>
										<td style=\"text-align:center\">
											$act_num
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=spiderinfo&p={$hash}&c=high\"><font color=\"red\">$evil_num</font></a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=spiderinfo&p={$hash}&c=medium\"><font color=\"orange\">$key_num</font></a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=spiderinfo&p={$hash}&c=low\"><font color=\"green\">$bad_num</font></a>
										</td>
										<td style=\"text-align:center\">
											$snap_num
										</td>
										
										<td style=\"text-align:center\">
											$scan_delay
										</td>
										<td style=\"text-align:center\">
											$finishtime
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=spiderinfo&p={$hash}\">详情</a>|<a href=\"?m=vul&p={$hash}\">扫描</a>|<a href=\"?m=siteinfo&p={$hash}\">信息</a>|<a href=\"javascript:exportreport('{$hash}')\">报告</a>
										</td>
									</tr>\r\n";
            $i ++;
        }

        return $html_str;
    }else{
        return "";

    }
}

function info_index() {
    global $db;

    $sql = "SELECT * FROM info INNER JOIN  customer ON info.customer = customer.id order by rand()  LIMIT 5";
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $i;
            $cus_id = $fs["customer"];
            $url = substr($fs["1"],0,30);
			$link_url = $fs["1"];
            $hash = $fs["2"];
            $ip = $fs["ip"];
            $customer = $fs["name"];
            $port_num = $fs["port_num"];
            $sub_num = $fs["sub_num"];
            $cms = mb_substr($fs["cms"],0,8,"utf-8");
            $waf = substr($fs["waf"],0,8);;
            $os = substr(str_replace("Microsoft ","",$fs["os"]),0,13);
            $language = substr($fs["language"],0,12);
            $middleware = substr($fs["middleware"],0,18);
            $weakfile_num = $fs["weakfile_num"];
            $other = $fs["other"];
            $title = mb_substr($fs["title"],0,8,"utf-8");
            $class = 'success';


            $html_str .= "
									<tr class=\"$class\">
                                        <td style=\"text-align:center\">
                                           $id
                                        </td>
                                        <td style=\"word-break:break-all; word-wrap:break-word;text-align:center\">
                                            <div style=\"width: 210px;\"><a href=$link_url target=\"_blank\">$url</a></div>
                                        </td>
                                    
                                        <td style=\"text-align:center\">
                                           <div style=\"width: 120px;\"> <a href=\"?m=siteinfo&p={$hash}\">$title</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 90px;\">$ip</div>
                                        </td>
                                        <td style=\"text-align:center\">
                                           <div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$port_num</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$sub_num</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$weakfile_num</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 80px;\">$cms</div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 60px;\">$waf</div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 110px;\">$os</div>
                                        </td>
                                        
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 140px;\">$middleware</div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 60px;\">$language</div>
                                        </td>
                                        
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=siteinfo&p={$hash}\">详情</a>|<a href=\"javascript:delinfo('{$hash}')\">删除</a>|<a href=\"javascript:exportreport('{$hash}')\">报告</a>
                                        </td>
									</tr>\r\n";
            $i ++;
        }

        return $html_str;
    }else{
        return "";
    }
}


function search() {
    global $db;

    //print_r($_POST);
    $action = $_GET['c'];


    if ($action == 'search'){

        $i = 1;
        if(!empty($_POST['os']) or !empty($_POST['title']) or !empty($_POST['port'])  or !empty($_POST['middleware']) or !empty($_POST['cms']) or !empty($_POST['language'])){

            $os = $_POST['os'];
            $middleware = $_POST['middleware'];
            $cms = $_POST['cms'];
            $language = $_POST['language'];
            $title = $_POST['title'];
            $port = $_POST['port'];
            $sql1 = "select hash from info where language like '%$language%' and cms like '%$cms%' and port like '%$port%' and title like '%$title%' and middleware like '%$middleware%' and os like '%$os%'";
            $results1 = $db->query($sql1);
            if (mysql_num_rows($results1) > 0){
                while ($fs = $db->fetch_array($results1))
                {

                    $in_arr[$i] = $fs["0"];
//                    echo $in_arr[$i].'---';
                    $i = $i+1;
                }
                }
//            echo "<script>alert('info');</script>";
        }

        if(!empty($_POST['url']) or !empty($_POST['name']) or !empty($_POST['customer']) or !empty($_POST['delay'])){

            $url = $_POST['url'];
            $name = $_POST['name'];
            $customer = $_POST['customer'];
            $delay = $_POST['delay'];
            $sql2 = "select hash from scan_list where url like '%$url%' and customer like '%$customer%' and delay like '%$delay%'";
            $results2 = $db->query($sql2);
            if (mysql_num_rows($results2) > 0){
                while ($fs = $db->fetch_array($results2))
                {

                    $in_arr[$i] = $fs["0"];
//                    echo $in_arr[$i].'+++';
                    $i = $i+1;
                }
            }
//            echo "<script>alert('info');</script>";
        }
        $in_arr = array_unique($in_arr);
        $a = '';

        foreach ($in_arr as $in){

//            echo '***'.$in.'***';
            $a = $a.$in;
        }
        echo "<script>location.href='?m=search&hash=$a'</script>";
        //return $in_arr;
        }elseif ($action == 'spider'){

        $i = 1;
            $url = $_POST['url'];
            $customer = $_POST['customer'];
            $url_key = $_POST['url_key'];

            $sql1 = "select hash from spider where url like '%$url%' and customer like '%$customer%' and url_all like '%$url_key%'";
//            echo $sql1;
            $results1 = $db->query($sql1);
            if (mysql_num_rows($results1) > 0){
                while ($fs = $db->fetch_array($results1))
                {

                    $in_arr[$i] = $fs["0"];
//                    echo $in_arr[$i].'---';
                    $i = $i+1;
                }
            }else{
                $in_arr='';
            }
        $in_arr = array_unique($in_arr);
        $a = '';
        foreach ($in_arr as $in){

//            echo '***'.$in.'***';
            $a = $a.$in;
        }
        echo "<script>location.href='?m=spidersearch&key=$url_key&hash=$a'</script>";
        //return $in_arr;
    }
}

function search_center() {
    global $db;


    $hashs = $_GET['hash'];
    $hash = '';
    $a = strlen($hashs)/32;
    while ($a >= 1){
        $x = substr($hashs,($a-1)*32,32);
        $a = $a -1;
        $hash = $hash.$x."','";
    }
    $hash = "'".substr($hash,0,-2);


    $sql = "SELECT * FROM info INNER JOIN  customer ON info.customer = customer.id  INNER JOIN  spider ON spider.hash = info.hash where info.hash in ($hash) order by info.id desc";
//    echo $sql;
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $i;
            $cus_id = $fs["customer"];
            $url = substr($fs["1"],0,28);
			$link_url = $fs["1"];
            $hash = $fs["2"];
            $ip = $fs["ip"];
            $customer = $fs["name"];
            $port_num = $fs["port_num"];
            $sub_num = $fs["sub_num"];
            $cms = mb_substr($fs["cms"],0,8,"utf-8");
            $waf = substr($fs["waf"],0,8);
            $os = substr(str_replace("Microsoft ","",$fs["os"]),0,13);
            $language = substr($fs["language"],0,12);
            $middleware = substr($fs["middleware"],0,13);
            $weakfile_num = $fs["weakfile_num"];
            $high = get_severity($hash,'high');
            $medium = get_severity($hash,'medium');
            $low = get_severity($hash,'low');

            $key_num = $fs["key_num"];
            $bad_num = $fs["bad_num"];
            $evil_num = $fs["evil_num"];

            $title = mb_substr($fs["title"],0,10,"utf-8");
            $class = 'success';

            $html_str .= "
                                    <tr class=\"$class\">
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 10px;\">$id</div>
                                        </td>
                                        <td style=\"word-break:break-all; word-wrap:break-word;text-align:center\">
                                            <div style=\"width: 210px;\"><a href=$link_url target=\"_blank\">$url</a></div>
                                        </td>
                                    
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 140px;\"><a href=\"?m=siteinfo&p={$hash}\">$title</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=vul&p={$hash}&c=high\"><font color=\"red\">$high</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=vul&p={$hash}&c=medium\"><font color=\"orange\">$medium</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=vul&p={$hash}&c=low\"><font color=\"green\">$low</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$port_num</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$sub_num</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$weakfile_num</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=spiderinfo&p={$hash}\"><font color=\"red\">$evil_num</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=spiderinfo&p={$hash}\"><font color=\"orange\">$key_num</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=spiderinfo&p={$hash}\"><font color=\"green\">$bad_num</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 80px;\">$cms</div>
                                        </td>
                                        
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 100px;\"><a href=\"?m=siteinfo&p={$hash}\">$os</a></div>
                                        </td>
                                        
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 100px;\">$middleware</div>
                                        </td>
                                                                                
                                        <td style=\"text-align:center\">
                                            <a href=\"javascript:resetall('{$hash}')\">重置</a>|<a href=\"javascript:delall('{$hash}')\">删除</a>|<a href=\"javascript:exportreport('{$hash}')\">报告</a>
                                        </td>
                                    </tr>\r\n";
            $i ++;
        }

        return $html_str;
    }else{
        return "";

    }
}

function manager() {
    global $db;
    $action = $_GET['c'];
    $in_arr = '';
    $in_arr_spider = '';
    $in_arr_info = '';

    if ($action == 'new'){

        if(!empty($_POST['url'])){
            $pointserver = specify_server();
            if (!empty($pointserver)){
                $urls = str_replace(array("\r\n", "\r"), "\n", $_POST['url']);
                $urls = explode("\n",$urls);
                foreach ($urls as $url){
                    $in_arr['url'] = $url;
                    $in_arr['createtime'] = date('Y-m-d');
                    $in_arr['user'] = $_SESSION['username'];//当前session用户
                    $in_arr['pointserver'] = specify_server();//分配节点服务器ip
                    $in_arr['group'] = "";//项目组名称
                    $in_arr['siteuser'] = $_POST['user'];
                    $in_arr['sitepwd'] = $_POST['pwd'];
                    $in_arr['cookie'] = $_POST['cookie'];
                    $in_arr['rule'] = $_POST['rule'];
                    $in_arr['status'] = 'new';
                    $in_arr['customer'] = $_POST['customer'];
                    $in_arr['delay'] = $_POST['delay'];
                    $in_arr['hash'] = md5($in_arr['url'].time().authkey);

                    #if ( $_POST['auth'] == 'on' ) nginx_vhost( $in_arr['url'] , $in_arr['cookie'] );
                    $insert = $db->insert_into("scan_list",$in_arr);

                    $in_arr_spider['url'] = $url;
                    $in_arr_spider['createtime'] = date('Y-m-d');
                    $in_arr_spider['siteuser'] = $_POST['user'];
                    $in_arr_spider['sitepwd'] = $_POST['pwd'];
                    $in_arr_spider['status'] = 'new';
                    $in_arr_spider['check_status'] = 'wait';
                    $in_arr_spider['customer'] = $_POST['customer'];
                    $in_arr_spider['delay'] = $_POST['delay'];
                    $in_arr_spider['hash'] = $in_arr['hash'];

                    $insert = $db->insert_into("spider",$in_arr_spider);

                    $in_arr_info['url'] = $url;
                    $in_arr_info['createtime'] = date('Y-m-d');
                    $in_arr_info['status'] = 'new';
                    $in_arr_info['customer'] = $_POST['customer'];
                    $in_arr_info['hash'] = $in_arr['hash'];

                    $insert = $db->insert_into("info",$in_arr_info);
                }

                echo "<script>location.href='?m=manager'</script>";
                exit(0);

            }else{
                Message(" 请配置节点服务器 ","?m=point",0,3000);
            }}
    }

    $sql_num = "SELECT id FROM info";
    $totalnum = $db->db_num_rows($sql_num);
    $pagesize=50;
    //总共有几页
    $maxpage=ceil($totalnum/$pagesize);
    $page=isset($_GET['page'])?$_GET['page']:1;
    if($page <1)
    {
        $page=1;
    }
    if($page>$maxpage)
    {
        $page=$maxpage;
    }
    $limit=" limit ".($page-1)*$pagesize.",$pagesize";

    //$sql = "SELECT * FROM info LEFT JOIN  customer ON info.customer = customer.id  LEFT JOIN  spider ON spider.hash = info.hash  LEFT JOIN  scan_list ON scan_list.hash = info.hash order by info.id DESC {$limit}";
    $sql = "SELECT * FROM info INNER JOIN  customer ON info.customer = customer.id  INNER JOIN  spider ON spider.hash = info.hash  INNER JOIN  scan_list ON scan_list.hash = info.hash order by info.id DESC {$limit}";

    //    echo $sql;
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $i;
            $cus_id = $fs["customer"];
            $url = substr($fs["1"],0,28);
            $link_url = $fs["1"];
            $hash = $fs["2"];
            $ip = $fs["ip"];
            $status = $fs["67"];
            $customer = $fs["name"];
            $port_num = $fs["port_num"];
            $sub_num = $fs["sub_num"];
            $cms = mb_substr($fs["cms"],0,8,"utf-8");
            $waf = substr($fs["waf"],0,8);
            $os = substr(str_replace("Microsoft ","",$fs["os"]),0,13);
            $language = substr($fs["language"],0,12);
            $middleware = substr($fs["middleware"],0,12);
            $weakfile_num = $fs["weakfile_num"];
            $high = $fs["high"];
            $medium = $fs["medium"];
            $low =  $fs["low"];

            $key_num = $fs["key_num"];
            $bad_num = $fs["bad_num"];
            $evil_num = $fs["evil_num"];

            $title = mb_substr($fs["title"],0,10,"utf-8");

            if ($status == 'ok'){
                $class = 'success';
                $responsive = "已完成";
            }else if ($status == 'ing'){
                $class = 'warning';
                $responsive = "扫描中";
            }else if ($status == 'new'){
                $class = 'info';
                $responsive = "队列中";
            }else{
                $class = 'error';
            }


            $html_str .= "
                                    <tr class=\"$class\">
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 10px;\">$id</div>
                                        </td>
                                        <td style=\"word-break:break-all; word-wrap:break-word;text-align:center\">
                                            <div style=\"width: 210px;\"><a href=$link_url target=\"_blank\">$url</a></div>
                                        </td>
                                    
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 140px;\"><a href=\"?m=vul&p={$hash}\">$title</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=vul&p={$hash}&c=high\"><font color=\"red\">$high</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=vul&p={$hash}&c=medium\"><font color=\"orange\">$medium</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=vul&p={$hash}&c=low\"><font color=\"green\">$low</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$port_num</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$sub_num</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$weakfile_num</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=spiderinfo&p={$hash}\"><font color=\"red\">$evil_num</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=spiderinfo&p={$hash}\"><font color=\"orange\">$key_num</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=spiderinfo&p={$hash}\"><font color=\"green\">$bad_num</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 80px;\"><a href=\"?m=siteinfo&p={$hash}\">$cms</a></div>
                                        </td>
                                        
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 100px;\"><a href=\"?m=siteinfo&p={$hash}\">$os</a></div>
                                        </td>
                                        
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 100px;\"><a href=\"?m=siteinfo&p={$hash}\">$middleware</a></div>
                                        </td>
                                                                                
                                        <td style=\"text-align:center\">
                                            <a href=\"javascript:resetall('{$hash}')\">重置</a>|<a href=\"javascript:delall('{$hash}')\">删除</a>|<a href=\"javascript:exportreport('{$hash}')\">报告</a>
                                        </td>
                                    </tr>";

            $i ++;
        }
        $html_str =$html_str."<table class=\"table\" style=\"font-size:14px;\"><thead>
								<tr><td style=\"text-align:center\"><b>当前{$page}/{$maxpage}页 &nbsp;&nbsp;&nbsp;共{$totalnum}个项目  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   <a href='?m=manager&page=1'>首页</a> &nbsp;&nbsp;&nbsp;
                   <a href='?m=manager&page=".($page-1)."'>上一页</a> &nbsp;&nbsp;&nbsp;
                   <a href='?m=manager&page=".($page+1)."'>下一页</a> &nbsp;&nbsp;&nbsp;
                   <a href='?m=manager&page={$maxpage}'>尾页</a></b>
                                       </td></tr></thead>\r\n";
        return $html_str;
    }else{
        return "";

    }
}

function index() {
    global $db;

    #$sql = "SELECT * FROM scan_list as a,target_info as b where a.hash = b.hash";
    $sql = "SELECT * FROM scan_list INNER JOIN target_info ON scan_list.hash = target_info.hash order by createtime desc";
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $i;
            $url = $fs["1"];
            $user = $fs["3"];
            $pointserver = $fs["4"];
            $status = $fs["10"];
            $hash = $fs["11"];
            $finishtime = $fs["16"];
            $banner = $fs["17"];
            $responsive = $fs["18"];
            $technologies = $fs["20"];
            $os = $fs["19"];
            $high = $fs["high"];
            $medium = $fs["medium"];
            $low =  $fs["low"];

            if ($status == 'ok'){
                $class = 'success';
                $responsive = "已完成";
            }else if ($status == 'ing'){
                $class = 'ing';
                $responsive = "扫描中";
            }else if ($status == 'new'){
                $class = 'new';
                $responsive = "队列中";
            }else{
                $class = '';
            }

            $html_str .= "
									<tr class=\"$class\">
										<td style=\"text-align:center\">
											$id
										</td>
										<td style=\"word-break:break-all; word-wrap:break-word;text-align:center\">
											<a href=\"?m=info&p={$hash}\">$url</a>
										</td>
										<td style=\"text-align:center\">
											$user
										</td>
										<td style=\"text-align:center\">
											$responsive
										</td>
										<td style=\"text-align:center\">
											$pointserver
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=info&p={$hash}&c=high\"><font color=\"red\">$high</font></a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=info&p={$hash}&c=medium\"><font color=\"orange\">$medium</font></a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=info&p={$hash}&c=low\"><font color=\"green\">$low</font></a>
										</td>
										<td style=\"text-align:center\">
											$banner
										</td>
										<td style=\"text-align:center\">
											$os
										</td>
										<td style=\"text-align:center\">
											$finishtime
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=info&p={$hash}\">详情</a>|<a href=\"?m=edit&p={$hash}\">编辑</a>|<a href=\"javascript:del('{$hash}')\">删除</a>|<a href=\"javascript:exportexcel('{$hash}')\">报告</a>
										</td>
									</tr>\r\n";
            $i ++;
        }

        return "";
    }else{
        return "";

    }
}

function pro() {
    global $db;

    #$sql = "SELECT * FROM scan_list as a,target_info as b where a.hash = b.hash";
    $sql = "SELECT * FROM plugins order by id desc";
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $i;
            $name = $fs["name"];
            $describe = $fs["describe"];
            $type = $fs["type"];
            $dir = $fs["dir"];
            $other = $fs["other"];
            #$result = $fs["result"];
            $hash = '11';


            if ($type == '1'){
                $class = 'warning';
                $type = "通用插件";
            }else if ($type == '2'){
                $class = 'info';
                $type = "爬虫插件";
            }else if ($type == '3'){
                $class = 'error';
                $type = "POC插件";
            }else{
                $class = 'ing';
                $type = "其他插件";
            }

            $html_str .= "
									<tr class=\"$class\">
										<td style=\"text-align:center\">
											$id
										</td>
										<td style=\"text-align:center\">
											$name
										</td>
										<td>
											<div style=\"width: 450px;text-align:left;\">$describe</div>
										</td>
										<td style=\"text-align:center\">
											$type
										</td>
										<td style=\"text-align:center\">
											$dir
										</td>
										<td style=\"text-align:center\">
											<div style=\"width: 50px;\">$other</div>
										</td>
										
										<td style=\"text-align:center\">
											<a href=\"?m=info&p={$hash}\">详情</a>|<a href=\"?m=edit&p={$hash}\">编辑</a>|<a href=\"javascript:delpro('{$hash}')\">删除</a>
										</td>
									</tr>\r\n";
            $i ++;
        }

        return $html_str;
    }else{
        return "";

    }
}

function customer() {
    global $db;
    $action = $_GET['c'];

    if ($action == 'new'){
        //新添加
        #print_r($_POST);
        if(!empty($_POST['name'])){

            $in_arr['name'] = $_POST['name'];
            $in_arr['contact'] = $_POST['contact'];
            $in_arr['phone'] = $_POST['phone'];
            $in_arr['email'] = $_POST['email'];
            $in_arr['address'] = $_POST['address'];
            $in_arr['date1'] = $_POST['date1'];
            $in_arr['date2'] = $_POST['date2'];
            $in_arr['type'] = $_POST['type'];
            $in_arr['delay'] = $_POST['delay'];
            $in_arr['remark'] = $_POST['remark'];
            $in_arr['ctime'] = time();


            $insert = $db->insert_into("customer",$in_arr);
            echo "<script>alert('添加成功');location.href='?m=customer'</script>";

        }
    }else if ($action == 'update'){
        //更新
        //print_r($_POST);
        if(!empty($_POST['name'])){
            $in_arr['id'] = $_GET['id'];


            $in_arr['name'] = $_POST['name'];
            $in_arr['contact'] = $_POST['contact'];
            $in_arr['phone'] = $_POST['phone'];
            $in_arr['email'] = $_POST['email'];
            $in_arr['address'] = $_POST['address'];
            $in_arr['date1'] = $_POST['date1'];
            $in_arr['date2'] = $_POST['date2'];
            $in_arr['type'] = $_POST['type'];
            $in_arr['delay'] = $_POST['delay'];
            $in_arr['remark'] = $_POST['remark'];

            $update = $db->update("customer",$in_arr,"id='{$in_arr['id']}'");
            echo "<script>alert('更新成功');location.href='?m=customer'</script>";

        }
    }


    #$sql = "SELECT * FROM scan_list as a,target_info as b where a.hash = b.hash";
    $sql = "SELECT * FROM customer  order by id asc";
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $fs["0"];
            $name = $fs["1"];

            $sql1 = "select count(*) from scan_list where customer = $id";
            $results1 = $db->query($sql1);
            $fs1 = $db->fetch_array($results1);

            $sitenum = $fs1["0"];
            $contact = $fs["2"];
            $phone = $fs["3"];
            $email = $fs["4"];
            $address = $fs["5"];
            $date1 = $fs["6"];
            $date2 = $fs["7"];
            $type = $fs["8"];
            $delay = $fs["9"];
            $remark = $fs["10"];


            if ($delay == '1'){
                $class = 'success';
                $scan_delay = "每月一次";
            }else if ($delay == '2'){
                $class = 'warning';
                $scan_delay = "每季度一次";
            }else if ($delay == '3'){
                $class = 'error';
                $scan_delay = "每半年一次";
            }else if ($delay == '4'){
                $class = 'info';
                $scan_delay = "仅一次";
            }else{
                $class = 'info';
                $scan_delay = "仅一次";
            }

            if ($type == '1'){
                $scan_type = "定扫+预警+敏检";
            }else if ($type == '2'){
                $scan_type = "预警+敏检";
            }else if ($type == '3'){
                $scan_type = "定扫+预警";
            }else if ($type == '4'){
                $scan_type = "预警";
            }else{
                $scan_type = "定扫+预警+敏检";
            }
            $hash = "111";

            $html_str .= "
									<tr class=\"$class\">
										<td style=\"text-align:center\">
											$id
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=cusinfo&id={$id}\"> $name</a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=cusinfo&id={$id}\">$sitenum</a>
										</td>
										<td style=\"text-align:center\">
											$contact
										</td>
										<td style=\"text-align:center\">
											$phone
										</td>
										<td style=\"text-align:center\">
											$email
										</td>
										
										<td style=\"text-align:center\">
											$date1 -- $date2
										</td>
										
										<td style=\"text-align:center\">
											$scan_type
										</td>
										<td style=\"text-align:center\">
											$scan_delay
										</td>
										<td style=\"text-align:center\">
											$remark
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=cusinfo&id={$id}\">详情</a>|<a id=\"modal - 978241\" href=\"#$id\" role=\"button\"  data-toggle=\"modal\" >编辑</a>|<a href=\"javascript:delcustomer('{$id}')\">删除</a>
										</td>
									</tr>\r\n";
            $i ++;
        }

        return $html_str;
    }else{
        return "";

    }
}

function guestadd() {
    global $db;

    //print_r($_POST);

    if(!empty($_POST['url'])){

        $pointserver = specify_server();
        if (!empty($pointserver)){

            $in_arr['url'] = $_POST['url'];
            $in_arr['createtime'] = date('Y-m-d');
            $in_arr['user'] = $_SESSION['username'];//当前session用户
            $in_arr['pointserver'] = specify_server();//分配节点服务器ip
            $in_arr['group'] = "";//项目组名称
            $in_arr['siteuser'] = $_POST['user'];
            $in_arr['sitepwd'] = $_POST['pwd'];
            $in_arr['cookie'] = $_POST['cookie'];
            $in_arr['rule'] = $_POST['rule'];
            $in_arr['status'] = 'new';
            $in_arr['hash'] = md5($in_arr['url'].time().authkey);

            #if ( $_POST['auth'] == 'on' ) nginx_vhost( $in_arr['url'] , $in_arr['cookie'] );

            $insert = $db->insert_into("scan_list",$in_arr);

        }else{
            Message(" 请配置节点服务器 ","?m=point",0,3000);
        }
    }
}

function scan() {
	global $db;
	
	//print_r($_POST);
    $action = $_GET['c'];

    if ($action == 'new'){

	if(!empty($_POST['url'])){
		
		$pointserver = specify_server();
		if (!empty($pointserver)){
			$urls = str_replace(array("\r\n", "\r"), "\n", $_POST['url']);  

            $urls = explode("\n",$urls);
            foreach ($urls as $url){
                $in_arr['url'] = $url;
                $in_arr['createtime'] = date('Y-m-d');
                $in_arr['user'] = $_SESSION['username'];//当前session用户
                $in_arr['pointserver'] = specify_server();//分配节点服务器ip
                $in_arr['group'] = "";//项目组名称
                $in_arr['siteuser'] = $_POST['user'];
                $in_arr['sitepwd'] = $_POST['pwd'];
                $in_arr['cookie'] = $_POST['cookie'];
                $in_arr['rule'] = $_POST['rule'];
                $in_arr['status'] = 'new';
                $in_arr['customer'] = $_POST['customer'];
                $in_arr['delay'] = $_POST['delay'];
                $in_arr['hash'] = md5($in_arr['url'].time().authkey);

                #if ( $_POST['auth'] == 'on' ) nginx_vhost( $in_arr['url'] , $in_arr['cookie'] );

                $insert = $db->insert_into("scan_list",$in_arr);
            }

            echo "<script>alert('添加成功');location.href='?m=scan'</script>";



        }else{
            Message(" 请配置节点服务器 ","?m=point",0,3000);
        }}
	}else if ($action == 'update'){
        //更新
        //print_r($_POST);
        if(!empty($_POST['url'])){

        $in_arr['hash'] = $_GET['p'];

        $in_arr['url'] = $_POST['url'];
//        $in_arr['createtime'] = date('Y-m-d');
        $in_arr['user'] = $_SESSION['username'];//当前session用户
        $in_arr['pointserver'] = specify_server();//分配节点服务器ip
        $in_arr['group'] = "";//项目组名称
        $in_arr['siteuser'] = $_POST['user'];
        $in_arr['sitepwd'] = $_POST['pwd'];
        $in_arr['cookie'] = $_POST['cookie'];
        $in_arr['rule'] = $_POST['rule'];
//        $in_arr['status'] = 'new';
        $in_arr['customer'] = $_POST['customer'];
        $in_arr['delay'] = $_POST['delay'];


        #if ( $_POST['auth'] == 'on' ) nginx_vhost( $in_arr['url'] , $in_arr['cookie'] );

        $insert = $db->update("scan_list",$in_arr,"hash='{$in_arr['hash']}'");
        echo "<script>alert('更新成功');location.href='?m=scan'</script>";

        }}

    $sql_num = "SELECT id FROM scan_list";
    $totalnum = $db->db_num_rows($sql_num);
    $pagesize=100;
    //总共有几页
    $maxpage=ceil($totalnum/$pagesize);
    $page=isset($_GET['page'])?$_GET['page']:1;
    if($page <1)
    {
        $page=1;
    }
    if($page>$maxpage)
    {
        $page=$maxpage;
    }
    $limit=" limit ".($page-1)*$pagesize.",$pagesize";

    #$sql = "SELECT * FROM scan_list as a,target_info as b where a.hash = b.hash";
    $sql = "SELECT * FROM scan_list INNER JOIN  customer ON scan_list.customer = customer.id INNER JOIN info ON  scan_list.hash = info.hash order by scan_list.id DESC {$limit}";
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $i;
            $url = substr($fs["1"],0,32);
			$link_url = $fs["1"];
            $customer = $fs["name"];
            $title = mb_substr($fs["title"],0,10,"utf-8");
            $status = $fs["10"];
            $hash = $fs["11"];
            $finishtime = $fs["finishtime"];
            $finishtime = explode(',',$finishtime)[0];
            $banner = substr($fs["cms"],0,18);
            $os = substr(str_replace("Microsoft ","",$fs["os"]),0,13);
            $high = $fs["high"];
            $medium = $fs["medium"];
            $low =  $fs["low"];
            $delay = $fs["13"];


            if ($status == 'ok'){
                $class = 'success';
                $responsive = "已完成";
            }else if ($status == 'ing'){
                $class = 'warning';
                $responsive = "扫描中";
            }else if ($status == 'new'){
                $class = 'info';
                $responsive = "队列中";
            }else{
                $class = 'error';
            }

            if ($delay == '1'){
                $scan_delay = "每月一次";
            }else if ($delay == '2'){
                $scan_delay = "每季度一次";
            }else if ($delay == '3'){
                $scan_delay = "每半年一次";
            }else if ($delay == '4'){
                $scan_delay = "仅一次";
            }else{
                $scan_delay = "仅一次";
            }


            $html_str .= "
									<tr class=\"$class\">
										<td style=\"text-align:center\">
											$id
										</td>
										<td style=\"word-break:break-all; word-wrap:break-word;text-align:center\">
											<a href=\"$link_url\" target='_blank'>$url</a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=vul&p={$hash}\">$title</a>
										</td>
										<td style=\"text-align:center\">
											$customer
										</td>
										<td style=\"text-align:center\">
											$responsive
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=vul&p={$hash}&c=high\"><font color=\"red\">$high</font></a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=vul&p={$hash}&c=medium\"><font color=\"orange\">$medium</font></a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=vul&p={$hash}&c=low\"><font color=\"green\">$low</font></a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=siteinfo&p={$hash}\">$banner</a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=siteinfo&p={$hash}\">$os</a>
										</td>
										<td style=\"text-align:center\">
											$scan_delay
										</td>
										<td style=\"text-align:center\">
											$finishtime
										</td>
										<td style=\"text-align:center\">
											<a href=\"javascript:resetscan('{$hash}')\">重置</a>|<a id=\"modal - 978241\" href=\"#$hash\" role=\"button\"  data-toggle=\"modal\" >编辑</a>|<a href=\"javascript:del('{$hash}')\">删除</a>|<a href=\"javascript:exportreport('{$hash}')\">报告</a>
										</td>
									</tr>\r\n";
            $i ++;
        }
        $html_str =$html_str."<table class=\"table\" style=\"font-size:14px;\"><thead>
                                <tr><td style=\"text-align:center\"><b>当前{$page}/{$maxpage}页 &nbsp;&nbsp;&nbsp;共{$totalnum}个项目  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   <a href='?m=scan&page=1'>首页</a> &nbsp;&nbsp;&nbsp;
                   <a href='?m=scan&page=".($page-1)."'>上一页</a> &nbsp;&nbsp;&nbsp;
                   <a href='?m=scan&page=".($page+1)."'>下一页</a> &nbsp;&nbsp;&nbsp;
                   <a href='?m=scan&page={$maxpage}'>尾页</a></b>
                                       </td></tr></thead>\r\n";

        return $html_str;
    }else{
        return "";

    }
}

function info() {
    global $db;

    //print_r($_POST);
    $action = $_GET['c'];

    if ($action == 'new'){

        if(!empty($_POST['url'])){

//            $urls = $_POST['url'];
            $urls = explode("\n",$_POST['url']);
            foreach ($urls as $url){
//                $in_arr["url"] = $url;
//                echo $url;
                $in_arr['url'] = $url;
                $in_arr['createtime'] = date('Y-m-d');
                $in_arr['status'] = 'new';
                $in_arr['customer'] = $_POST['customer'];
                $in_arr['hash'] = md5($in_arr['url'].time().authkey);
                $insert = $db->insert_into("info",$in_arr);
            }

//            $in_arr['siteuser'] = $_POST['user'];
//            $in_arr['sitepwd'] = $_POST['pwd'];

            echo "<script>alert('添加成功');location.href='?m=info'</script>";

        }}else if ($action == 'update'){
        //更新
        //print_r($_POST);
        if(!empty($_POST['url'])){

            $in_arr['hash'] = $_GET['p'];
            $in_arr['customer'] = $_POST['customer'];
            $in_arr['title'] = $_POST['title'];
            $in_arr['ip'] = $_POST['ip'];
            $in_arr['cms'] = $_POST['cms'];
            $in_arr['waf'] = $_POST['waf'];
            $in_arr['os'] = $_POST['os'];
            $in_arr['language'] = $_POST['other'];
            $in_arr['middleware'] = $_POST['middleware'];

            $insert = $db->update("info",$in_arr,"hash='{$in_arr['hash']}'");
            echo "<script>alert('更新成功');location.href='?m=info'</script>";

        }}

    $sql_num = "SELECT id FROM info";
    $totalnum = $db->db_num_rows($sql_num);
    $pagesize=100;
    //总共有几页
    $maxpage=ceil($totalnum/$pagesize);
    $page=isset($_GET['page'])?$_GET['page']:1;
    if($page <1)
    {
        $page=1;
    }
    if($page>$maxpage)
    {
        $page=$maxpage;
    }
    $limit=" limit ".($page-1)*$pagesize.",$pagesize";

    #$sql = "SELECT * FROM scan_list as a,target_info as b where a.hash = b.hash";
    $sql = "SELECT * FROM info INNER JOIN  customer ON info.customer = customer.id order by info.id DESC {$limit}";
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $i;
            $cus_id = $fs["customer"];
            $url = substr($fs["1"],0,28);
			$link_url = $fs["1"];
            $hash = $fs["2"];
            $ip = $fs["ip"];
            $customer = $fs["name"];
            $port_num = $fs["port_num"];
            $sub_num = $fs["sub_num"];
            $cms = mb_substr($fs["cms"],0,8,"utf-8");
            $waf = substr($fs["waf"],0,8);
            $os = substr(str_replace("Microsoft ","",$fs["os"]),0,13);
            $language = substr($fs["language"],0,7);
            $middleware = substr($fs["middleware"],0,18);
            $weakfile_num = $fs["weakfile_num"];
            $status = $fs["status"];

            $title = mb_substr($fs["title"],0,10,"utf-8");

            if ($status == 'ok'){
                $class = 'success';
                $responsive = "已完成";
            }else if ($status == 'ing'){
                $class = 'warning';
                $responsive = "搜集中";
            }else if ($status == 'new'){
                $class = 'info';
                $responsive = "队列中";
            }else{
                $class = 'error';
                $responsive = "队列中";
            }


            $html_str .= "
									<tr class=\"$class\">
										<td style=\"text-align:center\">
											<div style=\"width: 10px;\">$id</div>
										</td>
										<td style=\"word-break:break-all; word-wrap:break-word;text-align:center\">
											<div style=\"width: 210px;\"><a href=$link_url target=\"_blank\">$url</a></div>
										</td>
									
										<td style=\"text-align:center\">
											<div style=\"width: 140px;\"><a href=\"?m=siteinfo&p={$hash}\">$title</a></div>
										</td>
										<td style=\"text-align:center\">
											<div style=\"width: 100px;\">$ip</div>
										</td>
										<td style=\"text-align:center\">
											<div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$port_num</a></div>
										</td>
										<td style=\"text-align:center\">
											<div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$sub_num</a></div>
										</td>
										<td style=\"text-align:center\">
											<div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$weakfile_num</a></div>
										</td>
										<td style=\"text-align:center\">
											<div style=\"width: 80px;\">$cms</div>
										</td>
										<td style=\"text-align:center\">
											<div style=\"width: 80px;\">$waf</div>
										</td>
										<td style=\"text-align:center\">
											<div style=\"width: 120px;\">$os</div>
										</td>
										
										<td style=\"text-align:center\">
											<div style=\"width: 140px;\">$middleware</div>
										</td>
										<td style=\"text-align:center\">
											<div style=\"width: 60px;\">$language</div>
										</td>
										
										<td style=\"text-align:center\">
											<a href=\"javascript:resetinfo('{$hash}')\">重置</a>|<a id=\"modal - 978241\" href=\"#$hash\" role=\"button\"  data-toggle=\"modal\" >编辑</a>|<a href=\"javascript:delinfo('{$hash}')\">删除</a>
										</td>
									</tr>\r\n";
            $i ++;
        }

        $html_str =$html_str."<table class=\"table\" style=\"font-size:14px;\"><thead>
                                <tr><td style=\"text-align:center\"><b>当前{$page}/{$maxpage}页 &nbsp;&nbsp;&nbsp;共{$totalnum}个项目  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   <a href='?m=info&page=1'>首页</a> &nbsp;&nbsp;&nbsp;
                   <a href='?m=info&page=".($page-1)."'>上一页</a> &nbsp;&nbsp;&nbsp;
                   <a href='?m=info&page=".($page+1)."'>下一页</a> &nbsp;&nbsp;&nbsp;
                   <a href='?m=info&page={$maxpage}'>尾页</a></b>
                                       </td></tr></thead>\r\n";

        return $html_str;
    }else{
        return "";

    }
}

function vul() {
    global $db;

    $action = $_GET['c'];
    $hash = $_GET['p'];

    if (empty($action)){
        $sql = "SELECT * FROM target_vul where hash='{$hash}' and severity='high' union all SELECT * FROM target_vul where hash='{$hash}' and severity='medium' union all SELECT * FROM target_vul where hash='{$hash}' and severity='low'  ";
    }else if ($action == 'high'){
        $sql = "SELECT * FROM target_vul where hash='{$hash}' and Severity='high' order by Severity";
    }else if ($action == 'medium'){
        $sql = "SELECT * FROM target_vul where hash='{$hash}' and Severity='medium' order by Severity";
    }else if ($action == 'low'){
        $sql = "SELECT * FROM target_vul where hash='{$hash}' and Severity='low' order by Severity";
    }

    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $i;
            $Affects = $fs["affects"];
            $Parameter = $fs["parameter"];
            $Severity = $fs["severity"];
            $details = $fs["details"];
            $Request = str_replace("\n",'<br>',$fs["request"]);
            $vul_cn_id = $fs["vul_cn_id"];
            $Name = get_vul_cn_name($vul_cn_id);
            if ($Name == ''){
                $Name = $fs["name"];
            }
            //$Response = str_replace("\n",'<br>',$fs["response"]);

            if (strtolower($Severity) == 'high'){
                $Severity = '高危';
                $class = 'error';
            }else if(strtolower($Severity) == 'medium'){
                $Severity = '中危';
                $class = 'warning';
            }else if(strtolower($Severity) == 'low' or strtolower($Severity) == 'info'){
                $Severity = '低危';
                $class = 'info';
            }

            if ($Parameter == 'Array'){
                $Parameter = '';
            }

            if ($Request == 'Array'){
                $Request = '';
            }


            $html_str .= "
									<tr class=\"$class\">
										<td style=\"text-align:center\">
											<div style=\"width: 20px;\">$id</div>
										</td>
										<td style=\"text-align:center\">
											<div style=\"width:130px;word-break: break-all; word-wrap:break-word;\">$Name</div>
										</td>
										<td style=\"text-align:center\">
											<div style=\"width: 30px;\">$Severity</div>
										</td>
										<td style=\"text-align:center\">
											<div style=\"width: 150px;word-break: break-all; word-wrap:break-word;\">$Affects</div>
										</td>
										<td style=\"text-align:center\">
											<div style=\"width: 80px;word-break: break-all; word-wrap:break-word;\">$Parameter</div>
										</td>
										<td>
											<div style=\"width: 400px;word-break: break-all; word-wrap:break-word;\">$details</div>
										</td>
										<td>
											<div style=\"width:400px;word-break: break-all; word-wrap:break-word;\">$Request</div> 
										</td>
									</tr>\r\n";
            $i ++;
        }

        return $html_str;
    }else{
        return "";
    }
}

function siteinfo() {
    global $db;

    //print_r($_POST);
    $hash = $_GET['p'];

    #$sql = "SELECT * FROM scan_list as a,target_info as b where a.hash = b.hash";
    $sql = "SELECT * FROM info where hash = '$hash'";
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;

        while ($fs = $db->fetch_array($results))
        {


            $url = $fs['url'];
            $ip = $fs['ip'];
            $port_num = $fs['port_num'];
            $port = $fs['port'];
            $sub_num = $fs['sub_num'];
            $sub = $fs['sub'];
            $cms = $fs['cms'];
            $waf = $fs['waf'];
            $os = $fs['os'];
            $os_info = $fs['os_info'];
            $whatweb_info = $fs['whatweb_info'];
            $language = $fs['language'];
            $middleware = $fs['middleware'];
            $weakfile_num = $fs['weakfile_num'];
            $weakfile = $fs['weakfile'];
            $title = $fs['title'];
            $status = $fs['status'];


            if ($status == 'ok'){
                $class = 'success';
                $responsive = "已完成";
            }else if ($status == 'ing'){
                $class = 'error';
                $responsive = "扫描中";
            }else if ($status == 'new'){
                $class = 'info';
                $responsive = "队列中";
            }else{
                $class = 'warning';
            }



            $html_str .= "
									<tr class=\"success\">
									
										<td style=\"word-break:break-all; word-wrap:break-word;\">
											<br><b>网站URL：</b>$url<br><br>
											<b>网站标题：</b>$title<br><br>
											<b>IP地址：</b>$ip<br><br>
										</td>
									</tr>
									
									<tr class=\"info\">

										<td>
											<br><b>操作系统：</b><br>$os_info<br><br>
											<b>开发语言：</b>$language<br><br>
											<b>WAF：</b>$waf<br><br>
											<b>CMS：</b>$cms<br><br>							
											<b>中间件：</b>$middleware<br><br>											
										</td>
									</tr>
									
								    <tr class=\"error\">

										<td>
											<br><b>端口开放：</b><br>$port<br>
											<b>子域名信息：</b><br>$sub<br>
											<b>敏感信息：</b><br>$weakfile<br>
											<b>Web信息：</b><br>$whatweb_info<br><br>
										</td>
									</tr>\r\n";
            $i ++;
        }

        return $html_str;
    }else{
        return "";

    }
}

function spider_search(){
    global $db;

    $hashs = $_GET['hash'];
    $key = $_GET['key'];
    $hash = '';
    $a = strlen($hashs)/32;
    while ($a >= 1){
        $x = substr($hashs,($a-1)*32,32);
        $a = $a -1;
        $hash = $hash.$x."','";
    }
    $hash = "'".substr($hash,0,-2);

    $sql = "SELECT * FROM spider  INNER JOIN info ON info.hash = spider.hash where spider.hash in ($hash) ";
//    echo $sql;
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;

        while ($fs = $db->fetch_array($results))
        {

            $url = $fs['1'];
            $ip = $fs['ip'];

            $title = $fs['title'];
            $status = $fs[4];
            $evil_page = $fs['evil_page'];
            $evil_num = $fs['evil_num'];
            $bad_page = $fs['bad_page'];
            $bad_num = $fs['bad_num'];
            $key_page = $fs['key_page'];
            $key_num = $fs['key_num'];
            $url_num = $fs['url_num'];
            $act_num = $fs['act_num'];
            $snap_num = $fs['snap_num'];
            $snap_file = $fs['snap_file'];
            $url_all = $fs['url_all'];
            $act_all = $fs['act_all'];
            $url_keys = explode('<br>',$url_all);
            $url_key_all = '';
            foreach ($url_keys as $url_key){
                if (strstr($url_key,$key)){
//                    print $url_key.'<br>';
                    $url_key_all = $url_key_all.$url_key.'<br>';
                }
            }

            $html_str .= "
									<tr class=\"success\">
									
										<td style=\"word-break:break-all; word-wrap:break-word;\">
											<br><b>网站URL：</b>$url<br><br>
											<b>网站标题：</b>$title<br><br>
											<b>IP地址：</b>$ip<br><br>
											<b>页面统计：</b><br><br>
											<b>URL总数：</b>$url_num&emsp;&emsp;
											<b>动态URL：</b>$act_num&emsp;&emsp;
											<b>暗链页面：</b>$evil_num&emsp;&emsp;							
											<b>敏感字页面：</b>$key_num&emsp;&emsp;
											<b>坏链页面：</b>$bad_num&emsp;&emsp;
											<b>快照页面：</b>$snap_num&emsp;&emsp;<br><br>	
										</td>
									</tr>
									<tr class=\"error\">
										<td>
											<br><b>检索到的URL：</b><br><br>
											$url_key_all<br><br>
										</td>
									</tr>\r\n";
            $i ++;
        }
        return $html_str;
    }else{
        return "";
    }
}

function spiderinfo() {
    global $db;

    //print_r($_POST);
    $hash = $_GET['p'];

    #$sql = "SELECT * FROM scan_list as a,target_info as b where a.hash = b.hash";
    $sql = "SELECT * FROM spider  INNER JOIN info ON info.hash = spider.hash where spider.hash = '$hash'";
//    echo $sql;
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;

        while ($fs = $db->fetch_array($results))
        {

            $url = $fs['1'];
            $ip = $fs['ip'];

            $title = $fs['title'];
            $status = $fs[4];
            $evil_page = $fs['evil_page'];
            $evil_num = $fs['evil_num'];
            $bad_page = $fs['bad_page'];
            $bad_num = $fs['bad_num'];
            $key_page = $fs['key_page'];
            $key_num = $fs['key_num'];
            $url_num = $fs['url_num'];
            $act_num = $fs['act_num'];
            $snap_num = $fs['snap_num'];
//            $snap_file = $fs['snap_file'];
            $url_all = $fs['url_all'];
            $act_all = $fs['act_all'];

            global $logspiderdir;
//            echo $logspiderdir;
            $dir=$logspiderdir.$hash;
            $webdir = "./TaskPython/logspider/".$hash;
            $snap_file ='';
            $file=array_slice(scandir($dir),2);
            foreach ($file as $f){
                $snap_url =  $webdir.'/'.$f;
                $snap_file .= "<a href=\"$snap_url\" target='_blank'>$snap_url</a><br>";
            }


            if ($status == 'ok'){
                $class = 'success';
                $responsive = "已完成";
            }else if ($status == 'ing'){
                $class = 'error';
                $responsive = "扫描中";
            }else if ($status == 'new'){
                $class = 'info';
                $responsive = "队列中";
            }else{
                $class = 'warning';
            }



            $html_str .= "
									<tr class=\"success\">
									
										<td style=\"word-break:break-all; word-wrap:break-word;\">
											<br><b>网站URL：</b>$url<br><br>
											<b>网站标题：</b>$title<br><br>
											<b>IP地址：</b>$ip<br><br>
										</td>
									</tr>
									
									<tr class=\"info\">

										<td>
											<br><b>页面统计：</b><br><br>
											<b>URL总数：</b>$url_num&emsp;&emsp;
											<b>动态URL：</b>$act_num&emsp;&emsp;
											<b>暗链页面：</b>$evil_num&emsp;&emsp;							
											<b>敏感字页面：</b>$key_num&emsp;&emsp;
											<b>坏链页面：</b>$bad_num&emsp;&emsp;
											<b>快照页面：</b>$snap_num&emsp;&emsp;<br><br>	

										</td>
									</tr>
									
								    <tr class=\"error\">

										<td>
											<br><b>暗链页面：</b><br><br>
											$evil_page<br>
										</td>
									</tr>
									
									<tr class=\"warning\">

										<td>
											<br><b>敏感字页面：</b><br><br>
											$key_page<br>
										</td>
									</tr>
									
									<tr class=\"info\">

										<td>
											<br><b>坏链页面：</b><br><br>
											$bad_page<br>
										</td>
									</tr>
									<tr class=\"success\">

										<td>
											<br><b>快照文件：</b><br><br>
										
											$snap_file
											<br><br>
										</td>
									</tr>
									<tr class=\"warning\">

										<td>
											<br><b>所有URL：</b><br><br>
											$url_all<br><br>
										</td>
									</tr>
									<tr class=\"error\">

										<td>
											<br><b>所有动态URL：</b><br><br>
											$act_all<br><br>
										</td>
									</tr>\r\n";
            $i ++;
        }

        return $html_str;
    }else{
        return "";

    }
}

function cusinfo() {
    global $db;

    //print_r($_POST);
    $id = $_GET['id'];

    #$sql = "SELECT * FROM scan_list INNER JOIN target_info ON scan_list.hash = target_info.hash  INNER JOIN  customer ON scan_list.customer = customer.id INNER JOIN info ON  scan_list.hash = info.hash  where scan_list.customer = $id order by scan_list.createtime desc";

    $sql = "SELECT * FROM info INNER JOIN  customer ON info.customer = customer.id  INNER JOIN  spider ON spider.hash = info.hash  where info.customer = $id order by info.createtime desc";
//    echo $sql;
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $i;
            $cus_id = $fs["customer"];
            $url = substr($fs["1"],0,32);
            $link_url = $fs["1"];
            $hash = $fs["2"];
            $ip = $fs["ip"];
            $customer = $fs["name"];
            $port_num = $fs["port_num"];
            $sub_num = $fs["sub_num"];
            $cms = mb_substr($fs["cms"],0,8,"utf-8");
            $waf = substr($fs["waf"],0,8);
            $os = substr(str_replace("Microsoft ","",$fs["os"]),0,13);
            $language = substr($fs["language"],0,12);
            $middleware = substr($fs["middleware"],0,18);
            $weakfile_num = $fs["weakfile_num"];
            $high = get_severity($hash,'high');
            $medium = get_severity($hash,'medium');
            $low = get_severity($hash,'low');

            $key_num = $fs["key_num"];
            $bad_num = $fs["bad_num"];
            $evil_num = $fs["evil_num"];

            $title = mb_substr($fs["title"],0,10,"utf-8");
            $class = 'success';

            $html_str .= "
                                    <tr class=\"$class\">
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 10px;\">$id</div>
                                        </td>
                                        <td style=\"word-break:break-all; word-wrap:break-word;text-align:center\">
                                            <div style=\"width: 210px;\"><a href=$link_url target=\"_blank\">$url</a></div>
                                        </td>
                                    
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 140px;\"><a href=\"?m=siteinfo&p={$hash}\">$title</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=vul&p={$hash}&c=high\"><font color=\"red\">$high</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=vul&p={$hash}&c=medium\"><font color=\"orange\">$medium</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=vul&p={$hash}&c=low\"><font color=\"green\">$low</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$port_num</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$sub_num</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 30px;\"><a href=\"?m=siteinfo&p={$hash}\">$weakfile_num</a></div>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=spiderinfo&p={$hash}\"><font color=\"red\">$evil_num</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=spiderinfo&p={$hash}\"><font color=\"orange\">$key_num</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=spiderinfo&p={$hash}\"><font color=\"green\">$bad_num</font></a>
                                        </td>
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 80px;\">$cms</div>
                                        </td>
                                        
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 120px;\">$os</div>
                                        </td>
                                        
                                        <td style=\"text-align:center\">
                                            <div style=\"width: 140px;\">$middleware</div>
                                        </td>
                                                                                
                                        <td style=\"text-align:center\">
                                            <a href=\"?m=siteinfo&p={$hash}\">详情</a>|<a href=\"javascript:delinfo('{$hash}')\">删除</a>|<a href=\"javascript:exportreport('{$hash}')\">报告</a>
                                        </td>
                                    </tr>\r\n";
            $i ++;
        }

        return $html_str;
    }else{
        return "";

    }
}


function spider() {
    global $db;

    //print_r($_POST);
    $action = $_GET['c'];

    if ($action == 'new'){

        if(!empty($_POST['url'])){

            $urls = explode("\n",$_POST['url']);
            foreach ($urls as $url) {

                $in_arr['url'] = $url;
                $in_arr['createtime'] = date('Y-m-d');
                $in_arr['siteuser'] = $_POST['user'];
                $in_arr['sitepwd'] = $_POST['pwd'];
                $in_arr['status'] = 'new';
                $in_arr['check_status'] = 'new';
                $in_arr['customer'] = $_POST['customer'];
                $in_arr['delay'] = $_POST['delay'];
                $in_arr['hash'] = md5($in_arr['url'] . time() . authkey);

                $insert = $db->insert_into("spider", $in_arr);

                $in_scan_arr['url'] = $url;
                $in_scan_arr['createtime'] = date('Y-m-d');
                $in_scan_arr['user'] = $_SESSION['username'];//当前session用户
                $in_scan_arr['pointserver'] = specify_server();//分配节点服务器ip
                $in_scan_arr['group'] = "";//项目组名称
                $in_scan_arr['siteuser'] = $_POST['user'];
                $in_scan_arr['sitepwd'] = $_POST['pwd'];
                $in_scan_arr['rule'] = '4';
                $in_scan_arr['status'] = 'new';
                $in_scan_arr['customer'] = $_POST['customer'];
                $in_scan_arr['delay'] = $_POST['delay'];

                $in_scan_arr['hash'] = $in_arr['hash'];

                $insert = $db->insert_into("scan_list",$in_scan_arr);

                $in_arr_info['url'] = $url;
                $in_arr_info['createtime'] = date('Y-m-d');
                $in_arr_info['status'] = 'new';
                $in_arr_info['customer'] = $_POST['customer'];
                $in_arr_info['hash'] = $in_arr['hash'];

                $insert = $db->insert_into("info",$in_arr_info);

            }
                echo "<script>alert('添加成功');location.href='?m=spider'</script>";

        }
    }else if ($action == 'update'){
        //更新
        //print_r($_POST);
        if(!empty($_POST['url'])){

            $in_arr['hash'] = $_GET['p'];

            $in_arr['url'] = $_POST['url'];
//        $in_arr['createtime'] = date('Y-m-d');
            $in_arr['user'] = $_SESSION['username'];//当前session用户
            $in_arr['pointserver'] = specify_server();//分配节点服务器ip
            $in_arr['group'] = "";//项目组名称
            $in_arr['siteuser'] = $_POST['user'];
            $in_arr['sitepwd'] = $_POST['pwd'];
            $in_arr['cookie'] = $_POST['cookie'];
            $in_arr['rule'] = $_POST['rule'];
//        $in_arr['status'] = 'new';
//        $in_arr['customer'] = $_POST['customer'];
            $in_arr['delay'] = $_POST['delay'];


            #if ( $_POST['auth'] == 'on' ) nginx_vhost( $in_arr['url'] , $in_arr['cookie'] );

            $insert = $db->update("scan_list",$in_arr,"hash='{$in_arr['hash']}'");
            echo "<script>alert('更新成功');location.href='?m=scan'</script>";

        }}

    $sql_num = "SELECT id FROM spider";
    $totalnum = $db->db_num_rows($sql_num);
    $pagesize=100;
    //总共有几页
    $maxpage=ceil($totalnum/$pagesize);
    $page=isset($_GET['page'])?$_GET['page']:1;
    if($page <1)
    {
        $page=1;
    }
    if($page>$maxpage)
    {
        $page=$maxpage;
    }
    $limit=" limit ".($page-1)*$pagesize.",$pagesize";

    #$sql = "SELECT * FROM scan_list as a,target_info as b where a.hash = b.hash";
    $sql = "SELECT * FROM spider INNER JOIN  customer ON spider.customer = customer.id INNER JOIN  info ON spider.hash = info.hash order by spider.id DESC {$limit}";
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results))
        {
            $id = $i;
            $url = substr($fs["1"],0,32);
			$link_url = $fs["1"];
            $customer = $fs["name"];
            $status = $fs["check_status"];
            $hash = $fs["3"];
            $url_num= $fs["url_num"];
            $act_num = $fs["act_num"];
            $key_num = $fs["key_num"];
            $bad_num = $fs["bad_num"];
            $snap_num = $fs["snap_num"];
            $evil_num = $fs["evil_num"];
            $delay = $fs["12"];
            $finishtime = $fs["10"];
            $title = mb_substr($fs["title"],0,8,"utf-8");

            if ($status == 'ok'){
                $class = 'success';
                $responsive = "已完成";
            }else if ($status == 'ing'){
                $class = 'warning';
                $responsive = "爬取中";
            }else if ($status == 'new'){
                $class = 'info';
                $responsive = "队列中";
            }else{
                $class = 'error';
                $responsive = "队列中";
            }


            if ($delay == '1'){
                $scan_delay = "每月一次";
            }else if ($delay == '2'){
                $scan_delay = "每季度一次";
            }else if ($delay == '3'){
                $scan_delay = "每半年一次";
            }else if ($delay == '4'){
                $scan_delay = "仅一次";
            }else{
                $scan_delay = "仅一次";
            }


            $html_str .= "
									<tr class=\"$class\">
										<td  style=\"text-align:center\">
											$id
										</td>
										<td style=\"word-break:break-all; word-wrap:break-word;text-align:center\">
											<a href=$link_url target=\"_blank\">$url</a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=spiderinfo&p={$hash}\">$title</a>
										</td>
										<td style=\"text-align:center\">
											$customer
										</td>
										<td style=\"text-align:center\">
											$responsive
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=spiderinfo&p={$hash}\">$url_num</a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=spiderinfo&p={$hash}\">$act_num</a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=spiderinfo&p={$hash}\"><font color=\"red\">$evil_num</font></a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=spiderinfo&p={$hash}\"><font color=\"orange\">$key_num</font></a>
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=spiderinfo&p={$hash}\"><font color=\"green\">$bad_num</font></a>
										</td>
										<td style=\"text-align:center\">
											$snap_num
										</td>
										
										<td style=\"text-align:center\">
											$scan_delay
										</td>
										<td style=\"text-align:center\">
											$finishtime
										</td>
										<td style=\"text-align:center\">
											<a href=\"?m=spiderinfo&p={$hash}\">详情</a>|<a href=\"javascript:resetspider('{$hash}')\">重置</a>|<a href=\"javascript:delspider('{$hash}')\">删除</a>
										</td>
									</tr>\r\n";
            $i ++;
        }

        $html_str =$html_str."<table class=\"table\" style=\"font-size:14px;\"><thead>
                                <tr><td style=\"text-align:center\"><b>当前{$page}/{$maxpage}页 &nbsp;&nbsp;&nbsp;共{$totalnum}个项目  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   <a href='?m=spider&page=1'>首页</a> &nbsp;&nbsp;&nbsp;
                   <a href='?m=spider&page=".($page-1)."'>上一页</a> &nbsp;&nbsp;&nbsp;
                   <a href='?m=spider&page=".($page+1)."'>下一页</a> &nbsp;&nbsp;&nbsp;
                   <a href='?m=spider&page={$maxpage}'>尾页</a></b>
                                       </td></tr></thead>\r\n";

        return $html_str;
    }else{
        return "";

    }
}

function editscan() {
    global $db;

    $hash = $_GET['p'];

    if (!empty($hash)){
        $sql = "SELECT * FROM scan_list where hash='{$hash}'";

        $results = $db->fetch_assoc($sql);

        return $results;
    }

}

function editcustomer() {
    global $db;

    $id = $_GET['id'];

    if (!empty($hash)){
        $sql = "SELECT * FROM customer where id='{$id}'";

        $results = $db->fetch_assoc($sql);

        return $results;
    }

}

function point() {
	global $db;
	
	$action = $_GET['c'];
	
	if ($action == 'new'){
		//新添加
		//print_r($_POST);
		if(!empty($_POST['ip'])){
			
			$in_arr['pointip'] = $_POST['ip'];
			$in_arr['pointport'] = $_POST['port'];
			$in_arr['status'] = $_POST['status'];
			$in_arr['hash'] = md5($in_arr['pointip'].$in_arr['pointport']);
			
			$insert = $db->insert_into("point_server",$in_arr);
		}
	}else if ($action == 'update'){
		//更新
		//print_r($_POST);
		$key = $_GET['p'];
		if(!empty($_POST['ip']) and !empty($key)){
			
			$in_arr['pointip'] = $_POST['ip'];
			$in_arr['pointport'] = $_POST['port'];
			$in_arr['status'] = $_POST['status'];
			
			$update = $db->update("point_server",$in_arr,"hash='{$key}'");
		}
	}
	
	$sql = "SELECT * FROM point_server";
	
	$results = $db->query($sql);
	if (mysql_num_rows($results) > 0){
		$i = 1;
		while ($fs = $db->fetch_array($results))
		{
			$id = $i;
			$ip = $fs["pointip"];
			$port = $fs["pointport"];
			$level = $fs["level"];
			$status = $fs["status"];
			$hash = $fs["hash"];
			
			if ($status == '1'){
				$class = 'success';
				$status = '启用';
			}else{
				$class = 'warning';
				$status = '禁用';
			}
			
			$html_str .= "
									<tr class=\"$class\">
										<td style=\"text-align:center\">
											$id
										</td>
										<td style=\"text-align:center\">
											$ip
										</td>
										<td style=\"text-align:center\">
											$port
										</td>
										<td style=\"text-align:center\">
											$level
										</td>
										<td style=\"text-align:center\">
											$status
										</td>
										<td style=\"text-align:center\">
											<a id=\"modal-978241\" href=\"#$hash\" role=\"button\" class=\"btn\" data-toggle=\"modal\">修改</a>
										</td>
										
									</tr>\r\n";
			$i ++;
		}
		
		return $html_str;
	}else{
		return "";
	}

}


function set() {
	global $db;
	
	$action = $_GET['c'];
	
	if ($action == 'new'){
		//新添加
		//print_r($_POST);
		if(!empty($_POST['username']) and !empty($_POST['passwd'])){
			
			$in_arr['username'] = $_POST['username'];
			$in_arr['passwd'] = $_POST['passwd'];
			$in_arr['phone'] = $_POST['phone'];
			$in_arr['email'] = $_POST['mail'];
			$in_arr['status'] = $_POST['status'];
			$in_arr['ctime'] = time();
			
			$insert = $db->insert_into("user",$in_arr);
		}
	}else if ($action == 'update'){
		//更新
		//print_r($_POST);
		if(!empty($_POST['username'])){
			
			$in_arr['username'] = $_POST['username'];
			//$in_arr['passwd'] = $_POST['passwd'];
			$in_arr['phone'] = $_POST['phone'];
			$in_arr['email'] = $_POST['mail'];
			$in_arr['status'] = $_POST['status'];
			
			$update = $db->update("user",$in_arr,"username='{$in_arr['username']}'");
            echo "<script>alert('更新成功');location.href='?m=set'</script>";

        }
	}
	
	$sql = "SELECT * FROM user";
	
	$results = $db->query($sql);
	if (mysql_num_rows($results) > 0){
		$i = 1;
		while ($fs = $db->fetch_array($results))
		{
			$id = $i;
			$username = $fs["username"];
			$email = $fs["email"];
			$phone = $fs["phone"];
			$status = $fs["status"];
			$hash = md5($username);
			
			if ($status == '1'){
				$class = 'success';
				$status = '启用';
			}else{
				$class = 'warning';
				$status = '禁用';
			}
			
			$html_str .= "
									<tr class=\"$class\">
										<td style=\"text-align:center\">
											$id
										</td>
										<td style=\"text-align:center\">
											$username
										</td>
										<td style=\"text-align:center\">
											$email
										</td>
										<td style=\"text-align:center\">
											$phone
										</td>
										<td style=\"text-align:center\">
											$status
										</td>
										<td style=\"text-align:center\">
											<a id=\"modal-978241\" href=\"#$hash\" role=\"button\" class=\"btn\" data-toggle=\"modal\">修改</a>
										</td>
									</tr>\r\n";
			$i ++;
		}
		
		return $html_str;
	}else{
		return "";
	}

}


function login() {
	global $db;
	
	$username = $_POST['username'];
	$password = $_POST['password'];
    $remember = $_POST['remember'];


    if($remember == 1){
        setcookie('user',$username,time()+3600*24);
        setcookie('pass',$password,time()+3600*24);
        setcookie('remember',$remember,time()+3600*24);
    }else{
        setcookie('user',$username,time()-3600*24);
        setcookie('pass',$password,time()-3600*24);
        setcookie('remember',$remember,time()-3600*24);
    }

	
	//print_r($_POST);
	
	if (!empty($username) and !empty($password)){
		$sql = "SELECT * FROM `user` where username='{$username}' and passwd='{$password}'";
		
		$results = $db->fetch_assoc($sql);
		$rows = $db->db_num_rows($sql);
		if ($rows > 0 and $results['status'] == 1){
			$_SESSION['username'] = $results['username'];
			$_SESSION['r_ip'] = $_SERVER['REMOTE_ADDR'];

			
			$up_arr['lasttime'] = time();
			$update = $db->update("user",$up_arr,"username='{$username}'");
			
			Message(" $username 登录成功! 正在跳转... ","?m=index",0,500);
		}else if ($rows > 0 and $results['status'] == 0){
			Message(" 账号被禁用，请联系管理员 ","?m=login",0,3000);
		}
	}

}

function logout() {
	unset($_SESSION['username']);
	header("Location: ?m=login");
}

?>