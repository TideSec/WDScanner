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
$path = dirname(__FILE__)."\TaskPython\TaskInfo\loginfo\\$hash\\$hash.txt";

//$path = "./$hash.txt";
//echo $path;
if (is_file($path)){
    echo file_get_contents($path);
}
else
	echo 'null';
?>