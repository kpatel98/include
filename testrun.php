<?php


/////////////////////////////////////////////////////////////////////////////
// 
// insert edit select delete
// 
/////////////////////////////////////////////////////////////////////////////
include_once('config.php');
include_once('senddata.php');


/////////////////////////////////////////////////////////////////////////////
// insert
/////////////////////////////////////////////////////////////////////////////

// insert record 
function userInsert($db){

	extract($_REQUEST);
	$data	=	array(
					'uname' => $name,
					'uemail' => $email,
					'upassword' => $password,
					);
	// insert($tableName, array $data)
	$data	=	$db->insert('users',$data);
	if($data){
		echoData(array('1','Record has been Insert!...',$data));
	}else{
		echoData(array('0','Please try again Insert!...',$data));
	}
}

// insert record when request parameter and table parameter both are same
function userInsertAll()
{
	$data = array(
				'request' => $_REQUEST,
				'table' => 'users',
				'base_name' =>'u',
				);
	$data = insertAll($data);
	if($data){
		echoData(array('1','Record has been Insert!...',$data));
	}else{
		echoData(array('0','Please try again Insert!...',$data));
	}
}

/////////////////////////////////////////////////////////////////////////////
// Select Record
/////////////////////////////////////////////////////////////////////////////

// get all record with parameter
function userSelect($db){

	extract($_REQUEST);
	// getAllRecords($tableName, $fields='*', $cond='', $orderBy='', $limit='')
	$data = $db->getAllRecords("users","*","AND uemail='$email' AND upassword='$password'","ORDER BY uid DESC");
	if (count($data)>0) {
		echoData(array('1','Record found!...',$data));
	}else{
		echoData(array('0','No Record found!...',$data));
	}
}

// get record with Query
function userSelectQry($db){

	extract($_REQUEST);
	// getRecFrmQry($query)
	$data = $db->getRecFrmQry("select * from users where 1");
	if (count($data)>0) {
		echoData(array('1','Record found!...',$data));
	}else{
		echoData(array('0','No Record found!...',$data));
	}
}

// count record
function userCountQry($db){

	extract($_REQUEST);
	// getQueryCount($tableName, $field, $cond='')
	$data = $db->getQueryCount("users","uname","AND uname='kpatel'");
	if (count($data)>0) {
		echoData(array('1','Record found!...',$data));
	}else{
		echoData(array('0','No Record found!...',$data));
	}
}


/////////////////////////////////////////////////////////////////////////////
// Update
/////////////////////////////////////////////////////////////////////////////
function userUpdate($db){

	extract($_REQUEST);
	$data	=	array(
					'uid' => $id,
					'uname' => $name,
					'uemail' => $email,
					'upassword' => $password,
					);
	// update($tableName, array $set, array $where)
	$data = $db->update("users",$data,array('uid'=>1));
	if($data>0){
		echoData(array('1','Record has been Update!...',$data));
	}else{
		echoData(array('0','Record is not edit or Error!...',$data));
	}
}

// update using query
function userUpdateQry($db){

	extract($_REQUEST);
	$data	=	array(
					'uid' => $id,
					'uname' => $name,
					'uemail' => $email,
					'upassword' => $password,
					);
	// update($tableName, array $set, $where)
	$data = $db->update("users",$data,'uid=1 and password=123');
	if($data>0){
		echoData(array('1','Record has been Update!...',$data));
	}else{
		echoData(array('0','Record is not edit or Error!...',$data));
	}
}

// update record when request parameter and table parameter both are same
function userUpdateAll()
{
	$data = array(
				'request' => $_REQUEST,
				'table' => 'users',
				'base_name' =>'u',
				'condition' => array('upassword'=>123),
				);
	$data = updateAll($data);
	if($data>0){
		echoData(array('1','Record has been Update!...',$data));
	}else{
		echoData(array('0','Record is not edit or Error!...',$data));
	}
}

// update record when request parameter and table parameter both are same with query
function userUpdateAllQry()
{
	$data2 = array(
				'request' => $_REQUEST,
				'table' => 'users',
				'base_name' =>'u',
				'condition' => 'uid=1',
				);
	$data = updateAllQry($data2);
	if($data>0){
		echoData(array('1','Record has been Update!...',$data));
	}else{
		echoData(array('0','Record is not edit or Error!...',$data));
	}
}


/////////////////////////////////////////////////////////////////////////////
// Delete
/////////////////////////////////////////////////////////////////////////////

// delete with parameter
function userDelete($db){

	extract($_REQUEST);
	$data	=	array(
					'uid' => $id,
					);
	// delete($tableName, array $where)
	$data	=	$db->update("users",$data);
	if($data){
		echoData(array('1','Record has been Delete!...',$data));
	}else{
		echoData(array('0','Please try again Delete!...',$data));
	}
}

// delete with Query
function userDeleteQry($db){

	extract($_REQUEST);
	$data	=	array(
					'uid' => $id,
					);
	// deleteQry($query)
	$data	=	$db->deleteQry("DELETE FROM users WHERE uid=1");
	if($data){
		echoData(array('1','Record has been Delete!...',$data));
	}else{
		echoData(array('0','Please try again Delete!...',$data));
	}
}

// $_REQUEST['f']($db);

// camelCaseToUnderscore($string);
// arprint($array)
// getModelMake($makeID,$nameID)
// getCache($sql,$cache_min=0)
// formatSizeUnits($bytes)
// get($tableName,  $whereAnd  =   array(), $whereOr   =   array(), $whereLike =   array())

function columnMatch($db)
{
	$data = $db->getColumn('users');
	echoAr($_REQUEST);

	// if (str_contains('How are you', 'are')) { 
 //    echo 'true';
	// }
}

columnMatch($db);


// SELECT `COLUMN_NAME` 
// FROM `INFORMATION_SCHEMA`.`COLUMNS` 
// WHERE `TABLE_SCHEMA`='include' 
//     AND `TABLE_NAME`='users';