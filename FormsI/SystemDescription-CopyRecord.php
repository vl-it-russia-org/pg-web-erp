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
if ($_REQUEST['NewStatus']!='Copy') { 
  die ('<br> Error: Copy expected');
}
$PdoArr=array();

if (empty($_REQUEST['Id'])) {
  die ('<br>Error: Id is empty');
}
$PdoArr['Id']=$_REQUEST['Id'];

$query = "select * FROM \"SystemDescription\" ".
         "WHERE (\"Id\"=:Id)";
try{
  $STH = $pdo->prepare($query);
  $STH->execute($PdoArr);  
  
  $NewId="";
  if ($db = $STH->fetch(PDO::FETCH_ASSOC)) {
    $PdoArr=array();
    $PdoArr['ParagraphNo']=$db['ParagraphNo'];
    $PdoArr['ElType']=$db['ElType'];
    $PdoArr['Description']=$db['Description'];
    $PdoArr['Ord1']=$db['Ord1'];
    $PdoArr['ParentId']=$db['ParentId'];

    $query="insert into \"SystemDescription\" (\"ParagraphNo\", \"ElType\", \"Description\", \"Ord1\", \"ParentId\") values (:ParagraphNo, :ElType, :Description, :Ord1, :ParentId)";

    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);
    $NewId=$pdo->lastInsertId();
  }
  else {
    die ("<br> Error: Record is not found");
  }
}
catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }

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