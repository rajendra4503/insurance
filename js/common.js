//DISABLE ALPHABETS IN PHONENUMBER FIELDS
$(document).ready(function() {
  $(".onlynumbers").keydown(function(event) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ( $.inArray(event.keyCode,[46,8,9,27,13]) !== -1 ||
       // Allow: Ctrl+A
      (event.keyCode == 65 && event.keyCode == 67 && event.keyCode == 86 || event.ctrlKey === true) ||
       // Allow: home, end, left, right
      (event.keyCode >= 35 && event.keyCode <= 39)) {
         // let it happen, don't do anything
         return;
    }
    else {
      // Ensure that it is a number and stop the keypress
      if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
        event.preventDefault();
      }
    }
  });
  
  $(".nonumbers").keydown(function(event) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ( $.inArray(event.keyCode,[46,8,9,27,13,32]) !== -1 ||
       // Allow: Ctrl+A
      (event.keyCode == 65 && event.keyCode == 67 && event.keyCode == 86 || event.ctrlKey === true) ||
       // Allow: home, end, left, right
      (event.keyCode >= 35 && event.keyCode <= 39)) {
         // let it happen, don't do anything
         return;
    }
    else {
      // Ensure that it is a number and stop the keypress
      if ((event.keyCode < 65 || event.keyCode > 90)) {
        event.preventDefault();
      }
    }
  });

  /*  $(".forminputs2").keydown(function(event) {
      alert(1);
      if (event.keyCode == 77) {
        event.preventDefault();
      } 
    });
*/
});