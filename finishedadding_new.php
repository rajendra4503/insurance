<?php
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php'); 
//$_SESSION['plancode_for_current_plan'] = "444444444444";
//$_SESSION['current_created_planname'] = "Diabetic Diet Plan";
$plancode_for_current_plan = $_SESSION['plancode_for_current_plan'];
//$current_created_planname = $_SESSION['current_created_planname'];
$get_plan_details = "select t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t1.CategoryID, t2.CategoryName from PLAN_HEADER as t1, CATEGORY_MASTER as t2  where PlanCode = '$plancode_for_current_plan' and t1.CategoryID = t2.CategoryID";
//echo $get_plan_details;exit;
$get_plan_details_run = mysql_query($get_plan_details);
$get_plan_details_count = mysql_num_rows($get_plan_details_run);
		 		if($get_plan_details_count > 0){
		 			while ($plan_details = mysql_fetch_array($get_plan_details_run)) {
		 				$plandet_name 		= $plan_details['PlanName'];
		 				$plandet_desc 		= substr($plan_details['PlanDescription'], 0, 120);
		 				if(strlen($plandet_desc) >= 120){
		 					$plandet_desc = $plandet_desc."...";
		 				}
		 				$plandet_path = "";
            $imagepresent = 0;
            if(($plan_details['PlanCoverImagePath'] != "")&&($plan_details['PlanCoverImagePath'] != NULL)){
		 					$plandet_path 		= "uploads/planheader/".$plan_details['PlanCoverImagePath'];
              $imagepresent = 1;
		 				} else {
              $plandet_path     = "uploads/planheader/default.jpg";
            }
		 				$plandet_catg 		= $plan_details['CategoryName'];
		 				$plandet_cid		= $plan_details['CategoryID'];
		 				}
		 		} else {
		 			?>
		 			<script type="text/javascript">
						alert("Please select a plan");
						window.location.href = "plan_list.php";
					</script>
					<?php
		 		}

if(isset($_REQUEST['hidden_value'])){
    //echo $_REQUEST['hidden_value'];exit;
    //echo "<pre>";print_r($_FILES);exit;



    if(!empty($_FILES)) // [START FILE UPLOADED]
{//echo 123;exit;
//include 'config.php';
/* 
1 = Check if the file uploaded is actually an image no matter what extension it has
2 = The uploaded files must have a specific image extension
*/

$validation_type = 1;

if($validation_type == 1)
{
   $mime = array('image/gif' => 'gif','image/jpeg' => 'jpeg','image/png' => 'png','image/jpg' => 'jpg');
}
// Check for a correct extension. The image file hasn't an extension? Add one

   if($validation_type == 1)
   {
    //echo 123;exit;
   $file_info = getimagesize($_FILES['cover_pic']['tmp_name']);

      if(empty($file_info)) // No Image?
      {//echo "INVALID";exit;
      //$error .= "The uploaded file doesn't seem to be an image.";
      ?>
      <script type="text/javascript">
            alert('Sorry, we only accept valid PNG,JPG and JPEG images');
            window.history.back();

            //$("#imageerror").fadeIn();
            //$("#imageerror").text("Sorry, we only accept valid PNG,JPG and JPEG images'");
            //$("#imageerror").fadeOut(1000);

        </script>
      <?php
      }
      else // An Image?
      {//echo "VALID";exit;
        $cover_pic        = (empty($_FILES['cover_pic']['name'])) ? ''    : $_FILES['cover_pic']['name'];
        $path               = "uploads/planheader/";
        if(!is_dir($path)){
            mkdir($path);
          }
          if($cover_pic){
              $no            = rand();
              $imgtype       = explode('.', $cover_pic);
              $ext           = end($imgtype);
              $fullfilename  = $no . '.' . $ext;
              $fullpath      = $path . $no . '.' . $ext;
              move_uploaded_file($_FILES['cover_pic']['tmp_name'], $fullpath);
          }
      $update_plan_image  = "update PLAN_HEADER set PlanCoverImagePath = '$fullfilename' where PlanCode='$plancode_for_current_plan'";
      //echo $update_profile_dp;exit;
        $update_qry     = mysql_query($update_plan_image);
          header("Location:finishedadding_new.php");
      }
   }
}

}

