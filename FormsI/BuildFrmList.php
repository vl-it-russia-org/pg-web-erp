<?php
session_start();

include ("../setup/common_pg.php");
include ("BuildChangeStatus.php");
include ("common_func.php");



CheckRight1 ($pdo, 'RIGHT_EDIT');
mb_internal_encoding("UTF-8");

include "../js_module.php";

$DefDir='../';

$Add_FldsArr=array ();   // Для повторяющихся полей связи (если к одной таблице несколько полей)
$Add_FldsArrT=array ();


$EnumFlds=array();

$FromFldsConn=array();
$ToFldsConn=array();
$ConnCount=0;

if (empty ($_REQUEST['TabNo'])) {
  die ("<br>Error: Bad TabNo ");
}

$TabNo = $_REQUEST['TabNo'];

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

try {

$query = "select * from \"AdmTabNames\" ".
         "where (\"TabCode\"=:TabNo)";

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);
  
$TabName='';
$TabEditable='';
if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $TabName=$dp2['TabName'];
  if ($dp2['TabEditable']!='') {
    $TabEditable=$dp2['TabEditable'];
  }      
}
else {
  die ("<br> Error: Bad Table Name ");
}

echo ("<br> TabName: $TabName ");

OutHtmlHeader ("Build forms for: $TabName $TabNo");

//================================================================
// AdmTabFields
// TypeId, ParamNo, ParamName, NeedSeria, DocParamType, NeedBrand, Ord, AddParam, 
// DocParamsUOM, CalcFormula, AutoInc, Description, BinCollation, ShortInfo, EnumLong

$query = "select * from \"AdmTabFields\" ".
         "where (\"TypeId\"=:TabNo) order by \"Ord\", \"ParamNo\"";
  
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$Fields=array();
$F2=array();

$EnumTxt='';
$Div='';


$DateTxt='';
$DateDiv='';


$AutoIncArr=array();


while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $Fields[$dp2['ParamName']]=$dp2;
  if ($dp2['AutoInc']) {
    $AutoIncArr[]=$dp2['ParamName'];
  }  
  $F2[$dp2['ParamNo']]=$dp2['ParamName'];


  if ($dp2['DocParamType']==50) {     // ----------------  Enum
    $EnName= $dp2['ParamName'];
    if ( !empty (trim($dp2['AddParam'])) ) {
      $EnName=$dp2['AddParam'];  
    }

    $EnumTxt.= "$Div'{$dp2['ParamName']}'=>'{$EnName}'";
    $Div=",\r\n        ";
  
    $EnumFlds[$dp2['ParamName']]=$EnName;
  }

  if ($dp2['DocParamType']==60) {     // ----------------  Date
    $DateTxt.= "$DateDiv'{$dp2['ParamName']}'=>1";
    $DateDiv=",\r\n        ";
  }

}


// Для ссылок на другие таблицы 
$query = "select * from \"AdmTab2Tab\" ".
         "where (\"TabName\"=:TabName) and (\"Tab2\"!='') and (\"Field2\"!='') ";

$PdoArr = array();
$PdoArr['TabName']= $TabName;
  

//echo ("<br>$query<br>");
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$FOtherTab=array();
$ExtTab=array();
$ExtTab1=array();

$ExtTab3=array();


$HaveRef=0;
while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FOtherTab[$dp2['FldName']][$dp2['LineNo']]=$dp2;
  

  $Tab2= $dp2['Tab2'];
  $i=0;
  $Tab2N=GetTableName ($pdo, $Tab2, $i);

  $ExtTab3 [$dp2['FldName']][$Tab2N]=$dp2['LineNo'];


  $FOtherTab[$dp2['FldName']][$dp2['LineNo']]['TabName2']=$Tab2N;

  if (empty ($Add_FldsArr[$Tab2N])) {
    $Add_FldsArr[$Tab2N]=1;
  }
  else {
    $Add_FldsArrT[$dp2['FldName']]=$Add_FldsArr[$Tab2N];
    $Add_FldsArr[$Tab2N]+=1;
  }      
      
  echo ("<br>ExtTab: $Tab2N / $Tab2 ");

  if ($Tab2N!='') {
    $ExtTab[$Tab2N]=1;
    $ExtTab1[$dp2['Id']]=$Tab2N;
    $HaveRef=1;
  } 


  $i=0;
  $Str=$dp2['AddConnFldFrom'];
  $Fin=0;
  while (!$Fin) {
    $NewFld=GetFieldName($pdo, $Str,$i);
    if ($NewFld=='') {
      $Fin=1;
    }
    else {
      $FromFldsConn[$dp2['Id']][]=$NewFld;
    }
  } 

}

//echo ("<br>");
//print_r ($FromFldsConn);


//echo ("<br>");
//print_r ($Fields);

//echo ("<br>");
//print_r ($F2);

//echo ("<br> --- ");
//print_r ($ExtTab);
//echo ("<br>");

//echo ("<br> --- ");
//print_r ($FOtherTab);
//echo ("<br>");

//================================================================

$query = "select F.* from \"AdmTabIndxFlds\" F ".
           "where (F.\"TabCode\"=:TabNo) and (F.\"IndxName\"='{$TabName}_pkey') ".
           "order by \"LineNo\"";
  
//echo ("<br>$query<br>");
$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$PKFields=array();
$Div='';
$PKList='';
$LastPK='';
while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FldNo = $dp2['FldNo']; 
  $FldName = $F2[$FldNo];

  $PKFields[]= $FldName;
  $PKList.= $Div.'"'.$FldName.'"';
  $Div=', ';
  $LastPK=$FldName;    
}

//echo ("<br> PkFields: ");
//print_r ($PKFields);

//================================================================
//                              List
//================================================================



