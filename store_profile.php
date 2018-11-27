<?php
session_start();
ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php');
//echo "<pre>";print_r($_SESSION);exit;
if(isset($logged_merchantid)){
	$get_profile_details = mysql_query("select CompanyName, CompanyRegistrationNo, CompanyEmailID, CompanyMobileNo, CompanyAreaCode1, 
							CompanyLandline1, CompanyWebsiteURL, CompanyCountryCode, CompanyStateID, CompanyCityID, 
							CompanyAddressLine1, CompanyAddressLine2, CompanyPinCode from MERCHANT_DETAILS 
							where MerchantID = '$logged_merchantid'");
	//echo $get_profile_details;exit;
	$get_profile_count = mysql_num_rows($get_profile_details);
	if($get_profile_count > 0){
		while ($details = mysql_fetch_array($get_profile_details)) {
			$det_companyname 	= stripslashes($details['CompanyName']);
			$det_regno 			= stripslashes($details['CompanyRegistrationNo']);
			$det_emailid 		= $details['CompanyEmailID'];
			$det_mobileno		= $details['CompanyMobileNo'];
			$det_areacode		= $details['CompanyAreaCode1'];
			$det_landline		= $details['CompanyLandline1'];
			$det_weburl 		= stripslashes($details['CompanyWebsiteURL']);
			$det_countrycode 	= $details['CompanyCountryCode'];
			//$det_countrycode    = "+".ltrim($det_countrycode, '0');
			$det_stateid 		= $details['CompanyStateID'];
			$det_cityid 		= $details['CompanyCityID'];
			$det_addressline1 	= stripslashes($details['CompanyAddressLine1']);
			$det_addressline2 	= stripslashes($details['CompanyAddressLine2']);
			$det_pincode 		= $details['CompanyPinCode'];
		}
	} else {
		header("Location:logout.php");
	}
} else {
	header("Location:logout.php");
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

if((isset($_REQUEST['mobilenumber'])) && (!empty($_REQUEST['mobilenumber']))){
	//echo "<pre>"; print_r($_REQUEST);exit;
$mobilenumber 			= ltrim(mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['mobilenumber']))),'0');
$email 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['email'])));
$companyname 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['companyname'])));
$lastname 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['lastname'])));
$country 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['country'])));
$state 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['state'])));
$city 					= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['city'])));
if($city == "-1"){
	$city = "0";
}
$companyregno 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['companyregno'])));
$companyurl 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['companyurl'])));
$addressline1 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['addressline1'])));
$addressline2 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['addressline2'])));
$pincode 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['pincode'])));
$countrycodelandline 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['countrycodelandline'])));
$areacode 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['areacode'])));
$areacode 				= ltrim($areacode, '0');
$landline 				= ltrim(mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['landline']))),'0');
//$mobilenumber 			= $country.$mobilenumber;

$update_details = mysql_query("update MERCHANT_DETAILS set `CompanyMobileNo` = '$mobilenumber', `CompanyEmailID` = '$email',
							`CompanyName`='$companyname',`CompanyRegistrationNo`='$companyregno',`CompanyAreaCode1`='$areacode',
							`CompanyLandline1`='$landline',`CompanyWebsiteURL`='$companyurl',`CompanyCountryCode`='$country',
							`CompanyStateID`='$state',`CompanyCityID`='$city ',`CompanyAddressLine1`='$addressline1',
							`CompanyAddressLine2`='$addressline2',`CompanyPinCode`='$pincode',
							`UpdatedBy` = '$logged_userid' WHERE `MerchantID` = '$logged_merchantid'");	
//echo $update_user_access;exit;
//header("Location:store_profile.php");
?>
			<script type="text/javascript">
			alert("Successfully Updated");
			window.location.href="store_profile.php";
			//window.location.reload(true);
			</script>
			<?php
			//header('Location:store_profile.php');
}

