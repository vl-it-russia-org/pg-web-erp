<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

$TabName='SystemDescription';
OutHtmlHeader ($TabName." card");


// Checklogin1();

include "../SubHtml.js";
include "../js_SelAll.js";
include ("../setup/HtmlTxt.php");


$Editable = CheckFormRight($pdo, 'SystemDescription', 'Card');
CheckTkn();
$FldNames=array('Id','ParagraphNo','ElType','Description'
          ,'Ord1','ParentId');
$enFields= array('ElType'=>'PGElType');
$PdoArr = array();
$Id=$_REQUEST['Id'];
$PdoArr["Id"]=$Id;
echo("<H3>".GetStr($pdo, 'SystemDescription')."</H3>");
  $dp=array();
  $FullLink="Id=$Id";
  
  try {
  
  
  $query = "select * FROM \"SystemDescription\" ".
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
  echo ('<form method=post action="SystemDescriptionSave.php">'.
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

  $Fld='ParagraphNo';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='ParagraphNo'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=text Name='$Fld'  ID='$Fld' Value='{$dp[$Fld]}' size=30>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ElType';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='ElType'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ( EnumSelection($pdo, "PGElType", "ElType ID='$Fld' ", $OutVal));
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Description';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Description'>".GetStr($pdo, $Fld).":</label></td><td>");
  BuildHtmlInput($Fld);
  echo ("<textarea Name='$Fld'  ID='$Fld'  cols=50 rows=3>{$dp[$Fld]}</textarea>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='Ord1';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='Ord1'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
  echo("</td>");
  echo ("</tr><tr>");

  $Fld='ParentId';
  $OutVal= $dp[$Fld];  echo ("<td align=right><label for='ParentId'>".GetStr($pdo, $Fld).":</label></td><td>");
  echo ("<input type=number Name='$Fld'  ID='$Fld'  Value='{$dp[$Fld]}'>");
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
  
  $Fld='ParagraphNo';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ElType';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ("<b>".GetEnum($pdo, "PGElType", $OutVal)."</b>");
  
  echo("</td></tr>");
  
  $Fld='Description';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
    echo (DivTxt(HtmlTxt($OutVal), 100));

  echo("</td></tr>");
  
  $Fld='Ord1';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
  
  $Fld='ParentId';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  
  echo ($OutVal);
  
  echo("</td></tr>");
    echo ("</table>");
}
echo ("  <hr><table><tr><td><a href='SystemDescriptionList.php'>".GetStr($pdo, 'List')."</a></td>");
if ($Editable)
  echo ("<td><a href='SystemDescriptionDelete.php?$FullLink' onclick='return confirm(\"Delete?\");'>".
        GetStr($pdo, 'Delete')."</a></td>");

  if ($New!=1) {
    $PostArr = array();
    $PostArr["Id"]=$Id;
    $StsArr = array();
    $StsCnt = 0;
    $StsCnt++;
    $StsArr[$StsCnt]['NewStatus']= 'Copy';
    $StsArr[$StsCnt]['tit']= GetStr($pdo, 'CopyRecToNew');
    $StsArr[$StsCnt]['Txt']= GetStr($pdo, 'CopyRecToNew');
    OutStatusChange ($pdo, 'SystemDescription-CopyRecord.php', $PostArr, $StsArr);
  }
echo ("</tr></table>");

?>
</body>
</html>
