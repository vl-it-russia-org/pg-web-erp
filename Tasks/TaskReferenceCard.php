<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='TaskReference';
OutHtmlHeader ($TabName." card");


// Checklogin1();
include "../js_module.php";
OutPostReq();
//------- For Ext Tables --------- 
  ScriptSelectionTabs('MeetingProtocol9', 'SelectMeetingProtocol.php', 'Протокол совещания', '9');
  ScriptSelectionTabs('ToDo_Line25', 'SelectToDo_Line.php', 'Что делать', '25');
  ScriptSelectionTabs('Tasks26', 'SelectTasks.php', 'Задачи', '26');

$Editable = CheckFormRight($pdo, 'TaskReference', 'Card');
CheckTkn();
$FldNames=array('Id','TaskId','ObjType','ObjName'
          );
$enFields= array('ObjType'=>'ObjType');

// ----- Out MasterTab: Tasks 
$PdoArr=array();
$PdoArr['Id']=$_REQUEST['TaskId'];
try {
  echo ('<h4>'.GetStr($pdo, 'Tasks').'</h4>'); 
  $query = "select * from \"Tasks\" ". 
           "where  (\"Id\"=:Id)";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  if ($dp_mt = $STH->fetch(PDO::FETCH_ASSOC)) { 
    echo('<table>');
    echo('<tr><td align=right>'.GetStr($pdo, 'Id').':</td><td>'.
          $dp_mt['Id'].'</td></tr>');
    echo('<tr><td align=right>'.GetStr($pdo, 'ShortName').':</td><td>'.
          $dp_mt['ShortName'].'</td></tr>');
    echo('<tr><td align=right>'.GetStr($pdo, 'Created').':</td><td>'.
          $dp_mt['Created'].'</td></tr>');
    echo('<tr><td align=right>'.GetStr($pdo, 'Author').':</td><td>'.
          $dp_mt['Author'].'</td></tr>');
    echo('<tr><td align=right>'.GetStr($pdo, 'Division').':</td><td>'.
          $dp_mt['Division'].'</td></tr>');
    echo('<tr><td align=right>'.GetStr($pdo, 'Status').':</td><td>'.
          $dp_mt['Status'].'</td></tr>');
    echo('<tr><td align=right>'.GetStr($pdo, 'RespPerson').':</td><td>'.
          $dp_mt['RespPerson'].'</td></tr>');
    echo('</table><hr>');
  };
}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}
  $PdoArr = array();
$Id=$_REQUEST['Id'];
$PdoArr["Id"]=$Id;
echo("<H3>".GetStr($pdo, 'TaskReference')."</H3>");
  $dp=array();
  $FullLink="Id=$Id";
  
  try {
  
  
  $query = "select * FROM \"TaskReference\" ".
           "WHERE (\"Id\"=:Id)";
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }  
  
  $New=$_REQUEST['New'];



if ($Editable) {
  if ($New==1) {
    $dp["TaskId"]= $_REQUEST["TaskId"];
  }
  echo ('<form method=post action="TaskReferenceSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");

  $LN=0;
  $Fld='Id';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldId' value='$OutVal'>");
  echo ("<td align=right><label for='Id'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$Id}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='TaskId';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='TaskId'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ObjType';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='ObjType'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "ObjType", "ObjType ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ObjName';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='ObjName'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=20>");
  echo(" <input type=button value='...' onclick='return SelectMeetingProtocol9Fld(\"ObjName\");'>");

  echo(" <input type=button value='...' onclick='return SelectToDo_Line25Fld(\"ObjName\");'>");

  echo(" <input type=button value='...' onclick='return SelectTasks26Fld(\"ObjName\");'>");

  echo("</td>");
  echo ("</tr><tr>");

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
  
  echo("</td></tr>");
  
  $Fld='TaskId';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ObjType';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "ObjType", $OutVal)."</b>");
  
  echo("</td></tr>");
  
  $Fld='ObjName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><table><tr><td><a href='TaskReferenceList.php'>".GetStr($pdo, 'List')."</a></td>");
if ($Editable)
  echo ("<td><a href='TaskReferenceDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a></td>");
echo ("</tr></table>");

?>
</body>
</html>
