<?php
require(dirname(__FILE__).'/include/config.inc.php');

$m_arr = array('cpasswd','del','rescan','export','delcustomer','delinfo','info','delspider','search11','delall','resetall','resetscan','resetspider','resetinfo');

$mode = $_GET['m'];

#Checklogin($mode);

if(in_array($mode,$m_arr)){
	call_user_func($mode);
}
?>