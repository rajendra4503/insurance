<?php
session_start();
//ini_set("display_errors","0");
include_once('include/configinc.php');
include ('SMTP/PHPMailerAutoload.php');
include ('SMTP/class.phpmailer.php');
include ('SMTP/class.smtp.php');
$remember_me = (empty($_COOKIE['remember_me'])) ? '' : $_COOKIE['remember_me'];
//GET COUNTRIES
$get_countries = mysql_query("select CountryCode, CountryName, CurrencyCode from COUNTRY_DETAILS");
$country_count = mysql_num_rows($get_countries);
$country_options = "";
if($country_count > 0){
	while ($countries = mysql_fetch_array($get_countries)) {
		$country_code = $countries['CountryCode'];
		$country_name = $countries['CountryName'];
		$currency_code = $countries['CurrencyCode'];
		$country_options .= "<option value='$country_code'>$country_name</option>";
	}
}
	if((isset($_REQUEST['mobilenumber'])) && (!empty($_REQUEST['mobilenumber']))){
	//echo "<pre>"; print_r($_REQUEST);exit;
	$countrycode 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['countrycode'])));
	$mobilenumber 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['mobilenumber'])));
	$email 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['email'])));
	$password1 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['password1'])));
	$firstname 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['firstname'])));
	$middlename 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['middlename'])));
	$lastname 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['lastname'])));
	$name 					= $firstname." ".$lastname;
	$gender 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['gender'])));
	$dddateofbirth 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['dddateofbirth'])));
	$mmdateofbirth 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['mmdateofbirth'])));	
	$yydateofbirth 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['yydateofbirth'])));
	if(($dddateofbirth != "0") && ($mmdateofbirth != "0") && ($yydateofbirth != "0")){
		$date = $dddateofbirth."-".$mmdateofbirth."-".$yydateofbirth;
		$dob = date('Y-m-d',strtotime($date));
	} else {
		$dob = "";
	}
	$country 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['country'])));
	$state 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['state'])));
	$city 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['city'])));
	$addressline1 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['addressline1'])));
	$addressline2 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['addressline2'])));
	$pincode 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['pincode'])));
	$countrycodelandline 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['countrycodelandline'])));
	$areacode 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['areacode'])));
	$areacode 				= ltrim($areacode, '0');
	$landline 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['landline'])));	
	//GENERATE 15 DIGIT RANDOM NUMBER
	$i = 0;
	$tmp = mt_rand(1,9);
	do {
	    $tmp .= mt_rand(0, 9);
	} while(++$i < 14);
	//echo $tmp;
	$userid = $country.$tmp;
	$mobilenumber = $country.$mobilenumber;
	$insert_user_access = "insert into USER_ACCESS (UserID, MobileNo, EmailID, Password, PasswordStatus, UserStatus, CreatedDate) values ('$userid','$mobilenumber','$email','$password1','0','I',now())";
	$insert_user_access_run = mysql_query($insert_user_access);
	$insert_user_details = "insert into USER_DETAILS (UserID, FirstName, MiddleName, LastName, Gender, DOB, CountryCode, StateID, CityID, AddressLine1,AddressLine2, PinCode, AreaCode, Landline, CreatedDate) values ('$userid', '$firstname', '$middlename', '$lastname' , '$gender', '$dob', '$country', '$state', '$city', '$addressline1', '$addressline2', '$pincode', '$areacode', '$landline', now())";
	$insert_user_details_run = mysql_query($insert_user_details);
			function mailresetlink($email,$country,$tmp,$name)
			{//echo $email;exit;
			
			//Create a new PHPMailer instance
			$mail = new PHPMailer();
			// Set PHPMailer to use the sendmail transport
			$mail->isSMTP();
			//Set who the message is to be sent from
			$mail->setFrom('noreply@appmantras.com', 'Admin');
			//Set who the message is to be sent to
			$mail->addAddress($email,$name);
			//Set the subject line
			$mail->Subject = 'Planpiper - Account Activation Link';
			
			$message = "
			<html>
			<head>
			<title> Account Activation Link</title>
			</head>
			<body>
			<p>Hi $name,</p>
			<p>Thank you for registering with Planpiper. Please click on the activation link given below to activate your planpiper account.</p>
			<p><a href='http://www.appmantras.com/planpiperv1/activate.php?c=$country&id=$tmp'>Click here to activate your account.</a></p>
			<p>While you cannot reply to this email, please feel free to write to us with any queries at admin@appmantras.com</p>	
			</body>
			</html>
			";	
					
			$mail->msgHTML($message, dirname(__FILE__));
			
			//Replace the plain text body with one created manually
			$mail->AltBody = 'This is a plain-text message body';
			//Attach an image file
			//$mail->addAttachment('images/phpmailer_mini.gif');
			
			//send the message, check for errors
				if(!$mail->send())
				{
					
				} else
				{
					
				}
			}
			//echo $email." ".$token." ".$name;exit;
			mailresetlink($email,$country,$tmp,$name);

	?>
	<script type="text/javascript">
	bootbox.alert("Thank you for registering with planpiper. An activation link has been sent to your registered email address. Click on it to activate your account. Thank you.");
	window.location.href = "login.php";
	</script>
	<?php
	}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>Plan Piper - Register</title>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/planpiper.css">
