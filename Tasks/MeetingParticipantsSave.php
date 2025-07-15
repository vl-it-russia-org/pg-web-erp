<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('MId','LineNo','PartType','EMail'
      ,'LastName','FirstName','MidName','IsHost');
CheckTkn();

if (empty ($_REQUEST["MId"])) {
  $_REQUEST["MId"]=0;
}

if (empty ($_REQUEST["LineNo"])) {
  $_REQUEST["LineNo"]=0;
}

if (empty ($_REQUEST["IsHost"])) {
  $_REQUEST["IsHost"]=0;
}
$PkArr=array('MId', 'LineNo');
$New=$_REQUEST['New'];
$PdoArr = array();
$MId=$_REQUEST['MId'];
if ($MId==''){ die ("<br> Error:  Empty MId");}
$PdoArr['MId']= $MId;
$LineNo=$_REQUEST['LineNo'];
if ($LineNo==''){ 
    if ($New==1) {  
      $query = "select MAX(\"LineNo\") \"MX\" FROM \"MeetingParticipants\" ".
               "WHERE (\"MId\"=:MId)";
      
      $sql2 = $pdo->query ($query)
                     or die("Invalid query:<br>$query<br>" . $pdo->error);
      $MX=0;
      if ($dp = $sql2->fetch_assoc()) {
        $MX=$dp['MX'];
      }
      $MX++;
      $_REQUEST['LineNo']=$MX;
      $LineNo=$MX;
    }
    else { die ("<br> Error:  Empty LineNo");}
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
  $Res = UpdateTable ($pdo, "MeetingParticipants", $FldNames, $_REQUEST, $PkArr, 1, "");
//------------- Master Tab --------------------------
  
$MTab=array();
$PdoArr = array();

$query = "select * FROM \"Meetings\" ".
         "WHERE (\"Id\"=:Id)"; 
$PdoArr["Id"] = $MId;


  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  if ($MTab = $STH->fetch(PDO::FETCH_ASSOC)) {
  }

$PdoArr=array();

  $PdoArr["Id"]=$_REQUEST['MId'];
  $PdoArr["FrmTkn"]=MakeTkn(1);
  AutoPostFrm ("MeetingsCard.php", $PdoArr, 10);
  echo('<H2>Saved</H2>');
?>
</body>
</html>