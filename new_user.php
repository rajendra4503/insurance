<?php
session_start();
ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php');
include ('SMTP/PHPMailerAutoload.php');
include ('SMTP/class.phpmailer.php');
include ('SMTP/class.smtp.php');
//GET ROLES
/*$role_options = "";
$get_roles = mysql_query("select RoleID, RoleName from ROLE_MASTER where RoleID = '1' order by RoleName");
$get_roles_count = mysql_num_rows($get_roles);
if($get_roles_count > 0) {
	while ($roles = mysql_fetch_array($get_roles)) {
		$role_id 		= $roles['RoleID'];
		$role_name 		= $roles['RoleName'];
		$role_options  .= "<option value='$role_id'>$role_name</option>";
	}
}*/
//GET COUNTRIES
$get_countries = mysql_query("select CountryCode, CountryName, CurrencyCode,Timezone from COUNTRY_DETAILS where Timezone!=''");
$country_count = mysql_num_rows($get_countries);
$country_options = "";
if($country_count > 0){
	while ($countries = mysql_fetch_array($get_countries)) {
		$country_code = $countries['Timezone'];
		$country_name = $countries['CountryName'];
		$currency_code = $countries['CurrencyCode'];
		$country_options .= "<option value='$country_code'>$country_name</option>";
	}
}

