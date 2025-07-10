<?php
session_start();

include "../setup/common_pg.php";
BeginProc();
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<meta http-equiv="Content-Language" content="ru">
<title>AdmTabRights Card</title></head>
<body>
<?php
//CheckLogin1 ();
CheckRight1 ($pdo, "Admin");

//print_r($_REQUEST);
//die();


if (is_array($_REQUEST['Chk'])) { 
  $Res='';
  $Div='';
  foreach ( $_REQUEST['Chk'] as $Indx=> $Val) {
    
      $PKValArr=json_decode(base64_decode($Val), true);
      
    $V="'".addslashes($PKValArr['TabNo'])."'";
    $V.=",'".addslashes($PKValArr['Right'])."'";
      $Res.="$Div($V)";
      $Div=",";
    }
    if ($_REQUEST['OpType']== GetStr($pdo, 'Delete')) {
      $query = "delete from AdmTabRights ".
               " WHERE ( (TabNo,Right) in ($Res) )";
      $sql2 = $pdo->query ($query)
                 or die("Invalid query:<br>$query<br>" . $pdo->error());
      
    }  
    
}
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">'.
'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL=AdmTabRightsList.php?'.$LNK.'">'.
'<title>Save</title></head>
<body>');
  
  echo('<H2>Saved</H2>');
?>
</body>
</html>