<link rel="stylesheet" type="text/css" href="fonts/font.css">
<link rel="shortcut icon" href="images/planpipe_logo.png"/>       
<script type="text/javascript">
	function keychk(event)
	{
	//bootbox.alert(123)
		if(event.keyCode==13)
		{
			$("#registerbutton").click();
		}
	}
</script>
</head>
<body style="overflow:hidden;">
	<div id="big_wrapper">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="left">
				<div style="margin-bottom:20px;"><img src="images/appmantras_logo.png" id="appmantras_logo"></div>
			</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="center">
					<div id="planpipe_reg"><img src="images/planpipe_logo.png" id="planpipe_logo_reg"><br>Plan Piper</div>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="left">
				</div>
			</div>
			<div align="center">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="center">
					<div style="margin-bottom:30px;">
						<span id="errormessage" style="color:#F65100;font-family:RalewayRegular;"></span>
					</div>
					<form method="post" id="register_form" name="register_form" action="register.php">
						<div class="col-lg-offset-2 col-lg-8 col-lg-offset-2 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" align="left">
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 paddingl0">
									 <input type="text" class="forminputs3" maxlength="3" placeholder="+91" name="countrycode" id="countrycode" value="+91">
								</div>
								<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 paddingr0">
									<input type="text" maxlength="10" placeholder="MOBILE NUMBER" name="mobilenumber" id="mobilenumber" class="forminputs3 onlynumbers"><span class="mandatoryfield">*</span>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<input type="email" placeholder="EMAIL ID" name="email" id="email" class="forminputs3" maxlength="50"><span class="mandatoryfield">*</span>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<input type="password" placeholder="PASSWORD" name="password1" id="password1" class="forminputs3" maxlength="20"><span class="mandatoryfield">*</span>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<input type="password" placeholder="CONFIRM PASSWORD" name="confirmpassword" id="confirmpassword" class="forminputs3" maxlength="20"><span class="mandatoryfield">*</span>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
								<input type="text" class="forminputs3" placeholder="FIRST NAME" name="firstname" id="firstname" maxlength="20"><span class="mandatoryfield">*</span>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
								 <input type="text" class="forminputs3" placeholder="MIDDLE NAME" name="middlename" id="middlename" maxlength="20">
							</div>
							<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
								<input type="text" class="forminputs3" placeholder="LAST NAME" name="lastname" id="lastname" maxlength="20">
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<select class="forminputs3" id="gender" name="gender">
						          <option style="display:none;" value="0">SELECT GENDER</option>
						          <option value="M">MALE</option>
						          <option value="F">FEMALE</option>
						        </select><span class="mandatoryfield">*</span>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 paddingl0">
									<select class="forminputs3" name="dddateofbirth" id="dddateofbirth">
										<option style="display:none;" value="0">DD</option>
										<?php 
											for ($i=1; $i <= 31 ; $i++) { 
												if($i < 10){
													$i = "0".$i;
												}
												echo "<option value='$i'>$i</option>";
											}
										?>
									</select>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 paddingrl0">
									<select class="forminputs3" name="mmdateofbirth" id="mmdateofbirth">
										<option style="display:none;" value="0">MMM</option>
										<?php 
										$months = array('Jan','Feb','Mar','Apr','May','Jun','Jul ','Aug','Sep','Oct','Nov','Dec');
										foreach ($months as $month) {
											echo "<option value='$month'>$month</option>";
										}
										?>
									</select>
								</div>
								<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 paddingr0">
									<select class="forminputs3" name="yydateofbirth" id="yydateofbirth">
										<option style="display:none;" value="0">YYYY</option>
										<?php 
											for ($y=2014; $y >= 1940 ; $y--) { 
												echo "<option value='$y'>$y</option>";
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<select class="selectpicker forminputs3" id="country" name="country">
						          <option style="display:none;" value="0">SELECT COUNTRY</option>
						          <?php echo $country_options;?>
						        </select><span class="mandatoryfield">*</span>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<select class="selectpicker forminputs3" id="state" name="state">
						          <option style="display:none;" value="0">SELECT STATE</option>
						        </select><span class="mandatoryfield">*</span>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<select class="selectpicker forminputs3" id="city" name="city">
						          <option style="display:none;" value="0">SELECT CITY</option>
						        </select><span class="mandatoryfield">*</span>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<input type="text" class="forminputs3" placeholder="ADDRESS LINE 1" name="addressline1" id="addressline1" maxlength="250">
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<input type="text" class="forminputs3" placeholder="ADDRESS LINE 2" name="addressline2" id="addressline2" maxlength="250">
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<input type="text" class="forminputs3 onlynumbers" maxlength="6" placeholder="PINCODE" name="pincode" id="pincode">
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 paddingl0">
									 <input type="text" class="forminputs3" maxlength="3" placeholder="+91" name="countrycodelandline" id="countrycodelandline" value="+91">
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingl0">
									 <input type="text" class="forminputs3 onlynumbers" maxlength="5" placeholder="AREA CODE" name="areacode" id="areacode">
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 paddingr0">
									 <input type="text" class="forminputs3 onlynumbers" maxlength="10" placeholder="LAND LINE" name="landline" id="landline">
								</div>
							</div>
						</div>
						    <div class="row">
						    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="center" style="margin-top:25px;">
						    	<div class="errormessages" id="registrationerror"></div>
						    </div>
						      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="addClientActionBar" align="center" style="margin-top:25px;">
						        <button id="registerbutton" class="formbuttonsmall">REGISTER</button>
						        <button type="reset" id="reset" class="formbuttonsmall">RESET</button>
						        <button id="cancelbutton" class="formbuttonsmall">CANCEL</button>
						      </div>
						    </div>
					</form>
				</div>
			</div>
		</div>
		<?php include("footer.php");?> 	
	</div><!-- big_wrapper ends -->

<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/ajax_city_state.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/modernizr.js"></script>
<script type="text/javascript" src="js/bootbox.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	var emailerrorflag = 0;
	var mobileerrorflag = 0;
	$("#email").keyup(function(){ 
        var mail = $("#email").val();
        if (validateEmail(mail)) {
        	var dataString = "type=check_duplicate_email&mailid="+mail;
        	$.ajax({
				type		: 'POST', 
				url			: 'ajax_validation.php', 
				crossDomain	: true,
				data		: dataString,
				dataType	: 'json', 
				async		: false,
				success	: function (response)
					{ 
						if(response.success == true){
							emailerrorflag = 1;
							$("#registrationerror").fadeIn();
				            $("#registrationerror").text("This email id is already registered with planpiper");
				            $("#registrationerror").fadeOut(5000);
							return false;
						} else {
							emailerrorflag = 0;
						}
					},
				error: function(error)
				{
					
				}
			}); 
        }
    });
    $("#mobilenumber").keyup(function(){ 
        var mobile = $("#mobilenumber").val();
        if (mobile.length > 7) {
        	var dataString = "type=check_duplicate_mobile&mobile=00091"+mobile;
        	//bootbox.alert(dataString);
        	$.ajax({
				type		: 'POST', 
				url			: 'ajax_validation.php', 
				crossDomain	: true,
				data		: dataString,
				dataType	: 'json', 
				async		: false,
				success	: function (response)
					{ 
						if(response.success == true){
							mobileerrorflag = 1;
							$("#registrationerror").fadeIn();
				            $("#registrationerror").text("This mobile number is already registered with planpiper");
				            $("#registrationerror").fadeOut(5000);
							return false;
						} else {
							mobileerrorflag = 0;
						}
					},
				error: function(error)
				{
					
				}
			}); 
        }
    });
	$("#registerbutton").click(function() {
		var mobilenumber = $('#mobilenumber').val();
		mobilenumber       = mobilenumber.replace(/ /g,''); //To check if the variable contains only spaces
        if(mobilenumber == ''){
            $('#mobilenumber').val('');
            $('#mobilenumber').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter your mobile number.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }

        if(isNaN(mobilenumber)){
            $('#mobilenumber').val('');
            $('#mobilenumber').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter a valid mobile number.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }

        if(Number(mobilenumber).toString().length < 7){
            //bootbox.alert("Please enter a valid mobile number");
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter a valid mobile number.");
            $("#registrationerror").fadeOut(5000);
            $("#mobilenumber").focus();
            return false;
        } else {
        	var country = $('#country').val();
        	var dataString = "type=check_duplicate_mobile&mobile="+country+mobilenumber;
        	//bootbox.alert(dataString);
        	$.ajax({
				type		: 'POST', 
				url			: 'ajax_validation.php', 
				crossDomain	: true,
				data		: dataString,
				dataType	: 'json', 
				async		: false,
				success	: function (response)
					{ 
						//bootbox.alert(response.success);
						if(response.success == true){
							mobileerrorflag = 1;
							$("#registrationerror").fadeIn();
				            $("#registrationerror").text("This mobile number is already registered with planpiper");
				            $("#registrationerror").fadeOut(5000);
						} else {
							mobileerrorflag = 0;
						}
					},
				error: function(error)
				{
					
				}
			});         	
        }
        if(mobileerrorflag == 1){
        		$('#mobilenumber').focus();
        		$("#registrationerror").fadeIn();
				$("#registrationerror").text("This mobile number is already registered with planpiper");
				$("#registrationerror").fadeOut(5000);
				return false;
        }
       var email = $('#email').val();
        email = email.replace(/ /g,''); //To check if the variable contains only spaces
        if(email == ''){
            //$('#email_id').val('');
            $('#email').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter your email id.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        else
        {
            var email = $('#email').val();
            if (validateEmail(email)) {
            //bootbox.alert('Nice!! your Email is valid, now you can continue..');
            }
            else {
            $('#email').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter a valid email id.");
            $("#registrationerror").fadeOut(5000);
            return false;
            e.preventDefault();
            }
            if(emailerrorflag == 1){
	            $('#email').focus();
	            $("#registrationerror").fadeIn();
	            $("#registrationerror").text("This email id is already registered with Planpiper. Please enter a new email id.");
	            $("#registrationerror").fadeOut(5000);
	            return false;
	            e.preventDefault();
            }
        }
        var password1 	= $("#password1").val();
		var password2 	= $("#confirmpassword").val();
        if(password1==""){
			$("#registrationerror").fadeIn();
			$("#registrationerror").text("Please enter a password");
			$("#password1").focus();
			$("#registrationerror").fadeOut(5000);
			return false;
			}

			if(password1.indexOf(" ") !== -1 ){
				$("#registrationerror").fadeIn();
				$("#registrationerror").text("Please enter a password without any whitespaces.");
				$("#password1").focus();
				$("#registrationerror").fadeOut(5000);
				return false;
			}
			if(password1!=""){
				//bootbox.alert(password1)
			var passw = /^(?=.*\d)(?=.*[a-zA-Z])(?=.*[a-zA-Z0-9!@#$%^&*]).{6,15}$/;
			    if(!password1.match(passw)) 
			    { 
			    $("#registrationerror").fadeIn();
				$("#registrationerror").text("Password should have 6-15 characters which contains atleast one numeric digit and one alphabet");
				$("#confirmpassword").focus();
				$("#registrationerror").fadeOut(5000);
			    return false;
			    }
			}
		if(password2==""){
			$("#registrationerror").fadeIn();
			$("#registrationerror").text("Please confirm your password");
			$("#confirmpassword").focus();
			$("#registrationerror").fadeOut(5000);
			return false;
			}
			if(password2.indexOf(" ") !== -1 ){
				$("#registrationerror").fadeIn();
				$("#registrationerror").text("Please enter a password without any whitespaces.");
				$("#registrationerror").fadeOut(5000);
				return false;
			}
			if(password1 != "" && password2 != ""){
				if(password1 != password2)
				{
				//bootbox.alert("Password Mismatch");
				$("#registrationerror").fadeIn();
				$("#registrationerror").text("Passwords don't match");
				$("#confirmpassword").focus();
				$("#registrationerror").fadeOut(5000);
				return false;
				}
			}
		var firstname = $('#firstname').val();
		firstname       = firstname.replace(/ /g,''); //To check if the variable contains only spaces
        if(firstname == ''){
            $('#firstname').val('');
            $('#firstname').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter your first name.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        var gender = $('#gender').val();
        if(gender == '0'){
            $('#gender').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please select your gender.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        var country = $('#country').val();
        if(country == '0'){
            $('#country').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please select your country.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
         var state = $('#state').val();
        if(state == '0'){
            $('#state').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please select your state.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        $('#register_form').submit();
	});
	$("#cancelbutton").click(function() {
		window.location.href="login.php";
		return false;
	});

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

	$('#country').on('change', function() {
		  var code = $(this).val();
		  code = code.replace(/^0+/, '');
		  code = "+"+code;
		  //bootbox.alert(code);
		  $('#countrycode').val(code);
		  $('#countrycodelandline').val(code);
		});

});
</script>
</body>
</html>