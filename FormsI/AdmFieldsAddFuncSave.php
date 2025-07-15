<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('Id','TabName','FldName','AddFunc');

$New=$_REQUEST['New'];

$Id=$_REQUEST['Id'];


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


// AdmFieldsAddFunc
$FldsArr=array( 'Id', 'TabName', 'FldName', 'AddFunc', 'Param');
$PKArr=array( 'Id');

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
        $SE.= $Div.'($dp["'.$FN.'"]=='.$V.')';
        $Div=' OR ';
      }
      
      // AdmTabNames
      // TabName, TabDescription, TabCode, TabEditable, 
      // AutoCalc, CalcTableName, ChangeDt, Ver
      $PdoArr = array();
      $PdoArr['SE']= $SE.';';      
      $PdoArr['TabCode']= $TabCode;      
      $query = "update \"AdmTabNames\" set \"TabEditable\"=:SE ". 
               "where (\"TabCode\" = :TabCode)"; 

      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);

    }
  }
  else
  if ( $AddFunc == 15) {   //----------------------  Порядок перехода статусов
    
    $Txt = '';
    
    if (is_array($_REQUEST['PV'])) {
      $Txt = json_encode ($_REQUEST['PV']);  
    }
    $Upd=1;
  }

  if ($Upd==1) {
    $_REQUEST['Param']=$Txt;
  }
  else
  if ( $AddFunc == 30) {   // Автоматическая нумерация из какой серии номеров
    $Txt = addslashes ("[SerNo={$_REQUEST['Param30']}]");
    $Upd=1;
  }

  if ($Upd==1) {
    $_REQUEST['Param']=$Txt;
  }

  UpdateTable ($pdo, 'AdmFieldsAddFunc', $FldsArr, $_REQUEST, $PKArr, 1, 'Id');

}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
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