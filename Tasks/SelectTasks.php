<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='Tasks';
OutHtmlHeader ($TabName." Select");

$CurrFile='SelectTasks.php';
$Frm='Tasks';
$Fields=array('Id','ShortName'
        ,'Created','StartDate','Author'
        ,'Division','Priority','Description'
        ,'WishDueDate','PlannedWorkload','FactWorkLoad'
        ,'PlannedDueDate','Status','RespPerson'
        ,'UserSatisfaction','WaitTill','TaskGroup'
        ,'SFProject','TaskYearCode');
$enFields= array('Division'=>'Divisions', 'Priority'=>'Priority', 'Status'=>'TaskStatus', 'UserSatisfaction'=>'UserSatisfaction', 'TaskGroup'=>'TaskGroup');
CheckRight1 ($pdo, 'Admin');

CheckTkn();
$ArrPostParams=array();
$BegPos = $_REQUEST['BegPos']+0;
if ($BegPos==''){
$BegPos=0;
}

$ORD = $_REQUEST['ORD'];
if ($ORD =='1') {
  $ORD = '"Id"';
}
else {
  $ORD = '"Id"';
}
$ORDS = ' order by  '; 
if ($ORD !='') {
  $ORDS = ' order by '.$ORD;
}
else {
  $ORDS = ' order by '.$ORD;
}

$PdoArr = array();

try{
  
  $WHS = '';
  $FullRef='?ORD='.$ORD;
  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST['Fltr_'.$Fld];
    if ($Fltr!='') {
      if ($WHS !='') {
        $WHS.= ' and ';
      }
      
      if ($enFields[$Fld]!='') {
        $PdoArr[$Fld]= $Fltr;
        $WHS.='("'.$Fld."\" = :$Fld)";
      }
      else {
        $WHS.= SetFilter2Fld ($Fld, $Fltr, $PdoArr );
      }
      $FullRef.='&Fltr_'.$Fld.'='.$Fltr ;
      $ArrPostParams[$Fld]=$Fltr;
    }
  }

$ElId   = $_REQUEST['ElId'];
$SubStr = $_REQUEST['SubStr'];
$SelId = $_REQUEST['SelId'];
$SelId2 = $_REQUEST['SelId2'];
$SelId3 = $_REQUEST['SelId3'];
$SelId4 = $_REQUEST['SelId4'];
$Par2   = $_REQUEST['Par2'];

if ($SelId== '26') { 

  echo ("<script>".
    "function SetSelect( Val ) { 
       OW=window.opener;
     
       var elem1 = OW.document.getElementById('$ElId');
       if (elem1) { 
         elem1.value=Val;
       }

       window.close();
    }
    </script>");

}
  $PageArr = array();
  
  // Get how many rows total we have   
  if ($WHS != '') {
    $WHS = ' where '.$WHS;
  };   

  $queryCNT = "select COUNT(*) \"CNT\" FROM \"Tasks\" ".
              "$WHS ";

  $STH = $pdo->prepare($queryCNT);
  $STH->execute($PdoArr);
  
  $CntLines=0;
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };

  $InfoPages=CalcPageArr($pdo, $PageArr, $CntLines);

  $query = "select * FROM \"Tasks\" ".
           "$WHS $ORDS ".AddLimitPos($PageArr['BegPos'], $PageArr['LPP']);

  echo ('<br><b>'.GetStr($pdo, 'Tasks').' '.
                    GetStr($pdo, 'List').'</b> '.$InfoPages) ;


  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
   
  echo ('<form method=post action="'.$CurrFile.'"><table><tr>'.
        "<input type=hidden name='ElId' value='$ElId'>".
        "<input type=hidden name='SubStr' value='$SubStr'>".
        "<input type=hidden name='SelId' value='$SelId'>".
        "<input type=hidden name='SelId2' value='$SelId2'>".
        "<input type=hidden name='SelId3' value='$SelId3'>".
        "<input type=hidden name='SelId4' value='$SelId4'>".
        "<input type=hidden name='Par2' value='$Par2'>");
  
  // Out CSRF protection
  MakeTkn();

  $i=0;
  foreach ( $Fields as $Fld) {
    if ($i==3){
      echo('</tr><tr>');
      $i=0;
    }     
    $i++;
    echo("<td align=right>".GetStr($pdo, $Fld).":</td>");

    if ($enFields[$Fld]!=''){
      echo("<td>".EnumSelection($pdo, $enFields[$Fld],'Fltr_'.$Fld, $_REQUEST['Fltr_'.$Fld], 1)."</td>");
    }
    else {
      echo("<td><input type=text length=30 size=20 name='Fltr_$Fld' value='".
        $_REQUEST['Fltr_'.$Fld]."'></td>");
    }
  }
  echo ("<td colspan=2><input type=text length=10 name=SubStr value='$SubStr'></td>");
  echo ('<td><button type="submit">Filter</button></td></tr></table></form>');
  //echo ('<hr><br><form method=post action="TasksCard.php">'.
    //    '<input type=hidden Name=New VALUE=1>');
    //MakeTkn();
    //echo (
    //    "<input type=submit Value='".GetStr($pdo, 'New')."'></form>" );
//--------------------------------------------------------------------------------

echo ('<table class=LongTable><tr class="header"><th></th>');

foreach ( $Fields as $Fld) {
  echo("<th>".GetStr($pdo, $Fld)."</th>");
}
echo("</tr>");

$n=0;
while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  
  $classtype="";
  $n++;
  if ($n==2) {
    $n=0;
    $classtype=' class="even"';
  }

  if ($SelId=='26'){
    $Res="\"".addslashes($dp['Id'])."\"";
}

  echo ("<tr$classtype><td><input type=button value='".GetStr($pdo, 'Select').
       "' onclick='return SetSelect($Res);'></td>");

  foreach ( $Fields as $Fld) {
    if ($Fld=='Id') {
      echo("<td align=left><a href='TasksCard.php?Id={$dp['Id']}'>{$dp[$Fld]}</a></td>");
    }
    else 
    if ($enFields[$Fld]!=''){
      echo("<td>".GetEnum($pdo, $enFields[$Fld], $dp[$Fld])."</td>");
    }
    else {
      echo('<td>'.$dp[$Fld]."</td>");
    }
  }
  echo("</tr>");
};
echo ("</table>");

$ArrPostParams["SelId"]=$SelId;
$ArrPostParams["ElId"]=$ElId;

$ArrPostParams["SubStr"]=$SubStr;
$ArrPostParams["Par2"]=$Par2;

$ArrPostParams["SelId2"]=$SelId2;
$ArrPostParams["SelId3"]=$SelId3;
$ArrPostParams["SelId4"]=$SelId4;


echo('<table><tr class="header">');
OutListFooter($pdo, $CurrFile, $ArrPostParams, $PageArr);


echo ('</tr></table>');

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
