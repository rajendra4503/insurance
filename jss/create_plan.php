<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php');
//Get Plan Categories
$categories = "";
$get_plan_categories = "select CategoryID, CategoryName from CATEGORY_MASTER";
$get_plan_categories_query = mysql_query($get_plan_categories);
$get_plan_categories_count = mysql_num_rows($get_plan_categories_query);
if($get_plan_categories_count > 0){
	while($category_row = mysql_fetch_array($get_plan_categories_query)){
		$category_id 	= $category_row['CategoryID'];
		$category_name 	= $category_row['CategoryName'];
		$categories 	.= "<option value='$category_id' class='selectoption'>$category_name</option>";
	}
}

//Adding a new plan
if(isset($_REQUEST['plan_name']) && (!empty($_REQUEST['plan_name']))){
	$plan_name 				  = mysql_real_escape_string(htmlspecialchars($_REQUEST['plan_name']));
	$plan_desc 				  = mysql_real_escape_string(htmlspecialchars($_REQUEST['plan_desc']));
	$plan_category 			= $_REQUEST['category'];
	$plan_cover_image   = (empty($_FILES['cover_image']['name'])) ? '' : $_FILES['cover_image']['name'];
	$path               = "uploads/planheader/";
	if(!is_dir($path)){
    	mkdir($path);
  	}
  	if($plan_cover_image){
        $no            = rand();
        $imgtype       = explode('.', $plan_cover_image);
        $ext           = end($imgtype);
        $fullfilename  = $no . '.' . $ext;
        $fullpath      = $path . $no . '.' . $ext;
        move_uploaded_file($_FILES['cover_image']['tmp_name'], $fullpath);
    }
   // $length = 12;
	//$plan_code = substr(str_shuffle(md5(time())),0,$length);
  $country_id = $logged_companycountryid;
  $get_last_plancode = mysql_query("select PlanCode from PLAN_HEADER where PlanCode like '$country_id%' order by PlanCode desc limit 1");
  $plancode_count = mysql_num_rows($get_last_plancode);
  if($plancode_count > 0){
    while ($plancodelast = mysql_fetch_array($get_last_plancode)) {
      $lastplancode = $plancodelast['PlanCode'];
    }
    $lastplancode = substr($lastplancode, 2);
    $lastplancode = $lastplancode  +1;
    $lastplancode = sprintf('%010d', $lastplancode);
    $plan_code     = $country_id.$lastplancode;
  } else {
    $plan_code = $country_id."0000000001";
  }

    $insert_plan_header = "insert into PLAN_HEADER (PlanCode, MerchantID, CategoryID, PlanName, PlanDescription, PlanStatus, PlanCoverImagePath, CreatedDate, CreatedBy) values ('$plan_code', '$logged_merchantid', '$plan_category', '$plan_name', '$plan_desc', 'P', '$fullfilename', now(), '$logged_userid')";
    //echo $insert_plan_header;exit;
    $insert_plan_header_run = mysql_query($insert_plan_header);
    $check_insert       = mysql_insert_id();
    $current_created_plancode = $plan_code;
    $_SESSION['current_created_plancode'] = $current_created_plancode;
    $_SESSION['current_created_planname'] = $plan_name;
        if($check_insert)
        {
            //header('Location:plan_medication.php');
            header("location:$header_url");
        }
        else
        {
            //header('Location:plan_medication.php');
            header("location:$header_url");
        }
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper - Create Plan</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>       
    <script type="text/javascript">
		function keychk(event)
		{
			//alert(123)
			if(event.keyCode==13)
			{
				$("#saveplanbutton").click();
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
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0"><div id="plantitle">Plan Details</div></div>
		 	<section>
		 		<div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" id="mainplanlistdiv">
								<form name="frm_new_plan" id="frm_new_plan" method="post" enctype="multipart/form-data" action="create_plan.php">
                                <div style="margin-top:60px;z-index:200;">
                                 <div style="height:50px;margin-left:40px;" id="error" >
									<span id="custerrormessage" style="color:#F65100;font-family:Raleway;"></span>
								</div>
									<div class="col-lg-offset-1 col-lg-8 col-md-offset-1 col-md-8 col-sm-offset-1 col-sm-8 col-xs-offset-1 col-xs-8" style="z-index:200;padding-top:5px;">
											<input type="text" placeholder="Enter the Plan Title here" name="plan_name" id="plan_name" class="firstlettercaps" title="Plan Title" onkeypress='keychk(event)' autofocus>
                                             
											<textarea placeholder="Type the plan description here" id="plan_desc" name="plan_desc" title="Plan Description" rows="4" style="resize:none;"  maxlength="499"></textarea>
                                            
                                            <!--ADDED-->
                                            <div id="textarea_feedback" style="color:#004F35;font-family:Raleway;padding-bottom:10px;text-align:right"></div>
                                            <!---->
									</div>
                                    
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="z-index:200;" align="center">
										<div class="image-upload">
											   <label for="cover_image">
											        <img src="images/edit.png" width="50px" height="50px" style="margin-top:15px;cursor:pointer;" title="Upload Cover Image" />
											    </label>
                                                <input id="width" type="hidden" value="820" />
  												<input id="height" type="hidden" value="300"/>
											   	<input id="cover_image" name="cover_image" type="file" multiple accept='image/*'/>
										</div>
									</div>
								</div>   
								
								<div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" id="coverimageforplan" style="z-index:0;">
									<div id="targetLayer" style="width:100%;padding:0px;margin-0px;"></div>
                                     <br /><span id="message"></span><br />
								</div>     
														
										<div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" style="margin-top:50px;">	
											<div>
												<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12" align="left" id="selectcategorytext">
													Select A Category For Your Plan : 
												</div>
												<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" align="left">
													<select class="selectbox2" name="category" id="category" onkeypress='keychk(event)' title="Choose a Category">
														<!--<option value="select" class="selectoption" style="display:none;">Select</option>-->
														<?php echo $categories;?>
													</select>
												</div>
											</div>
										</div>									
									</form>
									<div align="center" class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
										<input type="button" id="saveplanbutton" value="Save & Proceed" style="margin-top:20px;">
										<div class='errormessages' id="plan_error"></div>	
									</div>
								</div>
		 	</section>
		 </div>
		</div>		
	</div><!-- big_wrapper ends -->
      
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/resample.js"></script>
  <script type="text/javascript" src="js/modernizr.js"></script>
  <script type="text/javascript" src="js/placeholders.min.js"></script>
    <script type="text/javascript">
	$(document).ready(function() {
			var text_max = 499;
			$('#textarea_feedback').html(text_max + ' characters remaining');
		
			$('#plan_desc').keyup(function() {
				var text_length = $('#plan_desc').val().length;
				var text_remaining = text_max - text_length;
		
				$('#textarea_feedback').html(text_remaining + ' characters remaining');
			});
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
			 var currentpage = "plans";
    		$('#'+currentpage).addClass('active');
        $('#plapiper_pagename').html("New Plan");
			$('#saveplanbutton').click(function(){
				//alert(123);
				var plan_name = $('#plan_name').val();
				plan_name 		= plan_name.replace(/\s+/g, '');
				if(plan_name == ""){
					$('#plan_name').focus();
					$("#plan_error").fadeIn();
					$("#plan_error").text("Please enter the plan title.");
					$("#plan_error").fadeOut(5000);
					return false;
				}
				var plan_desc = $('#plan_desc').val();
				plan_desc 		= plan_desc.replace(/\s+/g, '');
				if(plan_desc == ""){
					$('#plan_desc').focus();
					$("#plan_error").fadeIn();
					$("#plan_error").text("Please enter the plan description.");
					$("#plan_error").fadeOut(5000);
					return false;
				}
				var category = $('#category').val();
				if(category == "select"){
					$("#plan_error").fadeIn();
					$("#plan_error").text("Please select a plan category.");
					$("#plan_error").fadeOut(5000);
					return false;
				}
				$('#frm_new_plan').submit();
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
	<script>
 (function (global, $width, $height, $file, $message, $img) {
  
  // (C) WebReflection Mit Style License
  
  // simple FileReader detection
  if (!global.FileReader)
   // no way to do what we are trying to do ...
   return $message.innerHTML = "FileReader API not supported"
  ;
  
  // async callback, received the
  // base 64 encoded resampled image
  function resampled(data) {
   $message.innerHTML = "done";
   ($img.lastChild || $img.appendChild(new Image)
   ).src = data;
  }
  
  // async callback, fired when the image
  // file has been loaded
  function load(e) {
   $message.innerHTML = "resampling ...";
   // see resample.js
   Resample(
     this.result,
     this._width || null,
     this._height || null,
     resampled
   );
   
  }
  
  // async callback, fired if the operation
  // is aborted ( for whatever reason )
  function abort(e) {
   $message.innerHTML = "operation aborted";
  }
  
  // async callback, fired
  // if an error occur (i.e. security)
  function error(e) {
   $message.innerHTML = "Error: " + (this.result || e);
  }
  
  // listener for the input@file onchange
  $file.addEventListener("change", function change() {
   var
    // retrieve the width in pixel
    width = parseInt($width.value, 10),
    // retrieve the height in pixels
    height = parseInt($height.value, 10),
    // temporary variable, different purposes
    file
   ;
   // no width and height specified
   // or both are NaN
   if (!width && !height) {
    // reset the input simply swapping it
    $file.parentNode.replaceChild(
     file = $file.cloneNode(false),
     $file
    );
    // remove the listener to avoid leaks, if any
    $file.removeEventListener("change", change, false);
    // reassign the $file DOM pointer
    // with the new input text and
    // add the change listener
    ($file = file).addEventListener("change", change, false);
    // notify user there was something wrong
    $message.innerHTML = "please specify width or height";
   } else if(
    // there is a files property
    // and this has a length greater than 0
    ($file.files || []).length &&
    // the first file in this list 
    // has an image type, hopefully
    // compatible with canvas and drawImage
    // not strictly filtered in this example
    /^image\//.test((file = $file.files[0]).type)
   ) {
    // reading action notification
    $message.innerHTML = "reading ...";
    // create a new object
    file = new FileReader;
    // assign directly events
    // as example, Chrome does not
    // inherit EventTarget yet
    // so addEventListener won't
    // work as expected
    file.onload = load;
    file.onabort = abort;
    file.onerror = error;
    // cheap and easy place to store
    // desired width and/or height
    file._width = width;
    file._height = height;
    // time to read as base 64 encoded
    // data te selected image
    file.readAsDataURL($file.files[0]);
    // it will notify onload when finished
    // An onprogress listener could be added
    // as well, not in this demo tho (I am lazy)
   } else if (file) {
    // if file variable has been created
    // during precedent checks, there is a file
    // but the type is not the expected one
    // wrong file type notification
    $message.innerHTML = "please choose an image";
   } else {
    // no file selected ... or no files at all
    // there is really nothing to do here ...
    $message.innerHTML = "nothing to do";
   }
  }, false);
 }(
  // the global object
  this,
  // all required fields ...
  document.getElementById("width"),
  document.getElementById("height"),
  document.getElementById("cover_image"),
  document.getElementById("message"),
  document.getElementById("targetLayer")
 ));
 </script>
</body>
</html>