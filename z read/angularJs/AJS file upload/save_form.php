<?php

// include_once('../../../config.php');
// include_once('../../../senddata.php');

echo json_encode("return value ok!...");

// $servername = "localhost";
//      $username = "root";
//      $password = "";
//      $dbname = "include";

//      // Create connection
//      $conn = new mysqli($servername, $username, $password, $dbname);
//      // Check connection
//      if ($conn->connect_error) {
//        die("Connection failed: " . $conn->connect_error);
//      }else{
//           // print_r("Connection successfully :)");

     // }


     // $req = array();
     // $request = array();
     // $info = json_decode(file_get_contents("php://input"));
     // foreach ($info as $key => $value) {
     //      $req[] = $key;
     // }
     // foreach ($req as $key) {
     //      $request[$key] = mysqli_real_escape_string($conn , $info->$key);

     // }  
     // return $request;



     // $target_dir = "./upload/";
     // $name = $_POST['name'];
     // print_r($_FILES);
     // $target_file = $target_dir . basename($_FILES["file"]["name"]);

     // move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
     //write code for saving to database 
     // include_once "config.php";

     // // Create connection
     // $conn = new mysqli($servername, $username, $password, $dbname);
     // // Check connection
     // if ($conn->connect_error) {
     //    die("Connection failed: " . $conn->connect_error);
     // }

     // $sql = "INSERT INTO MyData (name,filename) VALUES ('".$name."','".basename($_FILES["file"]["name"])."')";

     // if ($conn->query($sql) === TRUE) {
     //     echo json_encode($_FILES["file"]); // new file uploaded
     // } else {
     //    echo "Error: " . $sql . "<br>" . $conn->error;
     // }

     // $conn->close();

?>