//GET BLOOD GROUPS
$get_bloodgroup 	= mysql_query("select ID, BLOOD_GROUP from BLOOD_GROUPS");
$bloodgroup_count 	= mysql_num_rows($get_bloodgroup);
$bloodgroup_options = "";
if($bloodgroup_count > 0){
	while ($bloodgroups = mysql_fetch_array($get_bloodgroup)) {
		$bloodgroup_id 		= $bloodgroups['ID'];
		$bloodgroup_name 	= $bloodgroups['BLOOD_GROUP'];
		$bloodgroup_options .= "<option value='$bloodgroup_name'>$bloodgroup_name</option>";
	}
}
			if((isset($_REQUEST['mobilenumber'])) && (!empty($_REQUEST['mobilenumber']))){
	//echo "<pre>"; print_r($_REQUEST);exit;
			//$usertype 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['usertype'])));
			$usertype = "5";
			$mobilenumber 			= ltrim(mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['mobilenumber']))),'0');
			$email 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['email'])));
			$password1 				= "planpiper";
			$firstname 				= mysql_real_escape_string(trim(htmlspecialchars(ucfirst($_REQUEST['firstname']))));
			$lastname 				= mysql_real_escape_string(trim(htmlspecialchars(ucfirst($_REQUEST['lastname']))));
			$name 					= $firstname." ".$lastname;
			$mobile_type 			= (empty($_REQUEST['mobile_type']))          ? '' : $_REQUEST['mobile_type'];
			//echo $mobile_type;exit;
			$language 				= $_REQUEST['language'];
			$language 				= ($language == 0 ? '1' : $language);
			$supportpersonname      = (empty($_REQUEST['supportpersonname']))? '' : mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['supportpersonname'])));
			$supportpersonmobilenumber= (empty($_REQUEST['supportpersonmobilenumber']))? '' : mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['supportpersonmobilenumber'])));
			$country 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['country'])));
			$state 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['state'])));
			$city 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['city'])));
			if($city == "-1"){
				$city = "0";
			}

			/*Create email id(ajax_validation1.php)*/

				/*GET TWO DIGIT COUNTRY CODE FROM COUNTRY_DETAILS TABLE BASED ON FIVE DIGIT COUNTRY CODE*/
				$get_countrycode_query	= mysql_query("select substr(CountryID,1,2) from COUNTRY_DETAILS where CountryCode='$country' and Timezone!=''");
				$country_code 			= mysql_result($get_countrycode_query, 0);
				//echo $country_code."<br>";
				/*END OF GET TWO DIGIT COUNTRY CODE FROM COUNTRY_DETAILS TABLE BASED ON FIVE DIGIT COUNTRY CODE*/
				$planpiper_email	    = $country_code.$mobilenumber."@planpiper.com";
			$_SESSION['country_code'] 	= $country_code;
			$_SESSION['mobile_number'] 	= $mobilenumber;
			$_SESSION['name'] 			= $name;
			$_SESSION['redirection'] 	= 'U';/*U-New PLan User,V- Vendor*/
			/*End of Create email id(ajax_validation1.php)*/
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
			$addressline1 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['addressline1'])));
			$addressline2 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['addressline2'])));
			$pincode 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['pincode'])));
			$blood_group 			= $_REQUEST['blood_group'];
			$countrycodelandline 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['countrycodelandline'])));
			$areacode 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['areacode'])));
			$areacode 				= ltrim($areacode, '0');
			$landline 				= ltrim(mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['landline']))),'0');
			$i = 0;
			$tmp = mt_rand(1,9);
			do {
			    $tmp .= mt_rand(0, 9);
			} while(++$i < 14);
			//echo $tmp;
			$userid = $country.$tmp;
			$mobilenumber = $country.$mobilenumber;



			$insert_user_access = "insert into USER_ACCESS (UserID, MobileNo, EmailID, PlanpiperEmailID, Password, PasswordStatus, UserStatus, CreatedDate,CreatedBy,UpdatedBy) values ('$userid','$mobilenumber','$email','$planpiper_email','$password1','0','A',now(),'','')";
			

			$insert_user_access_run = mysql_query($insert_user_access);

			$insert_user_details = "insert into USER_DETAILS (UserID, FirstName, LastName, Gender, DOB, BloodGroup, CountryCode, StateID, CityID, AddressLine1,AddressLine2, PinCode, AreaCode, Landline,MobilePhoneType,LanguageID,SupportPersonName,SupportPersonMobileNo,AdStartDate,AdEndDate, CreatedDate,CreatedBy,UpdatedBy) values ('$userid', '$firstname', '$lastname' , '$gender' , '$dob' ,'$blood_group', '$country', '$state', '$city', '$addressline1', '$addressline2', '$pincode', '$areacode', '$landline','$mobile_type','$language','$supportpersonname','$supportpersonmobilenumber','0000-00-00','0000-00-00',now(),'','')";

			$insert_user_details_run = mysql_query($insert_user_details);
			
			$insert_merchant_mapping = "insert into USER_MERCHANT_MAPPING (MerchantID, UserID, RoleID, Status, CreatedDate,CreatedBy,UpdatedBy) values ('$logged_merchantid','$userid','$usertype','A',now(),'','')";

			$insert_merchant_mapping_run = mysql_query($insert_merchant_mapping);

			if($email != "")
			//	if((isset($_REQUEST['sendapk'])) && ($_REQUEST['sendapk'] == "Y") && ($email != ""))
			{
			function mailresetlink($useremail,$username)
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
			$mail->Subject = 'Planpiper - Welcome Email';
			
			$message = "
			<html>
			<head>
			<title> Welcome Email</title>
			</head>
			<body>
			<p>Hi $username,</p>
			<p>Thank you for registering with Plan Piper. Your account has been created.</p>
            <p>You can login to the app with the following details-</p>
            <p><b>Login id - $useremail</b></p>
            <p><b>Password - planpiper</b></p>
            <p>This is a one time use password that you will be asked to change when you login for the first time. Please create a suitable password with 6-15 characters which contains atleast one numeric digit and one alphabet that you can remember.</p>
            <p>If you wish to use this software for your patients, please email us at <i><u><a href='mailto:support@planpiper.com'>support@planpiper.com</a></u></i> and we will share the commercials with you and set you up for that.</p>
            <br>
            <p>Best Regards,</p>
            <p>Planpiper Team</p>
			</body>
			</html>
			";	
					
			$mail->msgHTML($message, dirname(__FILE__));
			
			//Replace the plain text body with one created manually
			$mail->AltBody = 'This is a plain-text message body';
			//Attach an image file
			//$mail->addAttachment('apk/PlanPiper.apk');
			
			//send the message, check for errors
				if(!$mail->send())
				{
					
				} else
				{
					
				}
			}
			//echo $email." ".$token." ".$name;exit;
			mailresetlink($email,$name);
			}

echo "<script type='text/javascript'>
alert('Patient Successfully Created');
window.location.href='ajax_validation1.php';
//window.location.href = 'plan_users.php';
</script>";

