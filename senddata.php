<?php

function echoError($value)
{
    print_r($value);
    exit();
}

function echoMsg($value)
{
    print_r($value);
}

function echoData($data){
    $row_array['status'] = $data[0];
    $row_array['message'] = $data[1];
    $row_array['data'] = $data[2];
    $json = json_encode($row_array);
    echo $json;
    exit();
}

function echoJson($data){
    $json = json_encode($data,JSON_NUMERIC_CHECK);
    echo $json;
    exit();
}

function echoAr($array=''){
        print"<pre>\n";
        print_r($array);
        print"\n</pre>";
    }

function extractJson($case='')
{
    global $conn;
    $req = array();
    $request = array();
    $info = json_decode(file_get_contents("php://input"));
    foreach ($info as $key => $value) {
         $req[] = $key;
    }
    foreach ($req as $key) {
         $value = mysqli_real_escape_string($conn , $info->$key);;
         if ($case!=''){
            $value = $case($value);
         }
         $request[$key] = $value;
    }  
    return $request;
}
