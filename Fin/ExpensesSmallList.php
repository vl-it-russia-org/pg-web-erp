<?php
  die();
  echo("<hr><h4>".GetStr($pdo, 'Expenses')."</h4>");
  $query = "select * FROM Expenses where ExpId='$ExpId' order by ExpId ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'ExpenseName').'</th>');
  echo('<th>'.GetStr($pdo, 'HaveRegions').'</th>');
  echo('<th>'.GetStr($pdo, 'FinDiv').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td>");
    echo ("{$dpL['ExpenseName']}</td>");
    echo ("<td align=center>");
    $Ch="";
      if ($dpL['HaveRegions']==1) {
        $Ch=" checked ";  
      }
        echo ("<input type=checkbox Name=HaveRegions value=1 $Ch></td>");
      
    echo ("<td align=center>");
    echo (GetEnum($pdo, "ExpensesDiv", $dpL['FinDiv'])."</td>");
    
  }
  echo("</tr></table>");
  echo("<a href='ExpensesCard.php?New=1&ExpId=$ExpId'>".GetStr($pdo, "Add")."</a>");
?>
