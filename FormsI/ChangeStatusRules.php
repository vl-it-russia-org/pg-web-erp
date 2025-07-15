<?php

//---- Выдает матрицу для внесения Порядка изменения статуса
function ChangeStatusRules(&$pdo, &$dp, &$FldInfo ) {
  echo ("<tr><td colspan=2>");
  //print_r($dp);
  //echo ("<br> FldInfo: ");
  //print_r($FldInfo);

  
  if ($FldInfo["DocParamType"]!=50) {
    echo ("<br>Expected Enum field</tr>");
    return 0;
  }

  $EnumName = $FldInfo["FldName"]; 
  if (!empty ($FldInfo["AddParam"])) {
    $EnumName = $FldInfo["AddParam"]; 
  }

  $Lang='EN';
  
  $EnumArr=array();
  // \"EnumValues\"
  // \"EnumName\", \"EnumVal\", \"Lang\", \"EnumDescription\"
  //-------------------------------
  // (\"EnumName\"=:EnumName)and(\"EnumVal\"=:EnumVal)and(\"Lang\"=:Lang)
  // $PdoArr["EnumName"]= $EnumName;
  // $PdoArr["EnumVal"]= $EnumVal;
  // $PdoArr["Lang"]= $Lang;

  // $ArrAll = array ('EnumName', 'EnumVal', 'Lang', 'EnumDescription'
  //        );

  // $ArrDig = array ('EnumVal');

  $PdoArr = array();
  $PdoArr['EnumName']= $EnumName;
  $PdoArr['Lang']= $Lang;

  $EnumCnt=0;
  try {
    $query = "select * from \"EnumValues\" ". 
             "where (\"EnumName\"=:EnumName) and (\"Lang\" = :Lang) order by \"EnumVal\""; 

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);

    while ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
      $EnumArr[$dp2['EnumVal']]=$dp2['EnumDescription'];
      $EnumCnt++;
    }

  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);
    die ("<br> Error: ".$e->getMessage());
  }




  echo ("<table><tr class=header>");
  echo ("<th rowspan=2>".GetStr($pdo, 'FromStatus')."</th>");
  echo ("<th colspan=$EnumCnt>".GetStr($pdo, 'ToStatus')."</th>");

  echo ("</tr><tr class=header>");
  foreach($EnumArr as $Val=>$Descr) {
    echo ("<th>[$Val]<br>$Descr</th>");
  }

  $PossibleStatusChange = array();
  if (!empty ($dp['Param']) ) {
    $PossibleStatusChange = json_decode($dp['Param'], 1);
  }
  $i=0;
  foreach($EnumArr as $Val=>$Descr) {
    $i=NewLine($i);
    echo ("<td>[$Val] $Descr</td>");

    foreach($EnumArr as $Val2=>$Descr2) {
      $Checked='';
      if ($PossibleStatusChange[$Val][$Val2]==1) {
        $Checked=' checked ';
      }
      echo ("<td align=center><input type=checkbox Name=PV[$Val][$Val2] value=1$Checked></td>");
        
    }
  }

  echo ("</tr>");
  
  
  echo ("</table></tr>");
}


?>