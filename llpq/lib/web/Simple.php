<?php
	$dir=__DIR__;
		 $dir_ex=explode("/",$dir);
		 if(in_array('runtime',$dir_ex)){//判断是否开启缓存
		    $simple_url=str_replace('lib/runtime/lib','lib/pack/simple_html_dom',$dir)."/simple_html_dom.php";	    
		 }else{
		 	$simple_url=str_replace('lib/web','lib/pack/simple_html_dom',$dir)."/simple_html_dom.php";
		 }
		 require_once($simple_url);

?>