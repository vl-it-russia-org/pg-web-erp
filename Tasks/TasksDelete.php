<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
$Editable = CheckFormRight($pdo, 'Tasks', 'Delete');

$FldNames=array('Id','ShortName','Created'
      ,'StartDate','Author','Division','Priority'
      ,'Description','WishDueDate','PlannedWorkload','FactWorkLoad'
      ,'PlannedDueDate','Status','RespPerson','UserSatisfaction'
      ,'WaitTill','TaskGroup','SFProject','TaskYearCode');
$New=$_REQUEST['New'];
$PdoArr = array();
$Id=$_REQUEST['Id'];
if ($Id==''){ die ("<br> Error:  Empty Id");}
$PdoArr["Id"] = $Id;

$dp=array();
  
  $query = "select * FROM \"Tasks\" ".
           "WHERE (\"Id\"= :Id)";
  try{
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  else {
    die ("<br> Error: not found record (\"Id\"= :Id)"); 
  }
  $Editable=($dp["Status"]==0);
  if (!$Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete FROM \"Tasks\" ".
           "WHERE (\"Id\"= :Id)";
  
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

  $V=$_REQUEST['Id'];
  $LNK.="Id=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=TasksList.php">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Deleted</H2>');
?>
</body>
</html>