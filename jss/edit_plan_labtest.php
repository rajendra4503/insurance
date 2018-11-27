<?php 
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php'); 
$current_created_plancode = $_SESSION['current_created_plancode'];
$current_created_planname = $_SESSION['current_created_planname'];
if((isset($_REQUEST['id'])) && (!empty($_REQUEST['id']))){
	$lab_edit_id = $_REQUEST['id'];
} else {
	$lab_edit_id = "1";
}
//echo $appo_edit_id;exit;
if((isset($_REQUEST['hidden_value']))&&(!empty($_REQUEST['hidden_value']))){
	//echo "<pre>";print_r($_REQUEST);exit;
	//DELETE LAB TESTS

	$labtestcount    			= mysql_real_escape_string(trim($_REQUEST['labtestcount'])); //Total number of lab tests present on screen
	$usedlabtestcount 			= mysql_real_escape_string(trim($_REQUEST['usedlabtestcount'])); //row ids of medicines present
	$labttestids 				= explode(",", $usedlabtestcount);
	$current_labtest_num 		= $lab_edit_id;
	if($labtestcount > '0'){
		$delete_user_header = mysql_query("delete from LAB_TEST_HEADER1 where  PlanCode = '$current_created_plancode' and LabTestID='$lab_edit_id'");
		$delete_user_details = mysql_query("delete from LAB_TEST_DETAILS1 where  PlanCode = '$current_created_plancode' and LabTestID='$lab_edit_id'");	
	}
	$insert_header_details 	= " insert into LAB_TEST_HEADER1 (Plancode, LabTestID, CreatedDate, CreatedBy) values ('$current_created_plancode', '$current_labtest_num', now(), '$logged_userid')";
	//echo $insert_header_details;exit;
	$insert_header_run  	= mysql_query($insert_header_details);
    $count = 1;
	foreach ($labttestids as $ids) {
		if($ids != ""){
			$labtestName = mysql_real_escape_string(trim($_REQUEST["labtestName$ids"]));
			if($labtestName != ""){
			$doctor_name 				= mysql_real_escape_string(trim($_REQUEST["doctorName$ids"]));
			$requirements 				= mysql_real_escape_string(trim($_REQUEST["requirements$ids"]));
			$insert_labttest_details = "insert into LAB_TEST_DETAILS1 (`PlanCode`,`LabTestID`,`RowNo`,`TestName`,`DoctorsName`,`LabTestRequirements`,`CreatedDate`,`CreatedBy`) values ('$current_created_plancode', '$current_labtest_num', '$count', '$labtestName','$doctor_name','$requirements', now(), '$logged_userid');";
			//echo $insert_labttest_details;exit;
			$insert_header_run  	= mysql_query($insert_labttest_details);
			$check_affected_rows= mysql_affected_rows();
		        if($check_affected_rows)
		       { 
		       	header("Location:plan_labtest.php");
		       } else {
		       	header("Location:plan_labtest.php");
		       }
		}
	}
		$count++;
	}
	//$update_plan_header = mysql_query("update PLAN_HEADER set PlanStatus = 'A' where Plancode='$current_created_plancode'");
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper | Lab Tests</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/ndatepicker.css">
		<link href="css/bootstrap-timepicker.min.css" type="text/css" rel="stylesheet" />
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
				<div id="plantitle"><?php echo $current_created_planname;?>
				<button type="button" class="btns" align="right" id="finished_adding"><img src="images/finishAdd.png" style="height:20px;width:auto;margin-bottom:3px;">&nbsp;FINISH ADDING</button>
				</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="nav nav-pills nav-justified">
					<?php
					echo $modules;
					?>
					<!--<li role="presentation" class="navbartoptabs"><a href="plan_medication.php">MEDICATION</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_appointments.php">APPOINTMENT</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_selftest.php">SELF TEST</a></li>
					<li role="presentation" class="active navbartoptabs"><a href="plan_labtest.php">LAB TEST</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_diet.php">DIET</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_exercise.php">EXERCISE</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_instruction.php">INSTRUCTION</a></li>-->
				</ul>
			</div>
			    <div  style="height:100%;">
    	<div class="col-lg-2 col-md-3 col-sm-3 hidden-xs paddingrl0" style="margin-right:0px;padding-right:0px;" id="activitylist">
    	<div class="navbar navbar-default" role="navigation" style="height: 100%;background-color:#004F35;position:fixed;width:16.7%;">
    		<div id="listBar">
		    	<div id="listHeader">
		    		<h6 style="font-family:Freestyle;font-size:180%;margin-top:-7px;letter-spacing:1px;">Lab Tests</h6>
		    	</div>
		    	<div id="listItems">
		    		<button type="button" id="addItemButton" class="btns" style="display:none;">Add Tests<img src="images/addItem.png"></button>
		    		<?php 
		    		$get_plan_labtests 		= "select PlanCode, LabTestID, CreatedDate from LAB_TEST_HEADER1 where PlanCode = '$current_created_plancode'";
		    		$get_plan_labtests_run 	= mysql_query($get_plan_labtests);
		    		$get_plan_labtests_count 	= mysql_num_rows($get_plan_labtests_run);
		    		if($get_plan_labtests_count > 0){
			    		while ($get_labtest_row = mysql_fetch_array($get_plan_labtests_run)) {
			    			$labtest_no   = $get_labtest_row['LabTestID'];
			    			$labtest_date = date('d-M-Y',strtotime($get_labtest_row['CreatedDate'])); 
			    			?>
			    			<button type="button" class="btns editmedicationbuttons" id="<?php echo $labtest_no; ?>">
				    			<span>Lab Test - <?php echo $labtest_no;?></span><br>
				    			<span>Created on <?php echo $labtest_date;?></span>
				    		</button>
			    			<?php
			    		}		    			
		    		}
		    		?>
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
	    	<div id="dynamicPagePlusActionBar">
	    		   <form name="frm_edit_labtest" id="frm_edit_labtest" method="post" action="edit_plan_labtest.php?id=<?php echo $lab_edit_id;?>">
			      <div class="table-responsive" style="float:left;width:95%;padding-bottom:100px;">
			      <table id="adata" class="table table-striped">
			        <tr id="aheader">
			          <th>Lab Test Name</th>
			          <th style="width:180px;">Doctor's Name</th>
			          <th>Requirements</th>
			          <th></th>
			        </tr>
			        <?php if(isset($lab_edit_id)){
			        	$labtest_count = 0;
				   			$labtest_count_string = "";
			        	$getlabtests = mysql_query("select TestName, DoctorsName, LabTestRequirements from LAB_TEST_DETAILS1 where LabTestID='$lab_edit_id' and PlanCode='$current_created_plancode'");
			        	$getlabtests_num = mysql_num_rows($getlabtests);
				   		if($getlabtests_num > 0){
				   			while ($detailsrow = mysql_fetch_array($getlabtests)) {
				   				$labtest_count++;
				   				$labtest_count_string 			.= $labtest_count.",";
				   				$testname 		 			= $detailsrow['TestName'];
				   				$doctorsname				= $detailsrow['DoctorsName'];
				   				$requirements 				= $detailsrow['LabTestRequirements'];
			        	?>
			        <tr>
			          <td><input type="text" name="labtestName<?php echo $labtest_count;?>" placeholder="Enter Lab test Name.." maxlength="25" id="labtestName<?php echo $labtest_count;?>" value="<?php echo $testname;?>" class="forminputs2"></td>
			          <td><input type="text" name="doctorName<?php echo $labtest_count;?>" placeholder="Enter Doctor Name.." maxlength="25"  value="<?php echo $doctorsname;?>" id="doctorName<?php echo $labtest_count;?>" class="forminputs2"></td>
			          <td><textarea name="requirements<?php echo $labtest_count;?>"  placeholder="Enter Requirements for the Lab test.." class="forminputs2"><?php echo $requirements;?></textarea></td>
			          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="<?php echo $labtest_count;?>"></td>
			        </tr>
			        <?php 
			    }}}
			        ?>
			      </table>
			      </div>
			      <input type="hidden" name="usedlabtestcount" id="usedlabtestcount" value="<?php echo $labtest_count_string;?>">
			      <input type="hidden" name="labtestcount" id="labtestcount" value="<?php echo $labtest_count;?>">
			      <input type="hidden" name="hidden_value" id="hidden_value" value="Y">
			    </form>
			    <div id="ActionBar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="position: fixed;bottom: 0;background-color: #fff;text-align: center;margin-left: -5%;width:90%;">
			        <button type="button" id="addLabTest" class="btns formbuttons">ADD A TEST</button>
			        <button type="button" id="saveAndEdit" class="btns formbuttons">SAVE</button>
			        <!--<button type="button" id="saveTemplate" class="btns formbuttons">SAVE THIS AS TEMPLATE</button>-->
			      </div>
	    	</div>
    	</div>
		</div>
		<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/ndatepicker-ui.js"></script>
		<script type="text/javascript" src="js/bootstrap-timepicker.js"></script>
		<script type="text/javascript" src="js/modernizr.js"></script>
		<script type="text/javascript" src="js/placeholders.min.js"></script>
		<script type="text/javascript" src="js/bootbox.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$('#3').addClass('active');
			mainHeader = 115;//include main header height also
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

			var labtestcount = 0;
			$('#plapiper_pagename').html("Lab Tests");
			  $(document).on('click', '.deleterow', function () {
			    var deleted_row_id = $(this).attr('id');  
			    //bootbox.alert(deleted_row_id);
			    var labtest_name = $('#labtestName'+deleted_row_id).val();
			   if(labtest_name.replace(/\s+/g, '') == ""){
			      $('#aslno tr:last').remove();
			      //this.parentNode.parentNode.remove();
			      $(this).closest('tr').remove();
			      labtestcount = labtestcount - 1;
			      if(labtestcount == 1){
			        $('.deleterow').hide();
			      }
			        var deleted_usedlabtest = deleted_row_id+",";
			        var current_usedlabtestcount = $('#usedlabtestcount').val();
			        var new_usedlabtestcount  = current_usedlabtestcount.replace(deleted_usedlabtest, "");
			        $('#usedlabtestcount').val(new_usedlabtestcount);
			        $('#labtestcount').val(labtestcount);
			   } else {
			    var deleteconfirm = confirm("This Lab test will be deleted. Click OK to continue.");
			    if(deleteconfirm == true){
			       $('#lslno tr:last').remove();
			      //this.parentNode.parentNode.remove();
			      $(this).closest('tr').remove();
			      labtestcount = labtestcount - 1;
			      if(labtestcount == 1){
			        $('.deleterow').hide();
			      }
			        var deleted_usedlabtest = deleted_row_id+",";
			        var current_usedlabtestcount = $('#usedlabtestcount').val();
			        var new_usedlabtestcount  = current_usedlabtestcount.replace(deleted_usedlabtest, "");
			        $('#usedlabtestcount').val(new_usedlabtestcount);
			        $('#labtestcount').val(labtestcount);
			    } else {

			    }
			   }
			  });
		$('#addLabTest').click(function(){
        labtestcount = labtestcount + 1;
        propercount = propercount + 1;
        $('.deleterow').show();
        var first = "<tr><td><input type='text' name='labtestName"+propercount+"' placeholder='Enter labtest Name..' maxlength='25' id='labtestName"+propercount+"' class='forminputs2'></td><td><input type='text' name='doctorName"+propercount+"' placeholder='Enter Doctor Name..' size='25px' maxlength='25' id='doctorName"+propercount+"' class='forminputs2'></td><td><textarea name='requirements"+propercount+"' id='requirements"+propercount+"' placeholder='Enter Requirements for the labtest..' class='forminputs2'></textarea></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"'></td></tr>";
        var slno  = "<tr><td>"+labtestcount+"</td></tr>";
        $('#aslno > tbody').append(slno);
        $('#adata > tbody').append(first);
        var current_usedlabtestcount = $('#usedlabtestcount').val();
        var new_usedlabtestcount = current_usedlabtestcount+propercount+",";
        $('#usedlabtestcount').val(new_usedlabtestcount);
        $('#labtestcount').val(labtestcount);
      });
			var labtestcount    = <?php echo $labtest_count;?>;
			var propercount 	= <?php echo $labtest_count;?>;
						//ON CLICK OF SAVE BUTTON
			$(document).on('click', '#saveAndEdit', function () {
				var numberOfLabTests = 0;
				var current_usedlabtestcount = $('#usedlabtestcount').val();//ID OF ALL THE FORM ROW ELEMENTS PRESENT SEPERATED BY COMMAS. EG: 1,2,3,
				var result = current_usedlabtestcount.split(',');
				labtestcount = $('#labtestcount').val(); //TOTAL NUMBER OF LAB TEST FIELDS PRESENT CURRENTLY ON THE PAGE
				//bootbox.alert(current_usedlabtestcount);
				for (i = 0; i < labtestcount; ++i) {
					var labtest_name = $('#labtestName'+result[i]).val();
					
					if(!labtest_name == ""){
						if(labtest_name.replace(/\s+/g, '') == ""){
					 		bootbox.alert("Lab Test name cannot be left blank");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#labtestName'+result[i]).focus();
							});
					 		$('#labtestName'+result[i]).val("");
							return false;
				 		}
						numberOfLabTests = numberOfLabTests + 1;
						var current_doctor_name = $('#doctorName'+result[i]).val();
						if(current_doctor_name.replace(/\s+/g, '') == ""){
							bootbox.alert("Please enter the doctors name");
							$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#doctorName'+result[i]).focus();
							});
							$('#doctorName'+result[i]).val("");
							return false;
						}
					}
				}
				if(numberOfLabTests == 0){
					bootbox.alert("Please enter atleast one lab tests to continue.");
					$('.bootbox').on('hidden.bs.modal', function() { 
						$('#labtestName1').focus();	    
					});
					return false;
				}
				$('#frm_edit_labtest').submit();
			});
			//FINISHED ADDING
			$('#finished_adding').click(function(){
				window.location.href = "finishedadding.php";
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
                var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
		});
		</script>
    </body>
    <?php
    include('include/unset_session.php');
	?>
</html>