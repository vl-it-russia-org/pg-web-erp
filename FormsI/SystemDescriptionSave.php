<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
$Editable = CheckFormRight($pdo, 'SystemDescription', 'Card');
CheckTkn();
$FldNames=array('Id','ParagraphNo','ElType','Description'
      ,'Ord1','ParentId');
CheckTkn();

if (empty ($_REQUEST["Ord1"])) {
  $_REQUEST["Ord1"]=0;
}

if (empty ($_REQUEST["ParentId"])) {
  $_REQUEST["ParentId"]=0;
}
$PkArr=array('Id');
$New=$_REQUEST['New'];
$PdoArr = array();
$Id=$_REQUEST['Id'];
if ($Id==''){ 
    if ($New==1) {  
      //$query = "select MAX(Id) MX FROM SystemDescription ".
      //         "WHERE ";
      //$sql2 = $pdo->query ($query)
      //               or die("Invalid query:<br>$query<br>" . $pdo->error);
      //$MX=0;
      //if ($dp = $sql2->fetch_assoc()) {
      //  $MX=$dp['MX'];
      //}
      //$MX++;
      //$_REQUEST['Id']=$MX;
      //$Id=$MX;
    }
    else { die ("<br> Error:  Empty Id");}
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
  $Res = UpdateTable ($pdo, "SystemDescription", $FldNames, $_REQUEST, $PkArr, 1, "Id");


$LNK='';
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<title>SystemDescription Save</title></head>
<body>');
  echo('<H2>SystemDescription Saved</H2>');


  echo('<form id="autoForm" method="post" action="SystemDescriptionList.php" style="display: none;">');

  $V=$_POST['Id'];
  echo("<input type=hidden name=Id value='$V'>\r\n");

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