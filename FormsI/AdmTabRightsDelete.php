<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

 $FldNames=array('TabNo','Right','CanList'
      ,'CanEdit','CanCardReadOnly','CanDelete','CanXlsUpload');
$New=$_REQUEST['New'];
$PdoArr = array();
$TabNo=$_REQUEST['TabNo'];
if ($TabNo==''){ die ("<br> Error:  Empty TabNo");}
$PdoArr["TabNo"] = $TabNo;
$Right=$_REQUEST['Right'];
if ($Right==''){ die ("<br> Error:  Empty Right");}
$PdoArr["Right"] = $Right;

$dp=array();
  
  $query = "select * FROM \"AdmTabRights\" ".
           "WHERE (\"TabNo\"= :TabNo) AND (\"Right\"= :Right)";
  try{
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  else {
    die ("<br> Error: not found record (\"TabNo\"= :TabNo) AND (\"Right\"= :Right)"); 
  }
  $Editable=1;
  if (!$Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete FROM \"AdmTabRights\" ".
           "WHERE (\"TabNo\"= :TabNo) AND (\"Right\"= :Right)";
  
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

  $V=$_REQUEST['TabNo'];
  $LNK.="TabNo=$V";
  
  $V=$_REQUEST['Right'];
  $LNK.="&Right=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=AdmTabRightsList.php">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Deleted</H2>');
?>
</body>
</html>