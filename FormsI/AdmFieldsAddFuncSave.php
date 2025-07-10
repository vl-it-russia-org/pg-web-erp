<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('Id','TabName','FldName','AddFunc');

$New=addslashes($_REQUEST['New']);

$Id=addslashes($_REQUEST['Id']);
if ($Id==''){ 
    if ($New==1) {  
      //$query = "select MAX(Id) MX FROM AdmFieldsAddFunc ".
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

//echo ("<br>");
//print_r($_REQUEST);
//die();

// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable, 
// AutoCalc, CalcTableName, ChangeDt, Ver
$TabCode='';
$TN= $_REQUEST['TabName'];


try{
$PdoArr = array();
$PdoArr['TN']= $TN;

$query = "select * from \"AdmTabNames\" ". 
         "where (\"TabName\" = :TN)"; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

if ($dp22 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $TabCode=$dp22['TabCode'];
}


// AdmTabFields
// TypeId, ParamNo, ParamName, NeedSeria, 
// DocParamType, NeedBrand, Ord, AddParam, DocParamsUOM, 
// CalcFormula, AutoInc, Description, BinCollation, ShortInfo, 
// EnumLong
$FldCode='';
$FN= $_REQUEST['FldName'];

$query = "select * from \"AdmTabFields\" ". 
         "where (\"TypeId\"=:TabCode)and (\"ParamName\"= :FN) "; 
$PdoArr = array();
$PdoArr['TabCode']= $TabCode;
$PdoArr['FN']= $FN;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

if ($dp22 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FldCode=$dp22['ParamNo'];
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
  $query = "select * FROM \"AdmFieldsAddFunc\" ".
           "WHERE (\"Id\"='$Id')";
  $sql2 = $pdo->query ($query)
                 or die("Invalid query:<br>$query<br>" . $pdo->error);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    if ($New==1){
      echo ("<br>");
      print_r($dp);
      die ("<br> Error: Already have record ");
    }

    $Editable=1;
    if (!$Editable) {
      die ("<br> Error: Not Editable record ");
    }      
  }
  
  if ($New==1){
    
    
    $q='insert into AdmFieldsAddFunc(';
    $S1='';
    $S2='';
    $Div='';

    foreach ($FldNames as $F) {
      $V=addslashes ($_REQUEST[$F]);
      $S1.=$Div.$F;
      $S2.="$Div'$V'";
      $Div=', ';
    }
    $q.=$S1.') values ('.$S2.')';
    
    $sql2 = $pdo->query ($q)
                 or die("Invalid query:<br>$q<br>" . $pdo->error);
    $Id= $pdo->insert_id ;
    $_REQUEST['Id']= $Id;
}
  else {
    $q='update AdmFieldsAddFunc set ';
    $S1='';
    $Div='';

    foreach ($FldNames as $F) {
      $V1=$_REQUEST[$F];
      $V=addslashes ($_REQUEST[$F]);
      if ( $V1 != $dp[$F]) {
        $S1.=$Div.$F."='$V'";
        $Div=', ';
      }
    }
    if ( $S1 != '' ) {
      $q.=$S1.' WHERE ';
      
      $S1='';

$V=addslashes ($_REQUEST['OldId']);
      $S1.="(Id='$V')";
  
      $q.= $S1;
      $sql2 = $pdo->query ($q)
                 or die("Invalid query:<br>$q<br>" . $pdo->error);
  
    }
  }

  //=======================================================================
  $AddFunc = $_REQUEST['AddFunc'];
  $Txt='';
  $Upd=0;

  if ( $AddFunc == 10) {   // Редактируемость
    $Var=';';
    
    $ArrList=array();
    
    foreach ($_REQUEST as $Param => $V) {
      if (strpos ($Param, 'Param10_Sel_') !== false) {
        $Var.=$V.';' ;
        $ArrList[$V]=1;
      }
    }

    if ( $Var!=';') {
      $Txt = addslashes ("[Editable={$Var}]");
    }
    $Upd=1;

    if ($_REQUEST['SetEditable']==1) {

      $SE='$Editable=';
      $Div='';

      foreach ($ArrList as $V=> $D) {
        $SE.= $Div.'($dp["'.$FN.'"]='.$V.')';
        $Div=' and ';
      }
      
      // AdmTabNames
      // TabName, TabDescription, TabCode, TabEditable, 
      // AutoCalc, CalcTableName, ChangeDt, Ver
      $SE1=addslashes($SE);
      
      $query = "update AdmTabNames set TabEditable='$SE1;' ". 
               "where (TabCode = '$TabCode')"; 

      $sql27 = $pdo->query ($query) 
                or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
    }
  }
  else
  if ( $AddFunc == 30) {   // Автоматическая нумерация из какой серии номеров
    $Txt = addslashes ("[SerNo={$_REQUEST['Param30']}]");
    $Upd=1;
  }

  if ($Upd==1) {

    // AdmFieldsAddFunc
    // Id, TabName, FldName, AddFunc
    $query = "update AdmFieldsAddFunc set Param='$Txt' ". 
             "where (Id = '$Id')"; 

    $sql27 = $pdo->query ($query) 
              or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);
  }
  //=======================================================================
  $LNK="TypeId=$TabCode&FldNo=$FldCode";

// TabFldCard.php?TypeId=7000&FldNo=70
  
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=TabFldCard.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>