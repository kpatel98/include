<?php
$html='<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
            @import url("https://fonts.googleapis.com/css2?family=Hind+Vadodara:wght@300&display=swap");
table, td, th {
  border: 1px solid black;
}

table {
  border-collapse: collapse;
}
table {                             
   width:530 !important;                         
   text-align:center !important;                 
     table-layout:fixed !important;
     border-collapse: collapse;           
 } 

 td {                                 
     word-wrap:break-word !important;

 }
    </style>
</head>
<body>
    <table>
        <caption>
            સોદા બુક માહિતી
        </caption>


        <tr>
            <td colspan="3">
                <h4>દલાલ : -- </h4>
                <h4 id="billingname"><br><br></h4>
            </td>
            <td colspan="3">
                <h4>લેનાર : -- </h4>
                <h4 id="billingname"><br><br></h4>
            </td>
            <td colspan="5">
                <h4>વેચનાર : -- </h4>
                <h4 id="customername"><br><br></h4>
            </td>
        </tr>


        <tr>
            <th>તારીખ</th>
            <th>મોબાઈલ નંબર</th>
            <th>ગામ</th>
            <th>વેરાયટી</th>
            <th>ભાવ</th>
            <th>ટોટલ ગુણી</th>
            <th>કન્ડીસન</th>
            <th>રિકવરી</th>
            <th>પેમેન્ટ કન્ડીસન</th>
            <th>આવક ગુણી</th>
            <th>નોધ</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>


    </table>

</body>
</html>';



// $html = $html2;
require 'vendor/autoload.php';
use Dompdf\Dompdf;

// generate pdf not save file
function generatePDF(array $data)
{
	if (array_key_exists("set_paper",$data)){
    	$set_paper = explode(',', $data['set_paper']);
    }else{
    	$set_paper = array('A4','portrait');
    }

	$dompdf= new Dompdf();

	$dompdf->loadHtml($data['html']);

	$dompdf->setPaper($set_paper[0],$set_paper[0]);

	$dompdf->render();

	$dompdf->stream("playerofcode",array("Attachment"=>0));
}

// generate pdf and save file
function generatePDFFile(array $data)
{
	$path='';
	if (array_key_exists("target_dir",$data)){
        $target_dir = $data['target_dir'];
        if (substr($target_dir, -1)!='/') {
            $path = $target_dir.'/';
        }else{
            $path = $target_dir;
        }
    }

    if (array_key_exists("set_paper",$data)){
    	$set_paper = explode(',', $data['set_paper']);
    }else{
    	$set_paper = array('A4','portrait');
    }

    

	$dompdf= new Dompdf();

	$dompdf->loadHtml($data['html']);

	$dompdf->setPaper($set_paper[0],$set_paper[1]);

	$dompdf->render();

	$output = $dompdf->output();
	$name = uniqid('pdf_',true).'.pdf';
	$path = $path.$name;
    $result=file_put_contents($path, $output);
    // echo $result;
    if ($result!=null) {
    	return array('status'=>1,'message'=>'PDF is created!...','file'=>$path);
    }else{
    	return array('status'=>0,'message'=>'PDF is not created!...','file'=>null);
    }
}
// generatePDFFile($html);
// echo "generate PDF";

?>