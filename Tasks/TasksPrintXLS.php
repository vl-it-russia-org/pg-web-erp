<?php
session_start();
require '../../composer/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
include ("../setup/common_pg.php");
BeginProc();
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
$TabName='Tasks';
$Frm='Tasks';
$Fields=array('Id','ShortName','Created'
      ,'StartDate','Author','Division','Priority'
      ,'Description','WishDueDate','PlannedWorkload','FactWorkLoad'
      ,'PlannedDueDate','Status','RespPerson','UserSatisfaction'
      ,'WaitTill');
$enFields= array('Division'=>'Divisions', 'Priority'=>'Priority', 'Status'=>'TaskStatus', 'UserSatisfaction'=>'UserSatisfaction');
CheckRight1 ($pdo, 'Admin');

 $ORD = $_REQUEST['ORD'];
if ($ORD =='1') {
$ORD = '"Id"';
  }
  else {
    $ORD = '"Id"';
  }

  $ORDS = ' order by  '; 
  if ($ORD !='') {
    $ORDS = ' order by '.$ORD;
  }

  $PdoArr = array();
  $WHS = '';
  $FullRef='?ORD='.$ORD;
  foreach ( $Fields as $Fld) {
    $Fltr=$_REQUEST['Fltr_'.$Fld];
    if ($Fltr!='') {
      if ($WHS !='') {
        $WHS.= ' and ';
      }
      if ($enFields[$Fld]!='') {
        $WHS.='("'.$Fld."\" = :$Fld)";
        $PdoArr[$Fld]= $Fltr;
      }
      else {
        $WHS.= SetFilter2Fld ( $Fld, $Fltr, $pdo );
      }
      $FullRef.='&Fltr_'.$Fld.'='.$Fltr ;
    }
  }


$Let = array ( 'A','B','C','D','E', 'F','G','H','I','J',
                   'K','L','M','N','0', 'P','Q','R','S','T','U');

$objPHPExcel = new Spreadsheet();
$objPHPExcel->getProperties()->setCreator("Vladislav Levitskiy")
             ->setLastModifiedBy($_SESSION['login'])
             ->setTitle("AccPhp Tasks")
             ->setSubject("Tasks")
             ->setDescription("VDL PHP+PDO+PostgreSQL")
             ->setKeywords("AccPhp;Tasks")
             ->setCategory("AccPhp;Tasks");
  
$objPHPExcel->setActiveSheetIndex(0);
$aSheet = $objPHPExcel->getActiveSheet();

$W= array ( 10, 10, 10, 10, 10, 10, 10, 11, 11, 12, 12);
$LastCol="";
foreach ($W as $i => $Val) {
  $aSheet->getColumnDimension($Let[$i])->setWidth($Val);
  $LastCol=$Let[$i];
};

if ($WHS != '') {
  $WHS = ' where '.$WHS;
};

$row=1;
$col=1;   


$aSheet->setCellValue([$col, $row], GetStr($pdo, 'Tasks').
      ' '.  GetStr($pdo, 'List'));
  
$row++;
$aSheet->setCellValue([$col, $row], GetStr($pdo, 'Created').
      ": {$_SESSION['login']} ". date("Y-m-d H:i:s"));


$query = "select * FROM \"Tasks\" ".
         "$WHS $ORDS";

try {
    $STH = $pdo->prepare($query);
    $STH->execute($PdoArr);



$row++;
$col=1;

$FL=$row;

foreach ( $Fields as $Fld) {
  $aSheet->setCellValue([$col, $row], GetStr($pdo, $Fld));
  $col++; 
}

$n=0;
$Cnt=0;
$row++;

while ($dp = $STH->fetch(PDO::FETCH_ASSOC)) {
  $col=1;

  $Fld='Id';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='ShortName';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='Created';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='StartDate';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='Author';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='Division';
  $aSheet->setCellValue([$col, $row], GetEnum($pdo, 'Divisions', $dp[$Fld]));
  $col++;

  $Fld='Priority';
  $aSheet->setCellValue([$col, $row], GetEnum($pdo, 'Priority', $dp[$Fld]));
  $col++;

  $Fld='Description';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='WishDueDate';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='PlannedWorkload';
  $OW=$dp[$Fld]; //$OW=number_format($dp[$Fld], 2, ".", "'");
    $aSheet->setCellValue([$col, $row], $OW);
  $col++;

  $Fld='FactWorkLoad';
  $OW=$dp[$Fld]; //$OW=number_format($dp[$Fld], 2, ".", "'");
    $aSheet->setCellValue([$col, $row], $OW);
  $col++;

  $Fld='PlannedDueDate';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='Status';
  $aSheet->setCellValue([$col, $row], GetEnum($pdo, 'TaskStatus', $dp[$Fld]));
  $col++;

  $Fld='RespPerson';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;

  $Fld='UserSatisfaction';
  $aSheet->setCellValue([$col, $row], GetEnum($pdo, 'UserSatisfaction', $dp[$Fld]));
  $col++;

  $Fld='WaitTill';
  $aSheet->setCellValue([$col, $row], $dp[$Fld]);
  $col++;
  $row++;
}

  $l=$row-1;
  $aSheet->setAutoFilter("A3:{$LastCol}3");
  $aSheet->freezePane("C4");

  $styleArray = array(
      'borders' => array(
          'outline' => array(
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
              //'color' => array('argb' => 'FFFF0000'),
          ),
          
          'inside' => array(
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
              //'color' => array('argb' => 'FFFF0000'),
          ),
      ),
  );  

  $aSheet->getStyle("A$FL:$LastCol$l")->applyFromArray($styleArray);



  $aSheet->getPageSetup()
                ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                                                                           // ::ORIENTATION_PORTRAIT );
  $aSheet->getPageSetup()
                ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);


  $margin = 0.5 / 2.54;
  $aSheet->getPageMargins()->setTop($margin*5);
  $aSheet->getPageMargins()->setRight($margin);
  $aSheet->getPageMargins()->setLeft($margin*2);
  $aSheet->getPageMargins()->setBottom($margin);

  //$aSheet->getPageSetup()->setScale(80);

  $aSheet->getPageSetup()->setFitToWidth(1);
  $aSheet->getPageSetup()->setFitToHeight(10);  

  $add_str=date('-Ymd_His');


  //MakeAdminRec ($pdo, $_SESSION['login'], 'EDI_ORD', $OrdId, 
  //                      'Out XLS', "Out file $add_str.XLS: $LineNo lines, amount $TotAmount");

  $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel , 'Xlsx');

 }
  catch (PDOException $e) {
    echo ("<hr> Line ".__LINE__."<br>");
    echo ("File ".__FILE__." :<br> $query<br>PDO Arr:");
    print_r($PdoArr);	
    die ("<br> Error: ".$e->getMessage());
  }



header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=Xls-Tasks'.$add_str.'.xlsx');
$writer->save('php://output');
?>