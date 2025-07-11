<?php
$file = fopen("../Forms/Frm-{$TabName}-XlsUpload.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= '
include ("../setup/common_pg.php");
BeginProc();

OutHtmlHeader ($TabName." form Excel upload");

// Checklogin1();'."\r\n";

fwrite($file,$S);

$S= "\$Editable = CheckFormRight(\$pdo, '$TabName', 'ExcelUpload');\r\n\r\n ";
$Div='';

$kk=0;

$Cnt=0;

$FldsList='';
foreach ($Fields as $Fld=>$Arr) {
  $kk++;
  $FldsList.="$Div";
  $Div=', ';
  $Cnt++;
  if ($kk==4) {
    $FldsList.="<br>\r\n      ";
    $kk=0;
  }
  $FldsList.=$Fld;

}

$S.="\r\n".

'  echo ("<form method=\'post\' action=\''.$TabName.'-UploadXlsx.php\' enctype=\'multipart/form-data\'>
   <table border=\'0\'>
   <tr>
     <td>Upload XLSX file to '.$TabName.' with up to '.$Cnt.' columns:<br>'.$FldsList.'</td>
    </tr>
    <tr>
      <td><input type=\'file\' accept=\'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet\' '.
      'value=\'File name:\' name=\'userfile\'></td>
    </tr> ".
    "<tr>".
     //"<td>Add lines to project: <input type=checkbox name=\'AddToProject\' value=1></td>".
    "</tr>".

    "<tr>
      <td align=right><input type=\'submit\' value=\'Upload\'></td>
    </tr> 
    </table>");
 MakeTkn();
 echo("</form>");
  //---------------------------------------------------------------------------------  
?>
</body></html>' ;

fwrite($file,$S);

fclose($file);
?>