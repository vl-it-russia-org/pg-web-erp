<?php
session_start();

include ("../setup/common.php");

CheckLogin1 ();
CheckRight1 ($mysqli, 'ExtProj.Admin');

//print_r($_REQUEST);

$FldNames=array('TabName','FldName','LineNo','TabCond','Tab2','Field2',
                'CondTab2','AddFldsListTo','AddFldsListFrom', 'AddConnFldTo','AddConnFldFrom');

$New=addslashes($_REQUEST['New']);
$TabName=addslashes($_REQUEST['TabName']);
if ($TabName==''){ die ("<br> Error:  Empty $TabName");}
$FldName=addslashes($_REQUEST['FldName']);
if ($FldName==''){ die ("<br> Error:  Empty $FldName");}
$LineNo=addslashes($_REQUEST['LineNo']);
if ($LineNo==''){ 
  if ($New!=1)
    die ("<br> Error:  Empty $LineNo");
}

$dp=array();
$query = "select * ".
       "FROM AdmTab2Tab ".
       " WHERE (TabName='$TabName') AND (FldName='$FldName') AND (LineNo='$LineNo')";
$sql2 = $mysqli->query ($query)
               or die("Invalid query:<br>$query<br>" . $mysqli->error);

if ($dp = $sql2->fetch_assoc()) {
  $query = "delete from AdmTab2Tab ".
           "WHERE (TabName='$TabName') AND (FldName='$FldName') AND (LineNo='$LineNo')";
  $sql2 = $mysqli->query ($query)
                 or die("Invalid query:<br>$query<br>" . $mysqli->error);
}

$LNK='';

  $V=$_REQUEST['TabName'];
  $LNK.="TabName=$V";
  
  $V=$_REQUEST['FldName'];
  $LNK.="&FldName=$V";
  
  $V=$_REQUEST['LineNo'];
  $LNK.="&LineNo=$V";

  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=AdmTab2TabCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>