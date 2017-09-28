<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * ThinkPHP Model模型类
 * 实现了ORM和ActiveRecords模式
 */
class Model {

    // 当前数据库操作对象
    protected $db               =   null;
    // 数据库对象池
    private   $_db				=	array();
    // 主键名称
    protected $pk               =   'id';
    // 主键是否自动增长
    protected $autoinc          =   false;
    // 数据表前缀
    protected $tablePrefix      =   null;
    // 模型名称
    protected $name             =   '';
    // 数据库名称
    protected $dbName           =   '';
    //数据库配置
    protected $connection       =   '';
    // 数据表名（不包含表前缀）
    protected $tableName        =   '';
    // 实际数据表名（包含表前缀）
  // protected $trueTableName    =   '';
    // 最近错误信息
    protected $error            =   '';
    // 字段信息
    protected $fields           =   array();
    // 数据信息
    protected $data             =   array();
    // 查询表达式参数
    protected $options          =   array();

// 实际数据表名（包含表前缀）
    protected $prefix    =   '';
    /**
     * 架构函数
     * 取得DB类的实例对象 字段检查
     * @access public
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
     */
    public function __construct() {
    	

        global $_CONFIG;
       
        if (isset($_CONFIG['Mysql']['prefix']))
        {
            $this->prefix = $_CONFIG['Mysql']['prefix'];
        }
        try {
            $dsn = 'mysql:host=' . $_CONFIG['Mysql']['server']. ';dbname=' .  $_CONFIG['Mysql']['database_name'];            
            $this->db = new PDO(
                $dsn,
                $_CONFIG['Mysql']['username'],
                $_CONFIG['Mysql']['password']
            );          
            $this->db->exec("SET SQL_MODE=ANSI_QUOTES"); //设置数据类型
            $this->db->exec("SET NAMES '" . $_CONFIG['Mysql']['charset'] . "'");//设置字符集
        }
        catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 新增数据
     * @access public
     * @param mixed $data 数据
     * @return mixed
     */
    public function add($datas=[]) {

        if (empty($this->options['table'])){
            return false;
        }
        if (empty($datas)){
            return false;
        }
        if (!isset($datas[ 0 ]))
        {
            $datas = array($datas);
        }
        foreach ($datas as $data)
        {
            $values = array(); //值
            $columns = array(); //字段

            foreach ($data as $key => $value)
            {
                $columns[] = $this->column_quote(preg_replace("/^(\(JSON\)\s*|#)/i", "", $key));
                switch (gettype($value))
                {
                    case 'NULL':
                        $values[] = 'NULL';
                        break;
                    case 'array':
                        preg_match("/\(JSON\)\s*([\w]+)/i", $key, $column_match);
                        $values[] = isset($column_match[ 0 ]) ?
                            $this->quote(json_encode($value)) :
                            $this->quote(serialize($value));
                        break;
                    case 'boolean':
                        $values[] = ($value ? '1' : '0');
                        break;
                    case 'integer':
                    case 'double':
                    case 'string':
                        $values[] = $this->fn_quote($key, $value);
                        break;
                }
            }
          
          $this->db->exec('INSERT INTO ' . $this->options['table'] . ' (' . implode(', ', $columns) . ') VALUES (' . implode($values, ', ') . ')');
         $lastId[] = $this->db->lastInsertId();
        }
        return count($lastId) > 1 ? $lastId : $lastId[ 0 ];
    }

    /**
     * 为普通字符串添加引导
     * @param $string
     * @return mixed
     */
    public function quote($string)
    {
        return $this->db->quote($string);
    }

    /**
     * 过滤字符串
     * @param $string
     * @return string
     */
    protected function column_quote($string)
    {
        preg_match('/(\(JSON\)\s*|^#)?([a-zA-Z0-9_]*)\.([a-zA-Z0-9_]*)/', $string, $column_match);

        if (isset($column_match[ 2 ], $column_match[ 3 ]))
        {
            return '"' . $this->prefix . $column_match[ 2 ] . '"."' . $column_match[ 3 ] . '"';
        }

        return '"' . $string . '"';
    }
    protected function fn_quote($column, $string)
    {
        return (strpos($column, '#') === 0 && preg_match('/^[A-Z0-9\_]*\([^)]*\)$/', $string)) ?
            $string :
            $this->quote($string);
    }

    /**
     * 保存数据
     * @access public
     * @param mixed $data 数据
     * @param array $options 表达式
     * @return boolean
     */
    public function save($data='') {
        if (empty($this->options['table'])){
            return false ;
        }
        if (empty($this->options['where'])){
            return false;
        }
        $fields = array();
        foreach ($data as $key => $value)
        {
            preg_match('/([\w]+)(\[(\+|\-|\*|\/)\])?/i', $key, $match);

            if (isset($match[ 3 ]))
            {
                if (is_numeric($value))
                {
                    $fields[] = $this->column_quote($match[ 1 ]) . ' = ' . $this->column_quote($match[ 1 ]) . ' ' . $match[ 3 ] . ' ' . $value;
                }
            }
            else
            {
                $column = $this->column_quote(preg_replace("/^(\(JSON\)\s*|#)/i", "", $key));

                switch (gettype($value))
                {
                    case 'NULL':
                        $fields[] = $column . ' = NULL';
                        break;

                    case 'array':
                        preg_match("/\(JSON\)\s*([\w]+)/i", $key, $column_match);

                        $fields[] = $column . ' = ' . $this->quote(
                                isset($column_match[ 0 ]) ? json_encode($value) : serialize($value)
                            );
                        break;

                    case 'boolean':
                        $fields[] = $column . ' = ' . ($value ? '1' : '0');
                        break;

                    case 'integer':
                    case 'double':
                    case 'string':
                        $fields[] = $column . ' = ' . $this->fn_quote($key, $value);
                        break;
                }
            }
        }
        return $this->db->exec('UPDATE ' . $this->options['table'] . ' SET ' . implode(', ', $fields) . $this->options['where']);
    }


    /**
     * 删除数据
     * @access public
     * @param mixed $options 表达式
     * @return mixed
     */
    public function delete($options=array()) {

        if (empty($this->options['table'])){
            return false;
        }
        if (empty($this->options['where'])){
            return  false;
        }
        return $this->db->exec('DELETE FROM ' . $this->options['table']. $this->options['where']);
    }

    protected function select_context()
    {
        preg_match('/([a-zA-Z0-9_\-]*)\s*\(([a-zA-Z0-9_\-]*)\)/i', $this->options['table'], $table_match);

        if (isset($table_match[ 1 ], $table_match[ 2 ]))
        {
            $table = $table_match[1];
            $table_query = $table_match[1] . ' AS ' . $table_match[ 2 ];
        }
        else
        {
            $table = $this->options['table'];
            $table_query = $table;
            $this->options['table'] = '';
        }
        if (empty($this->options['field'])){
            $field = "*";
        }else{
            $field = $this->options['field'];
            $this->options['field'] = '';
        }
        if(empty($this->options['where'])){
            $where = '';
        }else{
            $where =  $this->options['where'];
            $this->options['where'] = '';
        }
        if (!empty($this->options['order'])){
            $where .= '  ORDER BY  '. $this->options['order'];
            $this->options['order'] = '';
        }
        if (!empty($this->options['limit'])){
            $where .= '  LIMIT  '.$this->options['limit'];
            $this->options['limit'] = '';
        }
         return 'SELECT ' . $field . ' FROM ' . $table_query . $where;
    }

    public function select() {
        if (empty($this->options['table'])){
            return false;
        }
//       print_r($this->select_context());
//       exit;
        $row =   $this->query($this->select_context())->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
    /**
     * 查询数据集
     * @access public
     * @return mixed
     */
    public function count() {
        if (empty($this->options['table'])){
            return false;
        }
        $row =   $this->select();
        return empty($row)?0:count($row);
    }



    /*
     * 总是查找一条记录
     * @access public
     * @return mixed
     */
    public function find() {

        if (empty($this->options['table'])){
            return false;
        }
        $select_context =  $this->select_context()."  LIMIT 1";
        //print_r($select_context);
        //exit;
        $res = $this->db->query($select_context)->fetch(PDO::FETCH_ASSOC);
        //$res = $this->db->query($select_context);
       // print_r($_CONFIG);
         //print_r($this->db);
      // exit;
        return empty($res)?false:$res;
    }

    /**
     * 设置记录的某个字段值
     * 支持使用数据库字段和方法
     * @access public
     * @param string|array $field  字段名
     * @param string $value  字段值
     * @return boolean
     */
    public function setField($field,$value='') {
        if(is_array($field)) {
            $data           =   $field;
        }else{
            $data[$field]   =   $value;
        }
        return $this->save($data);
    }



    /**
     * SQL查询
     * @access public
     * @param string $sql  SQL指令
     * @param mixed $parse  是否需要解析SQL
     * @return mixed
     */
    public function query($sql) {
        return $this->db->query($sql);
    }

    /**
     * 切换当前的数据库连接
     * @access public
     * @param integer $linkNum  连接序号
     * @param mixed $config  数据库连接信息
     * @param boolean $force 强制重新连接
     * @return Model
     */
    public function db($linkNum='',$config='',$force=false) {
        if('' === $linkNum && $this->db) {
            return $this->db;
        }

        if(!isset($this->_db[$linkNum]) || $force ) {
            // 创建一个新的实例
            if(!empty($config) && is_string($config) && false === strpos($config,'/')) { // 支持读取配置参数
                $config  =  C($config);
            }
            $this->_db[$linkNum]            =    Db::getInstance($config);
        }elseif(NULL === $config){
            $this->_db[$linkNum]->close(); // 关闭数据库连接
            unset($this->_db[$linkNum]);
            return ;
        }

        // 切换数据库连接
        $this->db   =    $this->_db[$linkNum];
        $this->_after_db();
        // 字段检测
        if(!empty($this->name) && $this->autoCheckFields)    $this->_checkTableInfo();
        return $this;
    }
    /**
     * 查询数据库 信息
     * @return array
     */
    public function info()
    {
        $output = array(
            'server' => 'SERVER_INFO',
            'driver' => 'DRIVER_NAME',
            'client' => 'CLIENT_VERSION',
            'version' => 'SERVER_VERSION',
            'connection' => 'CONNECTION_STATUS'
        );
        foreach ($output as $key => $value)
        {
            $output[ $key ] = $this->db->getAttribute(constant('PDO::ATTR_' . $value));
        }

        return $output;
    }

    /**
     * 获取数据表字段信息
     * @access public
     * @return array
     */
    public function getDbFields(){
        if(isset($this->options['table'])) {// 动态指定表名
            if(is_array($this->options['table'])){
                $table  =   key($this->options['table']);
            }else{
                $table  =   $this->options['table'];
                if(strpos($table,')')){
                    // 子查询
                    return false;
                }
            }
            $fields     =   $this->db->getFields($table);
            return  $fields ? array_keys($fields) : false;
        }
        if($this->fields) {
            $fields     =  $this->fields;
            unset($fields['_type'],$fields['_pk']);
            return $fields;
        }
        return false;
    }


    /**
     * 排序
     * @param $order 字段
     * @return $this
     */
    public function order($order) {
        $this->options['order']  = empty($order)? '':$order;
        return $this;
    }

    /**
     * 指定当前的数据表
     * @access public
     * @param mixed $table
     * @return Model
     */
    public function table($table) {
        $prefix =  $this->prefix;
        if(is_array($table)) {
            $this->options['table'] =   $table;
        }elseif(!empty($table)) {
            $table  =  $prefix.$table;
            $this->options['table'] =   $table;
        }
        return $this;
    }

    /**
     * 查询SQL组装 join
     * @access public
     * @param mixed $join
     * @param string $type JOIN类型
     * @return Model
     */
    public function join($join,$type='INNER') {
        $prefix =   $this->tablePrefix;
        if(is_array($join)) {
            foreach ($join as $key=>&$_join){
                $_join  =   preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function($match) use($prefix){ return $prefix.strtolower($match[1]);}, $_join);
                $_join  =   false !== stripos($_join,'JOIN')? $_join : $type.' JOIN ' .$_join;
            }
            $this->options['join']      =   $join;
        }elseif(!empty($join)) {
            //将__TABLE_NAME__字符串替换成带前缀的表名
            $join  = preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function($match) use($prefix){ return $prefix.strtolower($match[1]);}, $join);
            $this->options['join'][]    =   false !== stripos($join,'JOIN')? $join : $type.' JOIN '.$join;
        }
        return $this;
    }

