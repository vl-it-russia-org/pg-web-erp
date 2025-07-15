<?php
  die();
  echo("<hr><h4>".GetStr($pdo, 'MeetingParticipants')."</h4>");
  $query = "select * FROM MeetingParticipants where MId='$MId' order by MId,LineNo ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'LineNo').'</th>');
  echo('<th>'.GetStr($pdo, 'PartType').'</th>');
  echo('<th>'.GetStr($pdo, 'EMail').'</th>');
  echo('<th>'.GetStr($pdo, 'LastName').'</th>');
  echo('<th>'.GetStr($pdo, 'FirstName').'</th>');
  echo('<th>'.GetStr($pdo, 'MidName').'</th>');
  echo('<th>'.GetStr($pdo, 'IsHost').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td align=center>");

    echo("<a href='MeetingParticipantsCard.php?MId={$dpL['MId']}&LineNo={$dpL['LineNo']}'>");
    echo ("{$dpL['LineNo']}</a></td>");
    echo ("<td align=center>");
    echo (GetEnum($pdo, "PartType", $dpL['PartType'])."</td>");
    echo ("<td>");
    echo ("{$dpL['EMail']}</td>");
    echo ("<td>");
    echo ("{$dpL['LastName']}</td>");
    echo ("<td>");
    echo ("{$dpL['FirstName']}</td>");
    echo ("<td>");
    echo ("{$dpL['MidName']}</td>");
    echo ("<td align=center>");
    $Ch="";
      if ($dpL['IsHost']==1) {
        $Ch=" checked ";  
      }
        echo ("<input type=checkbox Name=IsHost value=1 $Ch></td>");
      
    
  }
  echo("</tr></table>");
  echo("<a href='MeetingParticipantsCard.php?New=1&MId=$MId'>".GetStr($pdo, "Add")."</a>");
?>