//header("Location:ajax_validation1.php");
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper - Create Patient</title>
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
				$("#createuserbutton").click();
			}
		}
	</script>
	</head>
	<body style="overflow:hidden;">
	<div id="planpiper_wrapper">
	  	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="height:100%;">
	  	 <div class="col-sm-2 paddingrl0"  id="sidebargrid">
		  	<?php include("sidebar.php");?>
		 </div>
		 <div class="col-sm-10 paddingrl0" id="content_wrapper">
		 	<?php include_once('top_header.php');?>
		 	<section>
			<?php
			if($logged_usertype=='I')
			{
			$title 					= "Add a Family Member";
			$support_person_name 	= $logged_firstname." ".$logged_lastname;
			$support_person_mobile 	= $logged_mobile;
			}
			else
			{
			$title 					= "Create New Patient";
			$support_person_name	= "";
			$support_person_mobile 	= "";
			}
			?>
		 		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0"><div id="plantitle"><?php echo $title;?></div></div>
		 		<div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" id="mainplanlistdiv">
		 		<form name="register_form" id="register_form" method="post" enctype="multipart/form-data" action="new_user.php">
		 		<!--<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="registerherediv" align="right" style="height:50px;margin-top:20px;">
			 			Select User Type :  
			 	</div>
			 	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" align="left" style="height:50px;">
			 			<select class="selectpicker forminputs3" id="usertype" name="usertype" style="height:auto;margin-top:20px;max-width:200px;">
			 				<option value="0" style="display:none;">SELECT</option>
			 				<?php //echo $role_options;?>
			 			</select>
			 	</div>-->
			 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="margin-top:20px;">
			 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div id="pageheading">Patient Details</div>
			 	</div>
			 	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						
							<span class="asterisk"></span>
							<select class="selectpicker forminputs3" id="timezone" name="timezone" required>
						          <option style="display:none;" value="0">SELECT COUNTRY</option>
						          <?php echo $country_options;?>    
						    </select>							
							<span class="asterisk"></span><input type="text" class="forminputs3 nonumbers firstlettercaps" placeholder="FIRST NAME" name="firstname" id="firstname" maxlength="20" required>
							<span class="asterisk"></span><input type="text" class="forminputs3 nonumbers firstlettercaps" placeholder="LAST NAME" name="lastname" id="lastname" maxlength="20" required>
							<span class="asterisk"></span><input type="email" placeholder="EMAIL ID" name="email" id="email" class="forminputs3" maxlength="50" required autocomplete='off'>
							<span class="asterisk"></span><input type="text" maxlength="10" placeholder="MOBILE NUMBER" name="mobilenumber" id="mobilenumber" class="forminputs3 onlynumbers" required  autocomplete='off'>
							<span class="asterisk"></span>
							<select class="selectpicker forminputs3" id="mobile_type" name="mobile_type" required>
					          	<option value="0">SELECT MOBILE TYPE</option>
					          	<option value="A">ANDROID</option>
					           	<option value="I">IOS</option>
					           	<option value="O">OTHER</option>
						    </select>
							<select class="selectpicker forminputs3" id="language" name="language">
					          	<option style="display:none;" value="0">SELECT LANGUAGE</option>
					          	<?php

					          	$get_languages 		= mysql_query("select ID, Name from MASTER_LANGUAGES order by Name");
								$language_count 	= mysql_num_rows($get_languages);
								$language_options 	= "";
								$det_language 		= 1;
								if($language_count > 0){
									while ($languages = mysql_fetch_array($get_languages)) {
										$language_id 		= $languages['ID'];
										$language_name 		= $languages['Name'];
										if($det_language == $language_id){
											$language_options 	.= "<option value='$language_id' selected>$language_name</option>";
										} else {
											$language_options 	.= "<option value='$language_id'>$language_name</option>";			
										}

									}
								}

					          	?>
					         	<?php echo $language_options;?>
						    </select>
							<input type="text" class="forminputs3 nonumbers" placeholder="SUPPORT PERSON NAME" name="supportpersonname" id="supportpersonname" maxlength="40" value="<?php echo $support_person_name;?>">
							<input type="text" maxlength="10" placeholder="SUPPORT PERSON MOBILE NUMBER" name="supportpersonmobilenumber" id="supportpersonmobilenumber" class="forminputs3 onlynumbers" value="<?php echo substr($support_person_mobile,5);?>">
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						
						<!--<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 paddingl0">
						<input type="email" placeholder="EMAIL ID" name="email" id="email" class="forminputs3" maxlength="50">
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 paddingl0">
							<div class="sendapk">Send apk? <input type="checkbox" name="sendapk" id="sendapk" value="Y"></div>
						</div>-->
												  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingl0">
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
						  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingl0">
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
						  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingl0">
						  <select class="forminputs3" name="yydateofbirth" id="yydateofbirth">
										<option style="display:none;" value="0">YYYY</option>
										<?php 
											for ($y=2015; $y >= 1940 ; $y--) { 
												echo "<option value='$y'>$y</option>";
											}
										?>
									</select>
						  </div>
						  <select class="forminputs3" id="gender" name="gender">
						          <option style="display:none;" value="0">SELECT GENDER</option>
						          <option value="M">MALE</option>
						          <option value="F">FEMALE</option>
						        </select>
						<select class="selectpicker forminputs3" id="state" name="state">
						          <option style="display:none;" value="0">SELECT STATE</option>
						</select>
						 <select class="selectpicker forminputs3" id="city" name="city">
						          <option style="display:none;" value="0">SELECT CITY</option>
						 </select>
						 <input type="text" class="forminputs3" placeholder="ADDRESS LINE 1" name="addressline1" id="addressline1" maxlength="250">
						 <input type="text" class="forminputs3" placeholder="ADDRESS LINE 2" name="addressline2" id="addressline2" maxlength="250">
						 <input type="text" class="forminputs3 onlynumbers" maxlength="6" placeholder="PINCODE" name="pincode" id="pincode">
						 <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 paddingl0">
						 <input type="text" class="forminputs3" maxlength="3" placeholder="+91" name="countrycodelandline" id="countrycodelandline" value="">
						 </div>
						 <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingl0">
							<input type="text" class="forminputs3 onlynumbers" maxlength="5" placeholder="AREA CODE" name="areacode" id="areacode">
						 </div>
						 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 paddingr0">
							<input type="text" class="forminputs3 onlynumbers" maxlength="8" placeholder="LANDLINE" name="landline" id="landline">
						 </div>
						  <select class="selectpicker forminputs3" id="blood_group" name="blood_group">
						          <option style="display:none;" value="0">SELECT BLOOD GROUP</option>
						          <?php echo $bloodgroup_options; ?>
						 </select>
						 <input type="hidden" name="country" id="country" value="">
				</div>
				</div>
		 		
		 		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="center" style="margin-top:25px;">
						    	<div class="errormessages" id="registrationerror"></div>
						    </div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="ActionBar2" align="center" style="margin-top:25px;">
						        <button id="registerbutton" class="formbuttonsmall">SAVE</button>
						        <button type="reset" id="reset" class="formbuttonsmall">RESET</button>
						        </div>
				</form>		      
				</div>
		 	</section>
		 </div>
		</div>		
	</div><!-- big_wrapper ends -->
      
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/modernizr.js"></script>
	<script type="text/javascript" src="js/placeholders.min.js"></script>
	<script type="text/javascript" src='js/get_user_timezone.js'></script>
	<script type="text/javascript" src="js/bootbox.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
			var w = window.innerWidth;
    		var h = window.innerHeight;
		    var total = h - 200;
		    var each = total/12;
		    $('.navbar_li').height(each);
		    $('.navbar_href').height(each/2);
		    $('.navbar_href').css('padding-top', each/2.8);

		    var windowheight = h;
	        var available_height = h - 120;
	        $('#mainplanlistdiv').height(available_height);

	        var user_type  = "<?php echo $logged_usertype;?>";
	        var merchantid = "<?php echo $logged_merchantid;?>";

	        if(user_type=='I')
	        {
	        	/*Check Count of family member*/
		        $.ajax({
					type		: 'POST', 
					url			: 'ajax_validation.php', 
					crossDomain	: true,
					data 		:{merchantid:merchantid,type:"check_count"},
					dataType	: 'json', 
					async		: false,
					success	: function (response)
						{ 
							if(response.success == true){
								$("#register_form :input").prop("disabled", true);
								$("#registrationerror").fadeIn();
					            $("#registrationerror").text("You can only add upto 4 family members.");
					            $("#registrationerror").fadeOut(10000);
								return false;
							} else {
							}
						},
					error: function(error)
					{
						
					}
				});
	        }

			setTimeout(function(){ $("#firstname").click(); }, 1000);
	        $("#firstname,#timezone").on("click",function(){
			//alert(123)
			var timezone=$("#timezone").val();
			//alert(timezone)
			$.ajax({
				url:'ajax_validation.php',
				data:{timezone:timezone,type:"get_country_code"},
				type: 'post',
				success : function(resp){
				//bootbox.alert(resp)
				var code = resp;
				var ccode= resp;
				//alert(ccode)
				ccode = ccode.replace(/^0+/, '');
				ccode = "+"+ccode;
				//alert(ccode)
				 $("#countrycodelandline").val(ccode);
				
				document.getElementById('country').value = code; 

				//alert(document.getElementById('country').value);
				//alert(code)
					if(code!="")
					{
						var co = $.trim(code);
						var dataString = 'country='+co+'&type=get_states';	   
						//alert(dataString)
						$.ajax({
							url:'ajax_country_state_city.php',
							data:dataString,
							type: 'post',
							async: true,
							success : function(response,status){
								$("#state").html(response);
								$("#city").html("<option value='-1'>SELECT CITY</option>");              
							},
							error : function(resp){
								//alert(resp)
							}
						});
						$("#state").on("change",function(){
						//alert(123)
						var state=$("#state").val();
						//alert(state)
							$.ajax({
								url:'ajax_country_state_city.php',
								data:{state:state,type:"get_cities"},
								type: 'post',
								success : function(resp){
								//alert(resp)
									$("#city").html(resp);               
								},
								error : function(resp){}
							});
						});
					}
					
					//alert(123)         
				},
				error : function(resp){}
				});
			});


			 var currentpage = "planusers";
    		$('#'+currentpage).addClass('active');
    		$('#plapiper_pagename').html("New Patient");
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
    	/*var usertype = $('#usertype').val();
    	if(usertype == '0'){
            $('#usertype').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please select the user type.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }*/
         var country = $('#country').val();
         //bootbox.alert(country);
        if(country == '0'){
            $('#country').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please select the country.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
		
        var firstname = $('#firstname').val();
		firstname       = firstname.replace(/ /g,''); //To check if the variable contains only spaces
        if(firstname == ''){
            $('#firstname').val('');
            $('#firstname').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter first name.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        var lastname = $('#lastname').val();
		lastname       = lastname.replace(/ /g,''); //To check if the variable contains only spaces
        if(lastname == ''){
            $('#lastname').val('');
            $('#lastname').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter last name.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }

        var email = $('#email').val();
        email = email.replace(/ /g,''); //To check if the variable contains only spaces
        if(email == ''){
        	//$('#email').val('');
            $('#email').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter Email ID.");
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

        var mobilenumber = $('#mobilenumber').val();
		mobilenumber       = mobilenumber.replace(/ /g,''); //To check if the variable contains only spaces
        if(mobilenumber == ''){
            $('#mobilenumber').val('');
            $('#mobilenumber').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter mobile number.");
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

        var mobile_type = $('#mobile_type').val();
		mobile_type       = mobile_type.replace(/ /g,''); //To check if the variable contains only spaces
        if(mobile_type ==0){
            $('#mobile_type').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please select Mobile Type.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        var supportpersonname = $('#supportpersonname').val();
		supportpersonname       = supportpersonname.replace(/ /g,''); //To check if the variable contains only spaces
        //alert(supportpersonname);
        var supportpersonmobilenumber 	= $('#supportpersonmobilenumber').val();
		supportpersonmobilenumber      	= supportpersonmobilenumber.replace(/ /g,''); //To check if the variable contains only spaces
        //alert(supportpersonname);
        if(supportpersonname == ''){
        	//Do Nothing
        	//alert(1);
        } else {
        if(supportpersonmobilenumber==""){
        		$('#supportpersonmobilenumber').val('');
	            $('#supportpersonmobilenumber').focus();
	            $("#registrationerror").fadeIn();
	            $("#registrationerror").text("Please enter a mobile number for support Person.");
	            $("#registrationerror").fadeOut(5000);
	            return false;
			}
        }
        
		if(supportpersonmobilenumber!="")
		{
			if(isNaN(supportpersonmobilenumber)){
            $('#supportpersonmobilenumber').val('');
            $('#supportpersonmobilenumber').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter a valid mobile number for Support Person.");
            $("#registrationerror").fadeOut(5000);
            return false;
	        }

	        if(Number(supportpersonmobilenumber).toString().length < 7){
	            //bootbox.alert("Please enter a valid mobile number");
	            $("#registrationerror").fadeIn();
	            $("#registrationerror").text("Please enter a valid mobile number for Support Person.");
	            $("#registrationerror").fadeOut(5000);
	            $("#supportpersonmobilenumber").focus();
	            return false;
	        } 
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

				/*$('#country').on('change', function() {
					  var code = $(this).val();
					  code = code.replace(/^0+/, '');
					  code = "+"+code;
					  //bootbox.alert(code);
					  //$('#countrycode').val(code);
					  $('#countrycodelandline').val(code);
					});*/
				
				

        var sidebarflag = 1;
        $('#topbar-leftmenu').click(function(){

          if(sidebarflag == 1){
              //$('#sidebargrid').hide(150);
              $('#sidebargrid').hide("slow","swing");
              //$('#content_wrapper').addClass("col-sm-12");
              $('#content_wrapper').removeClass("col-sm-10");
              sidebarflag = 0;
          } else {
              $('#sidebargrid').show("slow","swing");
              $('#content_wrapper').addClass("col-sm-10");
              //$('#content_wrapper').removeClass("col-sm-12");
              sidebarflag = 1;
          }
          
        });
        var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
    		});
	</script>
	</body>
</html>