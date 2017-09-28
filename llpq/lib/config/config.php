<?php
return[
  'Mysql'=>[//数据库配置
            'server' => 'localhost',
            'database_name' => 'test', 
            'username' => 'root',
            'password' => '!QinPengLin1991',
            'charset' => 'utf8',
            'prefix'=>'hd_'//表前缀
  	 ],
  'Module'=>[//模块配置
            'web',
            'admin'
  ],
  'Index'=>[//默认首页配置
            'm'=>'admin',
            'c'=>'Reveal',
            'a'=>'Index'
  ]
  ,
  'Cache'=>false//系统代码是否开启缓存
  ,
  'LibFile'=>[//配置加载文件
   '/function/ce.php',
   '/web/base.php',
   '/web/QQAPI.php',
   '/web/Simple.php'
  ]
];