    public function field($field){
        if (!is_array($field)){
            return false;
        }
        $field      =  !empty($field)?implode(',',$field):'*';
        $this->options['field']   =   $field;
        return $this;
    }

    /**
     * 指定查询条件 支持安全过滤
     * @access public
     * @param mixed $where 条件表达式
     * @return Model
     */
    public function where($where =[]){
        if (empty($where)){
             $this->options['where'] = '';
        }

        $where_clause = '';
        if (is_array($where))
        {
            $where_keys = array_keys($where);
            $where_AND = preg_grep("/^AND\s*#?$/i", $where_keys);
            $where_OR = preg_grep("/^OR\s*#?$/i", $where_keys);

            $single_condition = array_diff_key($where, array_flip(
                array('AND', 'OR')
            ));
            if ($single_condition != array())
            {
                $condition = $this->data_implode($single_condition, '');

                if ($condition != '')
                {
                    $where_clause = ' WHERE ' . $condition;
                }
            }
            if (!empty($where_AND))
            {
                $value = array_values($where_AND);
                $where_clause = ' WHERE ' . $this->data_implode($where[ $value[ 0 ] ], ' AND');
            }
            if (!empty($where_OR))
            {
                $value = array_values($where_OR);
                $where_clause = ' WHERE ' . $this->data_implode($where[ $value[ 0 ] ], ' OR');
            }
        }
        else
        {
            if ($where != null)
            {
                $where_clause .= ' ' . $where;
            }
        }


        $this->options['where'] =   $where_clause;
        return $this;
    }

 



