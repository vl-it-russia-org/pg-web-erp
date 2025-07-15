<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='Tasks';
OutHtmlHeader ($TabName." card");


// Checklogin1();

include "../SubHtml.js";
include "../js_SelAll.js";
include ("../setup/HtmlTxt.php");
include "../js_module.php";
OutPostReq();
//------- For Ext Tables --------- 
  ScriptSelectionTabs('usrs10', 'Selectusrs.php', 'Пользователи', '10');
  ScriptSelectionTabs('usrs11', 'Selectusrs.php', 'Пользователи', '11');

$Editable = CheckFormRight($pdo, 'Tasks', 'Card');
CheckTkn();
$FldNames=array('Id','ShortName','Created','StartDate'
          ,'Author','Division','Priority','Description'
          ,'WishDueDate','PlannedWorkload','FactWorkLoad','PlannedDueDate'
          ,'Status','RespPerson','UserSatisfaction','WaitTill'
          );
$enFields= array('Division'=>'Divisions', 'Priority'=>'Priority', 'Status'=>'TaskStatus', 'UserSatisfaction'=>'UserSatisfaction');
$PdoArr = array();
$Id=$_REQUEST['Id'];
$PdoArr["Id"]=$Id;
echo("<H3>".GetStr($pdo, 'Tasks')."</H3>");
  $dp=array();
  $FullLink="Id=$Id";

  $query = "select * FROM \"Tasks\" ".
           "WHERE (\"Id\"=:Id)";
  
  try {
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  else {
    $dp=array();
  }
  
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }  
  
  $New=$_REQUEST['New'];
if ($New==1) {
  $dp['Created']=date("Y-m-d");
  $dp['Author']=$_SESSION['login'];
  //$dp['Division']=GetUserDefault 

}


