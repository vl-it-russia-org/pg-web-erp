<?php
  die();
  echo("<hr><h4>".GetStr($pdo, 'CB_Banks')."</h4>");
  $query = "select * FROM CB_Banks where BIK='$BIK' order by BIK ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'BankName').'</th>');
  echo('<th>'.GetStr($pdo, 'BankTransitAcc').'</th>');
  echo('<th>'.GetStr($pdo, 'City').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td>");
    echo ("{$dpL['BankName']}</td>");
    echo ("<td>");
    echo ("{$dpL['BankTransitAcc']}</td>");
    echo ("<td>");
    echo ("{$dpL['City']}</td>");
    
  }
  echo("</tr></table>");
  echo("<a href='CB_BanksCard.php?New=1&BIK=$BIK'>".GetStr($pdo, "Add")."</a>");
?>
