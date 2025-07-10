<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('BankId','Description','Country','BIK'
      ,'BankName','City','TransitAccount','AccountNo','Currency');
$PkArr=array('BankId');
$New=$_REQUEST['New'];
$PdoArr = array();
$BankId=$_REQUEST['BankId'];
if ($BankId==''){ 
    if ($New==1) {  
      //$query = "select MAX(BankId) MX FROM BankAccs ".
      //         "WHERE ";
      //$sql2 = $pdo->query ($query)
      //               or die("Invalid query:<br>$query<br>" . $pdo->error);
      //$MX=0;
      //if ($dp = $sql2->fetch_assoc()) {
      //  $MX=$dp['MX'];
      //}
      //$MX++;
      //$_REQUEST['BankId']=$MX;
      //$BankId=$MX;
    }
    else { die ("<br> Error:  Empty BankId");}
}


  //---------------------------- Для автонумерации ---------------
  //include ("NumSeq.php");
  //if($_REQUEST['DocNo']=='') {
  //  $D=$_REQUEST['OpDate'];
  //  if ($D=='') {
  //    $_REQUEST['OpDate']=date('Y-m-d');
  //    $D=$_REQUEST['OpDate'];
  //  }
  //  $_REQUEST['DocNo'] = GetNextNo ( $pdo, 'BankOp', $D);
  //}


  $dp=array();
  $Res = UpdateTable ($pdo, "BankAccs", $FldNames, $_REQUEST, $PkArr, 1, "");
$LNK='';

  $V=$_REQUEST['BankId'];
  $LNK.="BankId=$V";
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=BankAccsCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>