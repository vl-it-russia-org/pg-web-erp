<?php
  die();
  echo("<hr><h4>".GetStr($pdo, 'Meetings')."</h4>");
  $query = "select * FROM Meetings where Id='$Id' order by Id ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'MeetingDate').'</th>');
  echo('<th>'.GetStr($pdo, 'Subject').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td>");
    echo ("{$dpL['MeetingDate']}</td>");
    echo ("<td>");
    echo ("{$dpL['Subject']}</td>");
    
  }
  echo("</tr></table>");
  echo("<a href='MeetingsCard.php?New=1&Id=$Id'>".GetStr($pdo, "Add")."</a>");
?>
