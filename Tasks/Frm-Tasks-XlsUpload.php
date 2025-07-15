<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();

OutHtmlHeader ($TabName." form Excel upload");

// Checklogin1();
$Editable = CheckFormRight($pdo, 'Tasks', 'ExcelUpload');

 
  echo ("<form method='post' action='Tasks-UploadXlsx.php' enctype='multipart/form-data'>
   <table border='0'>
   <tr>
     <td>Upload XLSX file to Tasks with up to 19 columns:<br>Id, ShortName, Created, <br>
      StartDate, Author, Division, Priority, <br>
      Description, WishDueDate, PlannedWorkload, FactWorkLoad, <br>
      PlannedDueDate, Status, RespPerson, UserSatisfaction, <br>
      WaitTill, TaskGroup, SFProject, TaskYearCode</td>
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