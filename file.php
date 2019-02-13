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
$basedir = dirname(__FILE__);
#echo $basedir;
$path = $basedir."/report/$hash/export.xml";
#echo $path;
if (is_file($path)){
    echo file_get_contents($path);
}else{
    echo "null";
}
?> 