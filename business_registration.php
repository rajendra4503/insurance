<?php
session_start();
//ini_set("display_errors","0");
include_once('include/configinc.php');
date_default_timezone_set("Asia/Kolkata");
include ('SMTP/PHPMailerAutoload.php');
include ('SMTP/class.phpmailer.php');
include ('SMTP/class.smtp.php');
if(isset($_SESSION['registered_userid'])){
	$userid 		= $_SESSION['registered_userid'];
	$useremail 		= $_SESSION['registered_useremail'];
	$username 		= $_SESSION['registered_username'];
	$usercountry 	= $_SESSION['registered_usercountry'];
	$usertmp 		= $_SESSION['registered_usertmp'];
} else {
	header("Location:login.php");
}

//GET COUNTRIES
$get_countries = mysql_query("select CountryCode, CountryName, CurrencyCode from COUNTRY_DETAILS where Timezone!=''");
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
	//$countrycode 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['countrycode'])));
	$mobilenumber 			= ltrim(mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['mobilenumber']))),'0');
	$email 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['email'])));
	$companyname 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['companyname'])));
	$companyregno 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['companyregno'])));
	$companyurl 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['companyurl'])));
	$country 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['country'])));
	$state 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['state'])));
	$city 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['city'])));
	$addressline1 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['addressline1'])));
	$addressline2 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['addressline2'])));
	$pincode 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['pincode'])));
	$countrycodelandline 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['countrycodelandline'])));
	$areacode 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['areacode'])));
	$areacode 				= ltrim($areacode, '0');
	$landline 				= ltrim(mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['landline']))),'0');	
	//GENERATE 15 DIGIT RANDOM NUMBER
	$i = 0;
	$tmp = mt_rand(1,9);
	do {
	    $tmp .= mt_rand(0, 9);
	} while(++$i < 14);
	//echo $tmp;

	$merchantid = $country.$tmp;

	$insert_merchant_mapping = "insert into USER_MERCHANT_MAPPING (MerchantID, UserID, RoleID, Status,Type, CreatedDate,CreatedBy,UpdatedBy) values ('$merchantid','$userid','1','I','M',now(),'','')";
	
	$insert_merchant_mapping_run = mysql_query($insert_merchant_mapping);

	$insert_merchant_details = "insert into MERCHANT_DETAILS (MerchantID,CountryCode,StateID,CityID, CompanyCountryCode, CompanyStateID, CompanyCityID, CompanyName, CompanyRegistrationNo, CompanyEmailID, CompanyMobileNo, CompanyAreaCode1, CompanyLandline1, CompanyAddressLine1,CompanyAddressLine2, CompanyPinCode, CompanyWebsiteURL, CreatedDate,CreatedBy,UpdatedBy) values ('$merchantid','',0,0,'$country', '$state' , '$city', '$companyname', '$companyregno', '$email', '$mobilenumber', '$areacode', '$landline', '$addressline1','$addressline2','$pincode','$companyurl', now(),'','')";

	//echo $insert_merchant_details;exit;
	$insert_merchant_details_run = mysql_query($insert_merchant_details);

	$insert_modules1 = "insert into MAPPING_MERCHANT_WITH_MODULES (MerchantID,ModuleID,Status,CreatedDate,CreatedBy) select '$merchantid' as MerchantID ,ModuleID,'A',now(),'$userid' from PLANPIPER_MODULES where ModuleID>5 order by ModuleID";
	$insert_modules = mysql_query($insert_modules1);

			function mailresetlink($useremail,$usercountry,$usertmp,$username)
			{//echo $email;exit;
			
			//Create a new PHPMailer instance
			$mail = new PHPMailer();
			// Set PHPMailer to use the sendmail transport
			$mail->isSMTP();
			//Set who the message is to be sent from
			$mail->setFrom('support@planpiper.com', 'Admin');
			//Set who the message is to be sent to
			$mail->addAddress($useremail,$username);
			//Set the subject line
			$mail->Subject = 'Planpiper - Account Activation Link';
			
			$message = "
			<html>
			<head>
			<title> Account Activation Link</title>
			</head>
			<body>
			<p>Hi $username,</p>
			<p>Thank you for registering with Planpiper. Please click on the activation link given below to activate your planpiper account.</p>
			<p><a href='http://www.planpiper.com/activate.php?c=$usercountry&id=$usertmp'>Click here to activate your account.</a></p>
			<p>While you cannot reply to this email, please feel free to write to us with any queries at <i><u><a href='mailto:support@planpiper.com'>support@planpiper.com</a></u></i></p>	
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
			mailresetlink($useremail,$usercountry,$usertmp,$username);
			unset($_SESSION['registered_userid']);
			unset($_SESSION['registered_useremail']);
			unset($_SESSION['registered_username']);
			unset($_SESSION['registered_usercountry']);
			unset($_SESSION['registered_usertmp']);
			//header("Location:business_registration.php");

	?>
	<script type="text/javascript">
		alert("Thank you for registering with planpiper. An activation link has been sent to your registered email address. Click on it to activate your account. Thank you.");
		window.location.href = "login.php";
	</script>
	<?php
		//header("Location:login.php");
	}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>Plan Piper - Vendor Registration</title>
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
					<form method="post" id="register_form" name="register_form" action="business_registration.php">
						<div class="col-lg-offset-2 col-lg-8 col-lg-offset-2 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" align="left">
						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div id="pageheading">Company Details (Required)</div>
							<input type="text" class="forminputs3" placeholder="COMPANY NAME" name="companyname" id="companyname" maxlength="50" required>
							<input type="email" placeholder="OFFICIAL EMAIL ID" name="email" id="email" class="forminputs3" maxlength="50" required>
							<input type="text" maxlength="10" placeholder="MOBILE NUMBER" name="mobilenumber" id="mobilenumber" class="forminputs3 onlynumbers" required>
							<select class="selectpicker forminputs3" id="country" name="country" required>
						          <option style="display:none;" value="0">SELECT COUNTRY</option>
						          <?php echo $country_options;?>
						        </select>
						    <select class="selectpicker forminputs3" id="state" name="state" required >
						          <option style="display:none;" value="0">SELECT STATE</option>
						        </select>
						        <select class="selectpicker forminputs3" id="city" name="city" required>
						          <option style="display:none;" value="0">SELECT CITY</option>
						        </select>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<div id="pageheading">Additional Info (Optional)</div>
							<input type="text" class="forminputs3" placeholder="COMPANY REGISTRATION NO." name="companyregno" id="companyregno" maxlength="20">
							<input type="text" class="forminputs3" placeholder="COMPANY WEBSITE URL" name="companyurl" id="companyurl" maxlength="100">
						        <input type="text" class="forminputs3" placeholder="OFFICIAL ADDRESS LINE 1" name="addressline1" id="addressline1" maxlength="250">
								<input type="text" class="forminputs3" placeholder="OFFICIAL ADDRESS LINE 2" name="addressline2" id="addressline2" maxlength="250">
								<input type="text" class="forminputs3 onlynumbers" maxlength="6" placeholder="PINCODE" name="pincode" id="pincode">
								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 paddingl0">
									 <input type="text" class="forminputs3" maxlength="3" placeholder="+91" name="countrycodelandline" id="countrycodelandline" value="+91">
								</div>
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingl0">
									 <input type="text" class="forminputs3 onlynumbers" maxlength="5" placeholder="AREA CODE" name="areacode" id="areacode">
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 paddingr0">
									 <input type="text" class="forminputs3 onlynumbers" maxlength="8" placeholder="LANDLINE" name="landline" id="landline">
								</div>
						</div>
						</div>
						    <div class="row">
						    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="center" style="margin-top:25px;">
						    	<div class="errormessages" id="registrationerror"></div>
						    </div>
						      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="ActionBar2" align="center" style="margin-top:25px;">
						        <button id="registerbutton" class="formbuttonsmall">REGISTER</button>
						        <!--<button type="reset" id="reset" class="formbuttonsmall">RESET</button>-->
						        <button id="cancelbutton" class="formbuttonsmall">CANCEL</button>
						      </div>
						    </div>
					</form>
				</div>
			</div>
		</div>
	</div><!-- big_wrapper ends -->

