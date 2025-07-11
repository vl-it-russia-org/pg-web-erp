<?php

function GetNumberSeq ($ParamTxt) {
  
  $i=strpos($ParamTxt, '[SerNo=');
  
  echo ("<td></td><td>$Val</td>");
  $CurrVal='';
  if ($i!==false) {
    $last=strpos ($ParamTxt, ']', $i);  
    if ($last !== false) {
      $CurrVal= trim (substr($ParamTxt, $i+7, $last -$i-7));

    }
  
  }
  return $CurrVal;
}

//=================================================================

function GetEditableFld($ParamTxt) {
  
  $i=strpos($ParamTxt, '[Editable=');
  
  echo ("<td></td><td>$Val</td>");
  $CurrVal='';
  if ($i!==false) {
    $last=strpos ($ParamTxt, ']', $i);  
    if ($last !== false) {
      $CurrVal= trim (substr($ParamTxt, $i+10, $last -$i-10));

    }
  
  }
  return $CurrVal;
}


//=================================================================
//=================================================================
function GetMasterTabName(&$pdo, $TabName) {
  
  $MasterTabName='';
  // AdmTabsAddFunc
  // Id, TabName, AddFunc, Param


  $PdoArr = array();
  $PdoArr['TabName']= $TabName;
  
  try {
  $query = "select * from \"AdmTabsAddFunc\" F ". 
           "where (F.\"TabName\" = :TabName) and (F.\"AddFunc\"=10) "; 

  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);

  
  
  if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
    $Param = $dp2['Param'];
    $i=strpos($Param, '[MasterTab=');
    
    if ($i!==false) {
      $last=strpos ($Param, ']', $i);  
      if ($last !== false) {
        $MasterTabName = trim (substr($Param, $i+11, $last -$i-11));

      }
    
    }


  }

}
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }  
  return $MasterTabName;
}
//=================================================================
function GetTabCode(&$pdo, $TabName) {
  
  $TabCode='';
  $PdoArr = array();
  $PdoArr['TabName']= $TabName;
  try{
    // AdmTabNames
    // TabName, TabDescription, TabCode, TabEditable, 
    // AutoCalc, CalcTableName, ChangeDt, Ver

    $query = "select \"TabCode\" from \"AdmTabNames\" ". 
             "where (\"TabName\" = :TabName)"; 

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);  


    if ($HeadRec = $STH->fetch(PDO::FETCH_ASSOC)) {
      $TabCode=$HeadRec['TabCode'];
    }
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }


  return $TabCode;
}

//===========================================================================

function GetMasterCorrFields (&$pdo, $Param){
  $Arr=array();

  $i=0;

  $Arr1 = explode('[', $Param);
  //print_r($Arr1);
  foreach ($Arr1 as $Indx => $Val) {
    if ($Val != '') {
      //echo ("<br> $Indx : $Val ");

      $i=$Indx*10;

      $Arr2= explode(';', $Val); 

      $FldT= $Arr2[0];
      $Beg= substr($FldT, 0, 5); 
      if ($Beg=='FldT=') {
        $FldT= substr($FldT, 5);
        //echo ("<br>FldT=$FldT");
      }
      else {
        die ("<br> Error: Bad FldT in corrfields $i");
      }


      $FldM= $Arr2[1];
      $Beg= substr($FldM, 0, 5); 
      if ($Beg=='FldM=') {
        $FldM= substr($FldM, 5, -1);
        //echo ("<br>FldM=$FldM");
      }
      else {
        die ("<br> Error: Bad FldM in corrfields $i");
      }

      //echo ("<br> Arr2=");
      //print_r($Arr2);

      $Arr['FldT'][$FldT]=$i;
      $Arr['FldM'][$FldM]=$i;
    }
  }


  return $Arr;
}
//=================================================================
//=================================================================

