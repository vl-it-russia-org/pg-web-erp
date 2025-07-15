<?php

$file = fopen("../Forms/{$TabName}SmallList.php","w");
fwrite($file,"<?php\r\n  die();\r\n");
fwrite($file,"  echo(\"<hr><h4>\".GetStr(\$pdo, '{$TabName}').\"</h4>\");\r\n");



$SF='$'.$PKFields[0];

$Div='';
$Ord='';
$LastPkFld='';

foreach ($PKFields as $PK) {
  $i++;
  $Ord.=$Div.'\"'.$PK.'\"';
  $Div=',';
  $N++;
  $LastPkFld=$PK;
}

$query = "select * FROM \\\"{$TabName}\\\" ".
         "where \\\"{$PKFields[0]}\\\"=:{$PKFields[0]} order by $Ord ";  

$S='
  $PdoArr = array();
  $PdoArr["'.$PKFields[0].'"]= '.$SF.';
  $query = "'.$query.'";'."\r\n";
fwrite($file,$S);

$S='
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);
';
fwrite($file,$S);

$S="\r\n  echo('<table><tr class=header><th></th>');" ;



foreach ($Fields as $Fld=>$Arr) {
  $Pass=0;
  if ($N>1) {
    if ($Fld== $PKFields[0]) {
      $Pass=1;
    }
  }
  
  if ($Pass==0) {      
    $S.="\r\n  echo('<th>'.GetStr(\$pdo, '$Fld').'</th>');";
  }
}

$S.="\r\n".'  $i=0;'."\r\n  \$CardArr=array();\r\n".'
  $CrdNewWindow =GetStr($pdo, "CrdInNewWnd");
  $CrdHere =GetStr($pdo, "CrdInCurrWnd");
  $Tkn = MakeTkn(1);

';

$S.='  while ($dpL = $STH->fetch(PDO::FETCH_ASSOC)) {'."\r\n".
    '    $i=NewLine($i);'."\r\n";

foreach ($PKFields as $PK) {
  $S.='    $CardArr["'.$PK.'"]= $dpL["'.$PK.'"];'."\r\n";
}

$S.='    $CardArr["FrmTkn"]= $Tkn;'."\r\n";

$S.='    $Json = base64_encode(json_encode ($CardArr));
    OutCardButton("'.$TabName.'Card.php", $Json, $CrdHere, $CrdNewWindow);
    '."\r\n";

foreach ($Fields as $Fld=>$Arr) {
  //echo ("<br><br> -- $Fld: ");
  //print_r( $Arr );

  $Pass=0;
  if ($N>1) {
    if ($Fld== $PKFields[0]) {
      $Pass=1;
    }
  
  }
  if ($Pass==0) {      
    $EndB='</td>';
    if ($Arr['DocParamType']==50) {
      $S.="\r\n".'    echo ("<td align=center>");'. "\r\n";
    }
    else 
    if ($Arr['DocParamType']==20) {
      if ( $Arr['AddParam'] != '') {
        $S.="\r\n".'    echo ("<td align=right>");'. "\r\n";
      }
      else {
        $S.="\r\n".'    echo ("<td align=center>");'. "\r\n";
      }
    }
    else 
    if ($Arr['DocParamType']==30) {
      $S.="\r\n".'    echo ("<td align=center>");'. "\r\n";
    }
    else 
      $S.="\r\n".'    echo ("<td>");'. "\r\n";

    if (0==1) {   // $Fld==$LastPkFld) 
      $S.="\r\n    ".'echo("<a href=\''.$TabName.'Card.php?';
      $Div1='';
      foreach ($PKFields as $PK) {
        $S.=$Div1.$PK.'={$dpL[\''.$PK.'\']}';
        $Div1='&';
      }
      $EndB='</a></td>';
      $S.='\'>");'. "\r\n";
    }
    else {
      //$S.="\r\n  ".'echo("';
    }
    
    if ($Arr['DocParamType']==50) {
      $S.= '    echo (GetEnum($pdo, "'.$Arr['AddParam'].'", $dpL[\''.$Fld.'\'])."'.$EndB.'");';    
    }
    else
    if ($Arr['DocParamType']==20) {
      if ( $Arr['AddParam'] != '') {
        $S.='    $OW=number_format($dpL[\''.$Fld.'\'], 2, ".", "\'");'."\r\n";
        $S.='    echo ("$OW'.$EndB.'");'."\r\n";
      }
      else 
        $S.= '    echo ("{$dpL[\''.$Fld.'\']}'.$EndB.'");';    
    }
    else
    if ($Arr['DocParamType']==30) {
      
      $S.='    $Ch="";
      if ($dpL[\''.$Fld.'\']==1) {
        $Ch=" checked ";  
      }
    ';
      $S.='    echo ("<input type=checkbox Name='.$Fld.' value=1 $Ch></td>");
      ';
    }
    else 
      $S.= '    echo ("{$dpL[\''.$Fld.'\']}'.$EndB.'");';    
  }
}
$S.="\r\n    ".
    "\r\n  ".'}'.
    "\r\n  ".'echo("</tr></table>");'."\r\n".
    
    '  $CardArr=array();

  $CardArr[\''.$PKFields[0].'\']='.$SF.';
  $CardArr[\'New\']=1;
  $CardArr[\'FrmTkn\']=$Tkn;

  echo ("<table><tr><td>".GetStr($pdo, \''.$TabName.'\')." ".
        GetStr($pdo, \'AddNew\').":</td>");

  $Json = base64_encode(json_encode ($CardArr));
  OutCardButton("'.$TabName.'Card.php", $Json, $CrdHere, $CrdNewWindow);
  echo("</tr></table>" );
    ';
fwrite($file,$S);

$S="\r\n?>
";

fwrite($file,$S);

fclose($file);



?>