    protected function data_implode($data, $conjunctor)
    {
        $wheres = array();
        foreach ($data as $key => $value)
        {
            $type = gettype($value);
                preg_match('/(#?)([\w\.\-]+)(\[(\>|\>\=|\<|\%|\<\=|\!|\<\>|\>\<|\!?~)\])?/i', $key, $match);
                $column = $this->column_quote($match[ 2 ]);
                if (isset($match[ 4 ]))
                {
                    $operator = $match[ 4 ];

                    if ($operator == '!')
                    {
                        switch ($type)
                        {
                            case 'NULL':
                                $wheres[] = $column . ' IS NOT NULL';
                                break;

                            case 'array':
                                $wheres[] = $column . ' NOT IN (' . $this->array_quote($value) . ')';
                                break;

                            case 'integer':
                            case 'double':
                                $wheres[] = $column . ' != ' . $value;
                                break;

                            case 'boolean':
                                $wheres[] = $column . ' != ' . ($value ? '1' : '0');
                                break;

                            case 'string':
                                $wheres[] = $column . ' != ' . $this->fn_quote($key, $value);
                                break;
                        }
                    }

                    if ($operator == '<>' || $operator == '><')
                    {
                        if ($type == 'array')
                        {
                            if ($operator == '><')
                            {
                                $column .= ' NOT';
                            }

                            if (is_numeric($value[ 0 ]) && is_numeric($value[ 1 ]))
                            {
                                $wheres[] = '(' . $column . ' BETWEEN ' . $value[ 0 ] . ' AND ' . $value[ 1 ] . ')';
                            }
                            else
                            {
                                $wheres[] = '(' . $column . ' BETWEEN ' . $this->quote($value[ 0 ]) . ' AND ' . $this->quote($value[ 1 ]) . ')';
                            }
                        }
                    }

                    if ($operator == '~' || $operator == '!~'||$operator == '%')
                    {
                        if ($type != 'array')
                        {
                            $value = array($value);
                        }

                        $like_clauses = array();

                        foreach ($value as $item)
                        {
                            $item = strval($item);

                            if (preg_match('/^(?!(%|\[|_])).+(?<!(%|\]|_))$/', $item))
                            {
                                $item =  ($operator === '%' ? '' : '%') . $item . '%';
                            }

                            $like_clauses[] = $column . ($operator === '!~' ? ' NOT' : '') . ' LIKE ' . $this->fn_quote($key, $item);
                        }

                        $wheres[] = implode(' OR ', $like_clauses);
                    }

                    if (in_array($operator, array('>', '>=', '<', '<=')))
                    {
                        $condition = $column . ' ' . $operator . ' ';

                        if (is_numeric($value))
                        {
                            $condition .= $value;
                        }
                        elseif (strpos($key, '#') === 0)
                        {
                            $condition .= $this->fn_quote($key, $value);
                        }
                        else
                        {
                            $condition .= $this->quote($value);
                        }
                        $wheres[] = $condition;
                    }
                }
                else
                {
                    switch ($type)
                    {
                        case 'NULL':
                            $wheres[] = $column . ' IS NULL';
                            break;

                        case 'array':
                            $wheres[] = $column . ' IN (' . $this->array_quote($value) . ')';
                            break;

                        case 'integer':
                        case 'double':
                            $wheres[] = $column . ' = ' . $value;
                            break;

                        case 'boolean':
                            $wheres[] = $column . ' = ' . ($value ? '1' : '0');
                            break;

                        case 'string':
                            $wheres[] = $column . ' = ' . $this->fn_quote($key, $value);
                            break;
                    }
                }

        }
        return implode($conjunctor . ' ', $wheres);
    }

