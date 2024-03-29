<?php
class Database{
 
    /**
     * database connection object
     * @var \PDO
     */
    protected $pdo;
 
    /**
     * Connect to the database
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
 
    /**
     * Return the pdo connection
     */
    public function getPdo()
    {
        return $this->pdo;
    }
 
    /**
     * Changes a camelCase table or field name to lowercase,
     * underscore spaced name
     *
     * @param  string $string camelCase string
     * @return string underscore_space string
     */
    protected function camelCaseToUnderscore($string)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }
 
    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @param  string $param Name of the sequence object from which the ID should be returned.
     * @return string representing the row ID of the last row that was inserted into the database.
     */
    public function lastInsertId($param = null)
    {
        return $this->pdo->lastInsertId($param);
    }
 
    /**
     * handler for dynamic CRUD methods
     *
     * Format for dynamic methods names -
     * Create:  insertTableName($arrData)
     * Retrieve: getTableNameByFieldName($value)
     * Update: updateTableNameByFieldName($value, $arrUpdate)
     * Delete: deleteTableNameByFieldName($value)
     *
     * @param  string     $function
     * @param  array      $arrParams
     * @return array|bool
     */
    public function __call($function, array $params = array())
    {
        if (! preg_match('/^(get|update|insert|delete)(.*)$/', $function, $matches)) {
            throw new \BadMethodCallException($function.' is an invalid method Call');
        }
 
        if ('insert' == $matches[1]) {
            if (! is_array($params[0]) || count($params[0]) < 1) {
                throw new \InvalidArgumentException('insert values must be an array');
            }
            return $this->insert($this->camelCaseToUnderscore($matches[2]), $params[0]);
        }
 
        list($tableName, $fieldName) = explode('By', $matches[2], 2);
        if (! isset($tableName, $fieldName)) {
            throw new \BadMethodCallException($function.' is an invalid method Call');
        }
         
        if ('update' == $matches[1]) {
            if (! is_array($params[1]) || count($params[1]) < 1) {
                throw new \InvalidArgumentException('update fields must be an array');
            }
            return $this->update(
                $this->camelCaseToUnderscore($tableName),
                $params[1],
                array($this->camelCaseToUnderscore($fieldName) => $params[0])
            );
        }
 
        //select and delete method
        return $this->{$matches[1]}(
            $this->camelCaseToUnderscore($tableName),
            array($this->camelCaseToUnderscore($fieldName) => $params[0])
        );
    }
 
    /**
     * Record retrieval method
     *
     * @param  string     $tableName name of the table
     * @param  array      $where     (key is field name)
     * @return array|bool (associative array for single records, multidim array for multiple records)
     */
    public function get($tableName,  $whereAnd  =   array(), $whereOr   =   array(), $whereLike =   array())
    {
    $cond   =   '';
    $s=1;
    $params =   array();
    foreach($whereAnd as $key => $val)
    {
        $cond   .=  " And ".$key." = :a".$s;
        $params['a'.$s] = $val;
        $s++;
    }
    foreach($whereOr as $key => $val)
    {
        $cond   .=  " OR ".$key." = :a".$s;
        $params['a'.$s] = $val;
        $s++;
    }
    foreach($whereLike as $key => $val)
    {
        $cond   .=  " OR ".$key." like '% :a".$s."%'";
        $params['a'.$s] = $val;
        $s++;
    }
    $stmt = $this->pdo->prepare("SELECT  $tableName.* FROM $tableName WHERE 1 ".$cond);
        try {
            $stmt->execute($params);
            $res = $stmt->fetchAll();
           
            if (! $res || count($res) != 1) {
               return $res;
            }
            return $res;
        } catch (\PDOException $e) {
            throw new \RuntimeException("[".$e->getCode()."] : ". $e->getMessage());
        }
    }
     
    public function getAllRecords($tableName, $fields='*', $cond='', $orderBy='', $limit='')
    {
        //echo "SELECT  $tableName.$fields FROM $tableName WHERE 1 ".$cond." ".$orderBy." ".$limit;
        //print "<br>SELECT $fields FROM $tableName WHERE 1 ".$cond." ".$orderBy." ".$limit;
        $stmt = $this->pdo->prepare("SELECT $fields FROM $tableName WHERE 1 ".$cond." ".$orderBy." ".$limit);
        //print "SELECT $fields FROM $tableName WHERE 1 ".$cond." ".$orderBy." " ;
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public function getColumn($table)
    {
        //echo $query;
        $query = "SELECT COLUMN_NAME AS column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".SS_DB_NAME."' AND TABLE_NAME='".$table."'";
        // $query ="SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='include' AND TABLE_NAME='users'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cols = array();
        foreach ($rows as $key => $value) {
          $cols[] = $value['column_name'];
        }
        return $cols;
    }
     
    public function getRecFrmQry($query)
    {
        //echo $query;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
    
    public function getQueryCount($tableName, $field, $cond='')
    {
        $stmt = $this->pdo->prepare("SELECT count($field) as total FROM $tableName WHERE 1 ".$cond);
        try {
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
           
            if (! $res || count($res) != 1) {
               return $res;
            }
            return $res;
        } catch (\PDOException $e) {
            throw new \RuntimeException("[".$e->getCode()."] : ". $e->getMessage());
        }
    }
     
    /**
     * Update Method
     *
     * @param  string $tableName
     * @param  array  $set       (associative where key is field name)
     * @param  array  $where     (associative where key is field name)
     * @return int    number of affected rows
     */
    public function update($tableName, array $set, array $where)
    {
        $arrSet = array_map(
           function($value) {
                return $value . '=:' . $value;
           },
           array_keys($set)
         );
             
        $stmt = $this->pdo->prepare(
            "UPDATE $tableName SET ". implode(',', $arrSet).' WHERE '. key($where). '=:'. key($where) . 'Field'
         );
 
        foreach ($set as $field => $value) {
            $stmt->bindValue(':'.$field, $value);
        }
        $stmt->bindValue(':'.key($where) . 'Field', current($where));
        try {
            $stmt->execute();
 
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \RuntimeException("[".$e->getCode()."] : ". $e->getMessage());
        }
    }
 
    /**
     * Delete Method
     *
     * @param  string $tableName
     * @param  array  $where     (associative where key is field name)
     * @return int    number of affected rows
     */
    public function delete($tableName, array $where)
    {
        $stmt = $this->pdo->prepare("DELETE FROM $tableName WHERE ".key($where) . ' = ?');
        try {
            $stmt->execute(array(current($where)));
 
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \RuntimeException("[".$e->getCode()."] : ". $e->getMessage());
        }
    }
     
     
    public function deleteQry($query)
    {
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute();
    }
 
 
    /**
     * Insert Method
     *
     * @param  string $tableName
     * @param  array  $arrData   (data to insert, associative where key is field name)
     * @return int    number of affected rows
     */
    public function insert($tableName, array $data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO $tableName (".implode(',', array_keys($data)).")
            VALUES (".implode(',', array_fill(0, count($data), '?')).")"
        );
        try{
            $stmt->execute(array_values($data));
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \RuntimeException("[".$e->getCode()."] : ". $e->getMessage());
        }
    }
    /**
     * Print array Method
     *
     * @param  array 
     */
    public function arprint($array){
        print"<pre>";
        print_r($array);
        print"</pre>";
    }
    /**
     * Maker Model Name Method
     *
     * @param  Int make id
     * @param  Int name id 
     */
    public function getModelMake($makeID,$nameID){
        $vehMakeData    =   self::getRecFrmQry('SELECT veh_make_id,veh_make_name FROM tb_vehicle_make WHERE veh_make_id="'.$makeID.'"');
        $vehNameData    =   self::getRecFrmQry('SELECT veh_name_id,veh_name FROM tb_vehicle_name WHERE veh_name_id="'.$nameID.'"');
        return $vehMakeData[0]['veh_make_name'].' '.$vehNameData[0]['veh_name'];
    }
    /**
     * Cache Method
     *
     * @param  string QUERY
     * @param  Int Time default 0 set 
     */
    public function getCache($sql,$cache_min=0) {
      $f = 'cache/'.md5($sql);
      if ( $cache_min!=0 and file_exists($f) and ( (time()-filemtime($f))/60 < $cache_min ) ) {
        $arr = unserialize(file_get_contents($f));
      }
      else {
        unlink($f);
        $arr = self::getRecFrmQry($sql);
        if ($cache_min!=0) {
          $fp = fopen($f,'w');
          fwrite($fp,serialize($arr));
          fclose($fp);
        }
      }
      return $arr;
    }
     
// 
public function updateQry($tableName, array $set,$where)
    {
        $arrSet = array_map(
           function($value) {
                return $value . '=:' . $value;
           },
           array_keys($set)
         );
             
        $stmt = $this->pdo->prepare(
            "UPDATE $tableName SET ". implode(',', $arrSet).' WHERE '. $where
         );
 
        foreach ($set as $field => $value) {
            $stmt->bindValue(':'.$field, $value);
        }
        // $stmt->bindValue(':'.key($where) . 'Field', current($where));
        try {
            $stmt->execute();
 
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \RuntimeException("[".$e->getCode()."] : ". $e->getMessage());
        }
    }     
}

