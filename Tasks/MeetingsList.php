<?php
session_start();
include ("../setup/common_pg.php");
BeginProc();

$TabName='Meetings';
OutHtmlHeader ($TabName." list");

include ("../js_SelAll.js");

$CurrFile='MeetingsList.php';
$Frm='Meetings';
$Fields=array('Id','MeetingDate','Subject');
$enFields= array();

$Editable = CheckFormRight($pdo, 'Meetings', 'List');

CheckTkn();
$ArrPostParams=array();

// Какие параметры передаем в форму MeetingsCard.php
$CardArr=array();
$CardArr['FrmTkn']=MakeTkn(1); 

$BegPos = 0;
if (!empty($_REQUEST['BegPos'])) {
  $BegPos = $_REQUEST['BegPos'] +0;
};

$ORD = '"Id"';
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
  
  $WHS = '';
  $FullRef='?ORD=1';
  
  $PdoArr = array();
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
        $WHS.= SetFilter2Fld ( $Fld, $Fltr, $PdoArr );
      }
      $FullRef.='&Fltr_'.$Fld.'='.$Fltr ;
      $ArrPostParams[$Fld]=$Fltr;
    }
  }


try {
  if ($WHS != '') {
    $WHS = ' where '.$WHS;
  };

  $PageArr=array();

  $queryCNT = "select COUNT(*) \"CNT\" FROM \"Meetings\" ".
              "$WHS ";
  
  $STHCnt = $pdo->prepare($queryCNT);
  $STHCnt->execute($PdoArr);
  
  $CntLines=0;
  if ($dp = $STHCnt->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp['CNT'];  
  };
  
  $PageInfo = CalcPageArr($pdo, $PageArr, $CntLines);

  $query = "select * FROM \"Meetings\" ".
           "$WHS $ORDS ". AddLimitPos($PageArr['BegPos'], $PageArr['LPP']);

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  

  echo ('<br><b>'.GetStr($pdo, 'Meetings').' '.
        GetStr($pdo, 'List').
        '</b> '.$PageInfo) ;
  
  echo ('<form method=post action="'.$CurrFile.'"><table><tr>');
  $i=0;
  foreach ( $Fields as $Fld) {
    if ($i==4){
      echo('</tr><tr>');
      $i=0;
    }     
    $i++;
    $CN= "Fltr_$Fld";
    echo("<td align=right><label for=\"$CN\">".GetStr($pdo, $Fld).":</label></td>");

    if ($enFields[$Fld]!=''){
      echo("<td>".EnumSelection($pdo, $enFields[$Fld],"$CN ID=$CN", $_REQUEST['Fltr_'.$Fld], 1)."</td>");
    }
    else {
      echo("<td><input type=text size=12 name='$CN' id='$CN' value='".
        $_REQUEST[$CN]."'></td>");
    }
  }
  MakeTkn();
  echo ('<td><button type="submit">Filter</button></td></tr></table></form>');
  echo ('<hr><table><tr><td><form method=post action="MeetingsCard.php">'.
        '<input type=hidden Name=New VALUE=1>'.
        "<input type=submit Value='".GetStr($pdo, 'New')."'>");
    MakeTkn();
    echo("</form></td><td>" );
//--------------------------------------------------------------------------------
echo ('<form method=post action="MeetingsGroupOp.php">'.
        "<input type=submit  Name=OpType Value='".GetStr($pdo, 'Delete')."' 
          onclick='return confirm(\"Delete selected?\");'></td></tr></table>" );
MakeTkn();
echo ('<table class=LongTable><tr class="header">');

echo("<th><input type=checkbox onclick='return SelAll();'></th><th></th>");


foreach ( $Fields as $Fld) {
  echo("<th>".GetStr($pdo, $Fld)."</th>");
}
echo("</tr>");

$n=0;
$Cnt=0;
while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $Cnt++;
  $classtype="";
  $n++;
  if ($n==2) {
    $n=0;
    $classtype=' class="even"';
  }
  
  echo ("<tr".$classtype.">");

  $PKValArr=array();
  $PKValArr['Id']= $dp['Id'];
  $PKRes=base64_encode( json_encode($PKValArr));
  
  echo ("<td><input type=checkbox ID='Chk_$Cnt' Name=Chk[$Cnt] value='$PKRes'></td>");
  
  $CardArr['Id']= $dp['Id'];
  $Json = base64_encode(json_encode ($CardArr));

  $CrdNewWindow =GetStr($pdo, 'CrdInNewWnd');
  $CrdHere =GetStr($pdo, 'CrdInCurrWnd');
    echo("<td align=center>
         <button type=button onclick=\"openFormWithPost('MeetingsCard.php', '$Json', '_self')\" title='$CrdHere'>&#9900;</button>
         <button type=button onclick=\"openFormWithPost('MeetingsCard.php', '$Json', '_blank')\" title='$CrdNewWindow'>&#9856;</button> </td>");

  $Fld='Id';
  echo("<td><a href='MeetingsCard.php?Id={$dp['Id']}'>{$dp[$Fld]}</a></td>");
  

  $Fld='MeetingDate';
  echo('<td>'.$dp[$Fld]."</td>");
  

  $Fld='Subject';
  echo('<td>'.$dp[$Fld]."</td>");
  echo("</tr>");
}
echo("</table>".
     "<input type=hidden ID=AllCnt value='$Cnt'>".
     "<input type=submit Name=OpType Value='".GetStr($pdo, 'Delete')."' 
          onclick='return confirm(\"Delete selected?\");'></form>");

echo('<table><tr class="header">');

OutListFooter($pdo, $CurrFile, $ArrPostParams, $PageArr);


echo ('<td><a href="MeetingsPrintXLS.php'.$FullRef.'">Print XLS</a></td>'.

      '<td><a href="Frm-Meetings-XlsUpload.php">Upload from XLS</a></td>'.

       '</tr></table>');

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
