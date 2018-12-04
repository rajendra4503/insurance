<?php
session_start();
ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;
include('include/configinc.php');
include('include/session.php');
include('include/functions.php');
  mysql_set_charset('utf8');
  if( !empty( $_REQUEST['lang']) && $_REQUEST['lang'] != '' ){
    $table = $_REQUEST['lang'];
    if(table_exists($table)){
      $query  = mysql_query("SELECT FieldNo ,$table  FROM $table WHERE ScreenNo='004'");
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
		<title>Plan Piper - Plan Users</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link type="text/css" href="css/bootstrap-timepicker.min.css" />
    <style type="text/css">
      .toolbar {
      float: left;
      }
      .dataTables_filter {
      text-align: left !important;
      }
    </style>  
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
        $title = "Member List";
        $button="Add a Family Member";
      }
      else
      {
        $title = "CLAIM SEARCH";
        $button="Add a New Patient";
      }
      ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0"><div id="plantitle">
      <span><?php echo $title;?>  <?php if(isset($PatientList) &&  $PatientList !=''){echo '('.$PatientList.')';}?>  </span></div></div>


    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="margin-top:5px;" id="mainplanlistdiv">
 
      <div class="col-sm-6"> 
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
          <label style="width:95px;" for="inputEmail">Claim ID :</label>
          <input name="ClaimID" class="form-control" type="text" aria-label="Search" style="width: 300px;" required>
          <button name="ClaimIDSubmit" class="btn btn-primary" type="submit">Search</button>
        </form>
      </div>

      <div class="col-sm-6"> 
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
          <label style="width:110px;" for="inputEmail">Patient Name :</label>
          <input name="PatientName" class="form-control" type="text" aria-label="Search" style="width: 300px;" required>
          <button name="PatientNameSubmit" class="btn btn-primary" type="submit">Search</button>
        </form>
      </div>

      <div>&nbsp;</div>

      <div class="col-sm-6"> 
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
          <label style="width:95px;" for="inputEmail">Patient Ins Id :</label>
         <input name="PatientInsId" class="form-control" type="text" aria-label="Search" style="width: 300px;" required>
          <button name="PatientInsIdSubmit" class="btn btn-primary" type="submit">Search</button>
        </form>
      </div>


      <div class="col-sm-6"> 
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
          <label style="width:110px;" for="inputEmail">Hospital Name :</label>
         <input name="Hospital_Name" class="form-control" type="text" aria-label="Search" style="width: 300px;" required>
            <button name="HospitalNameSubmit" class="btn btn-primary" type="submit">Search</button>
        </form>
      </div>

      <div style="clear: both;"> &nbsp;</div>

       <div class="col-sm-12 col-md-offset-1">

          <label class="col-sm-2" for="inputEmail" style="margin-top:10px;">Admission Date :</label>
          <div class="col-sm-10">
          <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
               <label for="inputEmail">From&nbsp;&nbsp;</label>
               <input name="admission_from" id="admission_from" class="form-control" type="text" aria-label="Search" style="width: 200px;" required>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <label for="inputEmail">To&nbsp;&nbsp;</label>
                <input name="admission_to" id="admission_to" class="form-control" type="text" aria-label="Search" style="width: 200px;" required>
              <button name="AdmissionSubmit" class="btn btn-primary" type="submit">Search</button>
          </form>
        </div>
        </div>

          <div style="clear: both;">&nbsp;&nbsp;&nbsp;</div>

          <div class="col-sm-12 col-md-offset-1">
 
          <label class="col-sm-2" for="inputEmail" style="margin-top:10px;">Submission Date :</label>
          <div class="col-sm-10">
          <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
               <label for="inputEmail">From&nbsp;&nbsp;</label>
               <input name="submission_from" id="submission_from" class="form-control" type="text" aria-label="Search" style="width: 200px;" required>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <label for="inputEmail">To&nbsp;&nbsp;</label>
                <input name="submission_to" id="submission_to" class="form-control" type="text" aria-label="Search" style="width: 200px;" required>
              <button name="SubmissionSubmit" class="btn btn-primary" type="submit">Search</button>
          </form>
        </div>
      </div>

      <?php

            $query = "SELECT P.Patient_Name,P.Patient_Last_Name,P.Policy_No,P.Date_of_Admission,H.Hospital_Name,P.Claim_ID,H.Declaration_Date FROM patient_details AS P JOIN hospital_details AS H ON P.Claim_ID = H.Claim_ID";

            if(isset($_POST['ClaimID']) !=''){

               $query .= " WHERE P.Claim_ID='".$_POST['ClaimID']."'";

            }elseif (isset($_POST['PatientName']) !='') {

               $PatientName = $_POST['PatientName'];

               $query .= " WHERE P.Patient_Name LIKE '%".$PatientName."%' OR P.Patient_Last_Name LIKE '%".$PatientName."%'";

            }elseif (isset($_POST['PatientInsId']) !=''){

               $query .= " WHERE P.Policy_No='".$_POST['PatientInsId']."'";

            }elseif (isset($_POST['Hospital_Name']) !='') {

               $Hospital_Name = $_POST['Hospital_Name'];

               $query .= " WHERE H.Hospital_Name LIKE '%".$Hospital_Name."%'";

            }elseif(!empty($_POST['admission_from']) !='' && !empty($_POST['admission_to']) !=''){
                $from = $_POST['admission_from'];

                $to = $_POST['admission_to'];

                $query .= " WHERE P.Date_of_Admission BETWEEN  '".$from."' AND '".$to ."'";

            }elseif(!empty($_POST['submission_from']) !='' && !empty($_POST['submission_to']) !=''){
                $from = $_POST['submission_from'];

                $to   = $_POST['submission_to'];
                
                $query .= " WHERE H.Declaration_Date BETWEEN  '".$from."' AND '".$to ."'";
            }

            $query .= " ORDER BY P.Claim_ID DESC";

            $results = mysql_query($query);

            if(isset($_POST['ClaimID']) !=''){
              echo "<div style='text-align: center;font-size: 20px;font-weight: bold;'>Claim ID : ".$_POST['ClaimID']." </div>";
            }elseif (isset($_POST['PatientName']) !='') {
               echo "<div style='text-align: center;font-size: 20px;font-weight: bold;'>Patient Name : ".$_POST['PatientName']." </div>";
            }elseif (isset($_POST['PatientInsId']) !='') {
               echo "<div style='text-align: center;font-size: 20px;font-weight: bold;'>Patient Ins Id : ".$_POST['PatientInsId']." </div>";
            }elseif (isset($_POST['Hospital_Name']) !='') {
               echo "<div style='text-align: center;font-size: 20px;font-weight: bold;'>Hospital Name : ".$_POST['Hospital_Name']." </div>";
            }
      ?>



          <div class="table-responsive">
              <table id="example" class="table table-bordered">
              <thead>
              <tr class="tableheadings">
                  <th>#</th>
                  <th>Patient Name</th>
                  <th>Claim ID</th>
                  <th>Patient Policy ID</th>
                  <th>Hospital Name</th>
                  <th>Admission Date</th>
                  <th>Submission Date</th>
                  <th>View</th>
              </tr>
            </thead>
              <tbody>
                <?php 
                  $i = 1;
                  while($result = mysql_fetch_assoc($results)){ ?>
                <tr class="tablecontents" id="<?php echo $result['Claim_ID']?>">

                <td><?php echo $i;?></td>

                <td><?php echo $result['Patient_Name'].' '.$result['Patient_Last_Name'];?></td>

                <td><?php echo $result['Claim_ID']?></td>

                <td style="text-align:left;padding-left:5px;"><?php echo $result['Policy_No']?></td>

                <td>
                   <?php echo $result['Hospital_Name']?>
                </td>

                <td>
                   <?php echo date("Y-m-d", strtotime($result['Date_of_Admission']));?>
                </td>

                <td>
                  <?php echo date("Y-m-d", strtotime($result['Declaration_Date']));?>
                </td>

                <td id="<?php echo $result['Claim_ID']?>" title='Review Patient' class='reviewuser' style="cursor:pointer;"><span class="glyphicon glyphicon-folder-open" aria-hidden="true" style='color:#CE8C11;'></span></td>

              </tr>
                <?php $i++;} ?>
           
              </tbody>
          </table>
          </div>
        </div>
		 	</section>
		  </div>
		</div>		
	</div><!-- big_wrapper ends -->
      
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/bootbox.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
  <script type="text/javascript" src="js/bootstrap-timepicker.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script type="text/javascript">

    $(function() {
        $( "#submission_from" ).datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth : true,
            changeYear : true,
            yearRange: '-100y:c+nn',
            maxDate: '-1d'
        });
    });

    $(function() {
        $( "#submission_to" ).datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth : true,
            changeYear : true,
            yearRange: '-100y:c+nn',
            maxDate: '-1d'
        });
    });

    $(function() {
        $( "#admission_from" ).datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth : true,
            changeYear : true,
            yearRange: '-100y:c+nn',
            maxDate: '-1d'
        });
    });

    $(function() {
        $( "#admission_to" ).datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth : true,
            changeYear : true,
            yearRange: '-100y:c+nn',
            maxDate: '-1d'
        });
    });

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
 
    $('#example').DataTable({
        'select': true,
        'ordering': false,
        'info': false,
        'scrollY':500,
        'scrollCollapse': true,
        "paging":false,
        "dom": '<"toolbar">frtip',
        "searching": false,
        'language': {
          searchPlaceholder: "Search Patients By Patient Name Or Email.<?php if(isset($SearchPatients) &&  $SearchPatients !=''){echo '('.$SearchPatients.')';}?>",
          search: "_INPUT_"
        },
         "initComplete": function () {
          $('.dataTables_filter input[type="search"]').css({ 'width': '310px', 'display': 'inline-block' })
          }
    });

	$(document).ready(function() {
         var w = window.innerWidth;
        var h = window.innerHeight;
        var total = h - 150;
        var each = total/12;
        $('.navbar_li').height(each);
        $('.navbar_href').height(each/2);
        $('.navbar_href').css('padding-top', each/4.9);
        var currentpage = "patients_claim";
        $('#'+currentpage).addClass('active');
        $('#plapiper_pagename').html("Patients");

        var windowheight = h;
        var available_height = h - 150;
        $('#mainplanlistdiv').height(available_height);
        $('.viewactiveplans').click(function(){
        	var userid = $(this).attr('id');
          //bootbox.alert(plancode);
          window.location.href = "ajax_validation.php?type=view_active_plans&userid="+userid;
        });
        $('.editthisuser').click(function(){
          var userid = $(this).attr('id');
          //bootbox.alert(userid);
           window.location.href = "ajax_validation.php?type=edit_plan_user&userid="+userid;
         });

        $('.reviewuser').click(function(){
           var claimID = $(this).attr('id');
           window.location.href = "claim_view.php?claimID="+claimID;
        });

        $('.deactivatethisuser').click(function(){
          var userid = $(this).attr('id');
          //bootbox.alert(userid);
          var merchantid = '<?php echo $logged_merchantid;?>';
          //bootbox.alert(merchantid);
          var deact = confirm("This patient will be deactivated. Click OK to continue");
          if(deact == true){
          var dataString = "type=deactivate_plan_user&userid="+userid+"&merchantid="+merchantid;
          $.ajax({
              type    : 'POST', 
              url     : 'ajax_validation.php', 
              crossDomain : true,
              data    : dataString,
              dataType  : 'json', 
              async   : false,
              success : function (response)
                { 
                  alert("Patient successfully deactivated.");
                  window.location.href = "plan_users.php";
                },
              error: function(error)
              {
                
              }
            }); 
          } else{
            
          }
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