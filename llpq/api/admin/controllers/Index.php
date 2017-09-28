<?php
	class Index  extends llpq{
		private $dir;
		private $url;
		private $www;//
		private $static;//公共文件夹web中url的static
		private $tp_file;//选用模板数组
		private $tp_model_file;//选用模板文件夹数组
		private $sysconfig_url;//系统配置文件路径
		private $sysconfig;//系统配置文件信息
		public function __construct(){
			parent::__construct();
			$this->dir=__DIR__;
			$this->url=str_replace('controllers','vendor',$this->dir);
			$this->www="http://".HTTP_HOST."\\api\\admin\\vendor\\Index";
			$this->static="http://".HTTP_HOST."\\api\\admin\\vendor\\static";
			
			$this->sysconfig_url=str_replace('controllers','sysconfig',$this->dir)."/config.config";
			
			$this->sysconfig=redconfig($this->sysconfig_url);//获取系统配置文件信息

			$dir=str_replace('controllers','indextemplet',$this->dir)."/".$this->sysconfig['templet_model']."/";
            $file=scandir($dir);
            unset($file[0]);
            unset($file[1]);
            foreach($file as $k=>$v){
            
            	$file[$k]='/'.$this->sysconfig['templet_model'].'/'.$v;
            }
            $this->tp_file=$file;
            
            $dir_m=str_replace('controllers','indextemplet',$this->dir)."/";
            $file_m=scandir($dir_m);
            unset($file_m[0]);
            unset($file_m[1]);
            $this->tp_model_file=$file_m;  
		}
		
		public function actionDefault(){//默认模板文件夹设置
			if(IS_POST){
			  $post=$_POST;
			  $sav=savconfig($this->sysconfig_url,array('templet_model'=>$post['def']));//修改系统配置文件信息
			  $this->sysconfig=redconfig($this->sysconfig_url);//重新获取系统配置文件信息
			}
			$df='def';
			include($this->url."/Index/default.html");
		}
		
		public function actionDefaultIndex(){//默认首页模板文件
			$deindex='';
			$savv='';
			@$deindex=redconfig($this->sysconfig_url)['templet_index'];
			if(IS_POST){
			  $post=$_POST;
			  $sav=writeconfig($this->sysconfig_url,array('templet_index'=>$post['def']));
			  if($sav['cod']){
			  	    $savv=$sav['data'];
			  }else{
			        $sav=savconfig($this->sysconfig_url,array('templet_index'=>$post['def']));//修改系统配置文件信息
			        $savv=$sav['data'];
			  }
			  if($sav['cod']){
			    $deindex=redconfig($this->sysconfig_url)['templet_index'];//重新获取系统配置文件信息
			  }
			}
			
			  
			$df='defindex';
			
			include($this->url."/Index/default.html");
		}
		
		public function actionAdminClass(){//栏目及其分类设置
			
			$m=new Model();
			$re=$m->table('class')->select();
		
		
            $file=$this->tp_file;
           
          
            
			$c_1=array();
			
			foreach($re as $ks=>$vs){//将各				
				for($i=1;$i<4;$i++){
					if($vs['rate']==$i){
					   $c_1[$i][]=$vs;
				    }
				}
			}
			
			include($this->url."/Index/adminclass.html");
		}
		
		public function actionEdit(){//修改栏目及其分类
			$post=$_POST;
			$m=new Model();
			$re=$m->table('class')->where(array('id'=>$post['p_id']))->save(array(
			                                                                      'name'=>$post['p_name'],
			                                                                      'templet_name'=>$post['p_tem'],
			                                                                      'c_templet_name'=>$post['p_c_tem'],
			                                                                      'file_name'=>$post['p_file'])
			                                                                      );
			if($re){
				echo json_encode(array('cod'=>1,'data'=>$post));
			}else{
				echo json_encode(array('cod'=>0,'data'=>''));
			}
			
		}
		
		public function actionAdd(){//增加栏目及其分类
			$post=$_POST;
			$m=new Model();
			$post['a_pid']>0?$rate=2:$rate=1;
			$re=$m->table('class')->add(array(
			                                                                      'name'=>$post['a_name'],
			                                                                      'templet_name'=>$post['a_tem'],
			                                                                      'c_templet_name'=>$post['a_c_tem'],
			                                                                      'file_name'=>$post['a_file'],
			                                                                      'p_id'=>$post['a_pid'],
			                                                                      'rate'=>$rate
			                                                                     
			                                                                      )
			                                                                      );
			if($re){
				$post['id']=$re;
				$post['rate']=$rate;
				$post['tp_file']=$this->tp_file;
				echo json_encode(array('cod'=>1,'data'=>$post));
			}else{
				echo json_encode(array('cod'=>0,'data'=>''));
			}
			
		}
		
		public function actionDele(){//删除栏目及其分类
			$post=$_POST;
			$m=new Model();
			$ref=$m->table('class')->where(array('p_id'=>$post['id']))->find();
			if($ref){
				echo json_encode(array('cod'=>0,'data'=>'存在子级分类,需先删除子分类'));
			}else{
				$reft=$m->table('essay')->where(array('class_c'=>$post['id']))->find();
				if($reft){
					echo json_encode(array('cod'=>0,'data'=>'存在文章,需先删除文章'));
				}else{
					$ref=$m->table('class')->where(array('id'=>$post['id']))->delete();
				   echo json_encode(array('cod'=>1,'data'=>'已经删除'));	
				}
			}
			
		}
		
		public function actionSaveIndex(){//网站静态页面更新首页显示
			$v='';
			
		   include($this->url."/Index/saveindex.html");
		}
		
		public function actionRenewIndex(){//首页更新

                   $urlc = "http://".HTTP_HOST."/admin/Reveal/Index"; //网页地址  
                   $v['tp_index']=redconfig($this->sysconfig_url)['templet_index'];
	
                   $contents=curl_post($urlc,$v);//获取网页类容

                   
                   $handle=fopen("index.html","w+");
                   fwrite($handle,$contents);
                   fclose($handle);
                 
                  $v='首页更新成功！';
                  include($this->url."/Index/saveindex.html");		  
             exit;
		}
		
		public function actionRenewEssayAll(){//批量更新所有的文章页
            $m=new Model();
			$re=$m->table('essay')->select();
			$reclass=$m->table('class')->select();
			
		
            $filename='';
           foreach($re as $k=>$v){
                   $urlc = "http://".HTTP_HOST."/admin/Reveal/Essay"; //网页地址
                   foreach($reclass as $k1=>$v1){//将模板信息传递给Indexc
				    if($v['class_c']==$v1['id']){
					  $v['reclass']=$v1['c_templet_name'];
					  $filename=$v1['file_name'];//赋值保存静态文件夹
				    }
				    }
				    if($filename){
				    	
                   $contents=curl_post($urlc,$v);//获取网页类容

                   $handle=mkdir_file($filename."/".$v['id'].".html");
                   fwrite($handle,$contents);
                   fclose($handle);
                  }
                  }	
                  $v='网站所有内容页更新成功！';
                  include($this->url."/Index/saveindex.html");		  
             exit;
		   }
		   
		public function actionRenewClassAll(){//批量更新所有的栏目页
			$lmpa=lmpage($this->sysconfig_url);//获取所以栏目的每页显示条数
			$all=6;//默认栏目每页条数
            $m=new Model();
			$re=$m->table('class')->select();
			
            $filename='';
           foreach($re as $k=>$v){
                   $urlc = "http://".HTTP_HOST."/admin/Reveal/Column"; //网页地址  
                   $filename=$v['file_name'];               
				    if($filename){
				    	$slc=$m->table('essay')->where(array('class_c'=>$v['id']))->count();
				    	
				    	if($slc){
				    		foreach($lmpa as $vl){
				    			if($vl['id']==$v['id']){
				    				$all=$vl['page'];
				    			}
				    		}
				    		
				    		    $all_page=ceil($slc/$all);//计算出总页数
				    		
				    		for($i=1;$i<($all_page+1);$i++){
				    		   $v['page']=$i;
				    		   $v['all']=$all_page;
				    		   $contents=curl_post($urlc,$v);//获取网页类容
                               if($i==1){
                               	$file_hname="/index.html";
                               }else{
                               	$file_hname="/index_".$i.".html";
                               }
                               $handle=mkdir_file($filename.$file_hname);
                               fwrite($handle,$contents);
                               fclose($handle);
				    		}
				    		
				    		
				    	}else{
				    		 $v['page']='';
                             $contents=curl_post($urlc,$v);//获取网页类容

                             $handle=mkdir_file($filename."/index.html");
                             fwrite($handle,$contents);
                             fclose($handle);
                            }
                   
                  }
                  }	
                  $v='网站所有栏目页更新成功！';
                  include($this->url."/Index/saveindex.html");		  
             exit;
		   }
		   
		
		   
		   
	  
			
			
			
		public function actionContent_list(){//首次加载列表
            
            $m=new Model();
			$re=$m->table('class')->select();
           
           
           $c_1=array();
           foreach($re as $ks=>$vs){//将各			
				for($i=1;$i<4;$i++){
					if($vs['rate']==$i){
					   $c_1[$i][]=$vs;
				    }
				}
			}
          
           $get=$_GET;
           if(isset($get['c_id']) && !empty($get['c_id'])){
           
           	$all=10;//每页条数
           	$rel=$m->table('essay')->where(array('class_c'=>$get['c_id']))->order(' `id` DESC')->limit(0,$all)->select();
           	$end=end($rel);
           	$next=$end['id'];         
           }
         
          
           
			//替换example内容，并获取内容赋值给$str
			include($this->url."/Index/content_list_index.html");
			}
			
		public function actionContent_list_next(){//文章翻页加载
			 $m=new Model();
		     $get=$_GET;
           if(isset($get['c_id']) && !empty($get['c_id'])){
           	(isset($get['end_id']) && !empty($get['end_id']))?$sta=$get['end_id']:$sta=0;
           	$all=10;//每页条数
           	$rel=$m->table('essay')->where('WHERE class_c='.$get['c_id'].' AND id<'.$sta)->order('`id` DESC')->limit(0,$all)->select();
           	$end=end($rel);
           	$next=$end['id']; 
            if($rel){
            	echo json_encode(array('cod'=>1,'data'=>$rel,'next'=>$next,'c_id'=>$get['c_id'],'end_id'=>$sta));	
            }else{
            	echo json_encode(array('cod'=>0,'data'=>'加载失败'));
            }
               
           }
		}
	   
	   public function actionDeleEssay(){//删除文章
	   	 if(IS_POST){
	   	 	$post=$_POST;
	   	 	$m=new Model();
	   	 	$re=$m->table('essay')->where(array('id'=>$post['id']))->delete();
	   	 	$kl=1;
	   	 	$vl='';
	   	 	if($re){
	   	 		$re_e=$m->table('recommend')->field(array('id','essay_id'))->where("WHERE essay_id LIKE '%,".$post['id'].",%'")->select();
	   	 	   if($re_e){
	   	 		foreach($re_e as $k=>$v){
	   	 			$ex_arr=explode(',',$v['essay_id']);
	   	 			foreach($ex_arr as $k1=>$v1){
	   	 				if($v1==$post['id']){
	   	 					unset($ex_arr[$k1]);
	   	 				}
	   	 			}
	   	 			$re_e[$k]['essay_id']=implode(',',$ex_arr);
	   	 			$re_e[$k]['essay_id']=trim($re_e[$k]['essay_id'],',');
                    $re_e[$k]['essay_id']=','.$re_e[$k]['essay_id'].',';
                    
                    $re_rr=$m->table('recommend')->where(array('id'=>$v['id']))->save(array('essay_id'=>$re_e[$k]['essay_id']));
                    if(!$re_rr){
                    	$kl=0;
                    	$vl=$v['id'];
                    	break;
                    }
	   	 		}
	   	 		}
	   	 	}
	   	 	
	   	 	if($re){
	   	 		if($kl){
	   	 			echo json_encode(array('cod'=>1,'data'=>'文章删除成功'));
	   	 		}else{
	   	 		    echo json_encode(array('cod'=>0,'data'=>'文章已经删除，但是文章推荐位选中删除到ID为['.$vl.']时出错'));
	   	 		}
	   	 	}else{
	   	 		echo json_encode(array('cod'=>0,'data'=>'文章删除失败'));
	   	 	}
	   	 }
	   }
		
		public function actionFind_content(){
			$m=new Model();
		   $post=$_POST;
		   
		    if(isset($post['id']) && !empty($post['id'])){
		    	$rel=$m->table('essay')->where(array('id'=>$post['id']))->find();
		    	echo json_encode(array('cod'=>1,'data'=>$rel));
		    	
		    }
		}
		
		public function actionPostInfo(){
		    $post=$_POST;
		    $m=new Model();
		    $rel=$m->table('essay')->where(array('id'=>$post['id']))->save(array(
		                                                                        'title'=>$post['title'],
		                                                                        'content'=>$post['content'],
		                                                                        'class_c'=>$post['class_c'],
		                                                                        'outline'=>$post['outline'],
		                                                                        'author'=>$post['author'],
		                                                                        'outline_img'=>$post['outline_img']
		                                                                   ));
		                                                                   
		    if($rel){
		    	echo json_encode(array('cod'=>1,'data'=>$post));
		    }
	     }
	    
	    public function actionAdd_essay(){//添加内容
	     if(IS_GET){	
	    	$m=new Model();
			$re=$m->table('class')->select();
           
           $clss_c=$_GET['class_c'];
         
           $c_1=array();
           foreach($re as $ks=>$vs){//将各			
				for($i=1;$i<4;$i++){
					if($vs['rate']==$i){
					   $c_1[$i][]=$vs;
				    }
				}
			}
			include($this->url."/Index/add_essay.html");
			}
			
		 if(IS_POST){
		 	$post=$_POST;
		 	$m=new Model();
		 	$re=$m->table('essay')->add(array(
		 	                             'title'=>$post['list_title'],
		 	                             'content'=>$post['editorl'],
		 	                             'class_c'=>$post['class_c'],
		 	                             'author'=>$post['list_author'],
		 	                             'outline'=>$post['list_outline'],
		 	                             'outline_img'=>$post['outline_img'],
		 	                              'create_time'=>time()
		 	));
		 	if($re){
		 		header("Location: http://".HTTP_HOST."/admin/Index/Content_list?c_id=".$post['class_c']); 
		 	}
		 }
			
	    }
	    
	    public function actionOutLogin(){ //退出登陆
	    parent::outLogin();
        }
		
		
			
	}

?>