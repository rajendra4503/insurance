<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php');
include ('SMTP/PHPMailerAutoload.php');
include ('SMTP/class.phpmailer.php');
include ('SMTP/class.smtp.php');
$staff_to_be_edited = $_SESSION['staff_to_be_edited'];
if(isset($staff_to_be_edited)){
	$get_profile_details1 = "select t1.FirstName, t1.LastName, t1.Gender, t1.DOB, t1.CountryCode, t1.StateID, t1.CityID,  
										t2.MobileNo, t2.EmailID, t1.AddressLine1, t1.AddressLine2, t1.PinCode, t1.AreaCode, t1.Landline,
										t3.RoleID
										from USER_DETAILS as t1, USER_ACCESS as t2, USER_MERCHANT_MAPPING as t3 where t1.UserID = t2.UserID 
										and t1.UserID = t3.UserID and t1.UserID = '$staff_to_be_edited' and t3.MerchantID='$logged_merchantid'";
	
	//echo $get_profile_details1;exit;
	$get_profile_details = mysql_query($get_profile_details1);
	$get_profile_count = mysql_num_rows($get_profile_details);
	if($get_profile_count > 0){
		while ($details = mysql_fetch_array($get_profile_details)) {
			$det_firstname 		= stripslashes($details['FirstName']);
			$det_lastname 		= stripslashes($details['LastName']);
			$det_gender 		= $details['Gender'];
			if(($details['DOB'] != "")&&($details['DOB'] != "0000-00-00")&&($details['DOB'] != NULL)){
				$det_dobday			= date('d',strtotime($details['DOB']));
				$det_dobmon			= date('M',strtotime($details['DOB']));
				$det_dobyear		= date('Y',strtotime($details['DOB']));
			} else {
				$det_dobday			= "";
				$det_dobmon			= "";
				$det_dobyear		= "";
			}
			$det_countrycode 	= $details['CountryCode'];
			$det_countrycall    = "+".ltrim($det_countrycode, '0');
			$det_stateid 		= $details['StateID'];
			$det_cityid 		= $details['CityID'];
			$det_mobileno 		= substr($details['MobileNo'], 5);
			$det_emailid 		= $details['EmailID'];
			$det_addressline1 	= stripslashes($details['AddressLine1']);
			$det_addressline2 	= stripslashes($details['AddressLine2']);
			$det_pincode 		= $details['PinCode'];
			if($det_pincode == "0"){
				$det_pincode = "";
			}
			$det_areacode 		= $details['AreaCode'];
			if($det_areacode == "0"){
				$det_areacode = "";
			}
			$det_landline 		= $details['Landline'];
			$det_roleid 		= $details['RoleID'];
		}
	} else {
		header("Location:plan_users.php");
	}
} else {
	header("Location:plan_users.php");
}
//GET COUNTRIES
$get_countries = mysql_query("select CountryCode, CountryName, CurrencyCode, Timezone from COUNTRY_DETAILS where Timezone!=''");
$country_count = mysql_num_rows($get_countries);
$country_options = "";
if($country_count > 0){
	while ($countries = mysql_fetch_array($get_countries)) {
		$country_code 		= $countries['CountryCode'];
		$country_name 		= $countries['CountryName'];
		$currency_code 		= $countries['CurrencyCode'];
		if($det_countrycode == $country_code){
			$country_options 	.= "<option value='$country_code' selected>$country_name</option>";
		} else {
			$country_options 	.= "<option value='$country_code'>$country_name</option>";			
		}

	}
}
//GET STATES
$states       = "";
$get_states          = "select StateID,StateName from STATE_DETAILS where CountryCode = '$det_countrycode' order by StateName";
$get_states_query    = mysql_query($get_states);
$get_state_count      = mysql_num_rows($get_states_query);
if($get_state_count > 0)
{
    while($rowstate=mysql_fetch_array($get_states_query))
    {
        $state_code1    = $rowstate['StateID'];
        $state_name     = $rowstate['StateName'];
        if($det_stateid == $state_code1){
          $states      .= "<option value='$state_code1' selected>$state_name</option>";
        } else {
          $states      .= "<option value='$state_code1'>$state_name</option>";
        }
        
    }
}