///////////////////////////////////////////////////////////////////////
//  othe function function
/////////////////////////////////////////////////////////////////////// 
function columnMatch($db)
{
  $data = $db->getColumn('users');
  echoAr($_REQUEST);

  // if (str_contains('How are you', 'are')) { 
 //    echo 'true';
  // }
}
///////////////////////////////////////////////////////////////////////
//  Insert All function
/////////////////////////////////////////////////////////////////////// 
function insertAll(array $data){
  global $db;
  $request = $data['request'];
  $base_name ='';
  if (array_key_exists('base_name', $data)) {
    $base_name = $data['base_name'];
  }
  $data2 = array();
  foreach ($request as $key => $value) {
    if ($key != 'f') {
      $data2[$base_name.$key] = $value;
    } 
  }
  return $db->insert($data['table'],$data2);
}

///////////////////////////////////////////////////////////////////////
//  Update All function
/////////////////////////////////////////////////////////////////////// 
function updateAll(array $data){
  global $db;
  $request = $data['request'];
  $base_name ='';
  $condition ='';
  if (array_key_exists('base_name', $data)) {
    $base_name = $data['base_name'];
  }

  if (array_key_exists('condition', $data)) {
    $condition = $data['condition'];
  }
  $data2 = array();
  foreach ($request as $key => $value) {
    if ($key != 'f') {
      $data2[$base_name.$key] = $value;
    } 
  }
  return $db->update($data['table'],$data2,$condition);
}

