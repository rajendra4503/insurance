<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php');
mysql_set_charset('utf8');
  if( !empty( $_REQUEST['lang']) && $_REQUEST['lang'] != '' ){
    $table = $_REQUEST['lang'];
    if(table_exists($table)){
      $query  = mysql_query("SELECT FieldNo ,$table  FROM $table WHERE ScreenNo='010'");
      while ( $result = mysql_fetch_assoc($query) ) {
      ${$result['FieldNo']} = $result[$table];
      } 
    } 
  }
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper - Plans</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>       
	</head>
	<body style="overflow:hidden;">
	<div id="planpiper_wrapper">
	  	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="height:100%;">
	  	 <div class="col-sm-2 paddingrl0"  id="sidebargrid">
		  	<?php include("sidebar.php");?>
		 </div>
		 <div class="col-sm-10 paddingrl0" id="content_wrapper">
		 	<?php include_once('top_header.php');?>
     
      <!--<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
        <div id="plantitle">My Plans</div>
      </div>-->
		 	<section>
      <!--<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0"><div id="plantitle">My Plan Lists<a href="create_plan.php" style="color:#004f35;text-decoration:none;"><button type="button" class="btns" align="right" id="finished_adding">Add New Plan</button></a></div></div>-->
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0"><div id="plantitle">My Master Plans <?php if(isset($MyMasterPlans) &&  $MyMasterPlans !=''){echo '('.$MyMasterPlans.')';}?><?php if($logged_roleid !=4){?><a href="generate_plancode.php" style="color:#004f35;text-decoration:none;"><button type="button" class="btns" align="right" id="finished_adding">Add New Plan <?php if(isset($AddNewPlan) &&  $AddNewPlan !=''){echo '('.$AddNewPlan.')';}?></button></a><?php }?></div></div>
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" id="plantitlelight" style="font-size: 14px;">

         <?php
          if( !empty( $_REQUEST['lang']) && $_REQUEST['lang'] != '' ){
                $lang = $_REQUEST['lang'];
          } else{
            $lang = 'English';
          } 
          ?>

        <span>View <?php if(isset($View) &&  $View !=''){echo '('.$View.')';}?> : </span>
        <a href="plan_list.php?lang=<?php echo $lang ;?>" style="color:#000;text-decoration:none;"><span class="planfilters">Active Plans <?php if(isset($ActivePlans) &&  $ActivePlans !=''){echo '('.$ActivePlans.')';}?></span></a>
        <a href="pendingplans.php?lang=<?php echo $lang ;?>" style="color:#000;text-decoration:none;"><span class="planfilters">Pending Plans <?php if(isset($PendingPlans) &&  $PendingPlans !=''){echo '('.$PendingPlans.')';}?></span></a>
        <a href="deactivatedplans.php?lang=<?php echo $lang ;?>" style="color:#000;text-decoration:none;"><span class="planfilters active">Deactivated Plans <?php if(isset($DeactivatedPlans) &&  $DeactivatedPlans !=''){echo '('.$DeactivatedPlans.')';}?></span></a>
        <a href="assignedplans.php?lang=<?php echo $lang ;?>" style="color:#000;text-decoration:none;"><span class="planfilters">Assigned Plans <?php if(isset($AssignedPlans) &&  $AssignedPlans !=''){echo '('.$AssignedPlans.')';}?></span></a>

				</div>
              <div class="col-lg-offset-2 col-lg-8 col-lg-offset-2 col-md-offset-2 col-md-8 col-md-offset-2 col-sm-12 col-xs-12 paddingrl0" style="margin-top:5px;" id="mainplanlistdiv">        
          <?php 
          //GET DEACTIVE PLANS
          $get_deactive_plans = mysql_query("select t1.PlanCode, t1.PlanName, t1.PlanDescription, t1.PlanCoverImagePath, t1.CategoryID, t2.CategoryName from PLAN_HEADER as t1, CATEGORY_MASTER as t2 where MerchantID = '$logged_merchantid' and t1.CategoryID = t2.CategoryID and t1.PlanStatus = 'I' order by t1.CreatedDate desc");
          //echo $get_deactive_plans;exit;
          $deactive_plans_count = mysql_num_rows($get_deactive_plans);
          if($deactive_plans_count > 0){
            while ($plan_details = mysql_fetch_array($get_deactive_plans)) {
              $plan_code          = $plan_details['PlanCode'];
              $plan_name          = stripslashes($plan_details['PlanName']);
              $plan_desc          = stripslashes(substr($plan_details['PlanDescription'], 0, 120));
              if(strlen($plan_desc)  >= 120){
                $plan_desc = $plan_desc."...";
              }
               if(($plan_details['PlanCoverImagePath'] != "")&&($plan_details['PlanCoverImagePath'] != NULL)){
                $plan_coverimg    = "uploads/planheader/".$plan_details['PlanCoverImagePath'];
              } else {
                $plan_coverimg    = "uploads/planheader/default.jpg";
              }
              $plan_catname     = $plan_details['CategoryName'];
              $plan_catid       = $plan_details['CategoryID'];
              ?>
             <div class="smallplanbox">
                    <img src="<?php echo $plan_coverimg;?>" class="planboximg">
                    <div class="blackoverlay"></div>
                    <div class="planboxname"><?php echo $plan_name;?></div>
                    <div class="planboxcatg"><?php echo $plan_catname;?></div>
                    <div class="planboxdesc"><?php echo $plan_desc;?></div>
                    <?php if($logged_roleid !=4){?>
                    <div class="onhoveroptions">
                       <div class="onhoveroption publishplanbutton" id="<?php echo $plan_code;?>">Publish <?php if(isset($Publish) &&  $Publish !=''){echo '('.$Publish.')';}?></div>
                    </div>
                    <?php }?>
                  </div>
              <?php
            }
          } else {
            echo "<div style='color:#000;text-align:center;font-family:RalewayRegular;font-size:22px;'>You have no deactivated plans.</div>";
          }
        ?>
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
  <script type="text/javascript" src="js/min.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){
    var url = "<?php curPageName();?>";
    var value = "";
    $("select.language").change(function(){
      var selectedCountry = $(".language option:selected").val();
      if(selectedCountry != 'Chosse your language'){
      window.location.href = url+'?lang='+selectedCountry;
    }
    });
    });

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
        $('#plapiper_pagename').html("Plans");

        var windowheight = h;
        var available_height = h - 210;
        $('#mainplanlistdiv').height(available_height);
        $('.smallplanbox').hover(function(){
          $('.onhoveroptions').hide();
          $('.onhoveroptions', $(this)).show(); 
        });
        //PUBLISH PLAN BUTTON CLICKED
        $('.publishplanbutton').click(function(){
          var plancode = $(this).attr('id');
          //alert(plancode);
            var dataString = "type=plan_publish_eligibility&plancode="+plancode;

            //var dataString = "type=publish_plan&plancode="+plancode;
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
                  window.location.href = "deactivatedplans.php";
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
                      window.location.href = "deactivatedplans.php";
                    },
                    error: function(error)
                    {
                      
                    }
                  }); 
                } else {
                  
                }
                //alert("Plan successfully published.");
                //window.location.href = "pendingplans.php";
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
        var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
	});
	</script>
</body>
</html>