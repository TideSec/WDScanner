<?php

function report(){
    global $db;

    //print_r($_POST);
    $hash = $_GET['hash'];
}
function report_url(){
    global $db;
    $hash = $_GET['hash'];
    $sql = "select url from scan_list where hash ='$hash'";
//            echo $sql1;
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        while ($fs = $db->fetch_array($results))
        {
            return $fs[0];
        }
    }else{
        return $hash;
    }
}

function report_title(){
    global $db;
    $hash = $_GET['hash'];
    $sql = "select title from info where hash ='$hash'";
//            echo $sql1;
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        while ($fs = $db->fetch_array($results))
        {
            return $fs[0];
        }
    }else{
        return $hash;
    }
}

function report_ip(){
    global $db;
    $hash = $_GET['hash'];
    $sql = "select ip from info where hash ='$hash'";
//            echo $sql1;
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        while ($fs = $db->fetch_array($results))
        {
            return $fs[0];
        }
    }else{
        return $hash;
    }
}

function report_general(){
    global $db;
    $hash = $_GET['hash'];
    $port_num = '0';
    $sub_num = '0';
    $weakfile_num = '0';
    $high = '0';
    $medium = '0';
    $low = '0';
    $key_num = '0';
    $bad_num = '0';
    $evil_num = '0';
    $url_num = '0';
    $act_num = '0';
    $snap_num = '0';

    $sql = "SELECT * FROM info LEFT JOIN  spider ON spider.hash = info.hash where info.hash ='$hash' ";
//    echo $sql;
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;
        while ($fs = $db->fetch_array($results)) {
            $id = $i;
            $url = substr($fs["1"], 0, 35);
            $hash = $fs["2"];
            $ip = $fs["ip"];
            $port_num = $fs["port_num"];
            $sub_num = $fs["sub_num"];
            $cms = mb_substr($fs["cms"], 0, 9, "utf-8");
            $waf = substr($fs["waf"], 0, 8);
            $os = substr($fs["os"], 0, 13);
            $language = substr($fs["language"], 0, 12);
            $middleware = substr($fs["middleware"], 0, 18);
            $weakfile_num = $fs["weakfile_num"];
            $high = get_severity($hash, 'high');
            $medium = get_severity($hash, 'medium');
            $low = get_severity($hash, 'low');

            $key_num = $fs["key_num"];
            $bad_num = $fs["bad_num"];
            $evil_num = $fs["evil_num"];
            $url_num = $fs["url_num"];
            $act_num = $fs["act_num"];
            $snap_num = $fs["snap_num"];

        }}


    $html = "<tr style='mso-yfti-irow:1;height:59.2pt'>
            <td width=601 valign=top style='width:450.75pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:59.2pt'>
                <p class=MsoNormal align=left style='margin-top:6.0pt;text-align:left;
  line-height:150%;mso-pagination:none;text-autospace:none'><b
                        style='mso-bidi-font-weight:normal'><span lang=EN-US style='font-size:12.0pt;
  line-height:150%;font-family:宋体'>WEB</span></b><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;line-height:150%;font-family:宋体'>安全扫描结果：<span
                        lang=EN-US><o:p></o:p></span></span></b></p>
                <p class=MsoNormal align=left style='margin-top:6.0pt;text-align:left;
  line-height:150%;mso-pagination:none;text-autospace:none'><span
                        style='font-size:12.0pt;line-height:150%;font-family:宋体;color:red'>高危漏洞：<span
                        lang=EN-US>".$high. "</span>个</span><span lang=EN-US style='font-size:12.0pt;
  line-height:150%;font-family:宋体'><span style='mso-tab-count:2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><span
                        style='font-size:12.0pt;line-height:150%;font-family:宋体;color:#FFC000'>中危漏洞：<span
                        lang=EN-US>".$medium." </span>个</span><span lang=EN-US style='font-size:12.0pt;
  line-height:150%;font-family:宋体'><span style='mso-tab-count:2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><span
                        style='font-size:12.0pt;line-height:150%;font-family:宋体;color:#00B050'>低危漏洞：<span
                        lang=EN-US> ".$low." </span>个</span><b style='mso-bidi-font-weight:normal'><span
                        lang=EN-US style='font-size:12.0pt;line-height:150%;font-family:宋体'><o:p></o:p></span></b></p>
            </td>
        </tr>
        <tr style='mso-yfti-irow:2;height:89.05pt'>
            <td width=601 valign=top style='width:450.75pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:89.05pt'>
                <p class=MsoNormal align=left style='margin-top:6.0pt;text-align:left;
  line-height:150%;mso-pagination:none;text-autospace:none'><b
                        style='mso-bidi-font-weight:normal'><span style='font-size:12.0pt;line-height:
  150%;font-family:宋体'>页面安全检测结果：<span lang=EN-US><o:p></o:p></span></span></b></p>
                <p class=MsoNormal align=left style='margin-top:6.0pt;text-align:left;
  line-height:150%;mso-pagination:none;text-autospace:none'><span lang=EN-US
                                                                  style='font-size:12.0pt;line-height:150%;font-family:宋体'>URL</span><span
                        style='font-size:12.0pt;line-height:150%;font-family:宋体'>总数：<span lang=EN-US>".$url_num."<span
                        style='mso-spacerun:yes'>&nbsp;&nbsp;&nbsp; </span></span>动态<span lang=EN-US>URL</span>：<span
                        lang=EN-US>".$act_num."</span>&nbsp; &nbsp;  <span lang=EN-US><o:p></o:p></span></span></p>
                <p class=MsoNormal align=left style='margin-top:6.0pt;text-align:left;
  line-height:150%;mso-pagination:none;text-autospace:none'><span
                        style='font-size:12.0pt;line-height:150%;font-family:宋体;color:red'>暗链页面：<span
                        lang=EN-US>".$evil_num."</span></span><span style='font-size:12.0pt;line-height:150%;
  font-family:宋体'>&nbsp; &nbsp; <span style='color:#FFC000'>敏感字页面：<span lang=EN-US>".$key_num."</span></span>&nbsp; &nbsp; <span
                        style='color:#00B050'> 坏链页面：<span lang=EN-US>".$bad_num."</span></span>&nbsp; &nbsp; 快照页面：<span
                        lang=EN-US>".$snap_num."<b style='mso-bidi-font-weight:normal'><o:p></o:p></b></span></span></p>
            </td>
        </tr>
        <tr style='mso-yfti-irow:3;mso-yfti-lastrow:yes;height:62.35pt'>
            <td width=601 valign=top style='width:450.75pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0cm 5.4pt 0cm 5.4pt;height:62.35pt'>
                <p class=MsoNormal align=left style='margin-top:6.0pt;text-align:left;
  line-height:150%;mso-pagination:none;text-autospace:none'><b
                        style='mso-bidi-font-weight:normal'><span style='font-size:12.0pt;line-height:
  150%;font-family:宋体'>信息泄露测试结果：<span lang=EN-US><o:p></o:p></span></span></b></p>
                <p class=MsoNormal align=left style='margin-top:6.0pt;text-align:left;
  line-height:150%;mso-pagination:none;text-autospace:none'><span
                        style='font-size:12.0pt;line-height:150%;font-family:宋体'>开放端口：<span
                        lang=EN-US>".$port_num." </span>个<span lang=EN-US><span style='mso-tab-count:2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span>子域名：<span
                        lang=EN-US>".$sub_num." </span>个<span lang=EN-US><span style='mso-tab-count:2'>&nbsp;&nbsp;&nbsp;&nbsp; </span></span>敏感信息：<span
                        lang=EN-US> ".$weakfile_num."<b style='mso-bidi-font-weight:normal'><o:p></o:p></b></span></span></p>
            </td>
        </tr>";
    return $html;
}

