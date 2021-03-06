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
		<title>Plan Piper | Diet</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>       
        <script type="text/javascript">

        </script>
        <style type="text/css">
        	.carousel-control {
			  padding-top:5%;
			  width:5%;
			}
        </style>
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
					<li role="presentation" class="active navbartoptabs"><a href="plan_diet.php">DIET</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_exercise.php">EXERCISE</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_instruction.php">INSTRUCTION</a></li>
				</ul>
			</div>
			    <div style="height:100%;">
    	<div class="col-lg-2 col-md-3 col-sm-3 hidden-xs paddingrl0" style="margin-right:0px;padding-right:0px;" id="activitylist">
    	<div class="navbar navbar-default" role="navigation" style="height: 100%;background-color:#004F35;position:fixed;width:16.7%;">
    		<div id="listBar">
		    	<div id="listHeader">
		    		<h6 style="font-family:Freestyle;font-size:180%;margin-top:-7px;letter-spacing:1px;">Diet Plan List</h6>
		    	</div>
		    	<div id="listItems">
		    		<button type="button" id="addItemButton" class="btns">Add A Diet Plan<img src="images/addItem.png"></button>
		    		<button type="button" class="btns">
		    			<span>Breakfast Items</span><br>
		    			<span>Created on 11-Nov-2014</span>
		    		</button>
		    		<button type="button" class="btns">
		    			<span>Low Fat Energy Foods</span><br>
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
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="left">
          Diet Plan Name: 
          <input type="text" name="dietPlanName" class="forminputs">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="center">
         Advisor's Name:
          <input type="text" name="advisorName" class="forminputs">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" align="right">
         Duration:
          <input type="number" id="duration" class="forminputs" value="0" style="width:55px;text-align:center;" maxlength="3" min='0' max='999'>
          Days
        </div>
      </div>
	    	<div id="dynamicPagePlusActionBar">
	    		<div class="container">
    <div class="col-xs-12">
        <div class="well">
            <div id="myCarousel" class="carousel slide">
                
                <!-- Carousel items -->
                <div class="carousel-inner">
                    <div class="item active">
                        <div class="row">
                            <div class="col-xs-2"><a href="#x"><img src="http://placehold.it/500x500&text=1"  alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x"><img src="http://placehold.it/500x500&text=2" alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x"><img src="http://placehold.it/500x500&text=3" alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x"><img src="http://placehold.it/500x500&text=4" alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x"><img src="http://placehold.it/500x500&text=5" alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x"><img src="http://placehold.it/500x500&text=6" alt="Image" class="img-responsive"></a>
                            </div>
                        </div>
                        <!--/row-->
                    </div>
                    <!--/item-->
                    <div class="item">
                        <div class="row">
                            <div class="col-xs-2"><a href="#x" class="thumbnail"><img src="http://placehold.it/250x250&text=7" alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x" class="thumbnail"><img src="http://placehold.it/250x250&text=8" alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x" class="thumbnail"><img src="http://placehold.it/250x250&text=9" alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x" class="thumbnail"><img src="http://placehold.it/250x250&text=10" alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x" class="thumbnail"><img src="http://placehold.it/250x250&text=11" alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x" class="thumbnail"><img src="http://placehold.it/250x250&text=12" alt="Image" class="img-responsive"></a>
                            </div>
                        </div>
                        <!--/row-->
                    </div>
                    <!--/item-->
                    <div class="item">
                        <div class="row">
                            <div class="col-xs-2"><a href="#x" class="thumbnail"><img src="http://placehold.it/250x250&text=13" alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x" class="thumbnail"><img src="http://placehold.it/250x250&text=14" alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x" class="thumbnail"><img src="http://placehold.it/250x250&text=15" alt="Image" class="img-responsive"></a>
                            </div>
                            <div class="col-xs-2"><a href="#x" class="thumbnail"><img src="http://placehold.it/250x250&text=16" alt="Image" class="img-responsive"></a>
                            </div>
                             <div class="col-xs-2"><a href="#x" class="thumbnail"><img src="http://placehold.it/250x250&text=17" alt="Image" class="img-responsive"></a>
                            </div>
                             <div class="col-xs-2"><a href="#x" class="thumbnail"><img src="http://placehold.it/250x250&text=18" alt="Image" class="img-responsive"></a>
                            </div>
                        </div>
                        <!--/row-->
                    </div>
                    <!--/item-->
                </div>
                <!--/carousel-inner--> <a class="left carousel-control" href="#myCarousel" data-slide="prev">‹</a>

                <a class="right carousel-control" href="#myCarousel" data-slide="next">›</a>
            </div>
            <!--/myCarousel-->
        </div>
        <!--/well-->
    </div>
</div>
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
		$('#myCarousel').carousel({
		interval: 10000
		})
	    
	    $('#myCarousel').on('slid.bs.carousel', function() {
	    	//alert("slid");
		});
		});
		</script>
    </body>
</html>