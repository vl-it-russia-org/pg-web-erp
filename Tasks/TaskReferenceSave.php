<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('Id','TaskId','ObjType','ObjName');
CheckTkn();

if (empty ($_REQUEST["TaskId"])) {
  $_REQUEST["TaskId"]=0;
}
$PkArr=array('Id');
$New=$_REQUEST['New'];
$PdoArr = array();
$Id=$_REQUEST['Id'];
if ($Id==''){ 
    if ($New==1) {  
      //$query = "select MAX(Id) MX FROM TaskReference ".
      //         "WHERE ";
      //$sql2 = $pdo->query ($query)
      //               or die("Invalid query:<br>$query<br>" . $pdo->error);
      //$MX=0;
      //if ($dp = $sql2->fetch_assoc()) {
      //  $MX=$dp['MX'];
      //}
      //$MX++;
      //$_REQUEST['Id']=$MX;
      //$Id=$MX;
    }
    else { die ("<br> Error:  Empty Id");}
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
  $Res = UpdateTable ($pdo, "TaskReference", $FldNames, $_REQUEST, $PkArr, 1, "Id");
//------------- Master Tab --------------------------
  
$MTab=array();
$PdoArr = array();

$query = "select * FROM \"Tasks\" ".
         "WHERE (\"Id\"=:Id)"; 
$PdoArr["Id"] = $TaskId;


  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  if ($MTab = $STH->fetch(PDO::FETCH_ASSOC)) {
  }

$PdoArr=array();

  $PdoArr["Id"]=$_REQUEST['TaskId'];
  $PdoArr["FrmTkn"]=MakeTkn(1);
  AutoPostFrm ("TasksCard.php", $PdoArr, 10);
  echo('<H2>Saved</H2>');
?>
</body>
</html>