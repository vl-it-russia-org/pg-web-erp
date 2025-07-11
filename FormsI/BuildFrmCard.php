<?php
// Создание стандартной формы для просмотра карточки таблицы
// Создается как форма карточка для Просмотра так и карточка для редактирования
// записи таблицы *Card.php

$file = fopen("../Forms/{$TabName}Card.php","w");

fwrite($file,"<?php\r\n");
fwrite($file,"session_start();\r\n");

$S= '
include ("../setup/common_pg.php");
BeginProc();

$TabName=\''.$TabName.'\';
OutHtmlHeader ($TabName." card");


// Checklogin1();'."\r\n";

fwrite($file,$S);

$FldAccArr=array ();

if ($HaveRef) {
  //echo ("<br> FOtherTab: ");
  //print_r($FOtherTab);
  $S= 'include "../js_module.php";'."\r\nOutPostReq();\r\n//------- For Ext Tables --------- ";
  
  echo ("<br> Extab1 = ");
  print_r($ExtTab1);

  foreach ($ExtTab1 as $II=>$EXT ) {
    echo ("<br> $II -> $EXT ");

    $S.="\r\n  ScriptSelectionTabs('$EXT$II', 'Select$EXT.php', '".
        addslashes (GetStr($pdo, $EXT))."', '$II');";
  }
}
else {
  $S='';
}

$S.="\r\n\r\n";
fwrite($file,$S);

$S= "\$Editable = CheckFormRight(\$pdo, '$TabName', 'Card');\r\nCheckTkn();\r\n".
'$FldNames=array(';
$Div='';

$Cnt=0;
foreach ($Fields as $Fld=>$Arr) {
  $S.="$Div'$Fld'";
  $Div=',';

  $Cnt++;
  if ($Cnt==4) {
    $S.="\r\n          ";
    $Cnt=0;
  }
  
  if ($Arr['DocParamType']==50) {
    //$enS.="$enDiv'$Fld'";
    //$enDiv=',';
  }
  //echo (" Fld:$Fld ");
}

$S.=");\r\n".$enS.");\r\n";

$WH='';
$DW='';

$RR=0;
$WW='';
$WW1='';
$LastFld='';

$FullLink='';
$DivFL='';

//-------------------------------
$PDO1='';
$LPK='';

$S.='$PdoArr = array();'."\r\n";  


foreach ($PKFields as $PK) {
  $RR++;
  $S.='$'.$PK.'=$_REQUEST[\''.$PK."'];\r\n";
  $S.='$PdoArr["'.$PK.'"]=$'.$PK.";\r\n";
    
  $WH.= $DW. '(\"'.$PK."\\\"=:".$PK.')';
  if ($WW1!='') {
    $WW.= ' AND '. $WW1;
    $PDO1.='$PdoArr["'.$LPK.'"]= $'.$LPK.";\r\n";
  }
  
  $LastFld=$PK;

  $WW1= '(\"'.$PK."\\\"=:".$PK.')';
  $DW=' AND ';
  
  $LPK=$PK;

  $FullLink.=$DivFL.$PK.'=$'.$PK;
  $DivFL='&';

};

//=================================================

$FillNewFlds=array();


// AdmFieldsAddFunc
// Id, TabName, FldName, AddFunc
$query = "select * from \"AdmFieldsAddFunc\" ". 
         "where (\"TabName\" = :TabName) and (\"AddFunc\"=40) order by \"FldName\" "; 

$PdoArr = array();
$PdoArr['TabName']= $TabName;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

while ($dp22 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $FillNewFlds[$dp22['FldName']]=1;
}
//-------------------------------------------------------------------------------------------
// AdmTabsAddFunc
// Id, TabName, AddFunc, Param
$query = "select * from \"AdmTabsAddFunc\" ". 
         "where (\"TabName\" = :TabName) and (\"AddFunc\"=50) order by \"Id\" "; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

$PKNotEdit=1;
while ($dp22 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $PKNotEdit=0;
}


//=================================================



