<?php
	class url{
		private $server_url;
		private $uri_split;
		private $config;
		private $api_file;
		private $url_dir;

		private $controllers_name='controllers';//配置控制器的文件夹名称
        
        
        private $m;
        private $c;
        private $a;
        
		public function __construct($server='',$config=''){
			
		  $this->server_url=$server;
		  $this->url_dir=__DIR__;
		  $this->config=$config;
		  $this->api_file=str_replace('lib/web','api',$this->url_dir);
		  $this->url_split();
		  if($this->config['Cache']){//开启系统代码缓存
		  	if($this->_quer_file()){//需要重新编译
		  		self::_import_runtime(str_replace('web','runtime/lib/runtimelib.php',$this->url_dir));//编译加载缓存文件
		  	}else{//不需要编译
		  		require(str_replace('web','runtime/lib/runtimelib.php',$this->url_dir));//只是加载缓存文件
		  	}		  	
		  }else{//没有开启代码缓存
		  	self::_improt_file($this->url_dir);//引入核心文件
		  }

		  if(in_array('?',$this->uri_split)){
		  	if(isset($_GET['m']) && !empty($_GET['m']) && isset($_GET['c']) && !empty($_GET['c']) && isset($_GET['a']) && !empty($_GET['a']) ){
		  		  if(in_array($_GET['m'],$this->config['Module']) && $this->mak_m_file($_GET['m'])){
		  		                                                         $this->a=$_GET['a'];
		  	                                                             $this->c=$_GET['c'];
		  	                                                             $this->m=$_GET['m'];
		  	                                                        }else{		  	                                                         	
		  	    	                                                    $this->url_end('没有相关模块或者没有配置相关模块名');
		  	                                                         }
		  	    
		  	}else{		  		 
		  	    $this->url_explode_g($this->url_explode()[0]);
		  	}	  			  
		  }else{
		  	$this->url_explode_g($this->server_url);
		  }
		}
		
		
		private function url_split(){	
			$this->uri_split=str_split($this->server_url);
		}
		private function url_explode(){
			return explode('?',$this->server_url);
		}
		private function url_end($str){
			        echo $str;
		  	    	exit;
		}
		private function mak_m_file($model_name){//判断model文件夹是否创建完全
             if(file_exists($this->api_file.'/'.$model_name)){
                 if(file_exists($this->api_file.'/'.$model_name.'/'.$this->controllers_name)){
                 	$http_arr=explode('/',$_SERVER['SERVER_SOFTWARE']);
                 	if(!in_array('nginx',$_SERVER['SERVER_SOFTWARE'])){//判断http服务器是否是nginx如果是就不生成.htacces文件
                 	 if(!is_file($this->api_file.'/'.$model_name.'/'.$this->controllers_name.'/.htaccess')){//创建控制器文件中的.htaccess文件
                         $myfile = fopen($this->api_file.'/'.$model_name.'/'.$this->controllers_name.'/.htaccess', "w") or die("Unable to open file!");
                         $txt = "Order deny,allow\nDeny from all";
                         fwrite($myfile, $txt);                       
                         fclose($myfile);
                 	 }
                 	}
                     return true;
                 }else{ return false; }
             }else{ return false; }
		}
		private function url_explode_g($explode){
			$arr_g=explode('/',$explode);	
				  
		  	$kc=count($arr_g);
		  	if($kc>3){	
		  	    if(in_array($arr_g[($kc-3)],$this->config['Module']) && $this->mak_m_file($arr_g[($kc-3)])){
		  		   $this->a=$arr_g[($kc-1)];
		  	       $this->c=$arr_g[($kc-2)];
		  	       $this->m=$arr_g[($kc-3)];
		  	    }else{		  	    			  	    	
		  	    	$this->url_end('没有相关模块或者没有配置相关模块名');
		  	    }
		  	}else{//加载默认首页		 
		  		$this->a=$this->config['Index']['a'];
		  	    $this->c=$this->config['Index']['c'];
		  	    $this->m=$this->config['Index']['m'];
		  	}
		}
		
		private function _quer_file(){//判断是否应该重新编译系统文件
			$fac=0;
			$run_file=str_replace('web','runtime/lib/runtimelib.php',$this->url_dir);
			$runtime=0;
			if(is_file($run_file)){
				$runtime=filemtime($run_file);
			}else{
				$this->url_end('no find file '.$run_file);
			}			
			for($i=0;$i<count(self::_lib_file());$i++){
				$all_file=str_replace('web',self::_lib_file()[$i],$this->url_dir);
				
				if(is_file($all_file)){
					if($runtime<filemtime($all_file)){
						  $fac=1; 
						  break;
					}
				}else{
				    $this->url_end('no find file '.$all_file);
				    break;
				}
			}
			return $fac;
		}
		
		static function _import_runtime($file_runtime){
        $file = self::_lib_file();
        $content = '';
        $dir=__DIR__;
        foreach ($file as $v){        	
        	$vc=str_replace('web',$v,$dir);       	
              if(is_file($vc)){
              	$content .= self::compile($vc);
              }            
                
            
        }
        $tokens     = self::strip_whitespace('<?php '.$content);
        file_put_contents($file_runtime,$tokens);
        require $file_runtime;
        
    }
    
    
       /**
     * 编译文件
     * @param string $filename 文件名
     * @return string
     */
    static  function compile($filename) {
        $content    =   php_strip_whitespace($filename);
        $content    =   trim(substr($content, 5));
        // 替换预编译指令
        $content    =   preg_replace('/\/\/\[RUNTIME\](.*?)\/\/\[\/RUNTIME\]/s', '', $content);
        if ('?>' == substr($content, -2))
            $content    = substr($content, 0, -2);
        return $content;
    }
    
      /**
     * 去除代码中的空白和注释
     * @param string $content 代码内容
     * @return string
     */
    static function strip_whitespace($content) {
        $stripStr   = '';
        //分析php源码
        $tokens     = token_get_all($content);
        $last_space = false;
        for ($i = 0, $j = count($tokens); $i < $j; $i++) {
            if (is_string($tokens[$i])) {
                $last_space = false;
                $stripStr  .= $tokens[$i];
            } else {
                switch ($tokens[$i][0]) {
                    //过滤各种PHP注释
                    case T_COMMENT:
                    case T_DOC_COMMENT:
                        break;
                    //过滤空格
                    case T_WHITESPACE:
                        if (!$last_space) {
                            $stripStr  .= ' ';
                            $last_space = true;
                        }
                        break;
                    default:
                        $last_space = false;
                        $stripStr  .= $tokens[$i][1];
                }
            }
        }
        return $stripStr;
    }
		
		static function _improt_file($dir){//批量引入核心文件	
				
            for($i=0;$i<count(self::_lib_file());$i++){
            	require(str_replace('web',self::_lib_file()[$i],$dir));
            }
		}
		
		static function _lib_file(){//核心文件配置
			global $_CONFIG;
			$rearr=array_merge_recursive(
			[
			'/function/function.php',
			'/db/db.php',
			'web/llpq.php'
			],
			$_CONFIG['LibFile']
			);			
			return $rearr;
		}
		
		public function url_run(){
			$url_dir=str_replace('lib/web','api',$this->url_dir);
			$url_class_file=$url_dir.'/'.$this->m.'/'.$this->controllers_name.'/'.$this->c.'.php';
			
			
			
			if(is_file($url_class_file)){//检测文件是否存在
			     require($url_class_file);
			}else{				
				$this->url_end('no find file '.$this->c);
			}
			
			
			if(class_exists($this->c)){//检测类是否存在
				$run_ob=new $this->c;
			}else{
				$this->url_end('no find class '.$this->c);
			}
			
			$str_a='action'.$this->a;
			if(method_exists ($run_ob,$str_a)){//检测方法是否存在
				$run_ob->$str_a();
			}else{
				$this->url_end('no find function '.$this->a);
			}
			
		}
	}

?>