?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper - Publish</title>
    <link rel="stylesheet" type="text/css" href="css/jasny-bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/magicsuggest.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>       
	</head>
	<body style="overflow:hidden;">
	<div id="planpiper_wrapper"class="fullheight">
	  	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="height:100%;">
	  	 <div class="col-sm-2 paddingrl0"  id="sidebargrid">
		  	<?php include("sidebar.php");?>
		 </div>
		 <div class="col-sm-10 paddingrl0" id="content_wrapper">
		 	<?php include_once('top_header.php');?>
		 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
		 	<div id="pageheading">Publish this plan</div>
			</div>
		 	<section>
		 		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								  <form name="frm_upload_image" id="frm_upload_image" method="POST" enctype="multipart/form-data">
								<div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" style="margin-top:5%;margin-bottom:5%;">
									<div class="bigplanbox">
										<img src="<?php echo $plandet_path;?>" class="planboximg" id="planboximg">
										<div class="blackoverlay"></div>
										<div class="planboxname"><?php echo $plandet_name; ?></div>
										<div class="planboxcatg"><?php echo $plandet_catg; ?></div>
										<div class="planboxdesc"><?php echo $plandet_desc;?></div>
										<!--<div class="image-upload planboxcatg" style="top:10px;">
											   <label for="cover_image">
											        <img src="images/edit.png" width="50px" height="50px" style="cursor:pointer;" title="Upload Cover Image" />
											    </label>
                                                <input id="width" type="hidden" value="820" />
  												<input id="height" type="hidden" value="300"/>
											   	<input id="cover_image" name="cover_image" type="file" multiple accept='image/*'/>
										</div>
										<div id="" style="z-index:0;">
									<div id="targetLayer" style=""></div>
                                     <br /><span id="message"></span><br />
								</div> -->
									</div>
