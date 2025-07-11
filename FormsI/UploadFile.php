<?php
session_start();

include ("../setup/common_pg.php");
BeginProc();
CheckLogin1 ();
CheckRight1 ($pdo, 'Admin');


$real_extract = '/var/www/files';

$FLTxt= $_REQUEST['FL'];
if ($FLTxt=='') {
  die ("<br> Error: File is empty");
}


//echo ("<br>");
//print_r($_REQUEST);

$FL=base64_decode($FLTxt);

if (! file_exists ($FL) ) {
  die ("<br> Error: File is not found: $FL");
}

if ( is_dir($FL) ) {
  die ("<br> Error: $FL is directory ");
}

$handle = fopen($FL, "rb");
if (FALSE === $handle) {
  die("<br> Error: Not open file $FL");
}

  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename='.basename($FL));
  header('Content-Transfer-Encoding: binary');
  header('Expires: 0');
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
  header('Pragma: public');
  //header('Content-Length: ' . filesize($FL)); //Remove


while (!feof($handle)) {
  $contents = fread($handle, 8192);
  echo ($contents);
}
fclose($handle);  
?>