function report_vul_gen(){
    global $db;
    $hash = $_GET['hash'];

    $high = get_severity($hash, 'high');
    $medium = get_severity($hash, 'medium');
    $low = get_severity($hash, 'low');

    $html ="<p class=MsoNormal align=left style='margin-top:6.0pt;text-align:left;
line-height:150%;text-autospace:none'><b style='mso-bidi-font-weight:normal'><span
            style='font-size:12.0pt;line-height:150%;font-family:宋体;color:red'>高危漏洞：<span
            lang=EN-US>".$high." </span>个</span></b><b style='mso-bidi-font-weight:normal'><span
            lang=EN-US style='font-size:12.0pt;line-height:150%;font-family:宋体'><span
            style='mso-tab-count:2'>&nbsp;&nbsp;&nbsp; </span></span></b><b
            style='mso-bidi-font-weight:normal'><span style='font-size:12.0pt;line-height:
150%;font-family:宋体;color:#FFC000'>中危漏洞：<span lang=EN-US>".$medium." </span>个</span></b><b
            style='mso-bidi-font-weight:normal'><span lang=EN-US style='font-size:12.0pt;
line-height:150%;font-family:宋体'><span style='mso-tab-count:2'>&nbsp;&nbsp;&nbsp; </span></span></b><b
            style='mso-bidi-font-weight:normal'><span style='font-size:12.0pt;line-height:
150%;font-family:宋体;color:#00B050'>低危漏洞：<span lang=EN-US> ".$low." </span>个</span></b><b
            style='mso-bidi-font-weight:normal'><span lang=EN-US style='font-size:12.0pt;
line-height:150%;font-family:宋体'><o:p></o:p></span></b></p>";
    return $html;
}