    protected function array_quote($array)
    {
        $temp = array();

        foreach ($array as $value)
        {
            $temp[] = is_int($value) ? $value : $this->db->quote($value);
        }

        return implode($temp, ',');
    }

    /**
     * 指定查询数量
     * @access public
     * @param mixed $offset 起始位置
     * @param mixed $length 查询数量
     * @return Model
     */
    public function limit($offset,$length=null){
        if(is_null($length) && strpos($offset,',')){
            list($offset,$length)   =   explode(',',$offset);
        }
        $this->options['limit']     =   intval($offset).( $length? ','.intval($length) : '' );
        return $this;
    }

    /**
     * 指定分页
     * @access public
     * @param mixed $page 页数
     * @param mixed $listRows 每页数量
     * @return Model
     */
    public function page($page,$listRows=null){
        if(is_null($listRows) && strpos($page,',')){
            list($page,$listRows)   =   explode(',',$page);
        }
        $this->options['page']      =   array(intval($page),intval($listRows));
        return $this;
    }
    
    
       public function  put_port($data){
    	return $this->column_quote($data);
    }
    
    
     /**
     * 参数绑定条件过滤(可以在此写过滤规则)
     * @param $string
     * @return string
     */
    protected function column_quote_f($string)
    {
        return $string;
    }
    /**
     * 参数绑定模糊查询
     * pssql:SELECT j_key,j_detail FROM yi_job WHERE (j_detail LIKE :j_detail_0 OR j_detail LIKE :j_detail_1 OR j_detail LIKE :j_detail_2 OR j_detail LIKE :j_detail_3 OR j_key LIKE :j_key_0 OR j_key LIKE :j_key_1 OR j_key LIKE :j_key_2 OR j_key LIKE :j_key_3) AND j_add_time<:j_add_time AND j_cid=:j_cid
     * $style   模糊限制条件,如LIKE '%员%') AND j_add_time < 1483514172中的AND
     * $mode='' 如LIKE '%员%') AND j_add_time < 1483514172中的< 
     * $warr=[] 如果$warr中的有一个键对应的是数组，则该数组必须只有两个键如['st'=>'OR','data'=>'员,span']
     * 其中st对应的是(j_detail LIKE '%span%' OR j_detail LIKE '%员%')中的OR ，data则是该字段所有模糊查询得条件值
     * 如果$warr中的有一个键对应的是字符串且字符串中不含‘,’则对应AND j_add_time<:j_add_time
     	*如果含有','则对应 AND j_cid=:j_cid其中‘=’为字符串第一
     *$limt查询条数 
     * $order排序条件 ORDER BY j_id DESC
     */
    
