<?php
session_start();
include('include/configinc.php');
include('include/session.php');
include('include/functions.php');

//GET MEDICINE TYPES
$medicine_type_options = "";
$get_medicine_types = mysql_query("select SNo, MedicineType from MEDICINE_TYPES where SNo != '0'");
$type_count = mysql_num_rows($get_medicine_types);
if($type_count > 0){
  while ($medtype = mysql_fetch_array($get_medicine_types)) {
    $medtype_id     = $medtype['SNo'];
    $medtype_name   = $medtype['MedicineType'];
    $medicine_type_options .= "<option value='$medtype_id'>$medtype_name</option>";
  }
}

//GET COUNTRY CODE
  $det_countrycode = "";  
  $get_profile_details = mysql_query("select t1.FirstName, t1.LastName, t1.CountryCode from USER_DETAILS as t1, USER_ACCESS as t2 where t1.UserID = t2.UserID and t1.UserID = '$logged_userid'");
  //echo $get_profile_details;exit;
  $get_profile_count = mysql_num_rows($get_profile_details);
  if($get_profile_count > 0){
    while ($details = mysql_fetch_array($get_profile_details)) {
      //echo "<pre>";print_r($details);exit;
      $det_firstname    = stripslashes($details['FirstName']);
      $det_lastname     = stripslashes($details['LastName']);
      $det_countrycode  = $details['CountryCode'];
    }
  }
//echo $det_countrycode;
//SEARCH MEDICINE
$search_medicine = "";
if((isset($_REQUEST['query'])) && (!empty($_REQUEST['query']))){
  $search_medicine = mysql_real_escape_string($_REQUEST['query']);
}

