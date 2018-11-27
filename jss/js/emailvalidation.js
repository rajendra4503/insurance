$(document).ready(function(e) {
$('#submitbutton').click(function() {
var sEmail = $('#email_id').val();
if (validateEmail(sEmail)) {
//alert('Nice!! your Email is valid, now you can continue..');
}
else {
$('#email_id').focus();
$("#email_iderror").fadeIn();
$("#email_iderror").text("Please enter Valid Email Id.");
$("#email_iderror").fadeOut(1500);
return false;
e.preventDefault();
}
});
});

// Function that validates email address through a regular expression.
function validateEmail(sEmail) {
var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
if (filter.test(sEmail)) {
return true;
}
else {
return false;
}
}