<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='MeetingParticipants';
OutHtmlHeader ($TabName." card");


// Checklogin1();
include "../js_module.php";
OutPostReq();
//------- For Ext Tables --------- 
  ScriptSelectionTabs('usrs24', 'Selectusrs.php', 'Пользователи', '24');

$Editable = CheckFormRight($pdo, 'MeetingParticipants', 'Card');
CheckTkn();
$FldNames=array('MId','LineNo','PartType','EMail'
          ,'LastName','FirstName','MidName','IsHost'
          );
$enFields= array('PartType'=>'PartType');
$PdoArr = array();
$MId=$_REQUEST['MId'];
$PdoArr["MId"]=$MId;
$LineNo=$_REQUEST['LineNo'];
$PdoArr["LineNo"]=$LineNo;
echo("<H3>".GetStr($pdo, 'MeetingParticipants')."</H3>");
  $dp=array();
  $FullLink="MId=$MId&LineNo=$LineNo";

  $query = "select * FROM \"MeetingParticipants\" ".
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


if ($Editable) {
  echo ('<form method=post action="MeetingParticipantsSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  $PdoArr = array();
  $PdoArr["MId"]= $MId;

  if ($New==1) {
      $query = "select max(\"LineNo\") \"MX\" ".
      "FROM \"MeetingParticipants\" ".
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

  $Fld='PartType';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='PartType'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "PartType", "PartType ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='EMail';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='EMail'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=80>");
  echo(" <input type=button value='...' onclick='return Selectusrs24Fld(\"EMail\");'>");

  echo("</td>");
  echo ("</tr><tr>");

  $Fld='LastName';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='LastName'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='FirstName';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='FirstName'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='MidName';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='MidName'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='IsHost';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='IsHost'>".GetStr($pdo, $Fld).":</label></td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");
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
  
  $Fld='PartType';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "PartType", $OutVal)."</b>");
  
  echo("</td></tr>");
  
  $Fld='EMail';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='LastName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='FirstName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='MidName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='IsHost';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='MeetingParticipantsList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='MeetingParticipantsDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");
?>
</body>
</html>