<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/ajax_city_state.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/modernizr.js"></script>
<script type="text/javascript" src="js/placeholders.min.js"></script>
<script type="text/javascript" src="js/bootbox.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#registerbutton").click(function() {
		var companyname = $('#companyname').val();
		companyname       = companyname.replace(/ /g,''); //To check if the variable contains only spaces
        if(companyname == ''){
            $('#companyname').val('');
            $('#companyname').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter your company name.");
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
        }
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
        if(mobilenumber<999999){
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
        var city = $('#city').val();
        if(city == '0'){
            $('#city').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please select your city.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        var companyurl = $('#companyurl').val();
        if(companyurl != ""){

        }
        var pincode = $('#pincode').val();
		pincode       = pincode.replace(/ /g,''); //To check if the variable contains only spaces
        if(pincode != '' && pincode < 99999){
            $('#pincode').val('');
            $('#pincode').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter valid pincode.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        var areacode = $('#areacode').val();
		areacode       = areacode.replace(/ /g,''); //To check if the variable contains only spaces
        if(areacode != '' && (areacode ==0 || areacode ==00 || areacode ==000 || areacode ==0000 || areacode ==00000)){
            $('#areacode').val('');
            $('#areacode').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter valid areacode.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }

        var landline 	= $('#landline').val();
		landline       	= landline.replace(/ /g,''); //To check if the variable contains only spaces
        if(landline != '' && landline < 99999){
            $('#landline').val('');
            $('#landline').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter valid landline.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        if(landline!="" && areacode=="")
        {
        	$('#areacode').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter areacode.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        $('#register_form').submit();
	});
	$("#cancelbutton").click(function() {
		var userid 		= "<?php echo $userid;?>";
		var cancelreg = confirm("Your registration will be cancelled. Click OK to continue.");
		if(cancelreg == true){
		window.location.href="ajax_validation.php?type=incomplete_registration&userid="+userid;
		return false;
		} else {
			return false;
		}

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
		  //$('#countrycode').val(code);
		  $('#countrycodelandline').val(code);
		});

});
</script>
</body>
</html>