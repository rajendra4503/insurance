<?php
session_start();
//ini_set("display_errors","0");
include_once('include/configinc.php');
$remember_me = (empty($_COOKIE['remember_me'])) ? '' : $_COOKIE['remember_me'];
//echo $remember_me;
//GET COUNTRIES
$get_countries = mysql_query("select CountryCode, CountryName, CurrencyCode,Timezone from COUNTRY_DETAILS where Timezone!=''");
$country_count = mysql_num_rows($get_countries);
$country_options = "";
if($country_count > 0){
	while ($countries = mysql_fetch_array($get_countries)) {
		$country_code 	= $countries['Timezone'];
		//$country_code 	= $countries['CountryCode'];
		$country_name 	= $countries['CountryName'];
		$currency_code 	= $countries['CurrencyCode'];
		$country_options .= "<option value='$country_code'>$country_name</option>";
	}
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>Plan Piper - Login</title>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
<link href="css/custom.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/planpiper.css">
<link rel="stylesheet" type="text/css" href="fonts/font.css">
<link rel="shortcut icon" href="images/planpipe_logo.png"/>
<style type="text/css">
	#username{
		padding-left: 7px;
	}
	#password{
		padding-left: 7px;
	}
</style>     
<script type="text/javascript">
	function keychk(event)
	{
	//alert(123)
		if(event.keyCode==13)
		{
			$("#loginbutton").click();
		}
	}
</script>

<script type="text/javascript">
	var remember = 0;
	function rememberme()
	{
		document.getElementById('remember').checked = true;
		document.getElementById('changepic').innerHTML = "<img src='images/remember.png' width='30px' height='30px' id='rememberme_new' onclick='dontrememberme();'>";
		document.getElementById('rememberme_new').style.setProperty("margin-top", "28px");
		document.getElementById('rememberme_new').style.setProperty("margin-right", "36px");
		document.getElementById('rememberme_new').style.setProperty("cursor", "pointer");
		document.getElementById('rememberme_new').style.setProperty("vertical-align", "inherit");
		remember = 1;
	}
	function dontrememberme() {
		document.getElementById('remember').checked = false;
		document.getElementById('changepic').innerHTML = "<div id='checkbox' onclick='rememberme();'></div>";
		remember = 0;
	}
