<script>
function SelectItemSet() {
  
  a=window.open('SelectItemSet.php', 'Select',
                'width=800,height=520,left=50, resizable=yes,scrollbars=yes,status=yes');
  
  return false;
}
//==========================================================================================


function SelectItemSetNo(ISId) {
  elem2 = document.getElementById(ISId);
  StdIS=elem2.value;
  
  a=window.open('SelectItemSet.php?ISNo='+StdIS, 'Select',
                'width=800,height=400,left=150, resizable=yes,scrollbars=yes,status=yes');
  
  return false;
}



//==========================================================================================
function SetItemSetOk() {
  var elem1 = document.getElementById('Use10');
  
  if (elem1.checked ) {
    elem2 = document.getElementById('StdIS');
    StdIS=elem2.value;
    if ( StdIS == '') {
      alert ("Please fill Std Item Set number");
      return false;
    }
    else {
      OW=window.opener;
      
      elem3= OW.document.getElementById('ISName');
      elem3.value=StdIS;

    
      elem3= OW.document.getElementById('HaveItemSet');
      elem3.checked=true;

      
      elem5= document.getElementById('StdISDescr');
      elem4= OW.document.getElementById('ISDescription');
      elem4.innerHTML=elem5.innerHTML;

      
      window.close();
      return false;
    }
  }

  elem1 = document.getElementById('Use20');
  if (elem1.checked ) {
    OW=window.opener;
    
    elem3= OW.document.getElementById('HaveItemSet');
    elem3.checked=true;


    elem3= OW.document.getElementById('ISName');
    elem3.value='UD';
    
    
    elem4= OW.document.getElementById('ISDescription');
    elem4.innerHTML='User defined';

    window.close();
    return false;
  }
  else {
    alert ("Please select Standard or User-defined Item set You want to use"); 
  }

  


     
     

  return false;  

}

</script>