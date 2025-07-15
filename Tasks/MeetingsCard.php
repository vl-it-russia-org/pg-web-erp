<?php
session_start();

include ("../setup/common_pg.php");
include ("../setup/HtmlTxt.php");
BeginProc();

$TabName='Meetings';
OutHtmlHeader ($TabName." card");
include ("../js_SelAll.js");

// Checklogin1();


$Editable = CheckFormRight($pdo, 'Meetings', 'Card');
CheckTkn();
$FldNames=array('Id','MeetingDate','Subject');
$enFields= array();
$PdoArr = array();
$Id=$_REQUEST['Id'];
$PdoArr["Id"]=$Id;
echo("<H3>".GetStr($pdo, 'Meetings')."</H3>");
  $dp=array();
  
  $FullLink="Id=$Id";

  $query = "select * FROM \"Meetings\" ".
           "WHERE (\"Id\"=:Id)";
  
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
  echo ('<form method=post action="MeetingsSave.php">'.
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

  $Fld='MeetingDate';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='MeetingDate'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=date Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Subject';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Subject'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
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
  
  $Fld='MeetingDate';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Subject';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='MeetingsList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='MeetingsDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");

if ($Id != '') {
  $MId=$Id;

  //--------------------------------------------------------------  Участники

  echo("<hr><h4>".GetStr($pdo, 'MeetingParticipants')."</h4>");

  $PdoArr = array();
  $PdoArr["MId"]= $MId;
  $query = "select * FROM \"MeetingParticipants\" where \"MId\"=:MId order by \"MId\",\"LineNo\" ";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  echo('<table><tr class=header><th></th>');
  echo('<th>'.GetStr($pdo, 'LineNo').'</th>');
  echo('<th>'.GetStr($pdo, 'PartType').'</th>');
  echo('<th>'.GetStr($pdo, 'EMail').'</th>');
  echo('<th>'.GetStr($pdo, 'LastName').'</th>');
  echo('<th>'.GetStr($pdo, 'FirstName').'</th>');
  echo('<th>'.GetStr($pdo, 'MidName').'</th>');
  echo('<th>'.GetStr($pdo, 'IsHost').'</th>');
  $i=0;
  $CardArr=array();

  $CrdNewWindow =GetStr($pdo, "CrdInNewWnd");
  $CrdHere =GetStr($pdo, "CrdInCurrWnd");
  $Tkn = MakeTkn(1);

  while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
    $i=NewLine($i);
    $CardArr["MId"]= $dpL["MId"];
    $CardArr["LineNo"]= $dpL["LineNo"];
    $CardArr["FrmTkn"]= $Tkn;
    $Json = base64_encode(json_encode ($CardArr));
    OutCardButton("MeetingParticipantsCard.php", $Json, $CrdHere, $CrdNewWindow);
    

    echo ("<td align=center>");
    echo ("{$dpL['LineNo']}</td>");
    echo ("<td align=center>");
    echo (GetEnum($pdo, "PartType", $dpL['PartType'])."</td>");
    echo ("<td>");
    echo ("{$dpL['EMail']}</td>");
    echo ("<td>");
    echo ("{$dpL['LastName']}</td>");
    echo ("<td>");
    echo ("{$dpL['FirstName']}</td>");
    echo ("<td>");
    echo ("{$dpL['MidName']}</td>");
    echo ("<td align=center>");
    $Ch="";
      if ($dpL['IsHost']==1) {
        $Ch=" checked ";  
      }
        echo ("<input type=checkbox Name=IsHost value=1 $Ch></td>");
      
    
  }
  echo("</tr></table>");
  $CardArr=array();

  $CardArr['MId']=$MId;
  $CardArr['New']=1;
  $CardArr['FrmTkn']=$Tkn;

  echo ("<table><tr><td>".GetStr($pdo, 'MeetingParticipants')." ".
        GetStr($pdo, 'AddNew').":</td>");

  $Json = base64_encode(json_encode ($CardArr));
  OutCardButton("MeetingParticipantsCard.php", $Json, $CrdHere, $CrdNewWindow);
  echo("</tr></table>" );
  

  //-------------------------------------------------------------- Протокол

  echo("<hr><h4>".GetStr($pdo, 'MeetingProtocol')."</h4>");

  $PdoArr = array();
  $PdoArr["MId"]= $MId;
  $query = "select * FROM \"MeetingProtocol\" where \"MId\"=:MId order by \"MId\",\"LineNo\" ";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  echo('<table><tr class=header><th></th>');
  echo('<th>'.GetStr($pdo, 'LineNo').'</th>');
  echo('<th>'.GetStr($pdo, 'No').'</th>');
  echo('<th>'.GetStr($pdo, 'Description').'</th>');
  $i=0;
  $CardArr=array();

  $CrdNewWindow =GetStr($pdo, "CrdInNewWnd");
  $CrdHere =GetStr($pdo, "CrdInCurrWnd");
  $Tkn = MakeTkn(1);

  while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
    $i=NewLine($i);
    $CardArr["MId"]= $dpL["MId"];
    $CardArr["LineNo"]= $dpL["LineNo"];
    $CardArr["FrmTkn"]= $Tkn;
    $Json = base64_encode(json_encode ($CardArr));
    OutCardButton("MeetingProtocolCard.php", $Json, $CrdHere, $CrdNewWindow);
    

    echo ("<td align=center>");
    echo ("{$dpL['LineNo']}</td>");
    echo ("<td>{$dpL['No']}</td>");
    echo ("<td>".HtmlTxt($dpL['Description'])."</td>");
    
  }
  echo("</tr></table>");
  $CardArr=array();

  $CardArr['MId']=$MId;
  $CardArr['New']=1;
  $CardArr['FrmTkn']=$Tkn;

  echo ("<table><tr><td>".GetStr($pdo, 'MeetingProtocol')." ".
        GetStr($pdo, 'AddNew').":</td>");

  $Json = base64_encode(json_encode ($CardArr));
  OutCardButton("MeetingProtocolCard.php", $Json, $CrdHere, $CrdNewWindow);
  echo("</tr></table>" );


}

?>
</body>
</html>