?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper - Profile</title>
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
		function get_country_code(data)
		{
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
		 		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0"><div id="plantitle">Store Profile<button type="button" class="btns" align="right" id="user_profile">User Profile</button></div></div>
		 		<div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" id="mainplanlistdiv">
		 		<form name="updateprofile_form" id="updateprofile_form" method="post" enctype="multipart/form-data" action="store_profile.php">
			 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="margin-top:20px;">
			 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div id="pageheading">Company Details</div>
			 	</div>
			 	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<span class="asterisk"></span><input type="text" class="forminputs3" placeholder="COMPANY NAME" name="companyname" id="companyname" maxlength="50" value="<?php echo $det_companyname;?>" required>
							<span class="asterisk"></span><input type="email" placeholder="OFFICIAL EMAIL ID" name="email" id="email" class="forminputs3" maxlength="50" value="<?php echo $det_emailid;?>" required>
							<span class="asterisk"></span><input type="text" maxlength="10" placeholder="MOBILE NUMBER" name="mobilenumber" id="mobilenumber" class="forminputs3 onlynumbers" value="<?php echo $det_mobileno;?>" required>
							<span class="asterisk"></span><select class="selectpicker forminputs3" id="country" name="country" required onchange="get_country_code(this)">
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
						<input type="text" class="forminputs3" placeholder="COMPANY REGISTRATION NO." name="companyregno" id="companyregno" maxlength="20" value="<?php echo $det_regno;?>">
							<input type="text" class="forminputs3" placeholder="COMPANY WEBSITE URL" name="companyurl" id="companyurl" maxlength="100" value="<?php echo $det_weburl;?>">
						        <input type="text" class="forminputs3" placeholder="OFFICIAL ADDRESS LINE 1" name="addressline1" id="addressline1" maxlength="250" value="<?php echo $det_addressline1;?>">
								<input type="text" class="forminputs3" placeholder="OFFICIAL ADDRESS LINE 2" name="addressline2" id="addressline2" maxlength="250" value="<?php echo $det_addressline2;?>">
								<input type="text" class="forminputs3 onlynumbers" maxlength="6" placeholder="PINCODE" name="pincode" id="pincode" value="<?php echo $det_pincode;?>">
								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 paddingl0">
									 <input type="text" class="forminputs3" maxlength="3" placeholder="+91" name="countrycodelandline" id="countrycodelandline" value="+91">
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
						        <button id="updateprofilebutton" class="formbuttonsmall">UPDATE</button>
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
			 var currentpage = "profile";
    		$('#'+currentpage).addClass('active');
    		$('#plapiper_pagename').html("Update Profile");

    $("#updateprofilebutton").click(function() {
		var mobilenumber = $('#mobilenumber').val();
		mobilenumber       = mobilenumber.replace(/ /g,''); //To check if the variable contains only spaces
        if(mobilenumber == ''){
            $('#mobilenumber').val('');
            $('#mobilenumber').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter company mobile number.");
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

        //var a = Number(mobilenumber);
        //var b = a.length;
        //alert(b)
        //alert(Number(mobilenumber).toString().length);

        if(Number(mobilenumber).toString().length < 7){
            //bootbox.alert("Please enter a valid mobile number");
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter a valid mobile number.");
            $("#registrationerror").fadeOut(5000);
            $("#mobilenumber").focus();
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

       var companyname = $('#companyname').val();
		companyname       = companyname.replace(/ /g,''); //To check if the variable contains only spaces
        if(companyname == ''){
            $('#companyname').val('');
            $('#companyname').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter company name.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
       var email = $('#email').val();
        email = email.replace(/ /g,''); //To check if the variable contains only spaces
        if(email == ''){
            //$('#email_id').val('');
            $('#email').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter company email id.");
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
        var country = $('#country').val();
        if(country == '0'){
            $('#country').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please select country.");
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
        
        $('#updateprofile_form').submit();
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

				
				$('#user_profile').click(function(){
					window.location.href = "profile.php";
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