$Editable=($dp["Status"]==0);
if ($Editable) {
  echo ('<form method=post action="TasksSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");

  $LN=0;
  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldId' value='$OutVal'>");
  echo ("<td align=right><label for='Id'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$Id}' size=10 readonly>");
  echo("</td>");
  
  $Fld='Created';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Created'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=date Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ShortName';
  $OutVal= $dp[$Fld];  
  echo ("<td align=right><label for='ShortName'>".GetStr($pdo, $Fld).":</label></td><td colspan=3>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=80>");
  echo("</td>");
  echo ("</tr><tr>");


  $Fld='StartDate';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='StartDate'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=date Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");

  $Fld='Author';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Author'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=20>");
  echo(" <input type=button value='...' onclick='return Selectusrs10Fld(\"Author\");'>");

  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Division';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Division'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "Divisions", "Division ID='$Fld' ", $OutVal));
  echo("</td>");

  $Fld='Priority';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Priority'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "Priority", "Priority ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Description';
  $OutVal= $dp[$Fld];  
  echo ("<td align=right><label for='Description'>".GetStr($pdo, $Fld).":</label></td><td colspan=3>");
  BuildHtmlInput($Fld);
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='WishDueDate';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='WishDueDate'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=date Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");

  $Fld='RespPerson';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='RespPerson'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo(" <input type=button value='...' onclick='return Selectusrs11Fld(\"RespPerson\");'>");

  echo("</td>");
  echo ("</tr><tr>");

  /*
  $Fld='PlannedWorkload';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='PlannedWorkload'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' step=0.01>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='FactWorkLoad';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='FactWorkLoad'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' step=0.01>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='PlannedDueDate';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='PlannedDueDate'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=date Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");
  */

  $Fld='Status';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Status'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( "<b>".GetEnum($pdo, "TaskStatus", $OutVal)."</b>");
  if ($New==1) {
    echo("<input type=hidden Name='Status' value='0'>");
  }
  
  echo("</td>");
  echo ("</tr><tr>");

  /*
  $Fld='UserSatisfaction';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='UserSatisfaction'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "UserSatisfaction", "UserSatisfaction ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='WaitTill';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='WaitTill'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=date Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");
  */
  MakeTkn();
  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  
  $Fld='Created';
  $OutVal= $dp[$Fld];
  echo ("</td><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td></tr>");
  
  $Fld='ShortName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td colspan=3>");
  echo ($OutVal);
  echo("</td></tr>");
  
  $Fld='StartDate';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td>");
  
  $Fld='Author';
  $OutVal= $dp[$Fld];
  echo ("<td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td></tr>");
  
  $Fld='Division';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ("<b>".GetEnum($pdo, "Divisions", $OutVal)."</b>");
  echo("</td>");
  
  $Fld='Priority';
  $OutVal= $dp[$Fld];
  echo ("<td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ("<b>".GetEnum($pdo, "Priority", $OutVal)."</b>");
  echo("</td></tr>");
  
  $Fld='Description';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td colspan=3>");
  echo (DivTxt(HtmlTxt($OutVal), 100));
  echo("</td></tr>");
  
  $Fld='WishDueDate';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td>");
  
  $Fld='PlannedDueDate';
  $OutVal= $dp[$Fld];
  echo ("<td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td></tr>");

  
  $Fld='PlannedWorkload';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $OW=number_format($OutVal, 2, ".", "'");
  echo ("<b>$OW</b>");
  echo("</td>");
  
  $Fld='FactWorkLoad';
  $OutVal= $dp[$Fld];
  echo ("<td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $OW=number_format($OutVal, 2, ".", "'");
  echo ("<b>$OW</b>");
  echo("</td></tr>");
  
  
  $Fld='Status';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ("<b>".GetEnum($pdo, "TaskStatus", $OutVal)."</b>");
  echo("</td>");
  $Status=$OutVal;

  
  $Fld='RespPerson';
  $OutVal= $dp[$Fld];
  echo ("<td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  echo ($OutVal);
  echo("</td></tr>");
  
  if ( ($Status==12) OR ($Status==15)){
   
    $Fld='UserSatisfaction';
    $OutVal= $dp[$Fld];
    echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
    echo ("<b>".GetEnum($pdo, "UserSatisfaction", $OutVal)."</b>");
    echo("</td></tr>");
  }

  if ( ($Status==12) ){
    $Fld='WaitTill';
    $OutVal= $dp[$Fld];
    echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
    
    echo ($OutVal);
    
    echo("</td></tr>");
  }

  echo ("</table>");
}
echo ("  <hr><table><tr><td><a href='TasksList.php'>".GetStr($pdo, 'List')."</a></td>");
  if ($New!=1) {
    $PostArr = array();
    $PostArr["Id"]=$Id;
    // Tasks Status TaskStatus
    $EnName='TaskStatus';
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

    $StsArr = array();
    $StsCnt = 0;
    $CurrStatus=$dp['Status'];
    foreach($PossibleStatus[$CurrStatus] as $PV=> $V){ 
      $StsCnt++;
      $StsArr[$StsCnt]['NewStatus']= $PV;
      $StsArr[$StsCnt]['tit']= GetEnum($pdo, $EnName, $PV);
      $StsArr[$StsCnt]['Txt']= GetEnum($pdo, $EnName, $PV);
    }
 

    OutStatusChange ($pdo, 'Tasks-ChangeStatus.php', $PostArr, $StsArr);
  }
if ($Editable)
  echo ("<td><a href='TasksDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a></td>");
echo ("</tr></table>");

if ($New!=1) {
  echo("<hr><h4>".GetStr($pdo, 'TaskReference')."</h4>");

  $PdoArr = array();
  $PdoArr["TaskId"]= $Id;
  $query = "select * FROM \"TaskReference\" where \"TaskId\"=:TaskId order by \"Id\" ";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  echo('<table><tr class=header><th></th>');
  echo('<th>'.GetStr($pdo, 'ObjType').'</th>');
  echo('<th>'.GetStr($pdo, 'ObjName').'</th>');
  echo('<th>'.GetStr($pdo, 'Description').'</th>');
  $i=0;
  $CardArr=array();

  $CrdNewWindow =GetStr($pdo, "CrdInNewWnd");
  $CrdHere =GetStr($pdo, "CrdInCurrWnd");
  $Tkn = MakeTkn(1);

  while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
    $i=NewLine($i);
    $CardArr["Id"]= $dpL["Id"];
    $CardArr["FrmTkn"]= $Tkn;
    $Json = base64_encode(json_encode ($CardArr));
    OutCardButton("TaskReferenceCard.php", $Json, $CrdHere, $CrdNewWindow);
    

    echo ("<td align=center>");
    echo (GetEnum($pdo, "ObjType", $dpL['ObjType'])."</td>");
    echo ("<td>{$dpL['ObjName']}</td>");
    echo ("<td>{$dpL['ObjName']}</td>");

    
  }
  echo("</tr></table>");
  $CardArr=array();

  $CardArr['TaskId']=$Id;
  $CardArr['New']=1;
  $CardArr['FrmTkn']=$Tkn;

  echo ("<table><tr><td>".GetStr($pdo, 'TaskReference')." ".
        GetStr($pdo, 'AddNew').":</td>");

  $Json = base64_encode(json_encode ($CardArr));
  OutCardButton("TaskReferenceCard.php", $Json, $CrdHere, $CrdNewWindow);
  echo("</tr></table>" );
}

?>
</body>
</html>