// update All Query
function updateAllQry(array $data){

  global $db;
  $request = $data['request'];
  $base_name ='';
  if (array_key_exists('base_name', $data)) {
    $base_name = $data['base_name'];
  }
  $data2 = array();
  foreach ($request as $key => $value) {
    if ($key != 'f') {
      $data2[$base_name.$key] = $value;
    } 
  }
  return $db->updateQry($data['table'],$data2,$data['condition']);

}

///////////////////////////////////////////////////////////////////////
//  File upload function
/////////////////////////////////////////////////////////////////////// 

function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        $sizedata = array(
            'size'=>explode(' ', $bytes),
            'chart'=>array('GB'=>5,'MB'=>4,'KB'=>3,'bytes'=>2,'byte'=>1,'bytes'=>0)
        );
            
        return $sizedata;
}
function fileUpload(array $data){
    $fsize = basename($data['file']['size']);
    $ftype = basename($data['file']['type']);
    $ferror = $data['file']['error'];
    $target_dir = $data['target_dir'];
    $tempfile = $data["file"]["tmp_name"];
    $sizedata = formatSizeUnits($fsize);

    if (substr($target_dir, -1)!='/') {
        $target_dir =$target_dir.'/';
    }

    $check = array('type'=>0,'size'=>array('minimum'=>0,'maximum'=>0), 'error'=>$ferror);
    if (array_key_exists("type",$data)){
        
        $type = explode(',', $data['type']);
        if(in_array($ftype, $type)){
            $check['type'] = 1;
        }else{
            $check['type'] = 'only allow ('.$data['type'].') file !...';
        }
        
    }else{
        $check['type'] = 1;
    }


    if (array_key_exists("size",$data)){
        
        $size = explode(',', $data['size']);
        $sizemin = explode(' ', $size[0]);
        $sizemax =  explode(' ', $size[1]);
        

        if($sizemin[0]!=0){
            if ($sizedata['chart'][$sizedata['size'][1]]>$sizedata['chart'][$sizemin[1]]) {
                $check['size']['minimum']=1;
            }elseif ($sizedata['chart'][$sizedata['size'][1]]==$sizedata['chart'][$sizemin[1]]) {
                if ($sizedata['size'][0]>=$sizemin[0]) {
                    $check['size']['minimum']=1;
                }else{
                    $check['size']['minimum']='minimum size is '.$size[0];
                }
            }else{
                $check['size']['minimum']='minimum size is '.$size[0];
            }
        }else{
            $check['size']['minimum']=1;
        }


        if($sizemax[0]!=0){
            if ($sizedata['chart'][$sizedata['size'][1]]<$sizedata['chart'][$sizemax[1]]) {
                $check['size']['maximum']=1;
            }elseif ($sizedata['chart'][$sizedata['size'][1]]==$sizedata['chart'][$sizemax[1]]) {
                if ($sizedata['size'][0]<=$sizemax[0]) {
                    $check['size']['maximum']=1;
                }else{
                    $check['size']['maximum']='maximum size is '.$size[1];
                }
            }else{
                $check['size']['maximum']='maximum size is '.$size[1];
            }
        }else{
            $check['size']['maximum']=1;
        }

    }else{
        $check['size']['minimum'] = 1;
        $check['size']['maximum'] = 1;
    }

    if ($check['type']==1 && $check['size']['minimum']==1 && $check['size']['maximum']==1) {
        if(isset($data['save_name'])){
          $fnewname = $data['save_name'].'.'.$ftype;
        }else{
          $fnewname =uniqid('img_',true).'.'.$ftype;
        }
        $target_file = $target_dir. $fnewname;
        $data = array('name'=>$fnewname, 'path'=>$target_file, 'type'=>$ftype, 'size'=>join(" ",$sizedata['size']), 'error'=>$ferror);

        if(move_uploaded_file($tempfile, $target_file)){
            return array('status'=>1,'message'=>'file has been Upload!...','file'=>$data);
        }else{
            return array('status'=>0,'message'=>'File not Upload!...','file'=>$data);
        }
    }else{
        return array('status'=>0,'message'=>'File is not valid!...','file'=>$check);
    }
}

function fileDelete(array $data)
{
    $path = $data['file'];
    if (array_key_exists("target_dir",$data)){
        $target_dir = $data['target_dir'];
        if (substr($target_dir, -1)!='/') {
            $path =$target_dir.'/'.$path;
        }else{
            $path =$target_dir.$path;
        }
    }

    if(is_dir($path)){
        if (!unlink($path)) {  
            return array('status'=>0,'message'=>'File not Delete!...','file'=>$path);
        }  
        else {  
            return array('status'=>1,'message'=>'File has been Delete!...','file'=>$path);
        }
    }else{
        return array('status'=>0,'message'=>'File path not found!...','file'=>$path);
    }
}



?>