    public function parameter_kid($warr=[],$style='',$mode='',$limt='',$order=''){//参数绑定模糊查询
    	empty($style)?$style='AND':$style=$style;
    	empty($mode)?$mode='<':$mode=$mode;
        empty($limt)?$limt=10:$limt=$limt;
    	
  	$where='';
  	$where1='';
  	$vts='';
  	foreach($warr as $k=>$v){
  		if(!is_array($v)){
  			$farr=explode(",",$v);
  		if(count($farr)>1){
  			$where=$where.$k.''.$farr[0].':'.$k.' '.$style.' ';
  		}else{
  			$where=$where.$k.''.$mode.':'.$k.' '.$style.' ';
  		}
  		}else{
  			if(!empty($v['data'])){
  				$varr=explode(",",$v['data']);
  				foreach($varr as $ky=>$vy){
  					$where1=$where1.$k.' LIKE :'.$k.'_'.$ky.' '.$v['st'].' ';
  					$vts=$v['st'];
  				}
  				 
  			}else{echo '条件不能为空';exit;}
  		}
  	}
  	$where1='('.rtrim($where1,' '.$vts.' ').') '.$style.' ';
  	$where=$where1.$where;
  	$where=' WHERE '.rtrim($where,' '.$style.' ');
  	//$where=' WHERE '.rtrim($where,' '.$style.' ').' AND j_add_time < 1483514172';

        if(!empty($order)){
        	$sql='SELECT '.$this->options['field'].' FROM '.$this->options['table'].$where.' '.$order.' LIMIT 0,'.$limt.';';
        }else{
        	$sql='SELECT '.$this->options['field'].' FROM '.$this->options['table'].$where.' LIMIT 0,'.$limt.';';
        }
    	
    	//print_r($sql);
    	//exit;
    	$query = $this->db->prepare($sql);
    	foreach($warr as $k=>$v){
    		if(!is_array($v)){
    			$farr=explode(",",$v);
    			if(count($farr)>1){
    				$infoV=$this->column_quote_f($farr[1]);   			  
    		        $query->bindValue(':'.$k,$infoV, PDO::PARAM_STR);
    			}else{
    				$infoV=$this->column_quote_f($v);   			  
    		        $query->bindValue(':'.$k,$infoV, PDO::PARAM_STR);
    			}
    			  
    		}else{
    			  if(!empty($v['data'])){
  				$varr=explode(",",$v['data']);
  				foreach($varr as $ky=>$vy){
  				  $infoVy=$this->column_quote_f($vy); 				 
  				  $query->bindValue(':'.$k.'_'.$ky, '%'.$infoVy.'%', PDO::PARAM_STR);
  				}
  			}else{echo '条件不能为空';exit;}
    	  }
    	}

        $query->execute();
        $reft='';
        $i=0;
        
        while ($results = $query->fetch(PDO::FETCH_ASSOC)){       	
             $reft[$i]=$results;
             $i++;              
              }
        return $reft;
    	
    }




}