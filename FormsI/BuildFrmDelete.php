<?php

$file = fopen("../Forms/{$TabName}Delete.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= '
include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();'.
"\r\n";
fwrite($file,$S);

$S= '$Editable = CheckFormRight($pdo, \''.$TabName.'\', \'Delete\');

$FldNames=array(';
$Div='';

$kk=0;
foreach ($Fields as $Fld=>$Arr) {
  $kk++;
  if ($kk==4) {
    $S.="\r\n      ";
    $kk=0;
  }
  $S.="$Div'$Fld'";
  $Div=',';

  if ($Arr['DocParamType']==50) {
    
  }
  //echo (" Fld:$Fld ");
}

$S.=");\r\n";

$WH='';
$DW='';

$S.='$New=$_REQUEST[\'New\'];'."\r\n";  
$S.='$PdoArr = array();'."\r\n";  

$FullLink='';
$DivFL='';
foreach ($PKFields as $PK) {
  $S.='$'.$PK.'=$_REQUEST[\''.$PK."'];\r\n";  
  $S.='if ($'.$PK.'==\'\'){ die ("<br> Error:  Empty '.$PK.'");}'."\r\n";
  
  $WH.= $DW. '(\"'.$PK."\\\"= :".$PK.')';
  $S.='$PdoArr["'.$PK.'"] = $'.$PK.';'."\r\n";
  
  $DW=' AND ';
};

$S.="\r\n".
 '$dp=array();
  
  $query = "select * FROM \"'.$TabName.'\" ".
           "WHERE '.$WH.'";
  try{
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  }
  else {
    die ("<br> Error: not found record '.$WH.'"); 
  }
  '.$TabEditable.'
  if (!$Editable) {
    die ("<br> Error: Not Editable record ");
  }
  
  $query = "delete FROM \"'.$TabName.'\" ".
           "WHERE '.$WH.'";
  
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

';  
fwrite($file,$S);

$S='$LNK=\'\';
';

$DL='';
$N=0;
foreach ($PKFields as $PK) {
  $N++;
  $S.= '
  $V=$_REQUEST[\''.$PK.'\'];
  $LNK.="'.$DL.$PK.'=$V";
  ';
  $DL='&';
}

$S.="\r\n".

'echo (\'<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">\'.
\'<META HTTP-EQUIV="REFRESH" CONTENT="1;URL='.$TabName.'List.php">\'.
\'<title>Save</title></head>
<body>\');
  
  echo(\'<H2>Deleted</H2>\');
?>
</body>
</html>';

fwrite($file,$S);

fclose($file);
?>