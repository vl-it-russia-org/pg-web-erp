<?php
//==========================================================================================

function FldOrgEdit1(&$mysqli, $FldName, $FldVal, $Required='') {
  echo ("<td align=right><b>".GetStr($mysqli, $FldName).":</b></td>".
          "<td><input type=text size=50 id='$FldName' name='$FldName' value='$FldVal' $Required>".
          "<button onclick=\"return SelectFld('$FldName');\">...</button></td>");
};

//------------------------------------------------------------------------------------------
function OutPostReq() {

echo ("
<script>
// Открывает отдельное окно с заданными размерами и отправляет туда POST-форму
function openFormWithPostInWindow(formName, postParams, winName) {
    // Открываем новое окно с нужными параметрами
    window.open('', winName, 'width=300,height=400,left=50,top=50,resizable=yes,scrollbars=yes');

    // Создаем форму
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = formName;
    form.target = winName;

    // Добавляем параметры как скрытые поля
    postParams.forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    });

    // Добавляем форму на страницу, отправляем и удаляем
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
");
}
//==========================================================================================
function ScriptSelectionTabs ($TabName, $FrmName, $Caption, $Param2='') {
// Скрипт для выбора организации
$Tkn= MakeTkn(1);
$WinName = 'Select-'.$TabName;
echo ("<script>
 function Select{$TabName}Fld( ElId, EldId2, EldId3, EldId4 ) {
   let ArrParam = Array();
   ArrParam.push ([ 'ElId', ElId]);
   ArrParam.push ([ 'SelId', '$Param2']);

   SubVal = document.getElementById(ElId).value;
   ArrParam.push ([ 'SubStr', SubVal]);

   if (EldId2 !== undefined) {
     SubVal2 = document.getElementById(EldId2).value;
     ArrParam.push ([ 'SelId2', SubVal2]);
   }

   if (EldId3 !== undefined) {
     SubVal3 = document.getElementById(EldId3).value;
     ArrParam.push ([ 'SelId3', SubVal3]);
   }
   
   if (EldId4 !== undefined) {
     SubVal4 = document.getElementById(EldId4).value;
     ArrParam.push ([ 'SelId4', SubVal4]);
   }
   
   ArrParam.push (['FrmTkn', '$Tkn']);


   openFormWithPostInWindow('$FrmName', ArrParam, '$WinName');
   return false;
}
</script>");
};
//==========================================================================================
function ScriptSelectionTabs2 (&$mysqli, $TabName, $FrmName, $Caption) {
// Скрипт для выбора организации
echo ("<script>
 function Select{$TabName}Fld2(ElId, Par2 ) {
   El1 = document.getElementById(Par2);
   if (El1==null) {
     alert(Par2+' is not ok');
   }
   Par2Val = document.getElementById(Par2).value;
   SubVal = document.getElementById(ElId).value;
   a=window.open('{$FrmName}?ElId='+ElId+'&SubStr='+SubVal+'&Par2='+Par2Val, '".
     GetStr($mysqli, 'Select').' '.
     GetStr($mysqli, $Caption)."','width=900,height=520,resizable=yes,scrollbars=yes,status=yes');
   return false;
}
</script>");
};



//==========================================================================================
function GetTableName (&$db, $TabStr, &$BegPos) {
  $Tab2='';
  $i=strpos($TabStr, '[T:', $BegPos);
  if ($i!== false) {
    $end=strpos($TabStr, ']', $i);
    if ($end!==false) {
     $Tab2= substr($TabStr, $i+3, $end-$i-3);
     $BegPos=$end+1;
     //echo ("<br> Tab2: $Tab2 ");
    }
  }
  return $Tab2;
}
//==========================================================================================
function GetFieldName (&$db, $TabStr, &$BegPos) {
  $Tab2='';
  $i=strpos($TabStr, '[F:', $BegPos);
  if ($i!== false) {
    $end=strpos($TabStr, ']', $i+1);
    if ($end!==false) {
      $Tab2= substr($TabStr, $i+3, $end-$i-3);
      $BegPos=$end+1;
      //echo ("<br> Tab2: $TabStr $Tab2 I:$i End:$end");
    }
  }
  return $Tab2;
}
//==========================================================================================
 
?>