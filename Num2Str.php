<?php
//==========================================================================================
function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}
//==========================================================================================
function num2str($num) {
    $nul='����';
    $ten=array(
        array('','����','���','���','������','����','�����','����', '������','������'),
        array('','����','���','���','������','����','�����','����', '������','������'),
    );
    $a20=array('������','�����������','����������','����������','������������' ,'����������','�����������','����������','������������','������������');
    $tens=array(2=>'��������','��������','�����','���������','����������','���������' ,'�����������','���������');
    $hundred=array('','���','������','������','���������','�������','��������', '�������','���������','���������');
    $unit=array( // Units
        array('�������' ,'�������' ,'������',	 1),
        array('�����'   ,'�����'   ,'������'    ,0),
        array('������'  ,'������'  ,'�����'     ,1),
        array('�������' ,'��������','���������' ,0),
        array('��������','��������','����������',0),
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}
//==========================================================================================
//==========================================================================================
function num2str1($num, $ZN) {
    $nul='����';
    $ten=array(
        array('','����','���','���','������','����','�����','����', '������','������'),
        array('','����','���','���','������','����','�����','����', '������','������'),
    );
    $a20=array('������','�����������','����������','����������','������������' ,'����������','�����������','����������','������������','������������');
    $tens=array(2=>'��������','��������','�����','���������','����������','���������' ,'�����������','���������');
    $hundred=array('','���','������','������','���������','�������','��������', '�������','���������','���������');
    $unit=array( // Units
        array('�������' ,'�������' ,'������',	 1),
        array('�����'   ,'�����'   ,'������'    ,0),
        array('������'  ,'������'  ,'�����'     ,1),
        array('�������' ,'��������','���������' ,0),
        array('��������','��������','����������',0),
    );
    //
    if ($ZN=='KG') {
      $unit[0][0]='�����';
      $unit[0][1]='������';
      $unit[0][2]='�������';

      $unit[1][0]='���������';
      $unit[1][1]='���������';
      $unit[1][2]='���������';
    }
    else
    if ($ZN=='KL') {
      $unit[0][0]=' ';
      $unit[0][1]=' ';
      $unit[0][2]=' ';

      $unit[1][0]=' ';
      $unit[1][1]=' ';
      $unit[1][2]=' ';
    }

    //echo ("<br> $num $ZN Unit= ");
    //print_r($unit);

    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    
    //echo ("<br>Rub=$rub  $kop ");

    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    
    if ($ZN!='KL') {
      $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    }
    else {
      $out[] ='';
    }
    //echo ("<br>Out: ");
    //print_r($out);

    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}
//==========================================================================================
function ToUpper($Str) {
  $Subst= array ( '�' => '�', '�' => '�','�' => '�','�' => '�','�' => '�','�' => '�',
                  '�' => '�', '�' => '�','�' => '�','�' => '�','�' => '�','�' => '�',
                  '�' => '�', '�' => '�','�' => '�','�' => '�','�' => '�','�' => '�',
                  '�' => '�', '�' => '�','�' => '�','�' => '�','�' => '�','�' => '�',
                  '�' => '�', '�' => '�','�' => '�','�' => '�','�' => '�','�' => '�',
                  '�' => '�', '�' => '�','�' => '�');
  $Res =$Str;
  if ( $Subst[$Str]!='') {
    $Res= $Subst[$Str]; 
  }
  return $Res;
   
}
//==========================================================================================
function ordutf8($string, &$offset) {
    $code = ord(substr($string, $offset,1)); 
    if ($code >= 128) {        //otherwise 0xxxxxxx
        if ($code < 224) $bytesnumber = 2;                //110xxxxx
        else if ($code < 240) $bytesnumber = 3;        //1110xxxx
        else if ($code < 248) $bytesnumber = 4;    //11110xxx
        $codetemp = $code - 192 - ($bytesnumber > 2 ? 32 : 0) - ($bytesnumber > 3 ? 16 : 0);
        for ($i = 2; $i <= $bytesnumber; $i++) {
            $offset ++;
            $code2 = ord(substr($string, $offset, 1)) - 128;        //10xxxxxx
            $codetemp = $codetemp*64 + $code2;
        }
        $code = $codetemp;
    }
    $offset += 1;
    if ($offset >= strlen($string)) $offset = -1;
    return $code;
}
//==========================================================================================
function CapitalFL ($Str) {
  $FL= iconv( 'UTF-8', 'Windows-1251',   mb_substr($Str, 0, 1));
  $Arr = array ('�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', 
                 '�'=>'�', 
                 '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', 
                 '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', 
                 '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�', '�'=>'�' );
  $Ch = iconv( 'Windows-1251', 'UTF-8', $Arr[$FL]);

  $Res= $Ch.mb_substr($Str, 1);
  return $Res;
}

//==========================================================================================
function int2str($num1) {
    $nul='����';
    $num= floor($num1);
    $ten=array(
        array('','����','���','���','������','����','�����','����', '������','������'),
        array('','����','���','���','������','����','�����','����', '������','������'),
    );
    $a20=array('������','�����������','����������','����������','������������' ,'����������','�����������','����������','������������','������������');
    $tens=array(2=>'��������','��������','�����','���������','����������','���������' ,'�����������','���������');
    $hundred=array('','���','������','������','���������','�������','��������', '�������','���������','���������');
    $unit=array( // Units
        array('' ,'' ,'',	 1),
        array(''   ,''   ,''    ,0),
        array('������'  ,'������'  ,'�����'     ,1),
        array('�������' ,'��������','���������' ,0),
        array('��������','��������','����������',0),
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    $out[] = '';//$kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}
//==========================================================================================

?>