$S.='echo("<H3>".GetStr($pdo, \''.$TabName.'\')."</H3>");'."\r\n".
  '  $dp=array();
  $FullLink="'.$FullLink.'";

  $query = "select * FROM \"'.$TabName.'\" ".
           "WHERE '.$WH.'";
  
  try {
  
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
  
  $New=$_REQUEST[\'New\'];

'.$TabEditable.'
if ($Editable) {
';
$FillNewFT=0;  

foreach ($FillNewFlds as $Fld=>$V) {
  if ($FillNewFT==0) {
    $S.='  if ($New==1) {
  ';
    $FillNewFT=1;  
  };

  $S.='  $dp["'.$Fld.'"]= $_REQUEST["'.$Fld.'"];'."\r\n";
}

if ($FillNewFT==1) {
  $S.='  }'."\r\n";
}
  
$S.='  echo (\'<form method=post action="'.$TabName.'Save.php">\'.
        "<input type=hidden Name=\'New\' value=\'$New\'>");
  
  echo ("<table><tr>");';

fwrite($file,$S);

//=================================================================
$S="\r\n".


//=================================================================



$S="\r\n".'  $LN=0;';

//=================================================

$StatusFlds=array();

// AdmFieldsAddFunc
// Id, TabName, FldName, AddFunc
$query = "select * from \"AdmFieldsAddFunc\" ". 
         "where (\"TabName\" = :TabName) and (\"AddFunc\"=10) order by \"FldName\" "; 

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

while ($dp22 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $StatusFlds[$dp22['FldName']]=1;
}

//=================================================


if ($RR>1) {
  $S="\r\n  ".'$PdoArr = array();'."\r\n  $PDO1".
     "\r\n  if (".'$New==1) {'.
     "\r\n      ".'$query = "select max(\"'.$PKFields[$RR-1].'\") \"MX\" ".'.
     "\r\n      ".'"FROM \"'.$TabName.'\" ".'.
     "\r\n      ".'" WHERE (1=1) '.$WW.'";'.
     "\r\n      ".'$STH4 = $pdo->prepare($query);'.
     "\r\n      ".'$STH4->execute($PdoArr);'.
     "\r\n      ".'$LN=0;'.
     "\r\n      ".'if ($dp4 = $STH4->fetch(PDO::FETCH_ASSOC)) {'.
     "\r\n        ".'$LN=$dp4[\'MX\'];'.
     "\r\n      ".'}'.
     "\r\n      ".'$LN+=1;'.
     "\r\n  ".'}'."\r\n";
}

foreach ($Fields as $Fld=>$Arr) {
  $S.= "\r\n".'  $Fld=\''.$Fld.'\';
  $OutVal= $dp[$Fld];';
  if (in_array ($Fld, $PKFields)) {             
    $S.="\r\n  echo (\"<input type=hidden Name='Old$Fld' ".
        'value=\'$OutVal\'>");'.
        "\r\n";
  }
  
  $S.='  echo ("<td align=right><label for=\''.$Fld.'\'>".GetStr($pdo, $Fld).":</label></td><td>");'."\r\n";
  
  $FldType=$Arr['DocParamType'];
  if (in_array ($Fld, $PKFields)) {
    if (!empty($EnumFlds[$Fld])){
      $S.='  echo ( EnumSelection($pdo, "'.$EnumFlds[$Fld].'", "'.$Fld.' ID=\'$Fld\' ", $OutVal));';
    }
    else {
      
      $RO = '';
      if ($PKNotEdit==1) {
        $RO = ' readonly';

      }
      $S.='  echo ("<input type=text Name=\'$Fld\'  ID=\'$Fld\' Value=\'{$'.$Fld.'}\' size=10'.$RO.'>");';  
    }
  }
  else {
  
  
  
  if ($FldType==10) {
    // 10 EN Text50
    if ($Arr['AddParam']=='') {
      $S.='  echo ("<input type=text Name=\'$Fld\' ID=\'$Fld\' Value=\'{$dp[$Fld]}\' size=50>");';  
    }
    else {
      $N=$Arr['AddParam']+0;
      if ($N>100) {
        $S.='  echo ("<textarea Name=\'$Fld\'  ID=\'$Fld\'  cols=50 rows=3>{$dp[$Fld]}</textarea>");';  
      }
      else 
        $S.='  echo ("<input type=text Name=\'$Fld\'  ID=\'$Fld\' Value=\'{$dp[$Fld]}\' size='.$N.'>");';  
    } 
  }
  else 
  if ($FldType==15) {
      $S.='  echo ("<textarea Name=\'$Fld\'  ID=\'$Fld\'  cols=50 rows=3>{$dp[$Fld]}</textarea>");';  
  }
  else
  if ($FldType==20) {
    // Number
    if ($Arr['AddParam']=='') {
      $S.='  echo ("<input type=number Name=\'$Fld\'  ID=\'$Fld\'  Value=\'{$dp[$Fld]}\'>");';  
    }
    else {
      $S.='  echo ("<input type=number Name=\'$Fld\'  ID=\'$Fld\' Value=\'{$dp[$Fld]}\' step='.$Arr['AddParam'].'>");';  
    } 
  }
  else 
  if ($FldType==60) {
    // Date
    $S.='  echo ("<input type=date Name=\'$Fld\'  ID=\'$Fld\'  Value=\'{$dp[$Fld]}\'>");';  
  }
  
  else 
  if ($FldType==60) {
    // Date
    $S.='  echo ("<input type=\'datetime-local\' Name=\'$Fld\'  ID=\'$Fld\'  Value=\'{$dp[$Fld]}\'>");';  
  }
  
  else 
  if ($FldType==30) {
    
    $S.='  $Ch=\'\'; if ($dp[$Fld]==1) $Ch=\'Checked\';'.
        "\r\n".'  echo ("<input type=checkbox Name=\'$Fld\'  ID=\'$Fld\' Value=1 $Ch>");';  
  }
  else 
  if ($FldType==40) {
  }
  else 
  if ($FldType==45) {
  }
  else 
  if ($FldType==50) {
    if (empty ($StatusFlds[$Fld]) ) {
      $S.='  echo ( EnumSelection($pdo, "'.$Arr['AddParam'].'", "'.$Fld.' ID=\'$Fld\' ", $OutVal));';
    }
    else {
      $S.='  echo ( "<b>".GetEnum($pdo, "'.$Arr['AddParam'].'", $OutVal)."</b>");';
    }
  }
  } //------------------ 
  
  

  //echo ("<br><br> --$Fld-- ");
  //print_r ($ExtTab);
  //echo ("<br><br> --$Fld-- ");
  //print_r ($FOtherTab);


  if (!empty($FOtherTab[$Fld])) {
    $ET= $FOtherTab[$Fld];
    //echo ("<br><br>-- OtherTab: ");
    //print_r($ET);
    
    //echo ("<br><br>-- ExtTab3: ");
    //print_r($ExtTab3);
    //echo ("<br>");
    
    foreach ($ET as $Indx1=> $Arr1) {
      
      $ET1= $Arr1['TabName2'];
      $SMI= $Arr1['Id'];
      
      $S.="\r\n".'  echo(" <input type=button value=\'...\' '.
          'onclick=\'return Select'.$ET1.$SMI.'Fld(\"'.$Fld.'\"';
      if (! empty($FromFldsConn[$SMI])) {
        foreach ($FromFldsConn[$SMI] as $AI1=>$AIF1){
          $S.=', \"'.$AIF1.'\"'; 
        }
      }

      $S.=');\'>");'."\r\n";
    }
  }

  $S.="\r\n".'  echo("</td>");'.
  "\r\n".
  '  echo ("</tr><tr>");'."\r\n";  
} //-------------------------------- Fld foreach --------------------------

fwrite($file,$S);

$S="\r\n  MakeTkn();\r\n".
'  echo ("<td colspan=2 align=right>'.
   '<input type=submit value=\'".
         GetStr($pdo, \'Save\')."\'></td></tr></table></form>");
} //Editable
else {
  echo ("<table>");
';
  
  foreach ($Fields as $Fld=>$Arr) {
  $FldType=$Arr['DocParamType'];
  
  $S.= "\r\n".'  $Fld=\''.$Fld.'\';
  $OutVal= $dp[$Fld];
  echo ("<tr><td align=right>".GetStr($pdo, "$Fld").": </td><td>");
  ';
  if ( $FldType==30) {
    
    $S.='$Checked="";
  if ($OutVal) { 
    $Checked=" checked ";
  }
  echo ("<input type=checkbox $Checked value=1 disabled");
  ';
   
  }
  else
  if ( $FldType==50) {
    // Enum
    $S.='
  echo ("<b>".GetEnum($pdo, "'.$Arr['AddParam'].'", $OutVal)."</b>");
  ';
   
  }
  else 
  if ($DigArr[$Fld]!=0){
    $S.='$OW=number_format($OutVal, '.$DigArr[$Fld].', ".", "\'");
  echo ("<b>$OW</b>");
  ';

  }
  else {
    // All
    $S.='
  echo ($OutVal);
  ';
  }
  $S.='
  echo("</td></tr>");
  ';

  }  

$S.='  echo ("</table>");
}
echo ("  <hr><br><a href=\''.$TabName.'List.php\'>".GetStr($pdo, \'List\')."</a>");';

foreach ($StatusFlds as $Fld=>$V) {
  $S.="\r\n".'echo (" | <a href=\''.$TabName.
       'Change$Fld.php?$FullLink&NewStatus=0\'>".GetStr($pdo, \'Change'.$Fld.'\')."</a>");';
}

$S.='
if ($Editable)
  echo (" | <a href=\''.$TabName.'Delete.php?$FullLink\' onclick=\'return confirm(\"Delete?\");\'>".
        GetStr($pdo, \'Delete\')."</a>");
?>
</body>
</html>
';

fwrite($file,$S);
fclose($file);

?>