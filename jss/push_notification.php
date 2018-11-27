<?php
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php');
//echo "<pre>";print_r($_SESSION);exit;
if(isset($logged_userid)){

} else {
	header("Location:logout.php");
}
if((isset($_REQUEST['title'])) && (!empty($_REQUEST['title']))){
	//echo "<pre>"; print_r($_REQUEST);exit;
	if(isset($_POST['selectall'])){
		$selectall          = $_POST['selectall'];
	} else {
		$selectall = 0;
	}

	$title = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['title'])));
	$description = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['description'])));
	$link = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['link'])));
	$uploadedfile        = (empty($_FILES['uploadedfile']['name'])) ? ''    : $_FILES['uploadedfile']['name'];
	$path               = "uploads/generalnotification/";

    //echo $path;exit;
    if(!is_dir($path)){
        mkdir($path, 0777, true);
      }
      $fullfilename = "";	
      if($uploadedfile){
      $date          = round(microtime(true)*1000);
      $imgtype       = explode('.', $uploadedfile);
      $ext           = end($imgtype);
      $filename      = $imgtype[0];
      $fullfilename  = $date.".".$ext;
      $fullpath      = $path . $fullfilename;
      move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $fullpath);
  		}
	//Attachment Upload code end

	$i = 0;
	$tmp = mt_rand(1,9);
	do {
	    $tmp .= mt_rand(0, 9);
	} while(++$i < 14);