function GetParam_TabAddFunc(&$pdo, $TabName, $ParamCode) {
  $Res='';
  // Enum TabAddFunction
  //    0	  -- Выберите --
  //    10	  Головная таблица
  //    20	  Соотношение полей с Головной таблицей
  //    30	  Копирование полей из головной таблицы

  $PdoArr = array();
  $PdoArr['TabName']= $TabName;  
  $PdoArr['ParamCode']= $ParamCode;  

  
  // AdmTabsAddFunc
  // Id, TabName, AddFunc, Param
  $query = "select \"Param\" from \"AdmTabsAddFunc\" ". 
           "where (\"TabName\"=:TabName) and (\"AddFunc\" = :ParamCode)"; 
  try {
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
  
  
    if ($dp2 = $STH->fetch(PDO::FETCH_ASSOC)) {
      $Res=$dp2['Param'];
    }
  }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

  return $Res;
}
//=================================================================
//=================================================================



/*
function EnumMultiSelect (&$pdo, $EnumName, $SelName, $StrVals) {
  // StrVals -- какие должны быть выделены позиции строка: 0,3,5,6
  // Передает на сервер : 1)  _MaxQty -- сколько всего шт выбора
  //                      2) _Sel_3] => 20  -- какие опции выбрал пользователь
  // Array ( [TestUOM_MaxQty] => 6 [TestUOM_Sel_3] => 20 [TestUOM_Sel_5] => 40 
  $Res='';
  $VArr = array ();
  $i = strpos ($StrVals, ',');
  $Div=',';
  if ($i===false) {
    $Div=';';
  }

  $Arr=explode ($Div, $StrVals);
  foreach($Arr as $Indx=>$V) {
    $Tr=trim($V);
    if ($Tr!='') {
      $VArr[$Tr]=1;
    }
  }

  $Lang=$_SESSION['LANG'];
  if ($Lang=='') {
    $Lang='RU';
  }
  
  //EnumValues
  //EnumName, EnumVal, Lang, EnumDescription
  $query = "select count(*) CNT from EnumValues ".
           " WHERE (EnumName='$EnumName') and (Lang='$Lang')";

  $sql2 = $pdo->query ($query) 
                  or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);

  $Cnt=0;
  if ($dp2 = $sql2->fetch_assoc()) {
    $Cnt=$dp2['CNT'];
  }

  if ($Cnt==0) {
    die ("Error: No values for EnumName='$EnumName' and Lang='$Lang' ");
  }


  //EnumValues
  //EnumName, EnumVal, Lang, EnumDescription
  $query = "select EnumVal, EnumDescription from EnumValues ".
           " WHERE (EnumName='$EnumName') and (Lang='$Lang') order by EnumVal ";

  $sql2 = $pdo->query ($query) 
                  or die("Invalid query:<br>$query<br> Line:".__LINE__." ". $pdo->error);

  echo("<input type=hidden Name={$SelName}_MaxQty ID={$SelName}_MaxQty value=$Cnt>".
        "<table><tr class=header><td><input type=checkbox Name={$SelName}_SelAll ".
         "ID={$SelName}_SelAll onclick='MSChangeVal(\"$SelName\");'></td>".
        "<td align=center><input type=button onclick='return MSClrVal(\"$SelName\");' value='".
           GetStr($pdo, 'Clear')."'></td>");


  $n=0;
  $i=0;
  while ($dp2 = $sql2->fetch_assoc()) {
    $n=NewLine($n);
    $i++;
    $Ch='';
    if ( !empty ( $VArr[$dp2['EnumVal']]) ) {
      $Ch=' checked';
    }
    $Clr=addslashes ($dp2['EnumDescription']);
    echo ("<td><input type=checkbox Name={$SelName}_Sel_$i$Ch Id={$SelName}_SEL_$i value='{$dp2['EnumVal']}'></td>".
          "<td>{$dp2['EnumDescription']}</td>");  
  }
  echo("</tr></table>");

  
}
*/

?>