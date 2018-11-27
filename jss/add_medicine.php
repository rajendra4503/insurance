<?php
session_start();
ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php');
include('include/functions.php');
if((isset($_REQUEST['delete_id'])) && (!empty($_REQUEST['delete_id']))){
    $delete_id = $_REQUEST['delete_id'];
    $delete_query = mysql_query("delete from MERCHANT_MEDICINE_LIST where ID = '$delete_id'");
    header("Location:add_medicine.php?s=y");
}
if((isset($_REQUEST['medicine'])) && (!empty($_REQUEST['medicine']))){
  //echo "<pre>"; print_r($_REQUEST);exit;
  $medicinename = ucfirst($_REQUEST['medicine']);
      //Checking if the entered medicine already exists in the medicine database.
      //$medicine_exist_query = mysql_query("select MedicineName from MERCHANT_MEDICINE_LIST where MedicineName = '$medicinename' and MerchantID in ('0', '$logged_merchantid')");
       $medicine_exist_query = mysql_query("select MedicineName from MERCHANT_MEDICINE_LIST where MedicineName = '$medicinename' and MerchantID in ('$logged_merchantid')");
      $medicine_exists = mysql_num_rows($medicine_exist_query);
      if($medicine_exists > 0){

      } else {
        //If no, add
        $insert_new_medicine = mysql_query("insert into MERCHANT_MEDICINE_LIST(`MedicineName`, `MerchantID`, `CreatedDate`, `CreatedBy`) values('$medicinename', '$logged_merchantid',  now(), '$logged_userid')");
      }
}
$search_medicine = "";
if((isset($_REQUEST['query'])) && (!empty($_REQUEST['query']))){
  $search_medicine = mysql_real_escape_string($_REQUEST['query']);
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper - Add Medicine</title>
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
            $("#add_medicine_button").click();
          }
        }
      </script>
      <style type="text/css">
      .tableheadings > th{
          background-color:#5D5D5D;
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
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0"><div id="plantitle">Medicine Directory</div></div> 
    <form name="frm_add_medicine" id="frm_add_medicine" method="POST" action="add_medicine.php">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"style="font-size:1.1em;">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
          <div>
            GENERIC NAME:<input type="text" name="medicine" id="medicine" style="height:30px;border-radius:10px;width:60%;font-size:1.5em;padding:5px;" class="forminputs2" maxlength="50">
          </div>
          <div style="margin-top:5px;">
            BRAND NAME:<input type="text" name="medicine" id="medicine" style="height:30px;border-radius:10px;width:60%;font-size:1.5em;padding:5px;" class="forminputs2" maxlength="50">
          </div>
          <div style="margin-top:5px;">
            COMPANY NAME:<input type="text" name="medicine" id="medicine" style="height:30px;border-radius:10px;width:60%;font-size:1.5em;padding:5px;" class="forminputs2" maxlength="50">
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
          * Add a Medicine into the directory by typing it here. <br>
          Once added the medicine names will <br>
          appear while adding medicines in the plans.
        </div>
      </div>
    </form>
    <div align="center" ><!-- Add a Medicine into the directory by typing it here. --></div>
    <!-- <form name="frm_add_medicine" id="frm_add_medicine" method="POST" action="add_medicine.php">
          <div align="center" style="margin:10px;"><strong>ADD A MEDICINE</strong> :- <span style="border:1px solid #004F35;padding:20px;border-radius:10px;padding-left:0px;padding-right:0px;"><input type="text" name="medicine" id="medicine" style="border:none;height:50px;border-radius:10px;width:60%;font-size:1.5em;padding:5px;" maxlength="50" onkeypress='keychk(event)'><img src="images/add_green.png" height="30px" width="auto" id="add_medicine_button" style="cursor:pointer;margin-right:5px;" title="Add to Directory"></div>
    </form> -->
    <div align="center" style="font-size:1.1em;color:#7C0000;">
      <?php 
        if((isset($_REQUEST['s'])) && (!empty($_REQUEST['s']))){
          echo "Medicine Deleted Successfully";
          echo "<a href='add_medicine.php'><img src='images/delete.png' height='10px' width='auto' style='cursor:pointer;margin-left:5px;'></a>";
        }
      ?>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="margin-top:5px;" id="mainplanlistdiv">      
              <div class="table-responsive">
              <table class="table table-bordered">
              <tr class="tableheadings" style="height:45px;">
                <th>#</th>
                <th>Medicine Name <input type="text" placeholder="Search By Medicine Name" name="search_medicine" id="search_medicine" value='<?php echo $search_medicine;?>'>
                  <?php 
                  if((isset($_REQUEST['query'])) && (!empty($_REQUEST['query']))){
                    ?>
                    <a href='add_medicine.php'><span style="background-color:#4C7C6C;padding:5px;padding-top:0px;padding-bottom:8px;border-radius:5px;cursor:pointer;"><img src="images/delete_white.png" height="20px" width="auto" style="margin-top: 6px;"></span></a>
                    <?php } else {
                      ?>
                      <span style="background-color:#4C7C6C;padding:5px;padding-top:0px;padding-bottom:8px;border-radius:5px;cursor:pointer;" class='search_button'><img src="images/findw.png" height="20px" width="auto" style="margin-top: 6px;"></span>
                      <?php
                      }?>
                

                </th>
                <th>Date Added</th>
                <th>Action</th>
              </tr>
              <?php 
                  if((isset($_REQUEST['query'])) && (!empty($_REQUEST['query']))){
                    echo "<tr><td></td><td colspan='3' style='font-size:1.2em;'><strong>Search Result For '$search_medicine' :-</strong></td></tr>";
                  }
                  $get_medicines_query    = "select ID, MedicineName, CreatedDate from MERCHANT_MEDICINE_LIST where MerchantID ='$logged_merchantid'";
                  if((isset($_REQUEST['query'])) && (!empty($_REQUEST['query']))){
                    $get_medicines_query .= " and MedicineName like '%$search_medicine%'";
                  }
                  $get_medicines_query .= " order by MedicineName";

                  $get_medicines = mysql_query($get_medicines_query);
                  $medicine_count   = mysql_num_rows($get_medicines);
                  $count = 1;
                  $medicine_options   = "";
                  if($medicine_count > 0){
                    while ($medicines = mysql_fetch_array($get_medicines)) {
                      $medicine_id    = $medicines['ID'];
                      $medicine_name    = $medicines['MedicineName'];
                      $created_date     = date('jS M Y',strtotime($medicines['CreatedDate']));
                      ?>
                          <tr class="tablecontents">
                             <td><?php echo $count;?></td>
                             <td style="text-align:left;"><?php echo $medicine_name;?></td>
                             <td><?php echo $created_date;?></td>
                             <td><img src="images/delete_red.png" height="22px" width="auto" style="cursor:pointer;" id="<?php echo $medicine_id;?>" class='delete_medicine'></td>
                          </tr>
                      <?php
                      $count++;
                    }
                  } else {
                    echo "<tr><td colspan='4' style='text-align:center;'>No Records</td></tr>";
                  }
              ?>
          </table>
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
  <script type="text/javascript" src="js/bootbox.min.js"></script>
    <script type="text/javascript">
	$(document).ready(function() {
         var w = window.innerWidth;
        var h = window.innerHeight;
        var total = h - 200;
        var each = total/12;
        $('.navbar_li').height(each);
        $('.navbar_href').height(each/2);
        $('.navbar_href').css('padding-top', each/2.8);
        var currentpage = "medicine";
        $('#'+currentpage).addClass('active');
        $('#plapiper_pagename').html("Add Medicine");

        var windowheight = h;
        var available_height = h - 210;
        $('#mainplanlistdiv').height(available_height);
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
        $('#add_medicine_button').click(function(){
          var medicine = $('#medicine').val();
          medicine       = medicine.replace(/ /g,''); //To check if the variable contains only spaces
          if(medicine == ''){
              $('#medicine').val('');
              $('#medicine').focus();
              alert("Please enter a medicine name to continue");
              return false;
          }
          $('#frm_add_medicine').submit();
        });
        $('.search_button').click(function(){
          var search_medicine = $('#search_medicine').val();
          search_medicine       = search_medicine.replace(/ /g,''); //To check if the variable contains only spaces
          if(search_medicine == ''){
              $('#search_medicine').val('');
              $('#search_medicine').focus();
              alert("Please enter a search term to continue");
              return false;
          }
          window.location.href = "add_medicine.php?query="+search_medicine;
        });
        $('.delete_medicine').click(function(){
          var medicine_id = $(this).attr('id');
          //alert(medicine_id);
          var confirm_delete = confirm("This medicine will be deleted. Click OK to continue");
          if(confirm_delete == true){
            window.location.href = "add_medicine.php?delete_id="+medicine_id;
          }else {

          }
        });
        var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
	});
	</script>
</body>
</html>