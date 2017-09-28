<?php
function ces(){
	print_r('默认首页');
}
function curl_get($url){//GET提交

//初始化
     $curl = curl_init();
     //设置抓取的url
     curl_setopt($curl, CURLOPT_URL,$url);
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
     curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); 
      //执行命令
     $data = curl_exec($curl);
    //关闭URL请求
     curl_close($curl);

return $data;
}


function curl_post($url,$post){//post提交$post为数组
//初始化
     $curl = curl_init();
     //设置抓取的url
     curl_setopt($curl, CURLOPT_URL,$url);
     //设置头文件的信息作为数据流输出
     curl_setopt($curl, CURLOPT_HEADER, 0);
     //设置获取的信息以文件流的形式返回，而不是直接输出。
     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

//   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
//   curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); 
     
     //设置post方式提交
     curl_setopt($curl, CURLOPT_POST, 1);
     //设置post数据
     $post_data = $post;
     curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
     //执行命令
     $data = curl_exec($curl);
     //关闭URL请求
     curl_close($curl);
  
return $data;
}

//$params[$i] = array(
 //       "url"=>"http://www.so.com/s",
   //     "params"=>array('q'=>$keyword,'ie'=>"utf-8",'pn'=>($page-1)*10+$i+1)
    //);
//#多线程并发抓取函数mfetch：
function mfetch($params=array(), $method){
    $mh = curl_multi_init(); #初始化一个curl_multi句柄
    $handles = array();
    foreach($params as $key=>$param){
        $ch = curl_init(); #初始化一个curl句柄
        $url = $param["url"];
        $data = $param["params"];
        if(strtolower($method)==="get"){
           #根据method参数判断是post还是get方式提交数据
            $url = "$url?" . http_build_query( $data ); #get方式
        }else{
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $data ); #post方式
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
       // curl_setopt($ch, CURLOPT_HEADER, 1);开启获取http头
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
        curl_multi_add_handle($mh, $ch);
        @$handles[$ch] = $key;
        #handles数组用来记录curl句柄对应的key,供后面使用，以保证返回的数据不乱序。
    }
    $running = null;
    $curls = array(); #curl数组用来记录各个curl句柄的返回值
    do { #发起curl请求，并循环等等1/100秒，直到引用参数"$running"为0
        usleep(10000);
        curl_multi_exec($mh, $running);
        while( ( $ret = curl_multi_info_read( $mh ) ) !== false ){
#循环读取curl返回，并根据其句柄对应的key一起记录到$curls数组中,保证返回的数据不乱序
            @$curls[$handles[$ret["handle"]]] = $ret;
        }
    } while ( $running > 0 );
    foreach($curls as $key=>&$val){
        $val["content"] = curl_multi_getcontent($val["handle"]);
        curl_multi_remove_handle($mh, $val["handle"]); #移除curl句柄
    }
    curl_multi_close($mh); #关闭curl_multi句柄
    ksort($curls);
    return $curls;
}




function mkdir_file($mkdir_file_name){	//返回文件句柄$mkdir_file_name格式：html/kl/hjj.html
	$arr_url=explode('/',$mkdir_file_name);
	$max_i=count($arr_url);
	$dir='';
	for($i=0;$i<$max_i;$i++){
		$dir=$dir.$arr_url[$i].'/';
		$dirt=trim($dir,'/');
	  if($i<($max_i-1)){
		
		if(!file_exists($dirt)){
			mkdir($dirt);
		}
	  }else{
	  	$handle=fopen($dirt,"w+");
	  }
	}
	
	return $handle;
	
}

