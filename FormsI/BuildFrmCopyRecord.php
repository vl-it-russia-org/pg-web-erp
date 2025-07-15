<?php
$file = fopen("../Forms/{$TabName}-CopyRecord.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= '
include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();'.
"\r\n";
fwrite($file,$S);

$S= "\$Editable = CheckFormRight(\$pdo, '$TabName', 'Card');\r\nCheckTkn();\r\n".
'$FldNames=array(';
$Div='';


$R=0;


$DigArr='';
$DivDigArr='';

$DateArr='';
$DivDateArr='';

$AutoIncFld='';

foreach ($Fields as $Fld=>$Arr) {
  $R++;
  if ($R>4) {
    $R=0;
    $S.="\r\n      ";
  }

  $S.="$Div'$Fld'";
  $Div=',';
  
  //echo (" Fld:$Fld ");
  //echo ("<hr>$Fld");
  //print_r($Arr);
  //echo ("<hr>");

}
$S.=");\r\n".
    "CheckTkn();\r\n".
    "if (\$_REQUEST['NewStatus']!='Copy') { \r\n".
    "  die ('<br> Error: Copy expected');\r\n".
    "}\r\n".
    "\$PdoArr=array();\r\n\r\n";

$PKFldsList='';
$PkDiv='';
$WH='';
$DivWH='';
foreach ($PKFields as $PK) {
  $S.="if (empty(\$_REQUEST['$PK'])) {\r\n".
      "  die ('<br>Error: $PK is empty');\r\n".
      "}\r\n".
      "\$PdoArr['$PK']=\$_REQUEST['$PK'];\r\n\r\n";
  $WH="$DivWH(\\\"$PK\\\"=:$PK)";
  $DivWH='and';
}

$CopyRecord = GetParam_TabAddFunc($pdo, $TabName, 60);
$CopyRecordArr= json_decode($CopyRecord, 1);

$S.='$query = "select * FROM \"'.$TabName.'\" ".
         "WHERE '.$WH.'";
try{
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  $NewId="";
  if ($db = $STH->fetch(PDO::FETCH_ASSOC)) {
    $PdoArr=array();';

$S1='';
$S2='';
$Div='';
$AutoInc='';

foreach ( $CopyRecordArr as $CFld => $Arr) {
  //echo ("<br> $CFld : ");
  //print_r($Arr);

  if ($Arr['Val']==20) {        // 20	  Не копировать, оставить пустым
    echo ("<br> No Copy: ");
    print_r($Fields[$CFld]);
    if ($Fields[$CFld]['AutoInc']==1) { 
      $AutoInc=$CFld;
    }
  }
  else
  if ($Arr['Val']==10) {        //  10	  Копировать
    $S1.="$Div\\\"$CFld\\\"";
    $S2.="$Div:$CFld";
    $S.="\r\n    \$PdoArr['$CFld']=\$db['$CFld'];";
    $Div=', ';
  
  }
  else
  if ($Arr['Val']==30) {        //  30	  Заполнить текущей датой
    $S1.="$Div\\\"$CFld\\\"";
    $S2.="$Div{now()}";
    $Div=', ';
  }
  else
  if ($Arr['Val']==40) {        //  40	  Текущее дата и время
    $S1.="$Div\\\"$CFld\\\"";
    $S2.="$Div{now()}";
    $Div=', ';
  }
  else
  if ($Arr['Val']==50) {        //  50	  Использовать текущего пользователя
    $S.="\r\n    \$V=\$_SESSION['login'];";
    $S1.="$Div\\\"$CFld\\\"";
    $S2.="$Div:$CFld";
    $S.="\r\n    \$PdoArr['$CFld']=\$V;";
    $Div=', ';
  }
  else
  if ($Arr['Val']==60) {        //  60	  Внести указанное значение
    $S.="\r\n    \$V='{$Arr['AddVal']}';";
    $S1.="$Div\\\"$CFld\\\"";
    $S2.="$Div:$CFld";
    $S.="\r\n    \$PdoArr['$CFld']=\$V;";
    $Div=', ';
  }
}

$S.="\r\n\r\n    \$query=\"insert into \\\"$TabName\\\" ($S1) values ($S2)\";\r\n".
    "\r\n    \$STH = \$pdo->prepare(\$query);".
    "\r\n    \$STH->execute(\$PdoArr);";

if ($AutoInc!='') {
  $S.="\r\n    \$NewId=\$pdo->lastInsertId();";
}

$S.='
  }
  else {
    die ("<br> Error: Record is not found");
  }
}
catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

';


$S.='echo (\'<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">\'.
\'<title>'.$TabName.' Save</title></head>
<body>\');
  echo(\'<H2>'.$TabName.' Saved</H2>\');


  echo(\'<form id="autoForm" method="post" action="'.$TabName.'List.php" style="display: none;">\');
';

$DL='';
$N=0;
foreach ($PKFields as $PK) {
  $N++;
  $S.= '
  $V=$_POST[\''.$PK.'\'];
  echo("<input type=hidden name='.$PK.' value=\'$V\'>\r\n");
';
}
$S.='
  MakeTkn();
  echo (" </form>

            <script>
                // Автоматически отправляем форму через небольшую задержку
                window.onload = function() {
                    setTimeout(function() {
                        document.getElementById(\'autoForm\').submit();
                    }, 10); // Задержка 0.01 секунда для показа анимации
                };
            </script>
  ");
?>
</body>
</html>';



fwrite($file,$S);

fclose($file);
?>