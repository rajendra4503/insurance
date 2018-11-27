<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper | Medication</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>       
        <script type="text/javascript">

        </script>
    </head>
    <body style="overflow:hidden;">
		<div id="planpiper_wrapper">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingl0">
			<?php include_once('top_header.php');?>
			</div>

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingl0" style="margin-top:50px;position:fixed;z-index:10;">
			<div id="plantitle"> Pregnancy Plan - 9 Months Plan
			</div>
		<!--<a href="#"><button type="button" class="btn"><img src="images/finishAdd.png">&nbsp;FINISH ADDING</button></a>-->
	
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingl0" style="margin-top:100px;position:fixed;z-index:10;">
				<ul class="nav nav-pills nav-justified">
					<li role="presentation" class="active navbartoptabs"><a href="plan_medication.php">MEDICATION</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_appointments.php">APPOINTMENT</a></li>
					<li role="presentation" class="navbartoptabs"><a href="#">SELF TEST</a></li>
					<li role="presentation" class="navbartoptabs"><a href="#">LAB TEST</a></li>
					<li role="presentation" class="navbartoptabs"><a href="#">DIET</a></li>
					<li role="presentation" class="navbartoptabs"><a href="#">EXERCISE</a></li>
				</ul>
			</div>
			    <div class="row" style="margin-top:155px;">
    	<div class="col-lg-2 col-md-3 col-sm-3 hidden-xs" style="margin-right:0px;padding-right:0px;">
    	<div class="navbar navbar-default" role="navigation" style="height: 100%;background-color:#004F35;position:fixed;width:16.7%;">
    		<div id="listBar">
		    	<div id="listHeader">
		    		<label>Prescription List</label>
		    	</div>
		    	<div id="listItems">
		    		<button type="button" id="addItemButton" class="btn">Add A Prescription<img src="images/addItem.png"></button>
		    		<button type="button" class="btn">
		    			<span>Phase 1 Prescription</span><br>
		    			<span>Created on 11-Nov-2014</span>
		    		</button>
		    		<button type="button" class="btn">
		    			<span>Phase 2 Prescription</span><br>
		    			<span>Created on 11-Dec-2014</span>
		    		</button>
		    	</div>
		    	<div id="listTemplates">
		    		<ul>
		    			<li><a href="#1">Template 1</a></li>
		    			<li><a href="#2">Template 2</a></li>
		    			<li><a href="#3">Template 3</a></li>
		    		</ul>
		    	</div>
		    </div>
		    </div>
    	</div>
    	<div class="col-lg-10 col-md-9 col-sm-9 col-sm-12">
	    	<div id="dynamicPagePlusActionBar">
	    		<label>
	    			You must first add a Prescription to include all the Medicines. <span id='getmedications'>Click here</span> to start adding or Select A Template to get started.
	    		</label>
	    	</div>
    	</div>
    </div>
		</div>
		<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			mainHeader = 111;//include main header height also
			plantitleHeight = $("#plantitle").outerHeight(true);//true includes margin height also
			navigationbarHeight = $("#navigationbar").outerHeight(true);//true includes margin height also
			totalUsedHeight = plantitleHeight + navigationbarHeight + mainHeader;
			listBarHeight = ($(window).innerHeight()-totalUsedHeight)
		  	//$('#listBar').css({height: listBarHeight});
		  	$('#dynamicPagePlusActionBar').css({height: listBarHeight});
			var medicationcount = 0;
			$('#plapiper_pagename').html("Medication");
			$('#addItemButton, #getmedications').click(function(){
				$.ajax({
					type        : "GET",
					url			: "prescriptionDefaultPage.php",
					dataType	: "html",
					success	: function (response)
					{ 
						$('#dynamicPagePlusActionBar').html(response);
						medicationcount = 3;
					 },
					 error: function(error)
					 {
					 	alert(error);
					 }
				}); 		
			});
			$('#addMedicine').click(function(){
				var first = "  <tr><td><textarea rows='2' name='medicine1' placeholder='Enter Medicine Name..'></textarea></td><td><select name='when1'><option>select</option><option>0-0-0-X</option><option>0-0-X-0</option><option>0-0-X-X</option><option>0-X-0-0</option><option>0-X-0-X</option><option>0-X-X-0</option><option>0-X-X-X</option><option>X-0-0-0</option><option>X-0-0-X</option><option>X-0-X-0</option><option>X-0-X-X</option><option>X-X-0-0</option><option>X-X-0-X</option><option>X-X-X-0</option><option>X-X-X-X</option></select></td><td><select name='instruction1'><option>select</option><option>Before</option><option>With</option><option>After</option></select></td><td><select name='frequency1'><option>select</option><option>Once</option><option>Daily</option><option>Weekly</option><option>Monthly</option></select></td><td><div style='border-radius:10px;padding:5px 5px;background-color:#2B6D57;height:50px'><select class='form-control' id='count1' style='width:43%;float:left;height:40px'><option>select</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option><option>7</option><option>8</option><option>9</option><option>10</option></select><select class='form-control' id='countType1' style='width:55%;float:right;height:40px'><option>select</option><option>Days</option><option>Weeks</option><option>Months</option></select></div></td><td><input type='checkbox' name='critical1'></td><td><input type='checkbox' name='response1'></td><td style='text-align:left;font-size : 0.7em;'><input type='radio' name='gender' value='PS'> When the plan Starts/Updates<br><input type='radio' name='gender' value='MN'> After the Medicine No:&nbsp;<input type='text' size='6px'><br><input type='radio' name='gender' value='SD'> At Specific Date:&nbsp;<input type='text' size='10px'></td><td><img src='images/closeRow.png' width='30px' height='auto' onclick='this.parentNode.parentNode.remove()'></td></tr>";
				var slno  = "<tr><td>"+medicationcount+"</td></tr>";
				$('#pslno > tbody').append(slno);
				$('#pdata > tbody').append(first);
				medicationcount = medicationcount + 1;
			});
		});
		</script>
    </body>
</html>