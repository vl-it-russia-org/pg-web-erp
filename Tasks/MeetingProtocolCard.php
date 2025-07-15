<?php
session_start();

include ("../setup/common_pg.php");
include ("../setup/HtmlTxt.php");
BeginProc();

$TabName='MeetingProtocol';
OutHtmlHeader ($TabName." card");
include "../SubHtml.js";
include "../js_SelAll.js";

// Checklogin1();


$Editable = CheckFormRight($pdo, 'MeetingProtocol', 'Card');
CheckTkn();
$FldNames=array('MId','LineNo','No','Description'
          );
$enFields= array();
$PdoArr = array();
$MId=$_REQUEST['MId'];
$PdoArr["MId"]=$MId;
$LineNo=$_REQUEST['LineNo'];
$PdoArr["LineNo"]=$LineNo;
echo("<H3>".GetStr($pdo, 'MeetingProtocol')."</H3>");
  $dp=array();
  $FullLink="MId=$MId&LineNo=$LineNo";

  $query = "select * FROM \"MeetingProtocol\" ".
           "WHERE (\"MId\"=:MId) AND (\"LineNo\"=:LineNo)";
  
  try {
  
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

$HtmlArr= array('[/a]', '[/b]', '[/li]', '[/ul]', '[a]', '[br]', '[b]', '[f]', 
                 '[li]', '[p]',  '[ul]' ,'[html]','[/html]','[body]','[/body]', '[sup]', '[/sup]' , '[sp]', '[beg]');


if ($Editable) {
  $PartArr=array();
  $PdoArr = array();
  $PdoArr["MId"]= $MId;
  
  $query = "select * FROM \"MeetingParticipants\" ".
           "WHERE (\"MId\"=:MId)";
  
  try {
  
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);  
    
    // MeetingParticipants
    // MId, LineNo, PartType, EMail, LastName, FirstName, MidName, IsHost
    
    while($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
      $V= '[b]'.$dp2['FirstName'].' '.$dp2['LastName'].'[/b]';
      $PartArr[$V]=HtmlTxt($V);
    }
  
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }  

  
  echo ('<form method=post action="MeetingProtocolSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  $PdoArr = array();
  $PdoArr["MId"]= $MId;

  if ($New==1) {
      $query = "select max(\"LineNo\") \"MX\" ".
      "FROM \"MeetingProtocol\" ".
      " WHERE (1=1)  AND (\"MId\"=:MId)";
      $STH4 = $pdo->prepare($query);
      $STH4->execute($PdoArr);
      $LN=0;
      if ($dp4 = $STH4->fetch(PDO::FETCH_ASSOC)) {
        $LN=$dp4['MX'];
      }
      $LN+=1;
      $LineNo=$LN;
  }

  $Fld='MId';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldMId' value='$OutVal'>");
  echo ("<td align=right><label for='MId'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$MId}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='LineNo';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldLineNo' value='$OutVal'>");
  echo ("<td align=right><label for='LineNo'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$LineNo}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='No';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='No'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=20>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Description';
  $OutVal= $dp[$Fld];  
  echo ("<td align=right><label for='Description'>".GetStr($pdo, $Fld).":</label></td><td>");
  
  BuildHtmlArrInput($Fld, $PartArr, 4);
  echo ("<hr>");

  BuildHtmlInput($Fld);
  
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=80 rows=10>{$dp[$Fld]}</textarea>");
  
  echo("</td>");
  echo("<td>".HtmlTxt($dp[$Fld])."</td>");

  echo ("</tr><tr>");

  MakeTkn();
  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='MId';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='LineNo';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='No';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Description';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='MeetingProtocolList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='MeetingProtocolDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");

if ($New!=1) {  
  //---------------------------------------------------------- Tasks
  echo("<hr><h4>".GetStr($pdo, 'TaskReference')."</h4>");


  $PdoArr = array();
  $PdoArr["ObjType"]= 20;
  $PdoArr["ObjName"]= $MId.'/'.$LineNo;

  //
  // Tasks
  // Id, ShortName, Created, StartDate, Author, Division, Priority, Description, WishDueDate, 
  // PlannedWorkload, FactWorkLoad, PlannedDueDate, Status, RespPerson, UserSatisfaction, WaitTill, 
  // TaskGroup, SFProject, TaskYearCode  
  // 

  $query = "select t.* FROM \"TaskReference\" r, \"Tasks\" t ". 
           "where (r.\"ObjType\"=:ObjType) and (r.\"ObjName\"=:ObjName) and ".
                 "(r.\"TaskId\"=t.\"Id\") order by t.\"Id\" ";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  echo('<table><tr class=header><th></th>');
  echo('<th>'.GetStr($pdo, 'TaskId').'</th>');
  echo('<th>'.GetStr($pdo, 'ShortName').'</th>');
  echo('<th>'.GetStr($pdo, 'Author').'</th>');
  echo('<th>'.GetStr($pdo, 'WishDueDate').'</th>');
  echo('<th>'.GetStr($pdo, 'PlannedDueDate').'</th>');
  echo('<th>'.GetStr($pdo, 'RespPerson').'</th>');

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
    OutCardButton("Tasks.php", $Json, $CrdHere, $CrdNewWindow);

    echo ("<td align=center>");
    echo ("{$dpL['TaskId']}</td>");
    echo ("<td align=center>");
    echo (GetEnum($pdo, "ObjType", $dpL['ObjType'])."</td>");
    echo ("<td>");
    echo ("{$dpL['ObjName']}</td>");
    
  }
  echo("</tr></table>");
  $CardArr=array();

  $CardArr["ObjType"]=20;
  $CardArr["ObjName"]= $MId.'/'.$LineNo;

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
