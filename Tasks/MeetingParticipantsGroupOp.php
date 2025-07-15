<?php
session_start();

include "../setup/common_pg.php";
BeginProc();
OutHtmlHeader ("MeetingParticipants Group Opers");

$Editable = CheckFormRight($pdo, 'MeetingParticipants', 'Card');
CheckTkn();

//print_r($_REQUEST);
//die();


$PdoArr = array();
try {

if (is_array($_REQUEST['Chk'])) { 
  $Res='';
  $Div='';
  foreach ( $_REQUEST['Chk'] as $Indx=> $Val) {
    
    $PKValArr=json_decode(base64_decode($Val), true);
      
    $PdoArr["MId_$Indx"]=$PKValArr["MId"];
    $V=":MId_$Indx";
    $PdoArr["LineNo_$Indx"]=$PKValArr["LineNo"];
    $V.=",:LineNo_$Indx";
    $Res.="$Div($V)";
    $Div=",";
  }

  if ($_REQUEST['OpType']== GetStr($pdo, 'Delete')) {
    $Editable = CheckFormRight($pdo, 'MeetingParticipants', 'MassDelete');
    
    $query = "delete from \"MeetingParticipants\" ".
             " WHERE ( (\"MId\",\"LineNo\") in ($Res) )";
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  }  
    
}

}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=MeetingParticipantsList.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>