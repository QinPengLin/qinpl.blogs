<?php
	     $dir=__DIR__;
		 $dir_ex=explode("/",$dir);
		 if(in_array('runtime',$dir_ex)){//判断是否开启缓存
		    $qqapi_url=str_replace('lib/runtime/lib','lib/pack/QQAPI',$dir)."/qqConnectAPI.php";	    
		 }else{
		 	$qqapi_url=str_replace('lib/web','lib/pack/QQAPI',$dir)."/qqConnectAPI.php";
		 }
		 require_once($qqapi_url);
?>