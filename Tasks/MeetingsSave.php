<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');

$FldNames=array('Id','MeetingDate','Subject');
CheckTkn();

if (empty ($_REQUEST["MeetingDate"])) {
  $_REQUEST["MeetingDate"]="1900-01-01";
}
$PkArr=array('Id');
$New=$_REQUEST['New'];
$PdoArr = array();
$Id=$_REQUEST['Id'];
if ($Id==''){ 
    if ($New==1) {  
      //$query = "select MAX(Id) MX FROM Meetings ".
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
  $Res = UpdateTable ($pdo, "Meetings", $FldNames, $_REQUEST, $PkArr, 1, "Id");
$LNK='';
echo ('<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../style.css">'.
'<title>Meetings Save</title></head>
<body>');
  echo('<H2>Meetings Saved</H2>');


  echo('<form id="autoForm" method="post" action="MeetingsList.php" style="display: none;">');

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