<?php
function ld_Checkpath($str)
{
	$arr = array("\\","/","..",":");
	foreach ($arr as $k)
	{
		if(stristr("$str","$k")) exit();
	}
	return $str;
}
$hash = ld_Checkpath($_GET['p']);
//$basedir = dirname(__FILE__);
$path = dirname(__FILE__)."/TaskPython/logspider/$hash/urllog.txt";
//print $path;
$done = dirname(__FILE__)."/TaskPython/logspider/$hash/done.txt";
$urlall = dirname(__FILE__)."/TaskPython/logspider/$hash/urlall.txt";

if (is_file($done)){
    if (isset($_GET['c']) && ($_GET['c'] == 'urlall')){
    echo file_get_contents($urlall);
}else{
        echo file_get_contents($path);
    }
}else{
    echo "null";}

//echo $path;
//echo file_get_contents($path);
?>