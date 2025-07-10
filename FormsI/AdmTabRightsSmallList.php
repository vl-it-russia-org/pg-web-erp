<?php
  die();
  echo("<hr><h4>".GetStr($pdo, 'AdmTabRights')."</h4>");
  $query = "select * FROM AdmTabRights where TabNo='$TabNo' order by TabNo,Right ";
  $sql2 = $pdo->query ($query)
            or die("Invalid query:<br>$query<br>" . $pdo->error);

  echo('<table><tr class=header>');
  echo('<th>'.GetStr($pdo, 'Right').'</th>');
  echo('<th>'.GetStr($pdo, 'CanList').'</th>');
  echo('<th>'.GetStr($pdo, 'CanEdit').'</th>');
  echo('<th>'.GetStr($pdo, 'CanCardReadOnly').'</th>');
  echo('<th>'.GetStr($pdo, 'CanDelete').'</th>');
  echo('<th>'.GetStr($pdo, 'CanXlsUpload').'</th>');
  $i=0;
  while ($dpL = $sql2->fetch_assoc()) {
    $i=NewLine($i);

    echo ("<td>");

    echo("<a href='AdmTabRightsCard.php?TabNo={$dpL['TabNo']}&Right={$dpL['Right']}'>");
    echo ("{$dpL['Right']}</a></td>");
    echo ("<td align=center>");
    $Ch="";
      if ($dpL['CanList']==1) {
        $Ch=" checked ";  
      }
        echo ("<input type=checkbox Name=CanList value=1 $Ch></td>");
      
    echo ("<td align=center>");
    $Ch="";
      if ($dpL['CanEdit']==1) {
        $Ch=" checked ";  
      }
        echo ("<input type=checkbox Name=CanEdit value=1 $Ch></td>");
      
    echo ("<td align=center>");
    $Ch="";
      if ($dpL['CanCardReadOnly']==1) {
        $Ch=" checked ";  
      }
        echo ("<input type=checkbox Name=CanCardReadOnly value=1 $Ch></td>");
      
    echo ("<td align=center>");
    $Ch="";
      if ($dpL['CanDelete']==1) {
        $Ch=" checked ";  
      }
        echo ("<input type=checkbox Name=CanDelete value=1 $Ch></td>");
      
    echo ("<td align=center>");
    $Ch="";
      if ($dpL['CanXlsUpload']==1) {
        $Ch=" checked ";  
      }
        echo ("<input type=checkbox Name=CanXlsUpload value=1 $Ch></td>");
      
    
  }
  echo("</tr></table>");
  echo("<a href='AdmTabRightsCard.php?New=1&TabNo=$TabNo'>".GetStr($pdo, "Add")."</a>");
?>