function uplod_file($file_ob,$file_url,$typ=array("image/gif","image/jpeg","image/pjpeg"),$file_size=200000){
	if (in_array($file_ob["type"],$typ) && ($file_ob["size"] < $file_size))
  {
  if ($file_ob["error"] > 0)
    {
    return array('code'=>0,'data'=>$file_ob["error"]);
    }
  else
    {
			$cl_u=trim($file_url,'/');
			$arr_url=explode('/','upload/'.$cl_u);
	        $max_i=count($arr_url);
	$dir='';
	$dirt="";
	for($i=0;$i<$max_i;$i++){
		$dir=$dir.$arr_url[$i].'/';
		$dirt=trim($dir,'/');

		if(!file_exists($dirt)){
			mkdir($dirt);
		}	 
	}
	if(file_exists($dirt)){
		$file_name_h=explode('.',$file_ob["name"]);
		$re_file_url=$dirt.'/'.time().'.'.$file_name_h[1];
		
		if(move_uploaded_file($file_ob["tmp_name"],$re_file_url)){
			return array('code'=>1,'data'=>$re_file_url);
		}else{
			return array('code'=>0,'data'=>'文件移动错误');
		}		
	}else{
		return array('code'=>0,'data'=>'文件路径创建错误');
	}     
    }
  }
else
  {
  return array('code'=>0,'data'=>'文件过大或者文件格式不支持！');
  }
}


function redconfig($file_url){//获取系统配置文件内容并且返回数组（读）$file_url为系统文件路径
	                          //例子：redconfig($this->sysconfig_url);//获取系统配置文件信息	
			$op_file=$file_url;
			$content_file=fopen($op_file,"r");
			$rere=fread($content_file,filesize($op_file));
            fclose($content_file);
			$rearr=explode(',',$rere);
			$ref=array();
			foreach($rearr as $k=>$v){
				$ire=explode('=>',$v);
				@$ref[$ire[0]]=$ire[1];//加上@符号抑止有可能出现的变量未定义的错误
			}
			return $ref;
		}
		
function writeconfig($file_url,$arr=array()){//向系统配置文件写内容（写）
	                                          //例子：writeconfig($this->sysconfig_url,array('templet_model2'=>45,'templet_model1'=>'fdsf'));//写入系统配置文件信息
			$op_file=$file_url;
			$wstr='';
			foreach($arr as $k=>$v){
				foreach(redconfig($op_file) as $kt=>$vt){
					if($k==$kt){
						return array('cod'=>0,'data'=>$k.'与原有键冲突');
						break;
					}
				}
				$wstr=$wstr.','.$k.'=>'.$v;
			}
			       $content_file=fopen($op_file,"a");
			       fwrite($content_file,$wstr);
                   fclose($content_file);
			return array('cod'=>1,'data'=>'新增成功');
		}

function deleconfig($file_url,$arr=array()){//删除系统配置文件指定键（删）
	                                         //例子:deleconfig($this->sysconfig_url,array('templet_model1','4545'));//删除配置文件信息
			$op_file=$file_url;
			$wstrarr='';
			$kstr='';
			$pd=0;
			$rec=redconfig($op_file);
			foreach($arr as $v){
				foreach($rec as $kt=>$vt){
					if($v==$kt){
						unset($rec[$kt]);
						$kstr=$kstr.','.$kt;
						$pd=1;
					}
				}
			}
			foreach($rec as $k=>$v){
				$wstrarr=$wstrarr.','.$k.'=>'.$v;
			}
			
			$wstrarr=trim($wstrarr,',');
			 
			 if($pd){
			 	   $content_file=fopen($op_file,"w+");
			       fwrite($content_file,$wstrarr);
                   fclose($content_file);
                  return array('cod'=>1,'data'=>$kstr.'删除成功');
			 }else{
			 	return array('cod'=>0,'data'=>'没有删除项');
			 }

		}
