<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('TabNo','Right','CanList','CanEdit'
      ,'CanCardReadOnly','CanDelete','CanXlsUpload');
CheckTkn();

if (empty ($_REQUEST["TabNo"])) {
  $_REQUEST["TabNo"]=0;
}

if (empty ($_REQUEST["CanList"])) {
  $_REQUEST["CanList"]=0;
}

if (empty ($_REQUEST["CanEdit"])) {
  $_REQUEST["CanEdit"]=0;
}

if (empty ($_REQUEST["CanCardReadOnly"])) {
  $_REQUEST["CanCardReadOnly"]=0;
}

if (empty ($_REQUEST["CanDelete"])) {
  $_REQUEST["CanDelete"]=0;
}

if (empty ($_REQUEST["CanXlsUpload"])) {
  $_REQUEST["CanXlsUpload"]=0;
}
$PkArr=array('TabNo', 'Right');
$New=$_REQUEST['New'];
$PdoArr = array();
$TabNo=$_REQUEST['TabNo'];
if ($TabNo==''){ die ("<br> Error:  Empty TabNo");}
$PdoArr['TabNo']= $TabNo;
$Right=$_REQUEST['Right'];
if ($Right==''){ 
    if ($New==1) {  
      $query = "select MAX(\"Right\") \"MX\" FROM \"AdmTabRights\" ".
               "WHERE (\"TabNo\"=:TabNo)";
      
      $sql2 = $pdo->query ($query)
                     or die("Invalid query:<br>$query<br>" . $pdo->error);
      $MX=0;
      if ($dp = $sql2->fetch_assoc()) {
        $MX=$dp['MX'];
      }
      $MX++;
      $_REQUEST['Right']=$MX;
      $Right=$MX;
    }
    else { die ("<br> Error:  Empty Right");}
}


  //---------------------------- Для автонумерации ---------------
  //include ("NumSeq.php");
  //if($_REQUEST['DocNo']=='') {
  //  $D=$_REQUEST['OpDate'];
  //  if ($D=='') {
  //    $_REQUEST['OpDate']=date('Y-m-d');
  //    $D=$_REQUEST['OpDate'];
  //  }
  //  $_REQUEST['DocNo'] = GetNextNo ( $pdo, 'BankOp', $D);
  //}


  $dp=array();
  $Res = UpdateTable ($pdo, "AdmTabRights", $FldNames, $_REQUEST, $PkArr, 1, "");
$LNK='';
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<title>AdmTabRights Save</title></head>
<body>');
  echo('<H2>AdmTabRights Saved</H2>');


  echo('<form id="autoForm" method="post" action="AdmTabRightsList.php" style="display: none;">');

  $V=$_POST['TabNo'];
  echo("<input type=hidden name=TabNo value='$V'>\r\n");

  $V=$_POST['Right'];
  echo("<input type=hidden name=Right value='$V'>\r\n");

  MakeTkn();
  echo (" </form>

            <script>
                // Автоматически отправляем форму через небольшую задержку
                window.onload = function() {
                    setTimeout(function() {
                        document.getElementById('autoForm').submit();
                    }, 10); // Задержка 0.01 секунда для показа анимации
                };
            </script>
  ");
?>
</body>
</html>