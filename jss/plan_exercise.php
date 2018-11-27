<?php 
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php'); 
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper | Exercise</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>       
        <script type="text/javascript">

        </script>
    </head>
    <body id="wrapper">
     <div class="col-sm-2 paddingrl0"  style="display:none;" id="sidebargrid">
		  	<?php include("sidebar.php");?>
		 </div>
		<div id="planpiper_wrapper" class="fullheight">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
			<?php include_once('top_header.php');?>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
			<div id="plantitle"> Pregnancy Plan - 9 Months Plan
			</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="nav nav-pills nav-justified">
					<li role="presentation" class="navbartoptabs"><a href="plan_medication.php">MEDICATION</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_appointments.php">APPOINTMENT</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_selftest.php">SELF TEST</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_labtest.php">LAB TEST</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_diet.php">DIET</a></li>
					<li role="presentation" class="active navbartoptabs"><a href="plan_exercise.php">EXERCISE</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_instruction.php">INSTRUCTION</a></li>
				</ul>
			</div>
			    <div style="height:100%;">
    	<div class="col-lg-2 col-md-3 col-sm-3 hidden-xs paddingrl0" style="margin-right:0px;padding-right:0px;" id="activitylist">
    	<div class="navbar navbar-default" role="navigation" style="height: 100%;background-color:#004F35;position:fixed;width:16.7%;">
    		<div id="listBar">
		    	<div id="listHeader">
		    		<h6 style="font-family:Freestyle;font-size:180%;margin-top:-7px;letter-spacing:1px;">Exercise Plan List</h6>
		    	</div>
		    	<div id="listItems">
		    		<button type="button" id="addItemButton" class="btns">Add An Exercise Plan<img src="images/addItem.png"></button>
		    		<button type="button" class="btns">
		    			<span>First Trimester</span><br>
		    			<span>Created on 11-Nov-2014</span>
		    		</button>
		    		<button type="button" class="btns">
		    			<span>Second Trimester</span><br>
		    			<span>Created on 11-Dec-2014</span>
		    		</button>
		    	</div>
		    	<!--<div id="listTemplates">
		    		<ul>
		    			<li><a href="#1">Template 1</a></li>
		    			<li><a href="#2">Template 2</a></li>
		    			<li><a href="#3">Template 3</a></li>
		    		</ul>
		    	</div>-->
		    </div>
		    </div>
    	</div>
    	<div class="col-lg-10 col-md-9 col-sm-9 col-sm-12 maincontent">
    	      <div class="dietNameBar">
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="center">
          Plan Name: 
          <input type="text" name="dietPlanName" class="forminputs" style="float:right;">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="center">
         Advisor's Name:
          <input type="text" name="advisorName" class="forminputs" style="float:right;">
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-11" align="center">
         Duration
          <input type="text" id="duration" class="forminputs" style="float:right;">
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" align="center">
        <button type="button" id="daysButton" class="btns">OK</button>
        </div>
      </div>
	    	<div id="dynamicPagePlusActionBar">
	    		<label>
	    			You must first add an exercise plan.  <span id='getexercise'>Click here</span> to start adding or Select A Template to get started.
	    		</label>
	    	</div>
    	</div>
    </div>
		</div>
		<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			mainHeader = 155;//include main header height also
			plantitleHeight = $("#plantitle").outerHeight(true);//true includes margin height also
			navigationbarHeight = $("#navigationbar").outerHeight(true);//true includes margin height also
			totalUsedHeight = plantitleHeight + navigationbarHeight + mainHeader;
			listBarHeight = ($(window).innerHeight()-totalUsedHeight)
		  	//$('#listBar').css({height: listBarHeight});
		  	$('#dynamicPagePlusActionBar').css({height: listBarHeight});
			var h = window.innerHeight;
		  	var windowheight = h;
       		var available_height = h - 210;
        	$('.maincontent').height(available_height);			
			var dietcount = 0;
			$('#plapiper_pagename').html("Exercise");

						setTimeout(function() {
		        $("#addItemButton").trigger('click');
		    },1);

			$('#addItemButton, #getexercise').click(function(){
				$.ajax({
					type        : "GET",
					url			: "exerciseDefaultPage.php",
					dataType	: "html",
					success	: function (response)
					{ 
						$('#dynamicPagePlusActionBar').html(response);
						dietcount = 3;
					 },
					 error: function(error)
					 {
					 	alert(error);
					 }
				}); 		
			});
		var sidebarflag = 0;
        $('#topbar-leftmenu').click(function(){
	      if(sidebarflag == 1){
              $('#sidebargrid').hide("slow","swing");
              $('#activitylist').show("slow","swing");
              $('.maincontent').addClass("col-lg-10");
              sidebarflag = 0;
          } else {
              $('#sidebargrid').show("slow","swing");
              $('#activitylist').hide("slow","swing");
              $('.maincontent').removeClass("col-lg-10");
              $('.maincontent').removeClass("col-md-9");
              $('.maincontent').removeClass("col-sm-9");
              sidebarflag = 1;
          }
        });
		});
		</script>
    </body>
</html>