//GET CITIES
$cities       = "";
$get_cities          = "select CityID,CityName from CITY_DETAILS where StateID = '$det_stateid' order by CityName";
// echo $get_cities;exit;
$get_cities_query    = mysql_query($get_cities);
$get_city_count      = mysql_num_rows($get_cities_query);
if($get_city_count > 0)
{
    while($rowcity=mysql_fetch_array($get_cities_query))
    {
        $city_code1    = $rowcity['CityID'];
        $city_name     = $rowcity['CityName'];
        if($det_cityid == $city_code1){
          $cities      .= "<option value='$city_code1' selected>$city_name</option>";
        } else {
          $cities      .= "<option value='$city_code1'>$city_name</option>";
        }
        
    }
}
//GET ROLES
$get_roles = mysql_query("select RoleID, RoleName from ROLE_MASTER where RoleID IN (2,3,4)");
$roles_count = mysql_num_rows($get_roles);
$roles_options = "";
if($roles_count > 0){
	while ($roles = mysql_fetch_array($get_roles)) {
		$role_id 	 	= $roles['RoleID'];
		$role_name 		= $roles['RoleName'];
		if($det_roleid == $role_id){
			$roles_options 	.= "<option value='$role_id' selected>$role_name</option>";
		} else {
			$roles_options 	.= "<option value='$role_id'>$role_name</option>";			
		}

	}
}

//GET STATES
$states       = "";
$get_states          = "select StateID,StateName from STATE_DETAILS where CountryCode = '$det_countrycode' order by StateName";
$get_states_query    = mysql_query($get_states);
$get_state_count      = mysql_num_rows($get_states_query);
if($get_state_count > 0)
{
    while($rowstate=mysql_fetch_array($get_states_query))
    {
        $state_code1    = $rowstate['StateID'];
        $state_name     = $rowstate['StateName'];
        if($det_stateid == $state_code1){
          $states      .= "<option value='$state_code1' selected>$state_name</option>";
        } else {
          $states      .= "<option value='$state_code1'>$state_name</option>";
        }
        
    }
}

