<?php
if( preg_match("/public/",$_SERVER['REQUEST_URI'])){
    $_GET['page']=str_replace('/public/','',$_SERVER['REQUEST_URI']);
    echo $_GET['page'];
    //require 'public/index.php';
    return true;
}else if(preg_match("/api/",$_SERVER['REQUEST_URI'])){
    $_GET['request']=str_replace('/api/','',$_SERVER['REQUEST_URI']);
    require 'api/index.php';

    echo $_GET['request'];

    return true;
}else{
    include 'index.php';
    return true;
}

