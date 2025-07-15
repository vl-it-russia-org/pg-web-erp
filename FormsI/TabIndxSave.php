<?php
session_start();

include ("../setup/common_pg.php");

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>Tab index save</title></head>
<?php

CheckLogin1();

CheckRight1 ($pdo, 'Admin');


//print_r ($_REQUEST);
//die();

$TabName='AdmTabIndx';
$Frm='Tab';


$TabNo= $_REQUEST['TabCode'];
$IndxType= $_REQUEST['IndxType'];
$IndxName= $_REQUEST['IndxName'];
$IndxOldName= $_REQUEST['IndxOldName'];

$PdoArr = array();
$PdoArr['IndxName']= $IndxName;
$PdoArr['TabNo']= $TabNo;
$PdoArr['IndxType']= $IndxType;

try {



if ($_REQUEST['New'] !='1' ) {
  $PdoArr['IndxOldName']= $IndxOldName;
  
  $Proc='Updated';
  $query="update \"$TabName\" set \"IndxType\"=:IndxType,\"IndxName\"=:IndxName ";
  $query.=" where (\"TabCode\"=:TabNo) AND (\"IndxName\"=:IndxOldName)";
}
else {
  $Str1='"TabCode","IndxType","IndxName"';
  $Str2=":TabNo, :IndxType, :IndxName";
  $query="insert into $TabName ($Str1) values ($Str2)";
}

//echo ($query);
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable, 
// AutoCalc, CalcTableName, ChangeDt, Ver
$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

$query = "update \"AdmTabNames\" set  \"ChangeDt\"=now() ". 
         "where (\"TabCode\" = :TabNo)"; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);



echo('
<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$Frm.'Card.php?TabCode='.
     $TabNo.'#Indx_'.$IndxName.'">');

echo ('<body><br><b>'.GetStr($pdo, 'Edit'). '</b> ') ;
echo ($Proc);

}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}


?>
</body>
</html>
				       