//GET CITIES
$cities       = "";
$get_cities          = "select CityID,CityName from CITY_DETAILS where StateID = '$det_stateid' order by CityName";
// echo $get_cities;exit;
$get_cities_query    = mysql_query($get_cities);
$get_city_count      = mysql_num_rows($get_cities_query);
if($get_city_count > 0)
{
    while($rowcity=mysql_fetch_array($get_cities_query))
    {
        $city_code1    = $rowcity['CityID'];
        $city_name     = $rowcity['CityName'];
        if($det_cityid == $city_code1){
          $cities      .= "<option value='$city_code1' selected>$city_name</option>";
        } else {
          $cities      .= "<option value='$city_code1'>$city_name</option>";
        }
        
    }
}
if((isset($_REQUEST['mobilenumber'])) && (!empty($_REQUEST['mobilenumber']))){
	//echo "<pre>"; print_r($_REQUEST);exit;
			//$usertype 				= mysql_real_escape_string(trim(htmlspecialchars()$_REQUEST['usertype']));
			$usertype = "5";
			$mobilenumber 			= ltrim(mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['mobilenumber']))),'0');
			$email 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['email'])));
			$firstname 				= mysql_real_escape_string(trim(htmlspecialchars(ucfirst($_REQUEST['firstname']))));
			$lastname 				= mysql_real_escape_string(trim(htmlspecialchars(ucfirst($_REQUEST['lastname']))));
			$country 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['country'])));
			$state 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['state'])));
			$city 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['city'])));
			if($city == "-1"){
				$city = "0";
			}
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
			$countrycodelandline 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['countrycodelandline'])));
			$areacode 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['areacode'])));
			$areacode 				= ltrim($areacode, '0');
			$landline 				= ltrim(mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['landline']))),'0');
			$mobilenumber = $country.$mobilenumber;
			$role 					= $_REQUEST['role'];
			
			$update_user_mapping = mysql_query("update USER_MERCHANT_MAPPING set `RoleID` = '$role', `UpdatedBy` = '$logged_userid' WHERE `UserID` = '$staff_to_be_edited' and MerchantID='$logged_merchantid'");
			$update_user_access = mysql_query("update USER_ACCESS set `MobileNo` = '$mobilenumber', `EmailID` = '$email', `UpdatedBy` = '$logged_userid' WHERE `UserID` = '$staff_to_be_edited'");	
			//echo $update_user_access;exit;
			$update_user_details = mysql_query("update USER_DETAILS set FirstName = '$firstname', LastName = '$lastname', CountryCode = '$country', StateID = '$state', CityID = '$city', AddressLine1 = '$addressline1', AddressLine2 = '$addressline2', PinCode = '$pincode', AreaCode = '$areacode', DOB = '$dob', Gender = '$gender', Landline='$landline' where UserID = '$staff_to_be_edited'");
			//echo $update_user_details;exit;
			//header("Location:profile.php");
			?>
			<script type="text/javascript">
			alert("Successfully Updated");
			window.location.href="user_list.php";
			</script>
			<?php
			}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper - Edit Details</title>
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
		function get_cc_code(data)
		{
		   //alert(data.value);
		   var cc_landline_code = data.value;
		   var cc_landline_code = '+'+cc_landline_code.replace(/^0+/,'');
		   //alert(cc_landline_code)
		   document.getElementById("countrycodelandline").value = cc_landline_code;
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
		 		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0"><div id="plantitle">Edit Details</div></div>
		 		<div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" id="mainplanlistdiv">
		 		<form name="register_form" id="register_form" method="post" enctype="multipart/form-data" action="edit_staff_details.php">
		 		<!--<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="registerherediv" align="right" style="height:50px;margin-top:20px;">
			 			Select User Type :  
			 	</div>
			 	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" align="left" style="height:50px;">
			 			<select class="selectpicker forminputs3" id="usertype" name="usertype" style="height:auto;margin-top:20px;max-width:200px;">
			 				<option value="0" style="display:none;">SELECT</option>
			 				<?php echo $role_options;?>
			 			</select>
			 	</div>-->
			 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="margin-top:20px;">
			 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div id="pageheading">Details</div>
			 	</div>
			 	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<?php
					        if($logged_usertype!='I')
					        {
					        ?>
							<span class="asterisk"></span><select class="selectpicker forminputs3" id="role" name="role" required>
						          <option style="display:none;" value="0">SELECT USER TYPE</option>
						          <?php echo $roles_options;?>
						        </select>
						    <?php
							}
						    ?>
							<span class="asterisk"></span><input type="text" class="forminputs3 nonumbers firstlettercaps" placeholder="FIRST NAME" name="firstname" id="firstname" maxlength="20" value="<?php echo $det_firstname;?>" required>
							<span class="asterisk"></span><input type="text" class="forminputs3 nonumbers firstlettercaps" placeholder="LAST NAME" name="lastname" id="lastname" maxlength="20" value="<?php echo $det_lastname;?>" required>
							<span class="asterisk"></span><input type="email" placeholder="EMAIL ID" name="email" id="email" class="forminputs3" maxlength="50" value="<?php echo $det_emailid; ?>" required autocomplete='off'>
							<span class="asterisk"></span><input type="text" maxlength="10" placeholder="MOBILE NUMBER" name="mobilenumber" id="mobilenumber" class="forminputs3 onlynumbers" value="<?php echo $det_mobileno;?>" required autocomplete='off'>	
							<span class="asterisk"></span><select class="selectpicker forminputs3" id="country" name="country" required onchange="get_cc_code(this)">
						          <option style="display:none;" value="0">SELECT COUNTRY</option>
						          <?php echo $country_options;?>
						        </select>	
						    <select class="selectpicker forminputs3" id="state" name="state">
						          <option style="display:none;" value="0">SELECT STATE</option>
						          <?php echo $states;?>
						</select>
						 <select class="selectpicker forminputs3" id="city" name="city">
						          <option style="display:none;" value="0">SELECT CITY</option>
						          <?php echo $cities;?>
						 </select>						
						    
							

				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						
						<div class="col-lg-12 col-md-8 12-sm-12 col-xs-12 paddingl0">
						
						</div>
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingl0">
						  	<select class="forminputs3" name="dddateofbirth" id="dddateofbirth">
										<option style="display:none;" value="0">DD</option>
										<?php 
											for ($i=1; $i <= 31 ; $i++) { 
												if($i < 10){
													$i = "0".$i;
												}
												if($i == $det_dobday){
													echo "<option value='$i' selected>$i</option>";
												} else {
													echo "<option value='$i'>$i</option>";
												}
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
											if($month == $det_dobmon){
												echo "<option value='$month' selected>$month</option>";
											} else {
												echo "<option value='$month'>$month</option>";
											}
										}
										?>
									</select>
						  </div>
						  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingl0">
						  <select class="forminputs3" name="yydateofbirth" id="yydateofbirth">
										<option style="display:none;" value="0">YYYY</option>
										<?php 
											for ($y=2014; $y >= 1940 ; $y--) { 
												if($y == $det_dobyear){
													echo "<option value='$y' selected>$y</option>";
												} else {
													echo "<option value='$y'>$y</option>";
												}
											}
										?>
									</select>
						  </div>
						  <select class="forminputs3" id="gender" name="gender">
						          <option style="display:none;" value="0">SELECT GENDER</option>
						         <option value="M" <?php if($det_gender == "M"){echo "selected";}?>>MALE</option>
						          <option value="F" <?php if($det_gender == "F"){echo "selected";}?>>FEMALE</option>
						        </select>
						
						 <input type="text" class="forminputs3" placeholder="ADDRESS LINE 1" name="addressline1" id="addressline1" maxlength="250" value="<?php echo $det_addressline1;?>">
						 <input type="text" class="forminputs3" placeholder="ADDRESS LINE 2" name="addressline2" id="addressline2" maxlength="250" value="<?php echo $det_addressline2;?>">
						 <input type="text" class="forminputs3 onlynumbers" maxlength="6" placeholder="PINCODE" name="pincode" id="pincode" value="<?php echo $det_pincode;?>">
						 <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 paddingl0">
						 <input type="text" class="forminputs3" maxlength="3" placeholder="+91" name="countrycodelandline" id="countrycodelandline" value="<?php echo $det_countrycall;?>">
						 </div>
						 <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingl0">
							<input type="text" class="forminputs3 onlynumbers" maxlength="5" placeholder="AREA CODE" name="areacode" id="areacode" value="<?php echo $det_areacode;?>">
						 </div>
						 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 paddingr0">
							<input type="text" class="forminputs3 onlynumbers" maxlength="8" placeholder="LANDLINE" name="landline" id="landline" value="<?php echo $det_landline;?>">
						 </div>
						 
				</div>
				</div>
		 		
		 		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="center" style="margin-top:25px;">
						    	<div class="errormessages" id="registrationerror"></div>
						    </div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="ActionBar2" align="center" style="margin-top:25px;">
						        <button id="registerbutton" class="formbuttonsmall">UPDATE</button>
						        </div>
				</form>		      
				</div>
		 	</section>
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
	<script type="text/javascript" src='js/get_user_timezone.js'></script>
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
			var currentpage = "users";
    		$('#'+currentpage).addClass('active');
    		$('#plapiper_pagename').html("Edit");
		    var emailid 	= '<?php echo $det_emailid;?>';
    		var mobileno 	= '<?php echo $det_mobileno;?>';
    		var countrycode = '<?php echo $det_countrycode;?>';
		    var emailerrorflag = 0;
			var mobileerrorflag = 0;
			$("#email").keyup(function(){ 
		        var mail = $("#email").val();
		        if ((validateEmail(mail)) && (mail != emailid)) {
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
    //$("#mobilenumber").keyup(function(){ 
    	$("#mobilenumber,#country").bind("keyup change",function(){ 
        var mobile = $("#mobilenumber").val();
        var country = $("#country").val();
        if ((mobile.length > 7) && (mobile != mobileno || country!=countrycode)) {
        	var dataString = "type=check_duplicate_mobile&mobile="+country+mobile;
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

    	var firstname = $('#firstname').val();
		firstname       = firstname.replace(/ /g,''); //To check if the variable contains only spaces
        if(firstname == ''){
            $('#firstname').val('');
            $('#firstname').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter the first name.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        var lastname = $('#lastname').val();
		lastname       = lastname.replace(/ /g,''); //To check if the variable contains only spaces
        if(lastname == ''){
            $('#lastname').val('');
            $('#lastname').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter the last name.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
       var email = $('#email').val();
        email = email.replace(/ /g,''); //To check if the variable contains only spaces
        if(email == ''){

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
            $("#registrationerror").text("Please enter the mobile number.");
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
        } else if(mobilenumber != mobileno || country!=countrycode){
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
        var country = $('#country').val();
        if(country == '0'){
            $('#country').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please select the country.");
            $("#registrationerror").fadeOut(5000);
            return false;
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

				$('#country').on('change', function() {
					  var code = $(this).val();
					  code = code.replace(/^0+/, '');
					  code = "+"+code;
					  //bootbox.alert(code);
					  //$('#countrycode').val(code);
					  $('#countrycodelandline').val(code);
					});
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