</script>
</head>
<body style="overflow:hidden;">
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><img src="images/Plan-Pipe-newlogo.png"/><span>&nbsp;&nbsp;Plan Piper</span></a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse-1">
                <ul class="nav navbar-nav" style="border:none;margin-bottom:0px;border-radius:0px;">
                    <li>
                        <a href="index.html">Home</a>
                    </li>
                    <li>
                        <a href="about.html">About</a>
                    </li>
                    <li>
                        <a href="features.html">Features</a>
                    </li>
                    <li>
                        <a href="benefits.html">Benefits</a>
                    </li>
                    <li>
                        <a href="pricing.html">Pricing</a>
                    </li>
                    <li>
                        <a href="whocanuse.html">Who can use this app?</a>
                    </li>
                    <li class="active">
                        <a href="login.php">Login</a>
                    </li>
                    <li>
                        <a href="register.html">Register</a>
                    </li>
                    <li>
                        <a href="apps.html">Apps</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
	<div id="big_wrapper">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="mainplanlistdiv">
		<!--<div align="center">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="center">
				<div style="margin-bottom:20px;"><img src="images/appmantras_logo.png" id="appmantras_logo"></div>
			</div>
		</div>-->
			<div>
			<div align="center" style="margin-top:5%;">
				<div class="col-lg-offset-4 col-lg-4 col-lg-offset-4 col-md-offset-3 col-md-6 col-md-offset-6 col-sm-12 col-xs-12" align="center" style="background-color:#f4f4f4;-webkit-box-shadow: 4px 0px 5px 0px rgba(233,233,233,1);-moz-box-shadow: 4px 0px 5px 0px rgba(233,233,233,1);box-shadow: 4px 0px 5px 0px rgba(233,233,233,1);">
					<div id="planpipe_in">Sign In</div>
				</div>
			</div>
			<div align="center">
				<div class="col-lg-offset-4 col-lg-4 col-lg-offset-4 col-md-offset-3 col-md-6 col-md-offset-6 col-sm-12 col-xs-12" align="center" style="background-color:#f4f4f4;-webkit-box-shadow: 3px 6px 5px 0px rgba(233,233,233,1);-moz-box-shadow: 3px 6px 5px 0px rgba(233,233,233,1);box-shadow: 3px 6px 5px 0px rgba(233,233,233,1);">
					<div style="margin-bottom:30px;">
						<span id="errormessage" style="color:#F65100;font-family:RalewayRegular;"></span>
					</div>
					<form method="post" id="login_form" name="login_form" action="">
						<select id="timezone" name="timezone" class="loginselect">
							<option value="0" style="display:none;">Select</option>
							<?php echo $country_options;?>
						</select>
						<input type="text" name="username" id="username" placeholder="mobile or email" autofocus onkeypress='keychk(event)' value="<?php echo $remember_me; ?>" title="Enter your Email ID or Mobile Number" maxlength="50">
						<div class="errormessages" id="error_username">	</div>
						<div class="col-lg-10 col-md-10 col-sm-10 col-xs-8 paddingrl0" style="margin-top:3px;">
							<input type="password" name="password" id="password" placeholder="password"  onkeypress='keychk(event)' title="Enter your password" maxlength="20">
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 paddingrl0" style="height:40px;margin-top:3px;" id="sp">
							<div id="showpassword" style="display:none">Show</div>
						</div>
						<input type="hidden" name="country" id="country" value="">
						<div class="errormessages" id="error_password">	</div>
						<div align="right" style="margin-top:5px;"><span title="Forgot Password" style="cursor:pointer;" id="link_text_overlay"><img src="images/question.png" id="overlay_question_icon"><u class="dotted">Forgot Password?</u></span></div>
						<div id="error" style="color:red"></div>
						<div id="loader" style="display:none;margin-top:10px;"><img src="images/loader.gif"></div>
						<div id="passwordchanged" style="color:green;"></div>
						<div id="overlay_login_button"><input type="button" id="loginbutton" name="loginbutton" value="Login" title="Login to Plan Piper"><span id="flash"></span></div>
						<div align="center">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="center">
							<div id="remember_mediv" title="Remember Me" style="margin-top:10px;margin-bottom:10px;">
								<input type="checkbox" name="remember_me" id="remember_me" style="display:none;">
								<label id="remember" style="cursor:pointer;margin-top:10px;" for="remember_me">
									<img src="images/checkboxoff.png" style="width:22px;height:auto;margin-right:5px;margin-top:-5px;cursor:pointer;" id="remembermeimage">REMEMBER ME
								</label>       
							</div>
						</div>
					</form>
				</div>
			</div>
			</div>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="center" id="registerherediv" style="margin-top:50px;">
			<a href="vendor_registration.php" style="color:#004f35;"><span style='cursor:pointer;'><u>Click here to register</u></span></a>
		</div>
		<!-- Modal window for change password-->
		<div class="modal modalblack" id="changepassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  		<div class="modal-dialog" >
	    		<div class="modal-content modal-content-transparent">
	      			<div class="modal-header" align="center">
			 			<h4 class="modal-title"><span id="changepasswordtext">Change Password</span></h4>
	      			</div>
	      			<div class="modal-body">
						<form name="change_password_form" id="change_password_form" method="post">
						<div style="color:#000;">Password should have 6-15 characters which contains atleast one numeric digit and one alphabet</div>
							<div>
								<input type="password" name="password1" id="password1" maxlength="20" class="changepasswordinputs" placeholder="Enter new password">
							</div>
							<div id="password1error" style="color:#000;"></div>
							<div style="margin-top:20px;">
								<input type="password" name="password2" id="password2" maxlength="20" class="changepasswordinputs" placeholder="Confirm new password">
							</div>
							<div id="password2error" style="color:#000;"></div>
							<div id="passwordchanged1" style="color:#000;"></div>
							<input type="hidden" name="type" id="type" value='change_password'>
							<input type="hidden" name="changepassword_userid" id="changepassword_userid">
						</form>
	      			</div>
	      			<div align="center" style="margin-top:15px;margin-bottom:30px;">
	        		<button type="button" name="change_password" id="change_password" class="changepasswordbuttons">SAVE</button>
	        		<button type="button" id="cancel_button" class="changepasswordbuttons">CANCEL</button>
	      			</div>
	    		</div>
	  		</div>
		</div>
		<!-- Switch Merchant -->
		<div class="modal modalblack" id="switchcommunitymodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog" style="margin: 0;position: absolute;top: 50%;left: 50%;">
		    	<div class="modal-content modal-content-transparent">
		    		<form name="switch_community_form" id="switch_community_form">
		      			<div class="modal-header" align="center">
		        			<h5 class="modal-title" id="switchcommunitytitle">Choose Merchant</h5>
		      			</div>
		      			<div class="modal-body" align="center">
		        			<select id="switchcommunityselect" name="switchcommunityselect">
		          				<option selected style="display:none;" value="0">Select Merchant</option>
		        			</select>
		        			<input type="hidden" name="switchmerchant_userid" id="switchmerchant_userid">
		        			<div id="communityerror" style="color:#fff;"></div>
		      			</div> 
		      		</form>
		         	<div class="margintop20" align="center">
		             	<button id='switchcommunity' class="switchbuttons">SELECT</button>
		             	<a href='logout.php'><button id='cancelswitch' class="switchbuttons">CANCEL</button></a>
		         	</div>
		   		</div>
		  	</div>
		</div>	
		<?php //include("footer.php");?> 	
	</div><!-- big_wrapper ends -->
	<div style="position:fixed;	bottom:0;left:0;width: 100%;">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background-color:#0b3024;padding-top:2%;" align="center"  id="fixed_footer">
                    <ul class="list-inline footerdiv">
                        <li>
                            <a href="http://www.appmantras.com"><img src="images/appmantraslogo.png" width="20" height="20" />&nbsp;App Mantras</a>
                        </li>&nbsp;&nbsp;&nbsp;&nbsp;
                        <li>
                            <a href="useragreement.html">Terms of Usage</a>
                        </li>&nbsp;&nbsp;&nbsp;&nbsp;
                        
                        <li>
                                <a href="privacypolicy.html">Privacy Policy</a>
                            </li>&nbsp;&nbsp;&nbsp;&nbsp;
                            <li data-reveal-id="contact">
                                <a href="contactus.html">Contact Us</a>
                            </li>
                     </ul>
                     <p class="copyright text-muted small">Copyright &copy; App Mantras Software Pvt. Ltd. All Rights Reserved.</p>
            </div>	
 </div>		
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/modernizr.js"></script>
<script type="text/javascript" src="js/placeholders.min.js"></script>
<script type="text/javascript" src='js/get_user_timezone.js'></script>
<script type="text/javascript">
$(document).ready(function() {


setTimeout(function(){ $("#username").click(); }, 1000);
   	$("#username,#timezone").on("click",function(){
	//alert(123)
	var timezone=$("#timezone").val();
	//bootbox.alert(timezone)
	//alert(timezone)
	$.ajax({
		url:'ajax_validation.php',
		data:{timezone:timezone,type:"get_country_code"},
		type: 'post',
		success : function(resp){
		//bootbox.alert(resp)
		//alert(resp)
		var code = resp;		
		document.getElementById('country').value = code;
		//alert(code);			     
		},
		error : function(resp){}
		});
	});


var w = window.innerWidth;
var h = window.innerHeight;
var windowheight = h;
var available_height = h - 160;
$('#mainplanlistdiv').height(available_height);
localStorage.clear();
var logged_pwdstatus;
var logged_userid;
var logged_email;
var multiple = 0;
var remember_me = "<?php echo $remember_me;?>";
if(remember_me=="")
{
	$('#remembermeimage').attr("src","images/checkboxoff.png");
}
else
{
	$('#remembermeimage').attr("src","images/checkboxon.png");
	setTimeout(function () {
   $('#remember').trigger('click');
}, 1000);
}
$("#password").keyup(function(){
	var myLength = $("#password").val().length;
	if(myLength>0)
	{
		$("#showpassword").show();
	}
	else
	{
		$("#showpassword").hide();
	}
  	
});

	$('#remember').click(function(){
		var rmflag = $('#remember_me').is(':checked'); 
		//alert(rmflag);
		if(rmflag == false){
		$('#remembermeimage').attr("src","images/checkboxon.png");
		}
		if(rmflag == true){
		$('#remembermeimage').attr("src","images/checkboxoff.png");
		//$('#remembermeimage').css("width","22px");
		//$('#remembermeimage').css("margin-right","5px");
		}
	});
	    var passwordvisible = 0;
    $('#showpassword').click(function(){
    	if(passwordvisible == 0){
	    	$('#password').attr("type","text");
	    	$('#showpassword').text("Hide");
	    	passwordvisible = 1;
    	} else{
      		$('#password').attr("type","password");
	    	$('#showpassword').text("Show");
	    	passwordvisible = 0;  		
    	}

    });
	$("#loginbutton").click(function() {
	//alert(123)
	var form 		= $("#login_form");    
	var username 	= $("#username", form).val();
	var country 	= $("#country", form).val();
	var password 	= $("#password", form).val();
	var remember_me = $("#remember_me",form).prop('checked');
	var type  		= "login";
	var usernamecheck = username.replace(/ /g,'');
	var passwordcheck = password.replace(/ /g,'');
		if(usernamecheck=="")
		{
		$("#username").val('');
		$("#username").focus();
		$("#error_username").fadeIn();
		$("#error_username").text("Please enter your Email ID/Mobile No.");
		$("#error_username").fadeOut(6000);
		}
		else if(passwordcheck=="")
		{
		$("#password").val('');
		$("#password").focus();
		$("#error_password").fadeIn();
		$("#error_password").text("Please enter your password");
		$("#error_password").fadeOut(6000);	
		}
		else
		{
		$("#loader").show();
		var dataString = "username="+username+"&password="+password+"&remember_me="+remember_me+"&type="+type+"&country="+country;
		//alert(dataString);
			$.ajax({
				type 		: "POST",
				url			: "ajax_validation.php",
				data 		: dataString,
				dataType	: 'json', 
				async		: false,
				success		: function (response,status)
				{
				//alert(status)
				//alert(response.login)
				//alert(response.multiple_merchants)
				var resp_login 		       = response.login;
                var resp_userid 	       = response.userid;
				var resp_multiple 	       = response.multiple_merchants;
                var resp_passwordstatus    = response.passwordstatus;
					if(resp_login==1)
				    {
				    	$("#changepassword_userid").val(resp_userid)
                        window.localStorage["resp_userid"]	= resp_userid;
			            localStorage.setItem("resp_userid", resp_userid);
                       // alert(resp_userid)
                        if(resp_passwordstatus==1)
                        {
                        window.location.href = "plan_users.php";
                        }
                        else
                        {//alert(resp_userid)
                        //window.location.href="change_password.html";
                        $("#loader").hide();
                        $('#changepassword').modal('show');
                        }
				    }
					else if(resp_login==0 && resp_multiple==0)
					{
					$("#loader").hide();
					$("#error_password").fadeIn();
					$("#error_password").text("Invalid Login");
					$("#error_password").fadeOut(6000);
					}	
				}
			});
		}	
	});

	function trim(str){
	var str=str.replace(/^\s+|\s+$/,'');
	return str;
	}

	//Function that validates email address through a regular expression.
	function validateEmail(sEmail) {
		var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
		if (filter.test(sEmail))
		{
		return true;
		}
		else
		{
		return false;
		}
	}
$("#cancel_button").click(function() {
	window.location.href = 'logout.php';
});
//CHANGE PASSWORD
$("#change_password").click(function() {
//alert(123)
//alert(resp_userid)
//$("#email2").val(email);
var form 		= $("#change_password_form");    
var password1 	= $("#password1", form).val();
var password2 	= $("#password2",form).val();
var type 		= $("#type",form).val();

var a  = $("#changepassword_userid").val();
  //alert(a)  
var str = form.serialize();
//alert(str);
if(password1=="")
{
$("#password1error").fadeIn();
$("#password1error").text("Please enter new password");
$("#password1").focus();
$("#password1error").fadeOut(6000);
return false;
}
if(password1!=""){
	//alert(password1)
var passw = /^(?=.*\d)(?=.*[a-zA-Z])(?=.*[a-zA-Z0-9!@#$%^&*]).{6,15}$/;
    if(!password1.match(passw)) 
    { 
    $("#password2error").fadeIn();
	$("#password2error").text("Password should have 6-15 characters which contains atleast one numeric digit and one alphabet");
	$("#password1").focus();
	$("#password2error").fadeOut(6000);
    return false;
    }
}
if(password2=="")
{
$("#password2error").fadeIn();
$("#password2error").text("Please confirm new password");
$("#password2error").fadeOut(6000);
$("#password2").focus();
return false;
}
if(password1.indexOf(" ") !== -1 )
{
	$("#password1error").fadeIn();
	$("#password1error").text("Please enter password without any whitespaces.");
	$("#password1").focus();
	$("#password1error").fadeOut(6000);
	return false;
}

if(password2.indexOf(" ") !== -1 )
{
	$("#password2error").fadeIn();
	$("#password2error").text("Please enter password without any whitespaces.");
	$("#password2").focus();
	$("#password2error").fadeOut(6000);
	return false;
}
if(password1 != "" && password2 != "")
{
	if(password1 != password2)
	{
	//alert("Password Mismatch");
	$("#password2error").fadeIn();
	$("#password2error").text("Passwords don't match");
	$("#password2").focus();
	$("#errormessage").fadeOut(6000);
	return false;
	}
	$.ajax({
		type		: 'POST', 
		url			: 'ajax_validation.php', 
		crossDomain	: true,
		data		: str,
		dataType	: 'json', 
		async		: false,
		success	: function (response)
		{ 
			//alert(response);
			//alert(response.success);
			if(response.success==true)
			{ 
			$("#password2error").hide();
			$("#password1error").hide();
			$("#passwordchanged1").fadeIn();
			$("#passwordchanged1").text("Password changed successfully");
			$("#passwordchanged1").fadeOut(2000);
			var delay = 2000;
				setTimeout(function() {
				window.location.href="plan_users.php";
				}, delay);
			} 
			else
			{
			alert("Please try again");
			$('#changepassword').modal('show');
			}
	 	},
		error: function(error)
		{
			$("#password2error").fadeIn();
			$("#password2error").text("Failed to communicate with the server");
			$("#password2error").fadeOut(6000);
		}
	}); 
}
return false;
});
//END OF CHANGE PASSWORD

//FORGOT PASSWORD
$("#link_text_overlay").click(function() {
//alert("Temporarily Not Available")
var email = $('#username').val();
var type  = "forgot_password";
//alert(email)
if(email=="")
{
$("#error").fadeIn();
$("#error").html("<b>Please enter your email id<b>");
$("#error").fadeOut(6000);
}
else
{
email_id = email.replace(/ /g,''); //To check if the variable contains only spaces
if(email_id == ''){
//$('#email_id').val('');
$('#username').focus();
$("#error_username").fadeIn();
$("#error_username").text("Please enter your email id");
$("#error_username").fadeOut(6000);
return false;
}
else
{
var sEmail = $('#username').val();
if (validateEmail(sEmail)) {
//alert('Nice!! your Email is valid, now you can continue..');
}
else {
$('#username').focus();
$("#error_username").fadeIn();
$("#error_username").text("Please enter a valid Email Id.");
$("#error_username").fadeOut(6000);
return false;
e.preventDefault();
}
}
}

if(email!="")
{
$("#loader").show();
var dataString = 'email='+ email+'&type='+ type;
//alert(dataString)
$.ajax({
type	: "POST",
url		: "ajax_validation.php",
data 	: dataString,
cache 	: false,
success: function(result){
//alert(result)
//var result=trim(result);
if(result==1)
{
$("#loader").hide();
//alert("We have sent the password reset link to your email id");
$("#passwordchanged").fadeIn();
$("#passwordchanged").text("Password reset link is sent to your email address.");
$("#passwordchanged").fadeOut(2000);
}
else if(result==0)
{
$("#loader").hide();
$("#error").fadeIn();
$("#error").html("Mail not sent.Please try again");
$("#error").fadeOut(3000);
}
else if(result==2)
{
$("#loader").hide();
$("#error").fadeIn();
$("#error").html("Email ID Not Registered under Planpiper.");
$("#error").fadeOut(3000);
}
}
});
}
else
{
//alert('Please Enter Email')
}
});
});
</script>
</body>
</html>