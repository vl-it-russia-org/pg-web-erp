<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

 $FldNames=array('BankId','Description','Country'
      ,'BIK','BankName','City','TransitAccount'
      ,'AccountNo','Currency');
$New=$_REQUEST['New'];
$PdoArr = array();
$BankId=$_REQUEST['BankId'];
if ($BankId==''){ die ("<br> Error:  Empty BankId");}
$PdoArr["BankId"] = $BankId;

$dp=array();
  
  $query = "select * FROM \"BankAccs\" ".
           "WHERE (\"BankId\"= :BankId)";
  try{
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  else {
    die ("<br> Error: not found record (\"BankId\"= :BankId)"); 
  }
  $Editable=1;
  if (!$Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete FROM \"BankAccs\" ".
           "WHERE (\"BankId\"= :BankId)";
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

$LNK='';

  $V=$_REQUEST['BankId'];
  $LNK.="BankId=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=BankAccsList.php">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Deleted</H2>');
?>
</body>
</html>