$notif_id = $tmp;
	$insert_to_details = mysql_query("insert into MERCHANT_NOTIFICATIONS_DETAILS (NotificationID, NotificationTitle, NotificationContent, DocumentName, Link, CreatedDate, CreatedBy) values ('$notif_id','$title','$description','$fullfilename','$link',now(),'$logged_userid')");
	$useridarray = array();
	if($selectall == "1"){
				$get_clients      = "select distinct t1.UserID, concat(t2.FirstName,' ',t2.Lastname,' - ',t1.EmailID,' - +', TRIM(LEADING '0' FROM t1.MobileNo)) as name from USER_ACCESS as t1, USER_DETAILS as t2, USER_MERCHANT_MAPPING as t3 where t1.UserID = t2.UserID and t2.UserID = t3.UserID and t3.RoleID = '5' and t3.Status = 'A' and t1.UserStatus = 'A' and t3.MerchantID = '$logged_merchantid' and t1.OSType IN ('A','I')  and t1.DeviceID <> '' order by t2.FirstName";
		//echo $get_clients;exit;
		$get_clients_qry	= mysql_query($get_clients);
		$get_user_count	= mysql_num_rows($get_clients_qry);
		if($get_user_count)
		{
			while($rows = mysql_fetch_array($get_clients_qry))
			{
				$id 		= $rows['UserID'];
			array_push($useridarray,$id);
			}
	}} else {
	$magicsuggest       = $_POST['magicsuggest'];
	    foreach($magicsuggest as $val){
	    	array_push($useridarray, $val);
	    }
	}
	//print_r($useridarray);exit;
	if(count($useridarray) > 0){
		$notificationID = $notif_id;
		foreach ($useridarray as $userid) {
			$insert_to_users = mysql_query("insert into MERCHANT_NOTIFICATIONS (NotificationID, MerchantID, UserID, CreatedDate, CreatedBy) values ('$notif_id', '$logged_merchantid', '$userid', now(), '$logged_userid')");
		$get_user    =   "select UserID,OSType,DeviceID from USER_ACCESS where UserID='$userid'";
//echo $get_user;exit;
$get_user_qry  = mysql_query($get_user);
$get_user_count= mysql_num_rows($get_user_qry);
	if($get_user_count)
	{//echo 123;exit;
		while($user_rows = mysql_fetch_array($get_user_qry))
		{
		$user_id  		= $user_rows['UserID'];
		$user_os_type  	= $user_rows['OSType'];
		$user_device_id	= $user_rows['DeviceID'];
		//echo $user_id;exit;
			if($user_id)
			{

				//Push notification for Android and IOS
				if($user_os_type=='A' && $user_device_id!='')
				{
					
				$regId          = $user_device_id;
				$res['message'] = $title;
				$res['userid']  = $logged_companyname;
				$res['report_id']  = $notificationID;
				$res['flag']  	= "general_notification";
				$message        = json_encode($res); 
				include("gcm_server_php/send_message.php");
				}
				else if($user_os_type=='I' && $user_device_id!='')
				{

				$deviceToken= $user_device_id;
				//echo "<br>";
				$userid  	= $logged_companyname;
				//echo "<br>";exit;
				$flag 		= "general_notification";
				$report_id  = 	$notificationID;
				$message 	= $title;

				//echo "Token: ".$deviceToken."<br>"."UserID: ".$userid."<br>"."Flag: ".$flag."<br>"."Message: ".$message;exit;

				//include("apple/local/push.php");
				include("apple/production/push.php");
				}
			}
		}
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
		<title>Plan Piper - Push Notification</title>
		<link rel="stylesheet" type="text/css" href="css/jasny-bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/magicsuggest.css">
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
		 	<section>
		 		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0"><div id="plantitle">Send Notification</div></div>
		 		<div class="col-lg-offset-1 col-lg-10 col-lg-offset-1 col-md-offset-1 col-md-10 col-md-offset-1 col-sm-12 col-xs-12" id="mainplanlistdiv">
		 		<form name="notification_form" id="notification_form" method="post" enctype="multipart/form-data" action="push_notification.php">
			 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="margin-top:20px;">
			 	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
				 	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 paddingrl0">
				 		<div id="magicsuggest" name="magicsuggest" style="width:100%;"></div>
				 	</div>
				 	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
				 		<input type="checkbox" name='selectall' value='1' class='selectall' id='selectall'> Send To all
				 	</div>			 		
			 	</div>
			 	<input type="text" class="forminputs3 firstlettercaps" placeholder="Title" name="title" id="title" maxlength="30" style='height:35px;border:1px solid #004f35;margin-top:10px;'>
			 	<textarea class="forminputstextarea" id="description" name="description" rows="3" maxlength='100' style="margin-top:10px;border:1px solid #004f35;margin-bottom:4px;" placeholder="Enter Notification Content here"></textarea>
				
		 		<div class="fileinput fileinput-new input-group col-xs-12" data-provides="fileinput" style="width:100%;height:35px;border:1px solid #004f35;margin-top:5px;">
                      <div class="form-control" data-trigger="fileinput" style="border:none;"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                      <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Click To Upload A Document (Optional)</span><span class="fileinput-exists">Change</span><input type="file"  name="uploadedfile" id="uploadedfile"></span>
                      <a href="#" class="input-group-addon btn btn-default fileinput-exists removelinkbutton" data-dismiss="fileinput">Remove</a>
                    </div>
                <input type="text" class="forminputs3" placeholder="Enter a Link (Optional)" name="link" id="link" maxlength="250" style='height:35px;border:1px solid #004f35;'>
		 		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="center" style="margin-top:25px;">
						    	<div class="errormessages" id="notificationerror"></div>
						    </div></div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="ActionBar2" align="center" style="margin-top:25px;">
						        <button id="sendnotificationbutton" class="formbuttonsmall">SEND</button>
						        </div>
				</form>		      
				</div>
		 	</section>
		 </div>
		</div>		
	</div><!-- big_wrapper ends -->
      
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/magicsuggest.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/ajax_city_state.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/modernizr.js"></script>
	<script type="text/javascript" src="js/placeholders.min.js"></script>
	<script type="text/javascript" src="js/bootbox.min.js"></script>
	<script type="text/javascript" src='js/get_user_timezone.js'></script>
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
		    var windowheight = h;
	        var available_height = h - 120;
	        $('#mainplanlistdiv').height(available_height);

			$('#magicsuggest').magicSuggest({
		        allowDuplicates: false,
		        allowFreeEntries: false,
		        selectionPosition: 'bottom',
		        name: 'magicsuggest',
		        data: 'ajax_get_active_patients.php',
		        placeholder : 'Search for your active patients',
		        maxSelection : null,
		        ajaxConfig: {
		            xhrFields: {
		            withCredentials: true,
		            }
		        }
		    });

    $('#selectall').click(function(event) {  //on click 
        if(this.checked) { // check select status
            $('#magicsuggest').css("opacity",'0.3');
        	$('#magicsuggest').css("pointer-events",'none');
        }else{
            $('#magicsuggest').css("opacity",'1');
        	$('#magicsuggest').css("pointer-events",'all');      
        }
    });
		var currentpage = "notification";
    		$('#'+currentpage).addClass('active');
    		$('#plapiper_pagename').html("");
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
	        $('#sendnotificationbutton').click(function(){
	        	if($('#selectall').prop("checked") != true){
	            //alert($('div.ms-sel-item').length);
	            if(!$('div.ms-sel-item').length){
	              alert("Please select members to continue");  
	              return false;
	            }
	        }
		    var title = $('#title').val();
			title       = title.replace(/ /g,''); //To check if the variable contains only spaces
	        if(title == ''){
	            $('#title').val('');
	            $('#title').focus();
	            $("#notificationerror").fadeIn();
	            $("#notificationerror").text("Please enter the title.");
	            $("#notificationerror").fadeOut(5000);
	            return false;
	        }
	        var description = $('#description').val();
			description       = description.replace(/ /g,''); //To check if the variable contains only spaces
	        if(description == ''){
	            $('#description').val('');
	            $('#description').focus();
	            $("#notificationerror").fadeIn();
	            $("#notificationerror").text("Please enter the description.");
	            $("#notificationerror").fadeOut(5000);
	            return false;
	        }
	        var link 	= $('#link').val();
			link      	= link.replace(/ /g,''); //To check if the variable contains only spaces
			if(link!="")
			{
				var myRegExp =/^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i;
				var urlToValidate = link;
				if (!myRegExp.test(urlToValidate)){
	            $('#link').focus();
	            $("#notificationerror").fadeIn();
	            $("#notificationerror").text("Please enter a valid link.");
	            $("#notificationerror").fadeOut(5000);
		        return false;
				}
			}
	        $('#notification_form').submit();
	        });
	            var fileTypes = ['pdf', 'doc', 'jpg', 'jpeg', 'png', 'txt'];  //acceptable file types
		        function readURL(input) {
		            if (input.files && input.files[0]) {
		                var extension = input.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
		                isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
		                if (isSuccess) {
		                //var reader = new FileReader();        
		                //reader.onload = function (e) {
		                    //$('#frm_upload_report').submit(); //To upload to server directly
		                //}          
		                //reader.readAsDataURL(input.files[0]);
		            } else {
		                alert("Attachment should be in doc, pdf, txt, jpg, png or jpeg format.");
		                $('#uploadedfile').replaceWith($('#uploadedfile').val('').clone(true));
		                return false;
		            }
		        }
		        }
	        $("#uploadedfile").change(function(){
		            readURL(this);
		        });
         var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
    		});
	</script>
	</body>
</html>