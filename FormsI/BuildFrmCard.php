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

//==========================================================
// HTML editor- ?
//=================================================
$S='';

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
  $S.='
include "../SubHtml.js";
include "../js_SelAll.js";
include ("../setup/HtmlTxt.php");
';
}

//==========================================================
//                 Default status -- AddFieldFunc 17
//==========================================================
$DefaultStatusFlds=array();
// AdmFieldsAddFunc
// Id, TabName, FldName, AddFunc
$query = "select * from \"AdmFieldsAddFunc\" ". 
         "where (\"TabName\" = :TabName) and (\"AddFunc\" =17) order by \"FldName\" "; 

$PdoArr = array();
$PdoArr['TabName']= $TabName;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

while ($dp25 = $STH->fetch(PDO::FETCH_ASSOC)) {
  $DefaultStatusFlds[$dp25['FldName']]= $dp25['Param'];
}
//==========================================================


$FldAccArr=array ();

if ($HaveRef) {
  //echo ("<br> FOtherTab: ");
  //print_r($FOtherTab);
  $S.= 'include "../js_module.php";'."\r\nOutPostReq();\r\n//------- For Ext Tables --------- ";
  
  echo ("<br> Extab1 = ");
  print_r($ExtTab1);

  foreach ($ExtTab1 as $II=>$EXT ) {
    echo ("<br> $II -> $EXT ");

    $S.="\r\n  ScriptSelectionTabs('$EXT$II', 'Select$EXT.php', '".
        addslashes (GetStr($pdo, $EXT))."', '$II');";
  }
}
else {
  //$S='';
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

//--------------------------------------------------------------
// Может ли пользователь копировать карточку
//--------------------------------------------------------------
$CopyRecord = GetParam_TabAddFunc($pdo, $TabName, 60);

echo ("<br>CopyRecord: $CopyRecord <br>");



//==============================================================
//                                  Master Tab             =====
//==============================================================

$MasterTab = GetMasterTabName($pdo, $TabName);
$MasterFldsCorr = array();                      
if ( $MasterTab != '') {
  $Param = GetParam_TabAddFunc($pdo, $TabName, 20);
  if ( $Param != '') {
    echo ("<br> MasterTab: $MasterTab $Param <br>");
    $MasterFldsCorr1 = GetMasterCorrFields ($pdo, $Param);
    
    echo ("<br> Flds Corr: ");
    print_r($MasterFldsCorr1);
    echo ("<br>");

    foreach ( $MasterFldsCorr1 as $TypeFld=> $Arr3) {
      foreach ($Arr3 as $Fld1=>$Indx) {
        $MasterFldsCorr[$Indx][$TypeFld]=$Fld1;        
      }
    }

    $MFields = GetParam_TabAddFunc($pdo, $TabName, 35); // Какие поля мастер таблицы выводить
    if ( $MFields!='') {
      $MFieldsArr=json_decode($MFields, 1);
      
      print_r($MasterFldsCorr);
      echo (" ---- <br> ---- ");


      //---------------------------------------- Out master tab fields ---
      $S.="\r\n// ----- Out MasterTab: $MasterTab ".
          "\r\n\$PdoArr=array();";
      $WH_MT='';
      $DivMT='';
      foreach ($MasterFldsCorr as $Indx=>$Arr) {
        $FldMT = $Arr['FldM'];
        $FldCT = $Arr['FldT'];
        $S.="\r\n\$PdoArr['$FldMT']=\$_REQUEST['$FldCT'];";
        $WH_MT.="$DivMT (\\\"$FldMT\\\"=:$FldMT)";
        $DivMT='and';
      }
      
      $S.="\r\ntry {".
          "\r\n  echo ('<h4>'.GetStr(\$pdo, '$MasterTab').'</h4>'); ".
          "\r\n  \$query = \"select * from \\\"$MasterTab\\\" \". ".
          "\r\n           \"where $WH_MT\";\r\n".
          "\r\n  \$STH = \$pdo->prepare(\$query);".
          "\r\n  \$STH->execute(\$PdoArr);".
          "\r\n\r\n  if (\$dp_mt = \$STH->fetch(PDO::FETCH_ASSOC)) { ".
          "\r\n    echo('<table>');";
          
          foreach ($MFieldsArr as $MFld=>$V) {
            $S.="\r\n    echo('<tr><td align=right>'.GetStr(\$pdo, '$MFld').':</td><td>'.".
                "\r\n          \$dp_mt['$MFld'].'</td></tr>');";
          }
          $S.= "\r\n    echo('</table><hr>');".
               "\r\n  };".'
}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);	
  die ("<br> Error: ".$e->getMessage());
}
  ';
    }
  }
}