//ADD TO DATABASE
if((isset($_REQUEST['generic_name'])) && (!empty($_REQUEST['generic_name']))){
  //echo "<pre>"; print_r($_REQUEST);exit;

  $addoredit           = $_REQUEST['addoredit'];
  $medicine_edit_id    = $_REQUEST['medicine_edit_id'];

  //DELETE FROM CHILD TABLE DURING EDIT MEDICINE
  if(($addoredit == "edit")&&($medicine_edit_id != "")){
    $delete_query = mysql_query("delete from MERCHANT_MEDICINE_LIST where MedicineID = '$medicine_edit_id'");
  }

  $generic_name        = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['generic_name'])));
  $brand_name          = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['brand_name'])));
  $medicine_type       = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['medicine_type'])));
  $medicine_mass       = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['medicine_mass'])));
  $company_name        = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['company_name'])));
  $quantity            = "";  
  if($medicine_type == 2){
    $quantity          = $medicine_mass." mg";
  }

  //SEARCH FOR AN EXACT MATCH IN MASTER TABLE UNDER THE SAME MERCHANT
  $match_query  = mysql_query("select count(*) from MEDICINE_LIST as t1, MERCHANT_MEDICINE_LIST as t2 where t1.GenericName = '$generic_name' and t1.BrandName = '$brand_name' and t1.MedicineType = '$medicine_type' and t1.Quantity = '$quantity' and t1.CompanyName = '$company_name' and t1.MedicineID = t2.MedicineID and t2.MerchantID='$logged_merchantid'");
  $match_count  = mysql_result($match_query, "0");
  //echo $match_count;
  if($match_count > 0){
    ?>
    <script type="text/javascript">
      alert("Medicine Already exists in your Directory.");
      window.location.href = "medicine_directory.php";
    </script>
    <?php
  }else {

    //CHECK IF SAME MEDICINE EXISTS IN MASTER TABLE. IF YES, GET ID

    $get_match_id = mysql_query("select MedicineID from MEDICINE_LIST where GenericName = '$generic_name' and BrandName = '$brand_name' and MedicineType = '$medicine_type' and Quantity = '$quantity' and BrandName = '$brand_name' and MedicineType = '$medicine_type'");
    $match_num  = mysql_num_rows($get_match_id);
    if($match_num > 0){
      $random_medicine_id = mysql_result($get_match_id, "0");
    } else {
      //INSERT INTO MASTER LIST
      $random_medicine_id =  time();
      $random_medicine_id = $random_medicine_id.mt_rand(10000,99999);
      $insert_to_master  = mysql_query("insert into MEDICINE_LIST (`CountryCode`, `MedicineID`, `GenericName`, `BrandName`, `Quantity`, `CompanyName`, `MedicineType`, `Instruction`, `CreatedDate`, `CreatedBy`) values ('$det_countrycode', '$random_medicine_id', '$generic_name', '$brand_name', '$quantity', '$company_name', '$medicine_type','',now(),'$logged_userid')");      
    }

    //INSERT INTO CHILD
    $insert_to_child  = mysql_query("insert into MERCHANT_MEDICINE_LIST (`MerchantID`, `CountryCode`, `MedicineID`, `CreatedDate`, `CreatedBy`) values ('$logged_merchantid', '$det_countrycode', '$random_medicine_id', now(), '$logged_userid')");
    header("Location:medicine_directory.php");

  }
}
//DELETE FROM DATABASE
if((isset($_REQUEST['delete_id'])) && (!empty($_REQUEST['delete_id']))){
    $delete_id = $_REQUEST['delete_id'];
    $delete_query = mysql_query("delete from MERCHANT_MEDICINE_LIST where MedicineID = '$delete_id'");
    header("Location:medicine_directory.php?s=y");
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
        $(".search_button").click();
      }
    }
  </script>  
    <style type="text/css">
      .tableheadings > th{
          background-color:#5D5D5D;
      }
      .close {
         color: #fff;
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
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0"><div id="plantitle">Add A Medicine</div></div> 
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" align="center" style="margin-top:10px;margin-bottom:10px;">
    		<div style="height:50px;width:250px;border:1px solid #004f35;line-height:50px;border-radius:10px;cursor:pointer;color:#004f35;font-size:18px;" class="button addmedicinebutton"><img src='images/add_green.png' height="18px" width="auto" style="margin-top:-5px;">&nbsp;&nbsp;<strong>ADD A MEDICINE</strong></div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" align="center" style="margin-bottom:5px;">
    	<input type="text" placeholder="SEARCH MEDICINE" name="search_medicine" id="search_medicine" style="padding-left:10px;"  onkeypress='keychk(event)' <?php if((isset($_REQUEST['query'])) && (!empty($_REQUEST['query']))){ $query = $_REQUEST['query']; echo "value='$query'";}?>>
      <?php 
        if((isset($_REQUEST['query'])) && (!empty($_REQUEST['query']))){
          ?>
          <a href='medicine_directory.php'><span style="background-color: #4C7C6C;padding: 7px;padding-top: 11px;padding-bottom: 12px;cursor:pointer;"><img src="images/delete_white.png" height="20px" width="auto" style="margin-top: 2px;"></span></a>
          <?php } else {
            ?>
            <span style="background-color: #4C7C6C;padding: 7px;padding-top: 11px;padding-bottom: 12px;cursor:pointer;" class='search_button'><img src="images/findw.png" height="20px" width="auto" style="margin-top: 2px;"></span>
            <?php
            }
      ?>
    </div>
        <div align="center" style="font-size:1.1em;color:#7C0000;">
      <?php 
        if((isset($_REQUEST['s'])) && (!empty($_REQUEST['s']))){
          echo "Medicine Deleted Successfully";
          echo "<a href='medicine_directory.php'><img src='images/delete.png' height='10px' width='auto' style='cursor:pointer;margin-left:5px;'></a>";
        }
      ?>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" id="mainplanlistdiv">      
              <div class="table-responsive">
              <table class="table table-bordered">
              <tr class="tableheadings" style="height:45px;">
                <th>#</th>
                <th>Medicine Name</th>
                <th>Date Added</th>
                <th>Action</th>
              </tr>
              <?php 
                  if((isset($_REQUEST['query'])) && (!empty($_REQUEST['query']))){
                    echo "<tr><td></td><td colspan='3' style='font-size:1.2em;'><strong>Search Result For '$search_medicine' :-</strong></td></tr>";
                  }
                  //$get_medicines_query    = "select ID, MedicineName, CreatedDate from MERCHANT_MEDICINE_LIST where MerchantID ='$logged_merchantid'";
                  $get_medicines_query = "select t1.MedicineID, t1.GenericName, t1.BrandName, t1.Quantity, t1.CompanyName, t1.MedicineType, t1.Instruction, t2.CreatedDate from MEDICINE_LIST as t1, MERCHANT_MEDICINE_LIST as t2 where t1.MedicineID = t2.MedicineID and t2.MerchantID = '$logged_merchantid'";
                  if((isset($_REQUEST['query'])) && (!empty($_REQUEST['query']))){
                    $get_medicines_query .= " and (t1.GenericName like '%$search_medicine%' || t1.BrandName like '%$search_medicine%' || t1.CompanyName like '%$search_medicine%')";
                  }
                  $get_medicines_query .= " order by t1.GenericName";

                  $get_medicines = mysql_query($get_medicines_query);
                  $medicine_count   = mysql_num_rows($get_medicines);
                  $count = 1;
                  $medicine_options   = "";
                  if($medicine_count > 0){
                    while ($medicines = mysql_fetch_array($get_medicines)) {
                      $medicine_id      = $medicines['MedicineID'];
                      $generic_name     = $medicines['GenericName'];
                      $brand_name       = $medicines['BrandName'];
                      $company_name     = $medicines['CompanyName'];
                      $medicine_type    = $medicines['MedicineType'];
                      $quantity         = $medicines['Quantity'];
                      $created_date     = date('jS M Y',strtotime($medicines['CreatedDate']));
                      $display_name     = $generic_name." - ".$brand_name;
                      if($medicine_type == 2){
                         $display_name     .= " (".$quantity." )";
                      }
                      $display_name     .= " - ".$company_name;
                      ?>
                          <tr class="tablecontents">
                             <td><?php echo $count;?></td>
                             <td style="text-align:left;"><?php echo $display_name;?></td>
                             <td><?php echo $created_date;?></td>
                             <td><img src="images/edit_grey.png" height="22px" width="auto" style="cursor:pointer;" id="<?php echo $medicine_id;?>" class='edit_medicine' title='Click to edit this medicine'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/delete_red.png" height="22px" width="auto" style="cursor:pointer;" id="<?php echo $medicine_id;?>" class='delete_medicine' title='Click to delete this medicine'></td>
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
		    <!--ADD MEDICINE MODAL WINDOW-->
            <div class="modal" id="addmedicinemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog" style="width:750px;">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;background-color:#004f35;" >
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h5 class="modal-title" id="modalheadings" style="background-color:#004f35;color:#F2BD43;">ADD A MEDICINE</h5>
                  </div>
                  <form name="frm_add_medicine" id="frm_add_medicine" method="POST" action="">
                    	<div align="left" style="padding-left:5px;font-family:RalewayRegular;color:#004f35;font-weight:600;margin-top:30px;">
            						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
            							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="margin-bottom:10px;">
            								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 ">
            									GENERIC NAME:
            								</div>
            								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 ">
            									<input type="text" name="generic_name" id="generic_name" class="forminputs2 medicinedirectoryinputs" maxlength="50">
            								</div>
            							</div>
            							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="margin-bottom:10px;">
            								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 ">
            									BRAND NAME:
            								</div>
            								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 paddingr0">
            									<input type="text" name="brand_name" id="brand_name" class="forminputs2 medicinedirectoryinputs" maxlength="50">
            								</div>	
            								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 paddingr0">
            									<select name="medicine_type" id="medicine_type" class="forminputs2 medicinedirectoryinputs">
                                <option value="0" style="display:none;">SELECT</option>
            										<?php echo $medicine_type_options;?>
            									</select>
            								</div>	
            								<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 paddingr0">
            									<input type="text" name="medicine_mass" id="medicine_mass" class="forminputs2 medicinedirectoryinputs massinputs onlynumbers" style="display:none;" maxlength="4">
            								</div>	
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 paddingr0">
                              <span style="display:none;" class="massinputs">mg</span>
                            </div>  					
            							</div>
            							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="margin-bottom:10px;">
            								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 ">
            									COMPANY NAME:
            								</div>
            								<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 ">
            									<input type="text" name="company_name" id="company_name" class="forminputs2 medicinedirectoryinputs" maxlength="50">
            								</div>							
            							</div>
            						</div>
  					</div>
            <input type="hidden" name="addoredit" id="addoredit" value="add">
            <input type="hidden" name="medicine_edit_id" id="medicine_edit_id" value="">
          </form>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 errormessages" align="center">
            
          </div>
       	  <div class="margin10" align="center">
            <button type="button" id="addtodirectory" class="directorybuttons">ADD</button>
            <button type="button" id="cancelbutton" class="directorybuttons">CANCEL</button>
       	  </div>
              	</div>
              </div>
            </div>
    <!--END OF ADD MEDICINE MODAL WINDOW-->
	</div><!-- big_wrapper ends -->
      
	  <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	  <script type="text/javascript" src="js/bootstrap.min.js"></script>
	  <script type="text/javascript" src="js/resample.js"></script>
  	<script type="text/javascript" src="js/modernizr.js"></script>
  	<script type="text/javascript" src="js/placeholders.min.js"></script>
  	<script type="text/javascript" src="js/bootbox.min.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
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
        $('#plapiper_pagename').html("");

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

        $('.addmedicinebutton').click(function(){
  				$('#addmedicinemodal').modal('show');
  			});

        $('#medicine_type').change(function(){
          var type_value = $('#medicine_type').val();
          if(type_value == 2){
            $('.massinputs').show();
          } else {
            $('.massinputs').hide();
          }
        });

        $('#addtodirectory').click(function(){
          var generic_name = $('#generic_name').val();
          generic_name       = generic_name.replace(/ /g,''); //To check if the variable contains only spaces
          if(generic_name == ''){
              $('#generic_name').val('');
              $('#generic_name').focus();
              $(".errormessages").fadeIn();
              $(".errormessages").text("Please enter the generic name to continue.");
              $(".errormessages").fadeOut(5000);
              return false;
          }
          var brand_name = $('#brand_name').val();
          brand_name       = brand_name.replace(/ /g,''); //To check if the variable contains only spaces
          if(brand_name == ''){
              $('#brand_name').val('');
              $('#brand_name').focus();
              $(".errormessages").fadeIn();
              $(".errormessages").text("Please enter the brand name to continue.");
              $(".errormessages").fadeOut(5000);
              return false;
          }
          var medicine_type = $('#medicine_type').val();
          if(medicine_type == '0'){
              $('#medicine_type').focus();
              $(".errormessages").fadeIn();
              $(".errormessages").text("Please enter the medicine type to continue.");
              $(".errormessages").fadeOut(5000);
              return false;
          }
          if(medicine_type == 2){
            var medicine_mass = $('#medicine_mass').val();
            medicine_mass       = medicine_mass.replace(/ /g,''); 
            if(medicine_mass == ''){
                $('#medicine_mass').val('');
                $('#medicine_mass').focus();
                $(".errormessages").fadeIn();
                $(".errormessages").text("Please enter the medicine mass to continue.");
                $(".errormessages").fadeOut(5000);
                return false;
            }
          } else {
            
          }
          var company_name = $('#company_name').val();
          company_name       = company_name.replace(/ /g,''); //To check if the variable contains only spaces
          if(company_name == ''){
              $('#company_name').val('');
              $('#company_name').focus();
              $(".errormessages").fadeIn();
              $(".errormessages").text("Please enter the company name to continue.");
              $(".errormessages").fadeOut(5000);
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
          window.location.href = "medicine_directory.php?query="+search_medicine;
        });

        //DELETE FROM MEDICINE DIRECTORY
        $('.delete_medicine').click(function(){
          var medicine_id = $(this).attr('id');
          //alert(medicine_id);
          var confirm_delete = confirm("This medicine will be deleted. Click OK to continue");
          if(confirm_delete == true){
            window.location.href = "medicine_directory.php?delete_id="+medicine_id;
          }else {

          }
        });

        //EDIT MEDICINE
        $('.edit_medicine').click(function(){
          var medicine_id = $(this).attr('id');
          //alert(medicine_id);
          var dataString = "id="+medicine_id+"&type=get_medicine_details";
          //alert(dataString);
          $.ajax({
            type        :"GET",
            url         :"ajax_validation.php",
            data        :dataString,
            dataType    :"jsonp",
            jsonp       :"jsoncallback",
            async       :false,
            crossDomain :true,
            success   : function(data,status){
              $.each(data, function(i,item){ 
                //alert(item.GenericName);
                $('#generic_name').val(item.GenericName);
                $('#brand_name').val(item.BrandName);
                $('#company_name').val(item.CompanyName);
                var mass = item.Quantity.replace(" mg","");
                $('#medicine_mass').val(mass);
                if(item.MedicineType == '2'){
                  $('.massinputs').show();
                } else {
                  $('.massinputs').hide();
                }
                $('#medicine_type').val(item.MedicineType);
                $('#addoredit').val("edit");
                $('#medicine_edit_id').val(item.MedicineID);
                $('#addtodirectory').html("UPDATE");
                $('#addmedicinemodal').modal('show');
              });
            },
            error: function(){

            }
          });
        });

        //CANCEL BUTTON CLICKED
        $('#cancelbutton').click(function(){
          window.location.href = "medicine_directory.php";
        });
        var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
	});
	</script>
</body>
</html>