function savconfig($file_url,$arr=array()){//修改系统配置文件指定键（改）
	                                      //例子：savconfig($this->sysconfig_url,array('version'=>'0.2','4545'));
	            $op_file=$file_url;
			$wstrarr='';
			$kstr='';
			$pd=0;
			$rec=redconfig($op_file);
			foreach($arr as $k=>$v){
				foreach($rec as $kt=>$vt){
					if((string)$k==$kt){
						$rec[$kt]=$v;
						$kstr=$kstr.','.$kt;
						$pd=1;
					}
				}
			}
			foreach($rec as $k=>$v){
				$wstrarr=$wstrarr.','.$k.'=>'.$v;
			}
			
			$wstrarr=trim($wstrarr,',');
			 
			 if($pd){
			 	   $content_file=fopen($op_file,"w+");
			       fwrite($content_file,$wstrarr);
                   fclose($content_file);
                  return array('cod'=>1,'data'=>$kstr.'修改成功');
			 }else{
			 	return array('cod'=>0,'data'=>'没有修改项');
			 }
}

   function lmpage($sysurl){//获取每个分类列表每页显示数量
   	  $lmpage=redconfig($sysurl)['lmpage'];
   	  $lmp_arr=explode('|',$lmpage);
   	  $lmp=array();
   	  foreach($lmp_arr as $k=>$v){
   	  	if(!empty($v)){
   	  		$lap=explode('-',$v);
   	  		$lmp[$k]['id']=$lap[0];
   	  		$lmp[$k]['page']=$lap[1];
   	  	}
   	  }
   	   return $lmp;
   }
   
   
   
   
    function _data_to_tree(&$items, $topid = 0, $with_id = TRUE)
{//无限级分类递归函数
    $result = [];
    foreach($items as $v){
        if ($topid == $v['p_id'])  {
            $r = $v + ['children' => _data_to_tree($items, $v['id'], $with_id)];
            if ($with_id){
                $result[$v['id']] = $r;
            }else{
                $result[] = $r;
               }
        }
       }
            
    return $result;
}


function my_perk($info){//自定义过滤函数
	        return $info;
}
function red_dictionary($file_url){//获取敏感词汇字典文件并返回数组
			$op_file=$file_url;
			$content_file=fopen($op_file,"r");
			$rere=fread($content_file,filesize($op_file));
            fclose($content_file);
            
			$rearr=explode("\r\n",$rere);
			
			if(count($rearr)>1){
				return $rearr;
			}else{
				$rearr=explode("\n",$rere);
				return $rearr;
			}
			
	}
function perk($info,$file_url){//过滤html标签并且屏蔽掉敏感词汇$file_url敏感词典存放路径$info需要过滤字符串
			$badword = red_dictionary($file_url);
            $badword1 = array_combine($badword,array_fill(0,count($badword),'*')); 
            
            $info=my_perk($info);
            $htmli=htmlspecialchars($info);
            $htmli = strtr($htmli, $badword1); 


    	    return $htmli;
		}

function php_encode($str){
                          $enstr='';
                          $encrypt_key = 'ABCDEFGHIJKLMNOQPRST*UVWXYZabcdefghijklmnopqrstuvwxyz1234567890,';
                          $decrypt_key = 'nTgCzQ7tO,cNoUbmAuhLelBkpIdGaVwPxXfyiWvrDsKjSqH46R8Z0E213Y5*FM9J';
                          if (strlen($str) == 0) return false;
                          for ($i=0; $i<strlen($str); $i++){
                                             for ($j=0; $j<strlen($encrypt_key); $j++){
                                                     if ($str[$i] == $encrypt_key[$j]){
                                                                      $enstr .= $decrypt_key[$j];
                                                                       break;
                                                      }
                                             }
                           }
                                 return $enstr;
}
//简单的解密函数
function php_decode($str){
                          $enstr='';
                          $encrypt_key = 'ABCDEFGHIJKLMNOQPRST*UVWXYZabcdefghijklmnopqrstuvwxyz1234567890,';
                          $decrypt_key = 'nTgCzQ7tO,cNoUbmAuhLelBkpIdGaVwPxXfyiWvrDsKjSqH46R8Z0E213Y5*FM9J';
                          if (strlen($str) == 0) return false;
                          for ($i=0; $i<strlen($str); $i++){
                                    for ($j=0; $j<strlen($decrypt_key); $j++){
                                             if ($str[$i] == $decrypt_key[$j]){
                                                                      $enstr .= $encrypt_key[$j];
                                                                        break;
                                              }
                                     }
                           }
                                              return $enstr;
}


?>