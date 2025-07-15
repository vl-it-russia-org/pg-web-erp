<?php
function HtmlTxt($FromTxtIn) {
  // HtmlSubst
  // TagFrom, TagTo
  $FromTxt=array('[/a]', '[/b]', '[/li]', '[/ul]', '[a]', '[br]', '[b]', '[f]', 
                 '[li]', '[p]',  '[ul]' ,'[html]','[/html]','[body]','[/body]', '[sup]', '[/sup]' , '[sp]', '[beg]');
  $ToTxt=array(  '</a>', '</b>', '</li>', '</ul>', '<a',  '<br>', '<b>', '>',
                 '<li>', '<p>',  '<ul>','<html>', '</html>','<body>','</body>', '<sup>', '</sup>' , '<sup', '<');

  return str_replace($FromTxt, $ToTxt,$FromTxtIn);  
}

function BuildHtmlInput($IdName) {
  $FromTxt=array( 
    '[p]'=>'<p>','[br]'=>'<br>', 
    '[a]'=> '<a','[/a]'=>'</a>', 
    '[b]'=>'<b>','[/b]'=>'</b>', 
    
    '[li]'=> '<li>','[/li]'=> '</li>', 
    '[ul]'=>'<ul>' ,'[/ul]'=>'</ul>',     
    
    '[html]'=>'<html>',  '[/html]'=>'</html>',
    '[body]'=> '</body>','[/body]'=> '</body>', 
    '[sup]'=>'<sup>', '[/sup]'=>'</sup>' , 
    '[sp]'=>'<sup', '[beg]'=> '<', '[f]'=>'>');
  
  
  $N=5;
  $i=0;
  echo ("<table><tr>");
  foreach ($FromTxt as $Sym=>$ToSym) {
    if ($i>$N) {
      $i=0;
      echo ("</tr><tr>");  
    }
    $i++;

    echo ("<td><button type=button onclick='SubHtml(\"$IdName\", \"$Sym\")' title='will be: $ToSym html symbol'>$Sym</button></td>");
  }
  echo ("</tr></table>");
}
//--------------------------------------------------------------------------------
function BuildHtmlArrInput($IdName, &$Arr, $Cols) {
  $N=$Cols;
  $i=0;
  echo ("<table><tr>");
  foreach ($Arr as $Sym=>$ToSym) {
    if ($i>$N) {
      $i=0;
      echo ("</tr><tr>");  
    }
    $i++;

    echo ("<td><button type=button onclick='SubHtml(\"$IdName\", \"$Sym\")' title='will be: $ToSym html symbol'>$Sym</button></td>");
  }
  echo ("</tr></table>");
}

//------------------------------------------------------


?>