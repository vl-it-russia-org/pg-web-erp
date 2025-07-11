<?php
session_start();

include ("../setup/common_pg.php");
include ("common_func.php");

BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('Id','TabName','AddFunc');

$PdoArr = array();

try{

$New=$_REQUEST['New'];
$Id=$_REQUEST['Id'];


//print_r($_REQUEST);
//die();

if ($Id==''){ 
    if ($New==1) {  
      //$query = "select MAX(Id) MX FROM AdmTabsAddFunc ".
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

if (empty($_REQUEST['TabName'])) {
  die ("<br> Error: TabName is empty ");
} 


// AdmTabNames
// TabName, TabDescription, TabCode, TabEditable, 
// AutoCalc, CalcTableName, ChangeDt, Ver
$HeadRec=array();
$TabName=$_REQUEST['TabName'];


$PdoArr['TabName']= $TabName;

$TabCode='';
$query = "select * from \"AdmTabNames\" ". 
         "where (\"TabName\" = :TabName)"; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

if ($HeadRec = $STH->fetch(PDO::FETCH_ASSOC)) {
  $TabCode=$HeadRec['TabCode'];
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

$FldNames=array('Id', 'TabName', 'AddFunc', 'Param');
$PkArr=array('Id');

if ($_REQUEST['AddFunc']==10) {
  // MasterTab
  if ( $_REQUEST['MasterTabName']!='') {
    $_REQUEST['Param'] = "[MasterTab=".$_REQUEST['MasterTabName'].']';
  }
}
  else
  if ($_REQUEST['AddFunc']==20) {
    // MasterTab fileds correspondence
    $FldsCorr=array();

    foreach ($_REQUEST['FldT'] as $Indx => $CorrIndx) {
      if ($CorrIndx) {
        $FldsCorr[$CorrIndx]['FldT']=$Indx; 
      }
    }

    foreach ($_REQUEST['FldM'] as $Indx => $CorrIndx) {
      if($CorrIndx) {  
      
        $FldsCorr[$CorrIndx]['FldM']=$Indx; 
      }
    }


    //echo ("<hr>FldsCorr= ");
    //print_r($FldsCorr);
    //echo ("<hr>");

    $MasterTab = GetMasterTabName($pdo, $TabName);
    $MTabCode =GetTabCode ($pdo, $MasterTab); 

    
    $Txt='';

    foreach ($FldsCorr as $Indx => $Arr) {
      if (empty ($Arr['FldT']) ) {
        die ("<br> For $Idnx tie do not found Field from correspondence"); 
      }

      if (empty ($Arr['FldM']) ) {
        die ("<br> For $Idnx tie do not found Field Master table correspondence"); 
      }

      $FldT= GetFldName ($pdo, $TabCode, $Arr['FldT']);
      $FldM= GetFldName ($pdo, $MTabCode, $Arr['FldM']);

      $Txt.= "[FldT=$FldT;FldM=$FldM]";
    }
    $Upd=1;

    echo ("<br>Txt=$Txt<br>");
    $_REQUEST['Param']=$Txt;
    //==========================================================================

  }
  else
  if ($_REQUEST['AddFunc']==30) {
    // MasterTab fileds COPY
    $FldsCorr=array();

    foreach ($_REQUEST['FldT'] as $Indx => $CorrIndx) {
      if ($CorrIndx) {
        $FldsCorr[$CorrIndx]['FldT']=$Indx; 
      }
    }

    foreach ($_REQUEST['FldM'] as $Indx => $CorrIndx) {
      if($CorrIndx) {  
      
        $FldsCorr[$CorrIndx]['FldM']=$Indx; 
      }
    }


    //echo ("<hr>FldsCorr= ");
    //print_r($FldsCorr);
    //echo ("<hr>");

    $MasterTab = GetMasterTabName($pdo, $TabName);
    $MTabCode =GetTabCode ($pdo, $MasterTab); 

    
    $Txt='';

    foreach ($FldsCorr as $Indx => $Arr) {
      if (empty ($Arr['FldT']) ) {
        die ("<br> For $Idnx tie do not found Field from correspondence"); 
      }

      if (empty ($Arr['FldM']) ) {
        die ("<br> For $Idnx tie do not found Field Master table correspondence"); 
      }

      $FldT= GetFldName ($pdo, $TabCode, $Arr['FldT']);
      $FldM= GetFldName ($pdo, $MTabCode, $Arr['FldM']);

      $Txt.= "[FldT=$FldT;FldM=$FldM]";
    }
    $Upd=1;

    echo ("<br>Txt=$Txt<br>");
    $_REQUEST['Param']=$Txt;
    
    //==========================================================================

  }


ECHO ("<hr>");
print_r($_REQUEST);
ECHO ("<hr>");

$Res = UpdateTable ($pdo, "AdmTabsAddFunc", $FldNames, $_REQUEST, $PkArr, 1, "Id");
  

$LNK='';
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<title>AdmTabsAddFunc Save</title></head>
<body>');
  echo('<H2>AdmTabsAddFunc Saved</H2>');


  echo('<form id="autoForm" method="post" action="TabCard.php" style="display: none;">');
  echo("<input type=hidden name=TabCode value='$TabCode'>\r\n");

  MakeTkn();
  echo (" </form>

            <script>
                // Автоматически отправляем форму через небольшую задержку
                window.onload = function() {
                    setTimeout(function() {
                        document.getElementById('autoForm').submit();
                    }, 10); // Задержка 0.01 секунда для показа анимации
                };
            </script>
  ");


  //========================================================================
}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

?>
</body>
</html>