function report_vul(){
    global $db;
    $hash = $_GET['hash'];


    $sql = "SELECT * FROM target_vul where hash='{$hash}' and severity='high' union all SELECT * FROM target_vul where hash='{$hash}' and severity='medium' union all SELECT * FROM target_vul where hash='{$hash}' and severity='low'";;
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0) {
        $i = 1;
        while ($fs = $db->fetch_array($results)) {
            $id = $i;
            $Affects = $fs["affects"];
            $Parameter = $fs["parameter"];
            $Severity = $fs["severity"];
            $details = $fs["details"];
            $Request = str_replace("\n", '<br>', $fs["request"]);
            $Response = str_replace("\n",'<br>',$fs["response"]);

            $vul_cn_id = $fs["vul_cn_id"];
            $sql1 = "SELECT * FROM vul_cn where id='{$vul_cn_id}'";
            $results1 = $db->query($sql1);
            $fs1 = $db->fetch_array($results1);
            $Name = $fs1['name_cn'];
            $miaoshu = $fs1['miaoshu_cn'];
            $jianyi = $fs1['jianyi_cn'];
            if ($Name == ''){
                $Name = $fs["name"];
            }

            if (strtolower($Severity) == 'high') {
                $Severity = "<span style='color:red'>高危</span>";
            } else if (strtolower($Severity) == 'medium') {
                $Severity = "<span style='color:#FFC000'>中危</span>";
            } else if (strtolower($Severity) == 'low' or strtolower($Severity) == 'info') {
                $Severity = "<span style='color:#00B050'>低危</span>";
            }

            if ($Parameter == 'Array') {
                $Parameter = '';
            }

            if ($Request == 'Array') {
                $Request = '';
            }

            $html .="<h3 style='margin-top:12.0pt;margin-right:0cm;margin-bottom:12.0pt;margin-left:
24.0pt;line-height:18.9pt;page-break-after:avoid;mso-layout-grid-align:none'><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>4.".$id."</span></b><b><span style='font-size:14.5pt;
font-family:宋体;mso-ascii-font-family:\"Times New Roman\";mso-fareast-theme-font:
minor-fareast;mso-hansi-font-family:\"Times New Roman\";mso-no-proof:yes'> </span></b><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>".$Name."</span></b></h3>

            <div align=center>

        <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
               style='border-collapse:collapse;mso-table-layout-alt:fixed;mso-padding-alt:
 0cm 0cm 0cm 0cm'>
            <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;padding:
  0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>漏洞描述</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border:solid windowtext 1.0pt;
  border-left:none;mso-border-left-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$miaoshu."<o:p></o:p></span></p>
                </td>
            </tr>
            <tr style='mso-yfti-irow:1'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:normal'><span
                            style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:\"Times New Roman\";
  mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:\"Times New Roman\";
  mso-no-proof:yes'>风险等级</span></b><b style='mso-bidi-font-weight:normal'><span
                            lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$Severity."<o:p></o:p></span></b></p>
                </td>
            </tr>
            <tr style='mso-yfti-irow:2'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>文件路径</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$Affects."<o:p></o:p></span></p>
                </td>
            </tr>
            
            <tr style='mso-yfti-irow:3'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>风险参数</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$Parameter."<o:p></o:p></span></p>
                </td>
            </tr>
            <tr style='mso-yfti-irow:4'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>测试详情</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$details."<o:p></o:p></span></p>
                </td>
            </tr>
            <tr style='mso-yfti-irow:5'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'>HTTP</span></b><b style='mso-bidi-font-weight:normal'><span
                            style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:\"Times New Roman\";
  mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:\"Times New Roman\";
  mso-no-proof:yes'>请求</span></b><b style='mso-bidi-font-weight:normal'><span
                            lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$Request."<o:p></o:p></span></p>
                </td>
            </tr>
            
            <tr style='mso-yfti-irow:6;mso-yfti-lastrow:yes'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>加固建议</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span style='font-size:12.0pt;
  font-family:宋体;mso-ascii-font-family:\"Times New Roman\";mso-hansi-font-family:
  \"Times New Roman\";mso-bidi-font-family:\"Times New Roman\";mso-no-proof:yes'>".$jianyi."</span><span
                            lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></p>
                </td>
            </tr>
        </table>

    </div>";
            $i ++;
        }
    }

    return $html;

}
function report_spider(){
    global $db;

    //print_r($_POST);
    $hash = $_GET['hash'];

    #$sql = "SELECT * FROM scan_list as a,target_info as b where a.hash = b.hash";
    $sql = "SELECT * FROM spider  LEFT JOIN info ON info.hash = spider.hash where spider.hash = '$hash'";
//    echo $sql;
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0){
        $i = 1;

        while ($fs = $db->fetch_array($results))
        {

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
            $url_all = $fs['url_all'];
            $act_all = $fs['act_all'];

            global $logspiderdir;
//            echo $logspiderdir;
            $dir=$logspiderdir.$hash;
            $webdir = "./TaskPython/logspider/".$hash;
            $snap_file ='';
            $file=array_slice(scandir($dir),2);
            foreach ($file as $f){
                if (strstr($f,'urlall.txt') or strstr($f,'urllog.txt') or strstr($f,'done.txt')){
                    continue;
                }else{
                    $snap_url =  $webdir.'/'.$f;
                    $snap_file .= "<a href=\"$snap_url\" target='_blank'>$snap_url</a><br>";
                }
            }


            $html ="<h3 style='margin-top:12.0pt;margin-right:0cm;margin-bottom:12.0pt;margin-left:
24.0pt;line-height:18.9pt;page-break-after:avoid;mso-layout-grid-align:none'><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>5.1</span></b><b><span style='font-size:14.5pt;
font-family:宋体;mso-ascii-font-family:\"Times New Roman\";mso-fareast-theme-font:
minor-fareast;mso-hansi-font-family:\"Times New Roman\";mso-no-proof:yes'> </span></b><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>页面统计</span></b></h3>

<div align=center>

        <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
               style='border-collapse:collapse;mso-table-layout-alt:fixed;mso-padding-alt:
 0cm 0cm 0cm 0cm'>
            <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;padding:
  0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>页面统计</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border:solid windowtext 1.0pt;
  border-left:none;mso-border-left-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:6.0pt;text-align:left;
  text-indent:6.0pt;mso-char-indent-count:.5;line-height:150%;text-autospace:
  none'><span lang=EN-US style='font-size:12.0pt;line-height:150%;font-family:
  宋体'>URL</span><span style='font-size:12.0pt;line-height:150%;font-family:
  宋体'>总数：<span lang=EN-US>".$url_num."<span style='mso-spacerun:yes'>&nbsp;&nbsp;&nbsp;
  </span></span>动态<span lang=EN-US>URL</span>：<span lang=EN-US>".$act_num."</span>&#8195;&#8195; <span
                            lang=EN-US><o:p></o:p></span></span></p>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:150%;
  mso-layout-grid-align:none;text-autospace:none'><span style='font-size:12.0pt;
  line-height:150%;font-family:宋体;color:red'>暗链页面：<span lang=EN-US>".$evil_num."</span></span><span
                            style='font-size:12.0pt;line-height:150%;font-family:宋体'>&#8195;&#8195; <span
                            style='color:#FFC000'>敏感字页面：<span lang=EN-US>".$key_num."</span></span>&#8195;&#8195;<span
                            style='color:#00B050'> 坏链页面：<span lang=EN-US>".$bad_num."</span></span><span
                            lang=EN-US><o:p></o:p></span></span></p>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:150%;
  mso-layout-grid-align:none;text-autospace:none'><span style='font-size:12.0pt;
  line-height:150%;font-family:宋体'>快照页面：<span lang=EN-US>".$snap_num."<o:p></o:p></span></span></p>
                </td>
            </tr>
            </table></div>
            <h3 style='margin-top:12.0pt;margin-right:0cm;margin-bottom:12.0pt;margin-left:
24.0pt;line-height:18.9pt;page-break-after:avoid;mso-layout-grid-align:none'><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>5.2</span></b><b><span style='font-size:14.5pt;
font-family:宋体;mso-ascii-font-family:\"Times New Roman\";mso-fareast-theme-font:
minor-fareast;mso-hansi-font-family:\"Times New Roman\";mso-no-proof:yes'> </span></b><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>暗链检测</span></b></h3>

<div align=center>

        <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
               style='border-collapse:collapse;mso-table-layout-alt:fixed;mso-padding-alt:
 0cm 0cm 0cm 0cm'>
 
            <tr style='mso-yfti-irow:1'>
            
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><a name=\"_49\"></a><a
                            name=\"_80\"></a><b style='mso-bidi-font-weight:normal'><span style='font-size:
  12.0pt;font-family:宋体;mso-ascii-font-family:\"Times New Roman\";mso-hansi-font-family:
  \"Times New Roman\";mso-bidi-font-family:\"Times New Roman\";mso-no-proof:yes'>暗链页面</span></b><span
                            lang=EN-US style='font-size:12.0pt;font-family:宋体;mso-bidi-font-family:宋体'><o:p></o:p></span></p>
                </td>
                  <td width=456 valign=top style='width:341.8pt;border:solid windowtext 1.0pt;  
                  border-left:none;mso-border-left-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$evil_page."<o:p></o:p></span></p>
                </td>
            </tr>
            </table></div>
<h3 style='margin-top:12.0pt;margin-right:0cm;margin-bottom:12.0pt;margin-left:
24.0pt;line-height:18.9pt;page-break-after:avoid;mso-layout-grid-align:none'><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>5.3</span></b><b><span style='font-size:14.5pt;
font-family:宋体;mso-ascii-font-family:\"Times New Roman\";mso-fareast-theme-font:
minor-fareast;mso-hansi-font-family:\"Times New Roman\";mso-no-proof:yes'> </span></b><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>敏感字检测</span></b></h3>

<div align=center>

        <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
               style='border-collapse:collapse;mso-table-layout-alt:fixed;mso-padding-alt:
 0cm 0cm 0cm 0cm'>
            
            <tr style='mso-yfti-irow:2'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>敏感字页面</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                  <td width=456 valign=top style='width:341.8pt;border:solid windowtext 1.0pt;  border-left:none;mso-border-left-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$key_page."<o:p></o:p></span></p>
                </td>
            </tr>
            </table></div>
<h3 style='margin-top:12.0pt;margin-right:0cm;margin-bottom:12.0pt;margin-left:
24.0pt;line-height:18.9pt;page-break-after:avoid;mso-layout-grid-align:none'><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>5.4</span></b><b><span style='font-size:14.5pt;
font-family:宋体;mso-ascii-font-family:\"Times New Roman\";mso-fareast-theme-font:
minor-fareast;mso-hansi-font-family:\"Times New Roman\";mso-no-proof:yes'> </span></b><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>坏链检测</span></b></h3>

<div align=center>

        <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
               style='border-collapse:collapse;mso-table-layout-alt:fixed;mso-padding-alt:
 0cm 0cm 0cm 0cm'>
            <tr style='mso-yfti-irow:3'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>坏链页面</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                  <td width=456 valign=top style='width:341.8pt;border:solid windowtext 1.0pt;  border-left:none;mso-border-left-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$bad_page."<o:p></o:p></span></p>
                </td>
            </tr>
            </table></div>
<h3 style='margin-top:12.0pt;margin-right:0cm;margin-bottom:12.0pt;margin-left:
24.0pt;line-height:18.9pt;page-break-after:avoid;mso-layout-grid-align:none'><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>5.5</span></b><b><span style='font-size:14.5pt;
font-family:宋体;mso-ascii-font-family:\"Times New Roman\";mso-fareast-theme-font:
minor-fareast;mso-hansi-font-family:\"Times New Roman\";mso-no-proof:yes'> </span></b><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>网页快照</span></b></h3>

<div align=center>

        <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
               style='border-collapse:collapse;mso-table-layout-alt:fixed;mso-padding-alt:
 0cm 0cm 0cm 0cm'>
            <tr style='mso-yfti-irow:4;mso-yfti-lastrow:yes'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>快照文件</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                  <td width=456 valign=top style='width:341.8pt;border:solid windowtext 1.0pt;  border-left:none;mso-border-left-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$snap_file."<o:p></o:p></span></p>
                </td>
            </tr>
        </table>

    </div>";


        }
    }
    return $html;
}