$file = fopen("../Forms/{$TabName}List.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

//==========================================================
// HTML editor- ?
//=================================================
$AddInclude='';

$HtmlFlds=array();
$HaveHtmlFlds=0;
// AdmFieldsAddFunc
// Id, TabName, FldName, AddFunc
$query = "select * from \"AdmFieldsAddFunc\" ". 
         "where (\"TabName\" = :TabName) and (\"AddFunc\" =5) order by \"FldName\" "; 

$PdoArr = array();
$PdoArr['TabName']= $TabName;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

while ($dp25 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $HaveHtmlFlds=1;
  $HtmlFlds[$dp25['FldName']]= 1;
}

if ($HaveHtmlFlds==1) {
  $AddInclude='
include ("../setup/HtmlTxt.php");
';
}

//==========================================================


$S= 'include ("../setup/common_pg.php");'.$AddInclude.'
BeginProc();

$TabName=\''.$TabName.'\';
OutHtmlHeader ($TabName." list");

include ("../js_SelAll.js");'.
"\r\n";
fwrite($file,$S);

$S= '
$CurrFile=\''.$TabName.'List.php\';
$Frm=\''.$TabName.'\';'.
"\r\n";
fwrite($file,$S);

$S= '$Fields=array(';
$Div='';

$enS='$enFields= array(';
$enDiv='';
$enArr=array ();

$DigArr=array();

$kk=0;
foreach ($Fields as $Fld=>$Arr) {
  $kk++;
  if ($kk==4) {
    $S.="\r\n      ";
    $kk=0;
  }

  $S.="$Div'$Fld'";
  $Div=',';

  if ($Fields[$Fld]['DocParamType']==50) { // enum
    $SetName= $Fld;
    if ($Fields[$Fld]['AddParam']!='') {
      $SetName= $Fields[$Fld]['AddParam']; 
    } 
    
    $enS.="$enDiv'$Fld'=>'$SetName'";
    $enArr[$Fld]=$SetName;
    $enDiv=', ';
  }

  if ($Fields[$Fld]['DocParamType']==20) { // numbers
    if ($Fields[$Fld]['AddParam']!='') {
      $p=strpos($Fields[$Fld]['AddParam'], '.');
      if ($p!==false) {
        $len=strlen(trim($Fields[$Fld]['AddParam']));
        if ($len>2){
          $len-=2;
        }
        $DigArr[$Fld]=$len;
      }
    }
  }

}

$S.=");\r\n".
    $enS.");\r\n".

'
$Editable = CheckFormRight($pdo, \''.$TabName.'\', \'List\');

CheckTkn();
$ArrPostParams=array();

// Какие параметры передаем в форму '.$TabName.'Card.php
$CardArr=array();
$CardArr[\'FrmTkn\']=MakeTkn(1); 

$BegPos = 0;'."\r\n".
  'if (!empty($_REQUEST[\'BegPos\'])) {'."\r\n".
  '  $BegPos = $_REQUEST[\'BegPos\'] +0;'."\r\n".
  '};'."\r\n\r\n".
  '$ORD = \''.$PKList.'\';'."\r\n".
  'if ($ORD ==\'1\') {'."\r\n".
    '$ORD = \''.$PKList.'\';
  }
  else {
    $ORD = \''.$PKList.'\';
  }

  $ORDS = \' order by  \'; 
  if ($ORD !=\'\') {
    $ORDS = \' order by \'.$ORD;
  }
  
  $WHS = \'\';
  $FullRef=\'?ORD=1\';
  
  $PdoArr = array();
  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST[\'Fltr_\'.$Fld];
    
    if ($Fltr!=\'\') {
      if ($WHS !=\'\') {
        $WHS.= \' and \';
      }
      
      if ($enFields[$Fld]!=\'\') {
        $PdoArr[$Fld]= $Fltr;
        
        $WHS.=\'("\'.$Fld."\" = :$Fld)"; 
      }
      else {
        $WHS.= SetFilter2Fld ( $Fld, $Fltr, $PdoArr );
      }
      $FullRef.=\'&Fltr_\'.$Fld.\'=\'.$Fltr ;
      $ArrPostParams[$Fld]=$Fltr;
    }
  }
'.
"\r\n";
fwrite($file,$S);


$S='
try {
  if ($WHS != \'\') {
    $WHS = \' where \'.$WHS;
  };

  $PageArr=array();

  $queryCNT = "select COUNT(*) \"CNT\" FROM \"'.$TabName.'\" ".
              "$WHS ";
  
  $STHCnt = $pdo->prepare($queryCNT);
  $STHCnt->execute($PdoArr);
  
  $CntLines=0;
  if ($dp = $STHCnt->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp[\'CNT\'];  
  };
  
  $PageInfo = CalcPageArr($pdo, $PageArr, $CntLines);

  $query = "select * FROM \"'.$TabName.'\" ".
           "$WHS $ORDS ". AddLimitPos($PageArr[\'BegPos\'], $PageArr[\'LPP\']);

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
  

  echo (\'<br><b>\'.GetStr($pdo, \''.$TabName.'\').\' \'.
        GetStr($pdo, \'List\').
        \'</b> \'.$PageInfo) ;
  
  echo (\'<form method=post action="\'.$CurrFile.\'"><table><tr>\');
  $i=0;
  foreach ( $Fields as $Fld) {
    if ($i==4){
      echo(\'</tr><tr>\');
      $i=0;
    }     
    $i++;
    $CN= "Fltr_$Fld";
    echo("<td align=right><label for=\"$CN\">".GetStr($pdo, $Fld).":</label></td>");

    if ($enFields[$Fld]!=\'\'){
      echo("<td>".EnumSelection($pdo, $enFields[$Fld],"$CN ID=$CN", $_REQUEST[\'Fltr_\'.$Fld], 1)."</td>");
    }
    else {
      echo("<td><input type=text size=12 name=\'$CN\' id=\'$CN\' value=\'".
        $_REQUEST[$CN]."\'></td>");
    }
  }
  MakeTkn();
  echo (\'<td><button type="submit">Filter</button></td></tr></table></form>\');
  ';

fwrite($file,$S);


$S='echo (\'<hr><table><tr><td><form method=post action="'.$TabName.'Card.php">\'.
        \'<input type=hidden Name=New VALUE=1>\'.
        "<input type=submit Value=\'".GetStr($pdo, \'New\')."\'>");
    MakeTkn();
    echo("</form></td><td>" );
//--------------------------------------------------------------------------------
echo (\'<form method=post action="'.$TabName.'GroupOp.php">\'.
        "<input type=submit  Name=OpType Value=\'".GetStr($pdo, \'Delete\')."\' 
          onclick=\'return confirm(\"Delete selected?\");\'></td></tr></table>" );
MakeTkn();
echo (\'<table class=LongTable><tr class="header">\');

echo("<th><input type=checkbox onclick=\'return SelAll();\'></th><th></th>");


foreach ( $Fields as $Fld) {
  echo("<th>".GetStr($pdo, $Fld)."</th>");
}
echo("</tr>");

$n=0;
$Cnt=0;
while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $Cnt++;
  $classtype="";
  $n++;
  if ($n==2) {
    $n=0;
    $classtype=\' class="even"\';
  }
  
  echo ("<tr".$classtype.">");
';

if ($PKCnt==1) {
  $S.='
  echo ("<td><input type=checkbox ID=\'Chk_$Cnt\' Name=Chk[$Cnt] value=\'{$dp[\''.
         $LastPK.'\']}\'></td>");
  ';
}
else {
  $MS='';
  foreach ($PKFields as $Fld) {
    $MS.="\r\n  ".'$PKValArr[\''.$Fld.'\']= $dp[\''.$Fld.'\'];';  
  }
  $S.='
  $PKValArr=array();'.$MS.'
  $PKRes=base64_encode( json_encode($PKValArr));
  
  echo ("<td><input type=checkbox ID=\'Chk_$Cnt\' Name=Chk[$Cnt] value=\'$PKRes\'></td>");
  ';
}

foreach ($PKFields as $Fld) {
  $S.="\r\n  ".
      '$CardArr[\''.$Fld.'\']= $dp[\''.$Fld.'\'];';  
}

$S.="\r\n  ".
    '$Json = base64_encode(json_encode ($CardArr));

  $CrdNewWindow =GetStr($pdo, \'CrdInNewWnd\');
  $CrdHere =GetStr($pdo, \'CrdInCurrWnd\');';


$S.="\r\n    ".
    'echo("<td align=center>'.
    "\r\n        ".
    ' <button type=button onclick=\"openFormWithPost(\''.$TabName.'Card.php\', \'$Json\', \'_self\')\" title=\'$CrdHere\'>&#9900;</button>'.
    "\r\n        ".
    ' <button type=button onclick=\"openFormWithPost(\''.$TabName.'Card.php\', \'$Json\', \'_blank\')\" title=\'$CrdNewWindow\'>&#9856;</button>'.
    ' </td>");';


foreach ($Fields as $Fld=>$Arr) {
  $S.="\r\n\r\n".'  $Fld=\''.$Fld.'\';
  ';
  if (0==1) {
    $S.='echo("<td><a href=\''.$TabName.'Card.php?';

    $Div='';
    foreach ( $PKFields as $Fld) {
      $S.=$Div.$Fld.'={$dp[\''.$Fld.'\']}';
      $Div='&';  
    }

    $S.='\'>';
    if ($enArr[$LastPK]!='') {
      $S.='".GetEnum($pdo, \''.$enArr[$LastPK].'\',$dp[$Fld])."'; 
    }
    else {
      $S.='{$dp[$Fld]}';
    }
    $S.='</a></td>");
  ';
  }
  else 
  if ($enArr[$Fld]!=''){
    $S.='echo("<td>".GetEnum($pdo, \''.$enArr[$Fld].'\', $dp[$Fld])."</td>");
  ';

  }
  else 
  if ($DigArr[$Fld]!=0){
    $S.='$OW=number_format($dp[$Fld], '.$DigArr[$Fld].', ".", "\'");
  echo("<td align=right> $OW </td>");
  ';

  }
  else
  if ($Fields[$Fld]['DocParamType']==30) {
    $S.='echo(\'<td align=center>\'.$dp[$Fld]."</td>");
  ';
  
  }
  else 
  if ($HtmlFlds[$Fld]==1) {
    $S.='echo(\'<td>\'.HtmlTxt($dp[$Fld])."</td>");
  ';
  }
  else
  {
    $S.='echo(\'<td>\'.$dp[$Fld]."</td>");
  ';
  }
};
$S.='echo("</tr>");
}
echo("</table>".
     "<input type=hidden ID=AllCnt value=\'$Cnt\'>".
     "<input type=submit Name=OpType Value=\'".GetStr($pdo, \'Delete\')."\' 
          onclick=\'return confirm(\"Delete selected?\");\'></form>");

echo(\'<table><tr class="header">\');

OutListFooter($pdo, $CurrFile, $ArrPostParams, $PageArr);


echo (\'<td><a href="'.$TabName.'PrintXLS.php\'.$FullRef.\'">Print XLS</a></td>\''.".\r\n".'
      \'<td><a href="Frm-'.$TabName.'-XlsUpload.php">Upload from XLS</a></td>\''.".\r\n".'
       \'</tr></table>\');

}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}


?>
</body>
</html>
';
fwrite($file,$S);

fclose($file);

echo ("<br><a href='../Forms/{$TabName}List.php'>Frm List $TabName</a> ");

//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
//                             PrintXLS 
//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------

$file = fopen("../Forms/{$TabName}PrintXLS.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= 
"require '../../composer/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;\r\n".


'include ("../setup/common_pg.php");
BeginProc();'."\r\n";

fwrite($file,$S);

$S= 'define(\'EOL\',(PHP_SAPI == \'cli\') ? PHP_EOL : \'<br />\');'."\r\n"; 

//'require_once \'../PhpExcel/Classes/PHPExcel.php\';'."\r\n";

$S.= '$TabName=\''.$TabName.'\';
$Frm=\''.$TabName.'\';'."\r\n";

fwrite($file,$S);

$S= '$Fields=array(';
$Div='';

$enS='$enFields= array(';
$enDiv='';
$enArr=array ();

$DigArr=array();

$kk=0;
foreach ($Fields as $Fld=>$Arr) {
  $kk++;
  if ($kk==4) {
    $S.="\r\n      ";
    $kk=0;
  }

  $S.="$Div'$Fld'";
  $Div=',';

  if ($Fields[$Fld]['DocParamType']==50) { // enum
    $SetName= $Fld;
    if ($Fields[$Fld]['AddParam']!='') {
      $SetName= $Fields[$Fld]['AddParam']; 
    } 
    
    $enS.="$enDiv'$Fld'=>'$SetName'";
    $enArr[$Fld]=$SetName;
    $enDiv=', ';
  }

  if ($Fields[$Fld]['DocParamType']==20) { // numbers
    if ($Fields[$Fld]['AddParam']!='') {
      $p=strpos($Fields[$Fld]['AddParam'], '.');
      if ($p!==false) {
        $len=strlen(trim($Fields[$Fld]['AddParam']));
        if ($len>2){
          $len-=2;
        }
        $DigArr[$Fld]=$len;
      }
    }
  }

}

$S.=");\r\n".
   $enS.");\r\n".

"CheckRight1 (\$pdo, 'Admin');\r\n\r\n ".

  '$ORD = $_REQUEST[\'ORD\'];'."\r\n".
  'if ($ORD ==\'1\') {'."\r\n".
    '$ORD = \''.$PKList.'\';
  }
  else {
    $ORD = \''.$PKList.'\';
  }

  $ORDS = \' order by  \'; 
  if ($ORD !=\'\') {
    $ORDS = \' order by \'.$ORD;
  }

  $PdoArr = array();
  $WHS = \'\';
  $FullRef=\'?ORD=\'.$ORD;
  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST[\'Fltr_\'.$Fld];
    if ($Fltr!=\'\') {
      if ($WHS !=\'\') {
        $WHS.= \' and \';
      }
      if ($enFields[$Fld]!=\'\') {
        $WHS.=\'("\'.$Fld."\" = :$Fld)";
        $PdoArr[$Fld]= $Fltr;
      }
      else {
        $WHS.= SetFilter2Fld ( $Fld, $Fltr, $pdo );
      }
      $FullRef.=\'&Fltr_\'.$Fld.\'=\'.$Fltr ;
    }
  }
'.
"\r\n";
fwrite($file,$S);

$Let = array ( 'A','B','C','D','E', 'F','G','H','I','J',
                   'K','L','M','N','0', 'P','Q','R','S','T','U');

$S="
\$Let = array ( 'A','B','C','D','E', 'F','G','H','I','J',
                   'K','L','M','N','0', 'P','Q','R','S','T','U');
".

'
$objPHPExcel = new Spreadsheet();
$objPHPExcel->getProperties()->setCreator("Vladislav Levitskiy")
             ->setLastModifiedBy($_SESSION[\'login\'])
             ->setTitle("AccPhp '.$TabName.'")
             ->setSubject("'.$TabName.'")
             ->setDescription("VDL PHP+PDO+PostgreSQL")
             ->setKeywords("AccPhp;'.$TabName.'")
             ->setCategory("AccPhp;'.$TabName.'");
  
$objPHPExcel->setActiveSheetIndex(0);
$aSheet = $objPHPExcel->getActiveSheet();

$W= array ( 10, 10, 10, 10, 10, 10, 10, 11, 11, 12, 12);
$LastCol="";
foreach ($W as $i => $Val) {
  $aSheet->getColumnDimension($Let[$i])->setWidth($Val);
  $LastCol=$Let[$i];
};
';

fwrite($file,$S);

$S='
if ($WHS != \'\') {
  $WHS = \' where \'.$WHS;
};

$row=1;
$col=1;   


$aSheet->setCellValue([$col, $row], GetStr($pdo, \''.$TabName.'\').
      \' \'.  GetStr($pdo, \'List\'));
  
$row++;
$aSheet->setCellValue([$col, $row], GetStr($pdo, \'Created\').
      ": {$_SESSION[\'login\']} ". date("Y-m-d H:i:s"));


$query = "select * FROM \"'.$TabName.'\" ".
         "$WHS $ORDS";

try {
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);

';

fwrite($file,$S);


$S='

$row++;
$col=1;

$FL=$row;

foreach ( $Fields as $Fld) {
  $aSheet->setCellValue([$col, $row], GetStr($pdo, $Fld));
  $col++; 
}

$n=0;
$Cnt=0;
$row++;

while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $col=1;
';

foreach ($Fields as $Fld=>$Arr) {
  
  $S.="\r\n".
      '  $Fld=\''.$Fld.'\';'."\r\n";
  
  if ($enArr[$Fld]!=''){
    $S.='  $aSheet->setCellValue([$col, $row], GetEnum($pdo, \''.$enArr[$Fld].'\', $dp[$Fld]));'."\r\n";
  }
  else 
  if ($DigArr[$Fld]!=0){
    $S.='  $OW=$dp[$Fld]; //$OW=number_format($dp[$Fld], '.$DigArr[$Fld].', ".", "\'");
    $aSheet->setCellValue([$col, $row], $OW);'."\r\n";
  }
  else {
    $S.='  $aSheet->setCellValue([$col, $row], $dp[$Fld]);'."\r\n";
  }
  $S.='  $col++;'."\r\n";
};
$S.='  $row++;
}
';

fwrite($file,$S);
//------------------------------ Page setup

$S='
  $l=$row-1;
  $aSheet->setAutoFilter("A3:{$LastCol}3");
  $aSheet->freezePane("C4");

  $styleArray = array(
      \'borders\' => array(
          \'outline\' => array(
              \'borderStyle\' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
              //\'color\' => array(\'argb\' => \'FFFF0000\'),
          ),
          
          \'inside\' => array(
              \'borderStyle\' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
              //\'color\' => array(\'argb\' => \'FFFF0000\'),
          ),
      ),
  );  

  $aSheet->getStyle("A$FL:$LastCol$l")->applyFromArray($styleArray);



  $aSheet->getPageSetup()
                ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                                                                           // ::ORIENTATION_PORTRAIT );
  $aSheet->getPageSetup()
                ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);


  $margin = 0.5 / 2.54;
  $aSheet->getPageMargins()->setTop($margin*5);
  $aSheet->getPageMargins()->setRight($margin);
  $aSheet->getPageMargins()->setLeft($margin*2);
  $aSheet->getPageMargins()->setBottom($margin);

  //$aSheet->getPageSetup()->setScale(80);

  $aSheet->getPageSetup()->setFitToWidth(1);
  $aSheet->getPageSetup()->setFitToHeight(10);  

  $add_str=date(\'-Ymd_His\');


  //MakeAdminRec ($pdo, $_SESSION[\'login\'], \'EDI_ORD\', $OrdId, 
  //                      \'Out XLS\', "Out file $add_str.XLS: $LineNo lines, amount $TotAmount");

  $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel , \'Xlsx\');

 }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


';
fwrite($file,$S);


$S="
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=Xls-$TabName'.\$add_str.'.xlsx');
\$writer->save('php://output');
?>";

fwrite($file,$S);

fclose($file);


echo ("<br><a href='../Forms/{$TabName}List.php'>Print XLS $TabName</a> ");

//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
//                             GroupOp 
//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
echo ("<br> Make group op");

include ("BuildFrmGroupOp.php");

//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
//                         Card
//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
echo ("<br> Make Card");

include "BuildFrmCard.php";


//--------------------------------------------------------------------------------
//   Save file
//--------------------------------------------------------------------------------
echo ("<br> Make Save");
include "BuildFrmSave.php";

//--------------------------------------------------------------------------------
//                Delete file
//--------------------------------------------------------------------------------
echo ("<br> Make Delete");
include "BuildFrmDelete.php";

//--------------------------------------------------------------------------------
//                Form Xls Upload file
//--------------------------------------------------------------------------------
echo ("<br> Make Form Xls upload");
include "BuildFrmXlsFrm.php";

//===============================================================
//                        Upload XLS File
//===============================================================

$file = fopen("../Forms/{$TabName}-UploadXlsx.php","w");
fwrite($file,"<?php  
session_start();
include (\"../setup/common.php\");
BeginProc();\r\n");

$S='
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Language" content="ru">
<link rel="stylesheet" type="text/css" href="../style.css">
<link rel="icon" href="../favicon.ico" type="image/x-icon">
<title>Upload xlsx to '.$TabName.'</title></head>
<body>
<?php
echo \'<br>User: \' . $_SESSION[\'login\'].\'<br>\';

//print_r($_FILES);
//echo ("<br>");

//print_r($_REQUEST);
//echo ("<br>");

//error_reporting(E_ALL);
//ini_set(\'display_errors\', 1);

include "common_func.php";
require \'../../composer/vendor/autoload.php\';

use PhpOffice\PhpSpreadsheet\IOFactory;


CheckRight1 ($pdo, \'ExtProj.Admin\');

$FileName=\''.$TabName;


$S.='\';

$real_name = "$TmpFilesDir/SIUpl/$FileName.xlsx";

echo ("<br>File $real_name<br>");
ini_set(\'memory_limit\', \'2048M\');

//=============================================================================================
// Copy file to temp dir 
';
$S.='
$size = $_FILES[\'userfile\'][\'size\'];
$name_temp = $_FILES[\'userfile\'][\'tmp_name\'];
$type = $_FILES[\'userfile\'][\'type\'];

$dir = "$TmpFilesDir/SIUpl";
';

$S.='
$sizeStr=\'\';
if ($size> (1024*1024) ) {
  $sizeStr = round($size/1024/1024, 1).\'M\';
  if ($Size > 10000000 ) {
    die ("<br> file size $sizeStr is not Allowed try upload less");
  }
}';

$S.='else{
  if ($size>1024) {
    $sizeStr = round ($size/1024, 1).\'K\';
  }
  else
    $sizeStr = $size.\'b\';
};
echo ("File: $real_name $sizeStr<br>");
';


$S.='
if (file_exists  ( $real_name )) {
  unlink ($real_name);
};

if (move_uploaded_file($name_temp, $real_name)) {
  echo ("Document downloaded $Firm<br>");

   MakeAdminRec ($pdo, $_SESSION[\'login\'], \'UploadXlsx\', $sizeStr, 
                        $FileName, \'Uploaded xlsx file\');
  
}
else {
  die ("<br> Error: Uploading is not ok file:".__'. 'FILE__." line:".__'.'LINE__);
}';

$S.='

//=============================================================================================

// Запоминаем параметры (в какой колонке хранятся)
$ColsArr=array ();

$PkIndx=\'\';

$L=3;

$HeadersArr=array ();

$objPHPExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($real_name);

//$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
//  Get worksheet dimensions


echo("<hr><h4> Upload Xlsx file for ".GetStr($pdo, \''.$TabName.'\')."</h4>");
';

fwrite($file, $S);

$S='
$FldsIndxArr= array (';

$KK=0;
$Div='';
foreach ($Fields as $Fld=>$Arr) {
  $S.= $Div;
  $KK++;
  if ($KK>4) {
    $KK=0;
    $S.="\r\n          ";
  }

  $S.=' \''.$Fld.'\'=>-1';
  $Div=', ';
}
$S.=');

$ColHeader= array (' ;

$KK=0;
$Div='';
foreach ($Fields as $Fld=>$Arr) {
  $S.= $Div;
  $KK++;
  if ($KK>4) {
    $KK=0;
    $S.="\r\n          ";
  }

  $S.=' \''.$Fld.'\'=>\''. addslashes(GetStr($pdo, $Fld)) .'\'';
  $Div=', ';
}
$S.=');

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, false, false, false);

$Cnt = count($sheetData);
echo ("<br>Rows= $Cnt<br>");

$EnumArr=array('.$EnumTxt.');
$DateArr=array('.$DateTxt.');
';

fwrite($file,$S);

$S = '
$BegLine=7;
$HeadLine=$sheetData[$BegLine];

foreach ( $HeadLine as $Col=> $Val) {
  $FindIdnx=\'\';
  foreach ( $ColHeader as $ColName => $ColDescr ) {
    if ($FindIndx== "") {
      if ($Val == $ColDescr) {
        $FindIndx=$Col;
        $FldsIndxArr[$ColName]=$Col;       
      }
    }
  }
}

$PKArr=array ();
';

$DivPK='';
foreach ($PKFields as $PK) {
  $S.='$PKArr[\''.$PK.'\']= $FldsIndxArr[\''.$PK.'\'];
';
}

$S.='

$Err=0;
foreach ($FldsIndxArr as $Fld=>$Indx) {
  if ($Indx==-1) {
    $Err++;
    echo ("<br> Error $Err: $Fld is not defined ");
  }
}

if ($Err>0) {
  die ("<br> Have $Err errors. Upload stopped ");
}


foreach ($sheetData as $L=> $Arr) {
  echo ("<hr> $L : ");
  print_r($Arr);


  if ($L> $BegLine ) {
    $Vals=array();
';

fwrite($file,$S);

$S='
    foreach ( $FldsIndxArr as $Fld=>$Col ) {
      $Val = addslashes(trim($Arr[$Col]));
      if ($Val==\'#NULL!\') {
        $Val=\'\';
      }


      $Vals[$Fld]=$Val;
    }

  }


}


';

$Ord='';
$LastPkFld='';
foreach ($PKFields as $PK) {
  $i++;
  $S.="if (\$FldsIndxArr['$PK']==-1) {
  die (\"<br> Error: field $PK: {\$ColHeader['$PK']} is not found \");
}
";
};

fwrite($file,$S);

$S="\r\n?>
</body></html>";

fwrite($file,$S);

fclose($file);



//===============================================================
//                        SubLines |  Small List
//===============================================================
echo ("<br> Make Small list");
include "BuildFrmSmallList.php";


//================================================================
//                              Select -ExtTab-
//================================================================
//echo ("<br>");
//print_r($ExtTab);
echo ("<br>");

foreach ($ExtTab as $TabName=> $I) {

$TabNo='';

$PdoArr = array();
$PdoArr['TabName']= $TabName;

$query = "select \"TabCode\"  from \"AdmTabNames\" ".
         "where (\"TabName\"=:TabName)";
  
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);


if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $TabNo=$dp2['TabCode'];    
}
else {
  die ("<br> Error: Bad Table Name $TabName ");
}

echo ("<br> Build Select for $TabName ($TabNo) ");

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

$query = "select * from \"AdmTabFields\" ".
         "where (\"TypeId\"=:TabNo) order by \"Ord\", \"ParamNo\"";
  

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$Fields=array();
$F2=array();

while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $Fields[$dp2['ParamName']]=$dp2;    
  $F2[$dp2['ParamNo']]=$dp2['ParamName'];    
}

//================================================================
// AdmTabIndxFlds
// TabCode, IndxName, LineNo, FldNo, Ord
// AdmTabIndx
// TabCode, IndxType, IndxName
$query = "select F.* from \"AdmTabIndx\" I, \"AdmTabIndxFlds\" F ".
           "where  (I.\"TabCode\"=:TabNo) and (I.\"TabCode\"=F.\"TabCode\") and ".
           "(I.\"IndxType\"=10) and (F.\"IndxName\"=I.\"IndxName\") ".
           "order by F.\"Ord\", F.\"LineNo\"";
  
//echo ("<br>$query<br>");

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$PKFields=array();
$Div='';
$PKList='';
$LastPK='';

while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FldNo = $dp2['FldNo']; 
  $FldName = $F2[$FldNo];


  $PKFields[]= $FldName;
  $PKList.= $Div.'"'.$FldName.'"';
  $Div=',';
  $LastPK=$FldName;    
}

//echo ("<br> PkFields: ");
//print_r ($PkFields);
//echo("<br>");

$PdoArr = array();
$PdoArr['Tab2Sel']= "[T:$TabName]";
// Для ссылок на другие таблицы 
$query = "select * from \"AdmTab2Tab\" ".
         "where (\"Tab2\"=:Tab2Sel)";
  
//echo ("<br>$query<br>");
$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$FOtherTab=array();
$ExtTab=array();

$FromFlds=array();
$ToFlds=array();

$Fld2='';

while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FOtherTab[$dp2['Id']]=$dp2;
  
  
  $i=0;
  $Str=$dp2['Field2'];
  $Fld2=GetFieldName($pdo, $Str,$i);
  
  $FOtherTab[$dp2['Id']]['Fld2Name']=$Fld2;


  $i=0;
  $Str=$dp2['AddFldsListTo'];
  $Fin=0;
  while (!$Fin) {
    $NewFld=GetFieldName($pdo, $Str,$i);
    if ($NewFld=='') {
      $Fin=1;
    }
    else {
      $ToFlds[$dp2['Id']][]=$NewFld;
    }
  } 
  
  $i=0;
  $Str=$dp2['AddFldsListFrom'];
  $Fin=0;
  while (!$Fin) {
    $NewFld=GetFieldName($pdo, $Str,$i);
    if ($NewFld=='') {
      $Fin=1;
    }
    else {
      $FromFlds[$dp2['Id']][]=$NewFld;
    }
  }
  //----- connections ---------------------
  $i=0;
  $Str=$dp2['AddConnFldTo'];
  $Fin=0;
  
  while (!$Fin) {
    $NewFld=GetFieldName($pdo, $Str,$i);
    if ($NewFld=='') {
      $Fin=1;
    }
    else {
      $ToFldsConn[$dp2['Id']][]=$NewFld;
      $ConnCount++;
    }
  } 
  
  $i=0;
  $Str=$dp2['AddConnFldFrom'];
  $Fin=0;
  while (!$Fin) {
    $NewFld=GetFieldName($pdo, $Str,$i);
    if ($NewFld=='') {
      $Fin=1;
    }
    else {
      $FromFldsConn[$dp2['Id']][]=$NewFld;
    }
  } 

  
  
  $Tab2N=$TabName;

  $FOtherTab[$dp2['Id']]['TabName2']=$Tab2N;
  
  echo ("<br>ExtTab sel: $Tab2N ");

  if ($Tab2N!='') {
    $ExtTab[$Tab2N]=1;
    $HaveRef=1;
  }
   
}

//echo ("<br>FromFlds: ");
//print_r ($FromFlds);
//echo ("<br>ToFlds: ");
//print_r ($ToFlds);
//echo("<br>FOtherTab: ");
//print_r ($FOtherTab);


$file = fopen("../Forms/Select{$TabName}.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= '
include ("../setup/common_pg.php");
BeginProc();

$TabName=\''.$TabName.'\';
OutHtmlHeader ($TabName." Select");'."\r\n";
fwrite($file,$S);

$S='
$CurrFile=\'Select'.$TabName.'.php\';
$Frm=\''.$TabName.'\';'.
"\r\n";
fwrite($file,$S);

// ----------------------- Пробуем сделать вид ------------

$ShortList='';
$DivSL='';

$PdoArr = array();
$PdoArr['TabNo']= $TabNo;

$query = "select V.*, \"ParamName\" FROM \"AdmViewField\" V, \"AdmTabFields\" F ".
          " where (V.\"TabNo\"=:TabNo) and  (\"ViewNo\"=1) and (F.\"ParamNo\"=V.\"FieldNo\") and ".
          "(V.\"TabNo\"=F.\"TypeId\") order by V.\"TabNo\", \"Ord\"  ";

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$DD=0;
while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {
  $DD++;
  if ($DD==3) {
    $DD=0;
    $ShortList.= "\r\n        ";
  }

  $ShortList.="$DivSL'{$dpL['ParamName']}'";
  $DivSL=', ';
}

//=============================================================================

$S= '$Fields=array(';
$Div='';

$enS='$enFields= array(';
$enDiv='';

$DD=0;

foreach ($Fields as $Fld=>$Arr) {
  $DD++;
  if ($DD==3) {
    $DD=0;
    $S.= "\r\n        ";
  }
  
  $S.="$Div'$Fld'";
  $Div=',';

  if ($Fields[$Fld]['DocParamType']==50) {
    $SetName= $Fld;
    if ($Fields[$Fld]['AddParam']!='') {
      $SetName= $Fields[$Fld]['AddParam']; 
    } 
    
    $enS.="$enDiv'$Fld'=>'$SetName'";
    $enDiv=', ';
  }
}

$S.=");\r\n";

if ( $ShortList !=''){
  $S= '$Fields=array('. $ShortList. ");\r\n"; 
}

$S.=    $enS.");\r\n".

"CheckRight1 (\$pdo, 'Admin');\r\n\r\n".
'CheckTkn();
$ArrPostParams=array();
'.
  '$BegPos = $_REQUEST[\'BegPos\']+0;'."\r\n".
  'if ($BegPos==\'\'){'."\r\n".
    '$BegPos=0;'."\r\n".
  '}'."\r\n"."\r\n".

  '$ORD = $_REQUEST[\'ORD\'];'."\r\n".
  'if ($ORD ==\'1\') {'."\r\n".
    '  $ORD = \''.$PKList.'\';
}
else {
  $ORD = \''.$PKList.'\';
}
$ORDS = \' order by  \'; 
if ($ORD !=\'\') {
  $ORDS = \' order by \'.$ORD;
}
else {
  $ORDS = \' order by \'.$ORD;
}

$PdoArr = array();

try{
  
  $WHS = \'\';
  $FullRef=\'?ORD=\'.$ORD;
  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST[\'Fltr_\'.$Fld];
    if ($Fltr!=\'\') {
      if ($WHS !=\'\') {
        $WHS.= \' and \';
      }
      
      if ($enFields[$Fld]!=\'\') {
        $PdoArr[$Fld]= $Fltr;
        $WHS.=\'("\'.$Fld."\" = :$Fld)";
      }
      else {
        $WHS.= SetFilter2Fld ($Fld, $Fltr, $PdoArr );
      }
      $FullRef.=\'&Fltr_\'.$Fld.\'=\'.$Fltr ;
      $ArrPostParams[$Fld]=$Fltr;
    }
  }
'.
"\r\n";
fwrite($file,$S);
//===============================================================
$S='$ElId   = $_REQUEST[\'ElId\'];
$SubStr = $_REQUEST[\'SubStr\'];
$SelId = $_REQUEST[\'SelId\'];
$SelId2 = $_REQUEST[\'SelId2\'];
$SelId3 = $_REQUEST[\'SelId3\'];
$SelId4 = $_REQUEST[\'SelId4\'];
$Par2   = $_REQUEST[\'Par2\'];

';
fwrite($file,$S);

//echo ("<br> -- FromFlds: ");
//print_r($FromFlds);
//echo ("<br>");

foreach ( $FOtherTab as $I => $Arr) {
  $SH='';
  if (!empty($FromFlds[$I])) {
    foreach ( $FromFlds[$I] as $I1 => $FF1) {
      $Fld=$FF1;
      $SH.=", Val$Fld";
    }
  }

$S='if ($SelId== \''.$I.'\') { 
';
if ( $FOtherTab[$I]['CondTab2']!='') {
  $S.='  if ($WHS !=\'\') {
    $WHS.= \' and \';
  }
  $WHS.= ("'.addslashes ($FOtherTab[$I]['CondTab2']). '");
';
}
$S.='
  echo ("<script>".
    "function SetSelect( Val'.$SH.' ) { 
       OW=window.opener;
     
       var elem1 = OW.document.getElementById(\'$ElId\');
       if (elem1) { 
         elem1.value=Val;
       }
';

  if (!empty($FromFlds[$I])) {  
  foreach ( $ToFlds[$I] as $I1 => $FF1) {
    $Fld=$FromFlds[$I][$I1];
    $S.='
       elem1 = OW.document.getElementById(\''.$FF1.'\');
       if (elem1) { 
         elem1.value=Val'.$Fld.';
       }
      ';
  }
  }
$S.='
       window.close();
    }
    </script>");
';
  fwrite($file,$S);

  $S='';
  if (!empty($ToFldsConn[$I])) {  
    $CntFld=2;
    foreach ( $ToFldsConn[$I] as $I1 => $FF1) {
      $S.='  if ($SelId2!="") {
  if ($WHS!="") $WHS=" and ";
    $WHS.= " (\"'.$FF1.'\" = \'$SelId'.$CntFld.'\')"; 
  }  
    ';
      $CntFld++;
    }
  }
  $S.='
}
';
  fwrite($file,$S);
}
//================================================================
$S='  $PageArr = array();
  
  // Get how many rows total we have   
  if ($WHS != \'\') {
    $WHS = \' where \'.$WHS;
  };   

  $queryCNT = "select COUNT(*) \"CNT\" FROM \"'.$TabName.'\" ".
              "$WHS ";

  $STH = $pdo->prepare($queryCNT);
  $STH->execute($PdoArr);
  
  $CntLines=0;
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CntLines=$dp[\'CNT\'];  
  };

  $InfoPages=CalcPageArr($pdo, $PageArr, $CntLines);

  $query = "select * FROM \"'.$TabName.'\" ".
           "$WHS $ORDS ".AddLimitPos($PageArr[\'BegPos\'], $PageArr[\'LPP\']);

  echo (\'<br><b>\'.GetStr($pdo, \''.$TabName.'\').\' \'.
                    GetStr($pdo, \'List\').\'</b> \'.$InfoPages) ;


  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
   
  echo (\'<form method=post action="\'.$CurrFile.\'"><table><tr>\'.
        "<input type=hidden name=\'ElId\' value=\'$ElId\'>".
        "<input type=hidden name=\'SubStr\' value=\'$SubStr\'>".
        "<input type=hidden name=\'SelId\' value=\'$SelId\'>".
        "<input type=hidden name=\'SelId2\' value=\'$SelId2\'>".
        "<input type=hidden name=\'SelId3\' value=\'$SelId3\'>".
        "<input type=hidden name=\'SelId4\' value=\'$SelId4\'>".
        "<input type=hidden name=\'Par2\' value=\'$Par2\'>");
  
  // Out CSRF protection
  MakeTkn();

  $i=0;
  foreach ( $Fields as $Fld) {
    if ($i==3){
      echo(\'</tr><tr>\');
      $i=0;
    }     
    $i++;
    echo("<td align=right>".GetStr($pdo, $Fld).":</td>");

    if ($enFields[$Fld]!=\'\'){
      echo("<td>".EnumSelection($pdo, $enFields[$Fld],\'Fltr_\'.$Fld, $_REQUEST[\'Fltr_\'.$Fld], 1)."</td>");
    }
    else {
      echo("<td><input type=text length=30 size=20 name=\'Fltr_$Fld\' value=\'".
        $_REQUEST[\'Fltr_\'.$Fld]."\'></td>");
    }
  }
  echo ("<td colspan=2><input type=text length=10 name=SubStr value=\'$SubStr\'></td>");
  echo (\'<td><button type="submit">Filter</button></td></tr></table></form>\');
  ';

fwrite($file,$S);

//echo ("<br> OtherTab build select: ");
//print_r($FOtherTab);
//echo ("<br><br>");

$S='//echo (\'<hr><br><form method=post action="'.$TabName.'Card.php">\'.
    //    \'<input type=hidden Name=New VALUE=1>\');
    //MakeTkn();
    //echo (
    //    "<input type=submit Value=\'".GetStr($pdo, \'New\')."\'></form>" );
//--------------------------------------------------------------------------------

echo (\'<table class=LongTable><tr class="header"><th></th>\');

foreach ( $Fields as $Fld) {
  echo("<th>".GetStr($pdo, $Fld)."</th>");
}
echo("</tr>");

$n=0;
while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  
  $classtype="";
  $n++;
  if ($n==2) {
    $n=0;
    $classtype=\' class="even"\';
  }
';
  
  foreach ( $FOtherTab as $I => $Arr) {
    $Fld=$Arr['Fld2Name']; 
    $S.='
  if ($SelId==\''.$I.'\'){
    $Res="\"".addslashes($dp[\''.$Fld.'\'])."\"";
';
    if (!empty($FromFlds[$I])) {    
    foreach ( $FromFlds[$I] as $I1 => $FF1) {
      $Fld=$Arr['FldName'];

      $S.='    $Res.=",\"".addslashes($dp[\''.$FF1.'\'])."\"";
  ';
    }
    } 
    //$FromFlds=array();
    //$ToFlds=array();
$S.='}
';
  }
  $S.='
  echo ("<tr$classtype><td><input type=button value=\'".GetStr($pdo, \'Select\').
       "\' onclick=\'return SetSelect($Res);\'></td>");
';


$S.='
  foreach ( $Fields as $Fld) {
    if ($Fld==\''.$LastPK.'\') {
      echo("<td align=left><a href=\''.$TabName.'Card.php?';

$Div='';
foreach ( $PKFields as $Fld) {
  $S.=$Div.$Fld.'={$dp[\''.$Fld.'\']}';
  $Div='&';  
}

$S.='\'>{$dp[$Fld]}</a></td>");
    }
    else 
    if ($enFields[$Fld]!=\'\'){
      echo("<td>".GetEnum($pdo, $enFields[$Fld], $dp[$Fld])."</td>");
    }
    else {
      echo(\'<td>\'.$dp[$Fld]."</td>");
    }
  }
  echo("</tr>");
};
echo ("</table>");

$ArrPostParams["SelId"]=$SelId;
$ArrPostParams["ElId"]=$ElId;

$ArrPostParams["SubStr"]=$SubStr;
$ArrPostParams["Par2"]=$Par2;

$ArrPostParams["SelId2"]=$SelId2;
$ArrPostParams["SelId3"]=$SelId3;
$ArrPostParams["SelId4"]=$SelId4;


echo(\'<table><tr class="header">\');
OutListFooter($pdo, $CurrFile, $ArrPostParams, $PageArr);


echo (\'</tr></table>\');

}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}


?>
</body>
</html>
';
fwrite($file,$S);

fclose($file);

echo ("<br><a href='../Forms/Select{$TabName}.php'>Frm Select $TabName</a> ");


}
//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------

  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

echo ("<br><a href='../Forms/{$TabName}List.php'>Frm List</a>");
?>
</body>
</html>                    