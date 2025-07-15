<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
$Editable = CheckFormRight($pdo, 'MeetingParticipants', 'Delete');

$FldNames=array('MId','LineNo','PartType'
      ,'EMail','LastName','FirstName','MidName'
      ,'IsHost');
$New=$_REQUEST['New'];
$PdoArr = array();
$MId=$_REQUEST['MId'];
if ($MId==''){ die ("<br> Error:  Empty MId");}
$PdoArr["MId"] = $MId;
$LineNo=$_REQUEST['LineNo'];
if ($LineNo==''){ die ("<br> Error:  Empty LineNo");}
$PdoArr["LineNo"] = $LineNo;

$dp=array();
  
  $query = "select * FROM \"MeetingParticipants\" ".
           "WHERE (\"MId\"= :MId) AND (\"LineNo\"= :LineNo)";
  try{
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  else {
    die ("<br> Error: not found record (\"MId\"= :MId) AND (\"LineNo\"= :LineNo)"); 
  }
  
  if (!$Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete FROM \"MeetingParticipants\" ".
           "WHERE (\"MId\"= :MId) AND (\"LineNo\"= :LineNo)";
  
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

  $V=$_REQUEST['MId'];
  $LNK.="MId=$V";
  
  $V=$_REQUEST['LineNo'];
  $LNK.="&LineNo=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=MeetingParticipantsList.php">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Deleted</H2>');
?>
</body>
</html>