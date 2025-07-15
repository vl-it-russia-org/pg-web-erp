<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

OutHtmlHeader ($TabName." form Excel upload");

// Checklogin1();
$Editable = CheckFormRight($pdo, 'Meetings', 'ExcelUpload');

 
  echo ("<form method='post' action='Meetings-UploadXlsx.php' enctype='multipart/form-data'>
   <table border='0'>
   <tr>
     <td>Upload XLSX file to Meetings with up to 3 columns:<br>Id, MeetingDate, Subject</td>
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