function report_info()
{
    global $db;

    //print_r($_POST);
    $hash = $_GET['hash'];

    #$sql = "SELECT * FROM scan_list as a,target_info as b where a.hash = b.hash";
    $sql = "SELECT * FROM info where hash = '$hash'";
    $results = $db->query($sql);
    if (mysql_num_rows($results) > 0) {
        $i = 1;

        while ($fs = $db->fetch_array($results)) {


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
        }
    }
    $html ="<h3 style='margin-top:12.0pt;margin-right:0cm;margin-bottom:12.0pt;margin-left:
24.0pt;line-height:18.9pt;page-break-after:avoid;mso-layout-grid-align:none'><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>6.1</span></b><b><span style='font-size:14.5pt;
font-family:宋体;mso-ascii-font-family:\"Times New Roman\";mso-fareast-theme-font:
minor-fareast;mso-hansi-font-family:\"Times New Roman\";mso-no-proof:yes'> </span></b><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>网站信息</span></b></h3>

<div align=center>

        <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
               style='border-collapse:collapse;mso-table-layout-alt:fixed;mso-padding-alt:
 0cm 0cm 0cm 0cm'>
            <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;padding:
  0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>网站</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'>URL</span></b><span lang=EN-US style='font-size:12.0pt;
  font-family:宋体;mso-bidi-font-family:宋体'><o:p></o:p></span></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border:solid windowtext 1.0pt;
  border-left:none;mso-border-left-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$url."<o:p></o:p></span></p>
                </td>
            </tr>
      
            <tr style='mso-yfti-irow:1'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>网站标题</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$title."<o:p></o:p></span></p>
                </td>
            </tr>
            <tr style='mso-yfti-irow:2'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'>IP</span></b><b style='mso-bidi-font-weight:normal'><span
                            style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:\"Times New Roman\";
  mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:\"Times New Roman\";
  mso-no-proof:yes'>地址</span></b><b style='mso-bidi-font-weight:normal'><span
                            lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$ip."<o:p></o:p></span></p>
                </td>
            </tr>
            <tr style='mso-yfti-irow:3'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>操作系统</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$os."<o:p></o:p></span></p>
                </td>
            </tr>
            <tr style='mso-yfti-irow:4'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>中间件</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$middleware."<o:p></o:p></span></p>
                </td>
            </tr>
            <tr style='mso-yfti-irow:5'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'>CMS<o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$cms."<o:p></o:p></span></p>
                </td>
            </tr>
            <tr style='mso-yfti-irow:6'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'>WAF<o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$waf."<o:p></o:p></span></p>
                </td>
            </tr>
            <tr style='mso-yfti-irow:7'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;border-top:
  none;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>开发语言</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                <td width=456 valign=top style='width:341.8pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext 1.0pt;mso-border-left-alt:solid windowtext 1.0pt;
  padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$language."<o:p></o:p></span></p>
                </td>
            </tr>
            </table></div>
<h3 style='margin-top:12.0pt;margin-right:0cm;margin-bottom:12.0pt;margin-left:
24.0pt;line-height:18.9pt;page-break-after:avoid;mso-layout-grid-align:none'><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>6.2</span></b><b><span style='font-size:14.5pt;
font-family:宋体;mso-ascii-font-family:\"Times New Roman\";mso-fareast-theme-font:
minor-fareast;mso-hansi-font-family:\"Times New Roman\";mso-no-proof:yes'> </span></b><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>端口开放</span></b></h3>

<div align=center>

        <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
               style='border-collapse:collapse;mso-table-layout-alt:fixed;mso-padding-alt:
 0cm 0cm 0cm 0cm'>
            <tr style='mso-yfti-irow:8'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>端口开放</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                  <td width=456 valign=top style='width:341.8pt;border:solid windowtext 1.0pt;  border-left:none;mso-border-left-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$port."<o:p></o:p></span></p>
                </td>
            </tr>
            </table></div>
<h3 style='margin-top:12.0pt;margin-right:0cm;margin-bottom:12.0pt;margin-left:
24.0pt;line-height:18.9pt;page-break-after:avoid;mso-layout-grid-align:none'><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>6.3</span></b><b><span style='font-size:14.5pt;
font-family:宋体;mso-ascii-font-family:\"Times New Roman\";mso-fareast-theme-font:
minor-fareast;mso-hansi-font-family:\"Times New Roman\";mso-no-proof:yes'> </span></b><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>子域名信息</span></b></h3>

<div align=center>

        <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
               style='border-collapse:collapse;mso-table-layout-alt:fixed;mso-padding-alt:
 0cm 0cm 0cm 0cm'>
            <tr style='mso-yfti-irow:9'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>子域名信息</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                  <td width=456 valign=top style='width:341.8pt;border:solid windowtext 1.0pt;  border-left:none;mso-border-left-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$sub."<o:p></o:p></span></p>
                </td>
            </tr>
            </table></div>
<h3 style='margin-top:12.0pt;margin-right:0cm;margin-bottom:12.0pt;margin-left:
24.0pt;line-height:18.9pt;page-break-after:avoid;mso-layout-grid-align:none'><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>6.4</span></b><b><span style='font-size:14.5pt;
font-family:宋体;mso-ascii-font-family:\"Times New Roman\";mso-fareast-theme-font:
minor-fareast;mso-hansi-font-family:\"Times New Roman\";mso-no-proof:yes'> </span></b><b><span
            lang=EN-US style='font-size:14.5pt;mso-fareast-font-family:宋体;mso-fareast-theme-font:
minor-fareast;mso-no-proof:yes'>敏感信息泄露</span></b></h3>

<div align=center>

        <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
               style='border-collapse:collapse;mso-table-layout-alt:fixed;mso-padding-alt:
 0cm 0cm 0cm 0cm'>
            <tr style='mso-yfti-irow:10;mso-yfti-lastrow:yes'>
                <td width=124 style='width:93.05pt;border:solid windowtext 1.0pt;mso-border-top-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=center style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:center;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><b style='mso-bidi-font-weight:
  normal'><span style='font-size:12.0pt;font-family:宋体;mso-ascii-font-family:
  \"Times New Roman\";mso-hansi-font-family:\"Times New Roman\";mso-bidi-font-family:
  \"Times New Roman\";mso-no-proof:yes'>敏感信息</span></b><b style='mso-bidi-font-weight:
  normal'><span lang=EN-US style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";
  mso-no-proof:yes'><o:p></o:p></span></b></p>
                </td>
                  <td width=456 valign=top style='width:341.8pt;border:solid windowtext 1.0pt;  border-left:none;mso-border-left-alt:solid windowtext 1.0pt;padding:0cm 0cm 0cm 0cm'>
                    <p class=MsoNormal align=left style='margin-top:3.0pt;margin-right:5.0pt;
  margin-bottom:3.0pt;margin-left:5.0pt;text-align:left;line-height:15.6pt;
  mso-layout-grid-align:none;text-autospace:none'><span lang=EN-US
                                                        style='font-size:12.0pt;font-family:\"Times New Roman\",\"serif\";mso-no-proof:
  yes'>".$weakfile."<o:p></o:p></span></p>
                </td>
            </tr>
        </table>

    </div>";

    return $html;

}
?>