//==============================================================
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

$StatusFlds=array();
$StatusChangeRules=array();


// AdmFieldsAddFunc
// Id, TabName, FldName, AddFunc
$query = "select * from \"AdmFieldsAddFunc\" ". 
         "where (\"TabName\" = :TabName) and (\"AddFunc\" in (10, 15)) order by \"FldName\" "; 

$PdoArr = array();
$PdoArr['TabName']= $TabName;

$STH = $pdo->prepare($query);
$STH->execute($PdoArr);

while ($dp25 = $STH->fetch(PDO::FETCH_ASSOC)) {
  if ($dp25['AddFunc']==10) {
    $StatusFlds[$dp25['FldName']]= 1;
  }
  else {
    $StatusChangeRules[$dp25['FldName']]= $dp25['Param'];
  }
}


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
  
  try {
  ';


//==================================================================
//------------------------------------------------------------------

$S.='
  
  $query = "select * FROM \"'.$TabName.'\" ".
           "WHERE '.$WH.'";
  
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
     "\r\n      ".'$'.$LastPK.'=$LN;'.
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
      if ($HtmlFlds[$Fld]==1) {
        $S.='  BuildHtmlInput($Fld);'."\r\n";
      }
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
      if ($DefaultStatusFlds[$Fld]!='') {
        $S.="\r\n  if (\$New==1) {".
            "\r\n    echo(\"<input type=hidden Name='$Fld' value='{$DefaultStatusFlds[$Fld]}'>\");".
            "\r\n  }\r\n";  
      }

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
  if ($FldType==15) {
      if ($HtmlFlds[$Fld]==1) {
        $S.='  echo (DivTxt(HtmlTxt($OutVal), 100));'."\r\n";
      }
      else {
        $S.='  echo (DivTxt($OutVal, 100));'."\r\n";

      }
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
echo ("  <hr><table><tr><td><a href=\''.$TabName.'List.php\'>".GetStr($pdo, \'List\')."</a></td>");';

foreach ($StatusFlds as $Fld=>$V) {
  $S.="\r\n".
  '  if ($New!=1) {'."\r\n";

  $S.='    $PostArr = array();'."\r\n";  


  foreach ($PKFields as $PK) {
    $S.='    $PostArr["'.$PK.'"]=$'.$PK.";\r\n";
  }

  //--------------------------------------------------
  $EnName = $Fld;
  if (!empty ($Fields[$Fld]['AddParam'])) {
    $EnName = $Fields[$Fld]['AddParam'];
  }
  $PossibleStatus = json_decode($StatusChangeRules[$Fld],1);
 
  $S.= "    // {$TabName} $Fld {$EnName}\r\n".
      '    $EnName=\''.$EnName.'\';'."\r\n".
      '    $PossibleStatus=array ( '."\r\n";
  
  $Div='';
  foreach ( $PossibleStatus as $Val => $Arr) {
    
    $S.=$Div.'      // '.$Val.' - '.GetEnum($pdo,$EnName,$Val) ."\r\n";
    $S.='      '.$Val.'=> array ( ';
    
    $Div2='';
    foreach ( $Arr as $Val2 => $Z) {
      $S.=$Div2.$Val2.'=>1';
      $Div2=", "; 
    }
    $S.=")";
    $Div=",\r\n";
  }
  $S.=");\r\n\r\n";
  $S.='    $StsArr = array();'."\r\n";
  $S.='    $StsCnt = 0;'."\r\n";
  $S.="    \$CurrStatus=\$dp['$Fld'];\r\n".
      "    foreach(\$PossibleStatus[\$CurrStatus] as \$PV=> \$V){ \r\n".
      "      \$StsCnt++;\r\n".
      "      \$StsArr[\$StsCnt]['NewStatus']= \$PV;\r\n".
      "      \$StsArr[\$StsCnt]['tit']= GetEnum(\$pdo, \$EnName, \$PV);\r\n".
      "      \$StsArr[\$StsCnt]['Txt']= GetEnum(\$pdo, \$EnName, \$PV);\r\n".
      "    }\r\n";



  //=================================================
  $S.=" 

    OutStatusChange (\$pdo, '{$TabName}-Change$Fld.php', \$PostArr, \$StsArr);
  }";
}

$S.='
if ($Editable)
  echo ("<td><a href=\''.$TabName.'Delete.php?$FullLink\' onclick=\'return confirm(\"Delete?\");\'>".
        GetStr($pdo, \'Delete\')."</a></td>");
';

//--------------------------------------------------
//      Кнопка копирования записи
if ($CopyRecord!='' ) {

  $S.="\r\n".
  '  if ($New!=1) {'."\r\n";

  $S.='    $PostArr = array();'."\r\n";  


  foreach ($PKFields as $PK) {
    $S.='    $PostArr["'.$PK.'"]=$'.$PK.";\r\n";
  }

  $S.='    $StsArr = array();'."\r\n";
  $S.='    $StsCnt = 0;'."\r\n";
  $S.="    \$StsCnt++;\r\n".
      "    \$StsArr[\$StsCnt]['NewStatus']= 'Copy';\r\n".
      "    \$StsArr[\$StsCnt]['tit']= GetStr(\$pdo, 'CopyRecToNew');\r\n".
      "    \$StsArr[\$StsCnt]['Txt']= GetStr(\$pdo, 'CopyRecToNew');\r\n".
      "    OutStatusChange (\$pdo, '{$TabName}-CopyRecord.php', \$PostArr, \$StsArr);\r\n".
      "  }\r\n";

}
//-------------------------------------------------
$S.='
echo ("</tr></table>");

?>
</body>
</html>
';

fwrite($file,$S);
fclose($file);
//======================================================================================
if ($CopyRecord!='' ) {
  echo ("<br> Build CopyRecord <br>"); 
  include ("BuildFrmCopyRecord.php");


}

//=======================================================================================
// Build ChangeStatus files
//=======================================================================================
//echo ("<br> StatusFlds : ");
//print_r($StatusFlds);
//echo ("<br> StatusChangeRules : ");
//print_r($StatusChangeRules);
$S='';

foreach ($StatusFlds as $Fld=>$V) {
  if (! empty ( $StatusChangeRules[$Fld])) {
    $file = fopen("../Forms/{$TabName}-Change$Fld.php","w");

    echo ("<br> {$TabName}-Change$Fld.php --- Field: ");
    print_r($Fld);
    echo("<br>"); 
    
    $EnName = $Fld;
    if (!empty ($Fields[$Fld]['AddParam'])) {
      $EnName = $Fields[$Fld]['AddParam'];
    }


    fwrite($file,"<?php\r\n");
    fwrite($file,"session_start();\r\n");
    //--------------------------------------------------

$S= '
include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
OutHtmlHeader ("{$TabName}-Change$Fld");

'.
"\r\n";
fwrite($file,$S);

$S= "\$Editable = CheckFormRight(\$pdo, '$TabName', 'Card');\r\nCheckTkn();\r\n".
'$FldNames=array(';
$Div='';

foreach ($PKFields as $PK) {  
  $S.="$Div'$PK'";
  $Div=', '; 
}

$S.="$Div'$Fld');\r\n\r\n";
    
    //-------------------------------------------------------
    $PossibleStatus = json_decode($StatusChangeRules[$Fld],1);
   
    $S.= "// {$TabName} $Fld {$EnName}\r\n".
        '$PossibleStatus=array ( '."\r\n";
    $Div='';
    foreach ( $PossibleStatus as $Val => $Arr) {
      
      $S.=$Div.'  // '.$Val.' - '.GetEnum($pdo,$EnName,$Val) ."\r\n";
      $S.='  '.$Val.'=> array ( ';
      
      $Div2='';
      foreach ( $Arr as $Val2 => $Z) {
        $S.=$Div2.$Val2.'=>1';
        $Div2=", "; 
      }
      $S.=")";
      $Div=",\r\n";
    }
    $S.=");\r\n";


$S.='$PdoArr = array();
';

$PK_WH='';
$AND='';
foreach ($PKFields as $PK) {  
  $S.='if(empty($_REQUEST["'.$PK.'"])) {
  die ("Error: empty '.$PK.'");
}
$'.$PK.'=$_REQUEST["'.$PK.'"];
$PdoArr["'.$PK.'"]= $'.$PK.';

';
  $PK_WH.="$AND(\\\"$PK\\\"=:$PK)";
  $AND="and";

}

$S.='

if ($_REQUEST["NewStatus"]=="") {
  die ("<br> Error: New status is empty");
}

$NewStatus=$_REQUEST["NewStatus"];
$EnName = "'.$EnName.'";

$NewStatusTxt= GetEnum($pdo, $EnName,$NewStatus); 

$CurrStatus=0;
$CurrStatusTxt="";
try{
  $query = "select * FROM \"'.$TabName.'\" ".
           "WHERE '.$PK_WH.'";

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  if ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
    $CurrStatus = $dp["'.$Fld.'"];
    $CurrStatusTxt= GetEnum($pdo, $EnName,$CurrStatus); 
  }
  else {
    echo ("<br> PDO: ");
    print_r($PdoArr);
    die ("<br> Error: record '.$TabName.' is not found ");
  }

  if ($CurrStatus==$NewStatus) {

    AutoPostFrm ( "'.$TabName.'Card.php", $PdoArr, 2000);
    echo ("<h2> Now '.$Fld.' = $CurrStatusTxt </h2>");
  }
  else {

  if ( $PossibleStatus[$CurrStatus][$NewStatus]==1) {
    $CheckOk = 0;
';

foreach ($PossibleStatus as $Val=> $Arr) {
  $S.="\r\n".'    if ($CurrStatus == '.$Val.') {   // '.$Val.' - '.GetEnum($pdo, $EnName, $Val).'
        $CheckOk=1;
    }
    else ';
}
$S.='      $CheckOk=0;
    if ($CheckOk==1) {
      MakeAdminRec ($pdo, $_SESSION["login"], "'.$TabName.'", "STS-CH", 
                        $NewStatus, "'.$Fld.' change $CurrStatusTxt -> $NewStatusTxt");

      $PdoArr["NewStatus"]=$NewStatus;

      $query = "update \"'.$TabName.'\" set \"'.$Fld.'\"=:NewStatus ".
               "WHERE '.$PK_WH.'";

      $STH = $pdo->prepare($query);
      $STH->execute($PdoArr);  
    }
  }
  else {
    die ("<br> Error: Possible status is not ok");
  }
  }
}
catch (PDOException $e) {
  echo ("<hr> Line ".__LINE__."<br>");
  echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
  print_r($PdoArr);
  die ("<br> Error: ".$e->getMessage());
}

$FrmTkn = MakeTkn(1);
$PdoArr["FrmTkn"]=$FrmTkn;

AutoPostFrm ("'.$TabName.'Card.php", $PdoArr, 10);

?>
</body>
</html>
';

    fwrite($file,$S);
    fclose($file);
  }
}
?>