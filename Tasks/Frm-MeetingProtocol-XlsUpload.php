<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

OutHtmlHeader ($TabName." form Excel upload");

// Checklogin1();
$Editable = CheckFormRight($pdo, 'MeetingProtocol', 'ExcelUpload');

 
  echo ("<form method='post' action='MeetingProtocol-UploadXlsx.php' enctype='multipart/form-data'>
   <table border='0'>
   <tr>
     <td>Upload XLSX file to MeetingProtocol with up to 4 columns:<br>MId, LineNo, No, <br>
      Description</td>
    </tr>
    <tr>
      <td><input type='file' accept='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' value='File name:' name='userfile'></td>
    </tr> ".
    "<tr>".
     //"<td>Add lines to project: <input type=checkbox name='AddToProject' value=1></td>".
    "</tr>".

    "<tr>
      <td align=right><input type='submit' value='Upload'></td>
    </tr> 
    </table>");
 MakeTkn();
 echo("</form>");
  //---------------------------------------------------------------------------------  
?>
</body></html>