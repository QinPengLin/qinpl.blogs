<?php
class Info  extends llpq{
	public function actionFileUpload(){//图片上传方法		
		$t=time();
		$re=@uplod_file($_FILES['upload'],'img/'.date("Y",$t).'/'.date("m",$t).'/'.date("d",$t),array("image/png","image/jpeg"),300000);//图片上传保存
        if($re['code']){
        	echo '/'.$re['data'];  	
        }else{
        	echo $re['data'];
        }  

	}
	public function actionFileUpload_suo(){//图片上传方法		
		$t=time();
		$re=@uplod_file($_FILES['myfile'],'img/'.date("Y",$t).'/'.date("m",$t).'/'.date("d",$t),array("image/png","image/jpeg"),300000);//图片上传保存
        if($re['code']){
        	echo json_encode('/'.$re['data']);  	
        }else{
        	echo json_encode($re['data']);
        }  

	}
}
?>