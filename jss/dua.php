<?php
session_start();
include('include/configinc.php');
include('include/session.php');

if((isset($_REQUEST['hidden_value'])) && (!empty($_REQUEST['hidden_value']))){
	$user_to_be_deleted = $_REQUEST['user_select'];
	//echo $user_to_be_deleted;
	$del_desktop = mysql_query("delete from DESKTOP_NOTIFICATION where UserID = '$user_to_be_deleted'");
	
	//Get MYFOLDER_PARENT ids
	$get_myfolder_parent = mysql_query("select ID from MYFOLDER_PARENT where UserID = '$user_to_be_deleted'");
	$get_myfolder_count  = mysql_num_rows($get_myfolder_parent);
	if($get_myfolder_count > 0){
		while ($myfolder_row = mysql_fetch_array($get_myfolder_parent)) {
			$parent_id   = $myfolder_row['ID'];
			//$del_child = mysql_query("delete from MYFOLDER_CHILD where MyFolderParentID = '$parent_id'");
		}
	//$del_desktop = mysql_query("delete from MYFOLDER_PARENT where UserID = '$user_to_be_deleted'");
	}
	
	$del_appd   	= mysql_query("delete from USER_APPOINTMENT_DETAILS where UserID = '$user_to_be_deleted'");
	$del_apph 		= mysql_query("delete from USER_APPOINTMENT_HEADER where UserID = '$user_to_be_deleted'");
	
	$del_uidfc 		= mysql_query("delete from USER_INSTRUCTION_DATA_FROM_CLIENT where UserID = '$user_to_be_deleted'");
	$del_uid 		= mysql_query("delete from USER_INSTRUCTION_DETAILS where UserID = '$user_to_be_deleted'");
	$del_uih 		= mysql_query("delete from USER_INSTRUCTION_HEADER where UserID = '$user_to_be_deleted'");
	
	$del_ultd 		= mysql_query("delete from USER_LAB_TEST_DETAILS1 where UserID = '$user_to_be_deleted'");
	$del_ulth 		= mysql_query("delete from USER_LAB_TEST_HEADER1 where UserID = '$user_to_be_deleted'");

	$del_midfc 		= mysql_query("delete from USER_MEDICATION_DATA_FROM_CLIENT where UserID = '$user_to_be_deleted'");
	$del_mid 		= mysql_query("delete from USER_MEDICATION_DETAILS where UserID = '$user_to_be_deleted'");
	$del_mih 		= mysql_query("delete from USER_MEDICATION_HEADER where UserID = '$user_to_be_deleted'");

	$del_sidfc 		= mysql_query("delete from USER_SELF_TEST_DATA_FROM_CLIENT where UserID = '$user_to_be_deleted'");
	$del_stth 		= mysql_query("delete from USER_SELF_TEST_DETAILS where UserID = '$user_to_be_deleted'");
	$del_sth 		= mysql_query("delete from USER_SELF_TEST_HEADER where UserID = '$user_to_be_deleted'");
	$del_sph 		= mysql_query("delete from USER_SELF_PLAN_HEADER where UserID = '$user_to_be_deleted'");
	$del_usph 		= mysql_query("delete from USER_SELF_PLAN_ACTIVITIES where UserID = '$user_to_be_deleted'");
	
	$del_uph 		= mysql_query("delete from USER_PLAN_HEADER where UserID = '$user_to_be_deleted'");

	$del_upm 		= mysql_query("delete from USER_PLAN_MAPPING where UserID = '$user_to_be_deleted'");

	//mysql_query("delete from USER_ACCESS where UserID = '$user_to_be_deleted'");
$del_access 	= mysql_query("update USER_ACCESS set `Password`='planpiper', `PasswordStatus`='0', `OSType`='', `DeviceID`='', `OSVersion`='', `AppVersion`='', `Model`='', `DateOfInstallation`='', `PaidUntil` = '' WHERE `UserID`='$user_to_be_deleted'");

	
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper - Delete Users</title>
	</head>
	<body style="overflow:hidden;">
	<form name="frm_delete_user" id="frm_delete_user" method="POST">
	<input type="hidden" value="1" name="hidden_value" id="hidden_value">
	<select name="user_select" id="user_select">
		<!-- <option value="0" style="display:none;">Select User</option> -->
	<?php
	$get_all_users = mysql_query("select UserID, MobileNo, EmailID from USER_ACCESS order by CreatedDate desc");
	$get_user_count = mysql_num_rows($get_all_users);
	//echo $get_user_count;	
	if($get_user_count > 0){
		while ($user_row = mysql_fetch_array($get_all_users)) {
			$userid  	= $user_row['UserID'];
			$mobile 	= $user_row['MobileNo'];
			$email 		= $user_row['EmailID'];

		//Get User Details
			$get_user_details = mysql_query("select FirstName, LastName from USER_DETAILS where UserID = '$userid'");
				$firstname 	= "";
				$lastname 	= "";
			while ($row = mysql_fetch_array($get_user_details)) {
				$firstname 	= $row['FirstName'];
				$lastname 	= $row['LastName'];
			}
				
				echo "<option value='$userid'>$firstname $lastname - $mobile - $email</option>";
		}
	} else {
		echo "No Users";
	}
	?>	
	</select>
	</form>
	<button class="deleteplan">Delete</button>
<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.deleteplan').click(function(){
			//alert(1);
			var dltcnfrm = confirm("All the plans assigned to this user will be deleted. Click OK to continue.");
			if(dltcnfrm == true){
				$("#frm_delete_user").submit();
			}else {

			}
		});
	});
	</script>
	</body>
</html>