<!--                   <div style="margin-top:10px;font-family:RalewayRegular;font-size:17px;">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" align="right">
                      Upload Cover Picture :
                    </div>
                     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                      <input id="cover_pic" name="cover_pic" type="file" multiple accept='image/*'/>
                      <input type="hidden" name="hidden_value" id="hidden_value" value="1">
                    </div>
                  </div> -->
                   <input type="hidden" name="hidden_value" id="hidden_value" value="1">
              <div class="fileinput fileinput-new col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" data-provides="fileinput" style="margin-top:10px;" align="center"> 
              <span class="btn btn-default btn-file profilebuttons"><span class="fileinput-new">Choose Cover Image</span><span class="fileinput-exists profilebuttons">Change</span><input type="file" name="cover_pic" id='cover_pic'></span>
              <span class="fileinput-filename"></span>
              <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
              <?php 
                if($imagepresent == 1){
                  echo "<span class='btn btn-default removecoverimage profilebuttons' data-dismiss='fileinput'>Remove</span>";
                }
              ?>
            </div>

								</div>

								</form>
								<!--  style="position: fixed;bottom: 0;background-color: #fff;text-align: center;left:0;"-->
								 <div id="ActionBar2" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:50px;">
							        <button type="button" id="publishthisplan" class="btns formbuttonsmall">PUBLISH</button>
							        <button type="button" id="publishandassign" class="btns formbuttonsmall">PUBLISH & ASSIGN</button>
							      </div>
								</div>
		 	</section>
		 </div>
		 <!--<div class="col-sm-2 hidden-xs paddingrl0" id="planlistBar" style="height:100%;">
		 <div style="height:100%;overflow:scroll;overflow-x:hidden;">
		 	<div id="rightmenuheading">Plan List</div>
		 	<?php 
		 		$get_active_plans = "select t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t2.CategoryName from PLAN_HEADER as t1, CATEGORY_MASTER as t2 where MerchantID = '$logged_merchantid' and t1.CategoryID = t2.CategoryID and t1.PlanStatus = 'A' order by t1.CreatedDate desc";
		 		//echo $get_active_plans;exit;
		 		$get_active_plans_run = mysql_query($get_active_plans);
		 		$get_active_plans_count = mysql_num_rows($get_active_plans_run);
		 		if($get_active_plans_count > 0){
		 			while ($active_plans = mysql_fetch_array($get_active_plans_run)) {
		 				$plan_name 		= $active_plans['PlanName'];
		 				$plan_desc 		= substr($active_plans['PlanDescription'], 0, 63);
		 				if(strlen($plan_desc) >= 63){
		 					$plan_desc = $plan_desc."...";
		 				}
		 				$plan_path 		= $active_plans['PlanCoverImagePath'];
		 				$plan_catg 		= $active_plans['CategoryName'];
		 				?>
		 				<div class="plandetails">
							<span class='planH' style="height:40px;"><?php echo $plan_name;?></span>
							<p style="height:85px;margin-top:5px;"><?php echo $plan_desc;?></p>
							<span class='planC'><?php echo $plan_catg;?></span>
						</div>
		 				<?php 
		 			}
		 		}
		 		else {
		 			echo "<div id='sidebartext'>No active plans. <a href='create_plan.php' style='color:#fff;border-bottom:1px dotted #fff;text-decoration:none;'>Add</a> a plan to assign.</div>";
		 		}
		 	?>
		 </div>
     		</div>-->
		</div>		
	<!-- Modal window to show the assigned plan code -->
		<div class="modal" id="showassignedplancode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  		<div class="modal-dialog" >
	    		<div class="modal-content modal-content-transparent">
	      			<div class="modal-header" align="center">
			 			<h4 class="modal-title"><span id="changepasswordtext">Plan Assigned</span></h4>
	      			</div>
	      			<div class="modal-body">
	      			</div>
	      			<div align="center" style="margin-top:15px;margin-bottom:30px;">
	      			<span class="plannamemodal"></span> <br>
	      			<span class="planassigned">is successfully assigned to</span> <br>
	      			<span class="planassignedto"></span> <br>
	      			<span class="plancode">Plan Code - <span id='assignedplancode'></span></span>
	      			</div>
	      			<div align="center" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	      				<div id="okbutton">OK</div>
	      			</div>
	    		</div>
	  		</div>
		</div>
	</div><!-- big_wrapper ends -->
      
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/magicsuggest.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/resample.js"></script>
	<script type="text/javascript" src="js/modernizr.js"></script>
	<script type="text/javascript" src="js/placeholders.min.js"></script>
	<script type="text/javascript" src="js/bootbox.min.js"></script>
  <script type="text/javascript" src="js/jasny-bootstrap.min.js"></script>
    <script type="text/javascript">
	$(document).ready(function() {
			var w = window.innerWidth;
    		var h = window.innerHeight;
		    var total = h - 200;
		    var each = total/12;
		    $('.navbar_li').height(each);
		    $('.navbar_href').height(each/2);
		    $('.navbar_href').css('padding-top', each/2.8);
			 var currentpage = "plans";
    		$('#'+currentpage).addClass('active');
    		$('#plapiper_pagename').html("Publish");
    		var plancode = '<?php echo $plancode_for_current_plan;?>';
    		$('#publishthisplan').click(function(){

   			var dataString = "type=plan_publish_eligibility&plancode="+plancode;
            $.ajax({
            type    : 'POST', 
            url     : 'ajax_validation.php', 
            crossDomain : true,
            data    : dataString,
            dataType  : 'json', 
            async   : false,
            success : function (response)
              { 
                if(response.success == false){
                  alert("This plan does not have any activities. Please edit the plan to publish.");
                  window.location.href = "<?php echo $header_url;?>";
                }
                else if(response.success == true){
                   var dataString = "type=publish_plan&plancode="+plancode;
                   $.ajax({
                  type    : 'POST', 
                  url     : 'ajax_validation.php', 
                  crossDomain : true,
                  data    : dataString,
                  dataType  : 'json', 
                  async   : false,
                  success : function (response)
                    { 
                      alert("Plan successfully published.");
                      window.location.href = "plan_list.php";
                    },
                    error: function(error)
                    {
                      
                    }
                  }); 
                } else {
                  
                }
              },
            error: function(error)
            {
              
            }
          }); 
			});
			$('#publishandassign').click(function(){

				   			var dataString = "type=plan_publish_eligibility&plancode="+plancode;
            $.ajax({
            type    : 'POST', 
            url     : 'ajax_validation.php', 
            crossDomain : true,
            data    : dataString,
            dataType  : 'json', 
            async   : false,
            success : function (response)
              { 
                if(response.success == false){
                  alert("This plan does not have any activities. Please edit the plan to publish.");
                  window.location.href = "<?php echo $header_url;?>";
                }
                else if(response.success == true){
                   var dataString = "type=publish_plan&plancode="+plancode;
                   $.ajax({
                  type    : 'POST', 
                  url     : 'ajax_validation.php', 
                  crossDomain : true,
                  data    : dataString,
                  dataType  : 'json', 
                  async   : false,
                  success : function (response)
                    { 
                      alert("Plan successfully published.");
                      window.location.href = "assign_to_user.php";
                    },
                    error: function(error)
                    {
                      
                    }
                  }); 
                } else {
                  
                }
              },
            error: function(error)
            {
              
            }
          }); 
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
            var fileTypes = ['jpg', 'jpeg', 'png'];  //acceptable file types
    function readURL(input) {
        if (input.files && input.files[0]) {
            var extension = input.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
            isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
            if (isSuccess) {
            var reader = new FileReader();        
            reader.onload = function (e) {
                $('#planboximg').attr('src', e.target.result);
                $('#frm_upload_image').submit(); //To upload to server directly
            }          
            reader.readAsDataURL(input.files[0]);
        } else {
            alert("Plan Cover Image should be in jpg, png or jpeg format.");
            return false;
        }
    }
    }
   $("#cover_pic").change(function(){
        readURL(this);
    });
            $('.removecoverimage').click(function(){
            var plancode     = "<?php echo $plancode_for_current_plan?>";
            //alert(plancode);
            var dataString = "type=remove_cover_image&plancode="+plancode;
            $.ajax({
            type    : 'POST', 
            url     : 'ajax_validation.php', 
            crossDomain : true,
            data    : dataString,
            dataType  : 'json', 
            async   : false,
            success : function (response)
              { 
                if(response.success == true){
                  //alert(1);
                  alert("Cover Image Removed Successfully");
                  $('#planboximg').prop("src","uploads/planheader/default.jpg");
                  $('.removecoverimage').hide();
                  return false;
                } else {
                  return false;
                }
              },
            error: function(error)
            {
              
            }
          }); 
          });
                    var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
	});
	</script>
		</body>
</html>