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

?>