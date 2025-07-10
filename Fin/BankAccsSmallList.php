<?php
  die();
  echo("<hr><h4>".GetStr($pdo, 'BankAccs')."</h4>");
  $query = "select * FROM BankAccs where BankId='$BankId' order by BankId ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'Description').'</th>');
  echo('<th>'.GetStr($pdo, 'Country').'</th>');
  echo('<th>'.GetStr($pdo, 'BIK').'</th>');
  echo('<th>'.GetStr($pdo, 'BankName').'</th>');
  echo('<th>'.GetStr($pdo, 'City').'</th>');
  echo('<th>'.GetStr($pdo, 'TransitAccount').'</th>');
  echo('<th>'.GetStr($pdo, 'AccountNo').'</th>');
  echo('<th>'.GetStr($pdo, 'Currency').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td>");
    echo ("{$dpL['Description']}</td>");
    echo ("<td align=center>");
    echo (GetEnum($pdo, "Country", $dpL['Country'])."</td>");
    echo ("<td>");
    echo ("{$dpL['BIK']}</td>");
    echo ("<td>");
    echo ("{$dpL['BankName']}</td>");
    echo ("<td>");
    echo ("{$dpL['City']}</td>");
    echo ("<td>");
    echo ("{$dpL['TransitAccount']}</td>");
    echo ("<td>");
    echo ("{$dpL['AccountNo']}</td>");
    echo ("<td align=center>");
    echo (GetEnum($pdo, "Currency", $dpL['Currency'])."</td>");
    
  }
  echo("</tr></table>");
  echo("<a href='BankAccsCard.php?New=1&BankId=$BankId'>".GetStr($pdo, "Add")."</a>");
?>
