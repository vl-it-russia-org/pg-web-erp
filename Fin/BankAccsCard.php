<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='BankAccs';
OutHtmlHeader ($TabName." card");


// Checklogin1();
include "../js_module.php";

//------- For Ext Tables --------- 
  ScriptSelectionTabs('CB_Banks13', 'SelectCB_Banks.php', '<a href=\'https://kolya.it-russia.org/PG2025/FormsI/TranslateFrm.php?Enum=CB_Banks\' target=Translate>_</a>CB_Banks', 'SelId=13&');

CheckRight1 ($pdo, 'Admin');

$FldNames=array('BankId','Description','Country','BIK'
          ,'BankName','City','TransitAccount','AccountNo'
          ,'Currency');
$enFields= array('Country'=>'Country', 'Currency'=>'Currency');
$PdoArr = array();
$BankId=$_REQUEST['BankId'];
$PdoArr["BankId"]=$BankId;
echo("<H3>".GetStr($pdo, 'BankAccs')."</H3>");
  $dp=array();
  $FullLink="BankId=$BankId";

  $query = "select * FROM \"BankAccs\" ".
           "WHERE (\"BankId\"=:BankId)";
  
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
  echo ('<form method=post action="BankAccsSave.php">'.
        "<input type=hidden Name='New' value='$New'>");
  
  echo ("<table><tr>");

  $LN=0;
  $Fld='BankId';
  $OutVal= $dp[$Fld];
  echo ("<input type=hidden Name='OldBankId' value='$OutVal'>");
  echo ("<td align=right><label for='BankId'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$BankId}' size=10 readonly>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Description';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Description'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=100>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Country';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Country'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "Country", "Country ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='BIK';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='BIK'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=20>");
  echo(" <input type=button value='...' onclick='return SelectCB_Banks13Fld(\"BIK\");'>");

  echo("</td>");
  echo ("</tr><tr>");

  $Fld='BankName';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='BankName'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='City';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='City'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=60>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='TransitAccount';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='TransitAccount'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='AccountNo';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='AccountNo'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Currency';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Currency'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "Currency", "Currency ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  echo ("<td colspan=2 align=right><input type=submit value='".
         GetStr($pdo, 'Save')."'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");

  $Fld='BankId';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Description';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Country';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "Country", $OutVal)."</b>");
  
  echo("</td></tr>");
  
  $Fld='BIK';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='BankName';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='City';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='TransitAccount';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='AccountNo';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='Currency';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "Currency", $OutVal)."</b>");
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><br><a href='BankAccsList.php'>".GetStr($pdo, 'List')."</a>");
if ($Editable)
  echo (" | <a href='BankAccsDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a>");
?>
</body>
</html>
