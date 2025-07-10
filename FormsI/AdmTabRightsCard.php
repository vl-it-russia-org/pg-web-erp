<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='AdmTabRights';
OutHtmlHeader ($TabName." card");


// Checklogin1();
include "../js_module.php";
OutPostReq();
//------- For Ext Tables --------- 
  ScriptSelectionTabs('Rights22', 'SelectRights.php', 'Права', '22');

CheckRight1 ($pdo, 'Admin');

$FldNames=array('TabNo','Right','CanList','CanEdit'
          ,'CanCardReadOnly','CanDelete','CanXlsUpload');
$enFields= array();
$PdoArr = array();
$TabNo=$_REQUEST['TabNo'];
$PdoArr["TabNo"]=$TabNo;
$Right=$_REQUEST['Right'];
$PdoArr["Right"]=$Right;
echo("<H3>".GetStr($pdo, 'AdmTabRights')."</H3>");
  $dp=array();
  $FullLink="TabNo=$TabNo&Right=$Right";

  $query = "select * FROM \"AdmTabRights\" ".
           "WHERE (\"TabNo\"=:TabNo) AND (\"Right\"=:Right)";
  
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

$Editable=1;
if ($Editable) {
  echo ('<form method=post action="AdmTabRightsSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");
  $PdoArr = array();
  $PdoArr["TabNo"]= $TabNo;

  if ($New==1) {
      $query = "select max(\"Right\") \"MX\" ".
      "FROM \"AdmTabRights\" ".
      " WHERE (1=1)  AND (\"TabNo\"=:TabNo)";
      $STH4 = $pdo->prepare($query);
      $STH4->execute($PdoArr);
      $LN=0;
      if ($dp4 = $STH4->fetch(PDO::FETCH_ASSOC)) {
        $LN=$dp4['MX'];
      }
      $LN+=1;
  }

  $Fld='TabNo';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldTabNo' value='$OutVal'>");
  echo ("<td align=right><label for='TabNo'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$TabNo}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Right';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldRight' value='$OutVal'>");
  echo ("<td align=right><label for='Right'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$Right}' size=10 readonly>");
  echo(" <input type=button value='...' onclick='return SelectRights22Fld(\"Right\");'>");

  echo("</td>");
  echo ("</tr><tr>");

  $Fld='CanList';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='CanList'>".GetStr($pdo, $Fld).":</label></td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='CanEdit';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='CanEdit'>".GetStr($pdo, $Fld).":</label></td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='CanCardReadOnly';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='CanCardReadOnly'>".GetStr($pdo, $Fld).":</label></td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='CanDelete';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='CanDelete'>".GetStr($pdo, $Fld).":</label></td><td>");
  $Ch=''; if ($dp[$Fld]==1) $Ch='Checked';
  echo ("<input type=checkbox Name='$Fld'  ID='$Fld' Value=1 $Ch>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='CanXlsUpload';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='CanXlsUpload'>".GetStr($pdo, $Fld).":</label></td><td>");
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

  $Fld='TabNo';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Right';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='CanList';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
  
  $Fld='CanEdit';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
  
  $Fld='CanCardReadOnly';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
  
  $Fld='CanDelete';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  $Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  
  echo("</td></tr>");
  
  $Fld='CanXlsUpload';
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
echo ("  <hr><br><a href='AdmTabRightsList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='AdmTabRightsDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");
?>
</body>
</html>
