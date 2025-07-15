<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
OutHtmlHeader ("{$TabName}-Change$Fld");

$Delay=10;
$Editable = CheckFormRight($pdo, 'Tasks', 'Card');
CheckTkn();
$FldNames=array('Id', 'Status');

// Tasks Status TaskStatus
$PossibleStatus=array ( 
  // 0 - Новый
  0=> array ( 3=>1, 5=>1, 7=>1),
  // 3 - К выполнению
  3=> array ( 0=>1, 7=>1, 10=>1),
  // 5 - Анализируется
  5=> array ( 0=>1, 7=>1, 10=>1),
  // 7 - Есть вопросы
  7=> array ( 0=>1),
  // 10 - Выполняется
  10=> array ( 7=>1, 12=>1, 20=>1),
  // 12 - Проверка пользователем
  12=> array ( 15=>1, 20=>1),
  // 15 - Завершено
  15=> array ( 0=>1),
  // 20 - Отложено
  20=> array ( 0=>1));
$PdoArr = array();
if(empty($_REQUEST["Id"])) {
  die ("Error: empty Id");
}
$Id=$_REQUEST["Id"];
$PdoArr["Id"]= $Id;



if ($_REQUEST["NewStatus"]=="") {
  die ("<br> Error: New status is empty");
}

$NewStatus=$_REQUEST["NewStatus"];
$EnName = "TaskStatus";

$NewStatusTxt= GetEnum($pdo, $EnName,$NewStatus); 

$CurrStatus=0;
$CurrStatusTxt="";
try{
  $query = "select * FROM \"Tasks\" ".
           "WHERE (\"Id\"=:Id)";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CurrStatus = $dp["Status"];
    $CurrStatusTxt= GetEnum($pdo, $EnName,$CurrStatus); 
  }
  else {
    echo ("<br> PDO: ");
    print_r($PdoArr);
    die ("<br> Error: record Tasks is not found ");
  }

  if ($CurrStatus==$NewStatus) {

    AutoPostFrm ( "TasksCard.php", $PdoArr, 2000);
    echo ("<h2> Now Status = $CurrStatusTxt </h2>");
  }
  else {

  if ( $PossibleStatus[$CurrStatus][$NewStatus]==1) {
    $CheckOk = 0;
    $AddUpd='';

    if ($CurrStatus == 0) {   // 0 - Новый
        //print_r($dp);
        //die();
        // Проверка заполнения полей: 
        $ErrorsStr='';
        if (mb_strlen($dp['ShortName'])<10) {
          $ErrorsStr.="<br> Error: ".GetStr($pdo, 'ShortName').": ".GetStr($pdo, 'Exp10Str');
        }
        
        if (empty($dp['Author'])) {
          $ErrorsStr.="<br> Error: ".GetStr($pdo, 'Author').": ".GetStr($pdo, 'IsEmpty');
        }

        if (empty($dp['Division'])) {
          $ErrorsStr.="<br> Error: ".GetStr($pdo, 'Division').": ".GetStr($pdo, 'IsEmpty');
        }

        
        if ($dp['WishDueDate']< '2005-01-01') {
          $ErrorsStr.="<br> Error: ".GetStr($pdo, 'WishDueDate').": ".GetStr($pdo, 'NotSetup');
        }

        if (!empty($ErrorsStr)) {
          echo ( $ErrorsStr) ;
          $Delay=5;
        }
        else {
          if ($dp['StartDate']<'2005-01-01') {
            $PdoArr['StartDate']= date("Y-m-d");
            $AddUpd.=", \"StartDate\"=:StartDate ";  
          }
          $CheckOk=1;
        }
    }
    else 
    if ($CurrStatus == 3) {   // 3 - К выполнению
        $CheckOk=1;
    }
    else 
    if ($CurrStatus == 5) {   // 5 - Анализируется
        $CheckOk=1;
    }
    else 
    if ($CurrStatus == 7) {   // 7 - Есть вопросы
        $CheckOk=1;
    }
    else 
    if ($CurrStatus == 10) {   // 10 - Выполняется
        $CheckOk=1;
    }
    else 
    if ($CurrStatus == 12) {   // 12 - Проверка пользователем
        $CheckOk=1;
    }
    else 
    if ($CurrStatus == 15) {   // 15 - Завершено
        $CheckOk=1;
    }
    else 
    if ($CurrStatus == 20) {   // 20 - Отложено
        $CheckOk=1;
    }
    else       $CheckOk=0;
    if ($CheckOk==1) {
      MakeAdminRec ($pdo, $_SESSION["login"], "Tasks", "STS-CH", 
                        $NewStatus, "Status change $CurrStatusTxt -> $NewStatusTxt");

      $PdoArr["NewStatus"]=$NewStatus;

      $query = "update \"Tasks\" set \"Status\"=:NewStatus$AddUpd ".
               "WHERE (\"Id\"=:Id)";

      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);  
    }
  }
  else {
    die ("<br> Error: Possible status is not ok");
  }
  }
}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);
  die ("<br> Error: ".$e->getMessage());
}

$FrmTkn = MakeTkn(1);
$PdoArr["FrmTkn"]=$FrmTkn;

AutoPostFrm ("TasksCard.php", $PdoArr, $Delay);

?>
</body>
</html>
