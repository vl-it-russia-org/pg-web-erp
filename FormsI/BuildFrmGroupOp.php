<?php
// Строит стандартную форму обработки операций над несколькими 
// записями таблицы *GroupOp.php (например удаление нескольких отмеченных 
// пользователем записей из списка *List.php)
$file = fopen("../Forms/{$TabName}GroupOp.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= '
include "'.$DefDir.'setup/common_pg.php";
BeginProc();
OutHtmlHeader ("'.$TabName.' Group Opers");

$Editable = CheckFormRight($pdo, \''.$TabName.'\', \'Card\');
CheckTkn();

//print_r($_REQUEST);
//die();
'.
"\r\n";
fwrite($file,$S);

$FldAccArr=array ();

$S= '
$PdoArr = array();
try {

if (is_array($_REQUEST[\'Chk\'])) { 
  $Res=\'\';
  $Div=\'\';
  foreach ( $_REQUEST[\'Chk\'] as $Indx=> $Val) {
    ';
    if ($PKCnt==1) {
      $S.='
      $PdoArr["V$Indx"]=$Val;
      
      $Res.="$Div:V$Indx";
      $Div=\',\';
    }
    if ($_REQUEST[\'OpType\']== GetStr($pdo, \'Delete\')) {
      $Editable = CheckFormRight($pdo, \''.$TabName.'\', \'MassDelete\');

      $query = "delete from \"'.$TabName.'\" ".
               " WHERE (\"'.$LastPK.'\" in ($Res)) ";
      
      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);
    }  
    ';
    }
    else {
      $S.='
    $PKValArr=json_decode(base64_decode($Val), true);
      ';
      $Div='';
      
      $PKStr='';
      $VDiv='';
      foreach ($PKFields as $Fld) {
        $S.="\r\n    ".
            '$PdoArr["'.$Fld.'_$Indx"]=$PKValArr["'.$Fld.'"];'.
        "\r\n    ".
            '$V'.$VDiv.'="'.$Div.':'.$Fld.'_$Indx";';
        $PKStr.= "$Div\\\"$Fld\\\"";
        $Div=',';
        $VDiv='.'; 
      }
      $S.='
    $Res.="$Div($V)";
    $Div=",";
  }

  if ($_REQUEST[\'OpType\']== GetStr($pdo, \'Delete\')) {
    $Editable = CheckFormRight($pdo, \''.$TabName.'\', \'MassDelete\');
    
    $query = "delete from \"'.$TabName.'\" ".
             " WHERE ( ('.$PKStr.') in ($Res) )";
    
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  }  
    ';

  }
$S.="\r\n}\r\n".

'
}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}
'.

'echo (\'<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">\'.
\'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$TabName.'List.php?\'.$LNK.\'">\'.
\'<title>Save</title></head>
<body>\');
  
  echo(\'<H2>Saved</H2>\');
?>
</body>
</html>';

fwrite($file,$S);

fclose($file);



?>