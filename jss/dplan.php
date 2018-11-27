<?php
session_start();
include('include/configinc.php');
include('include/session.php');

if((isset($_REQUEST['hidden_value'])) && (!empty($_REQUEST['hidden_value']))){
	$plan_to_be_deleted = $_REQUEST['plan_select'];
	//echo $plan_to_be_deleted;
	$del_ph 		= mysql_query("delete from PLAN_HEADER  where PlanCode = '$plan_to_be_deleted'");
	$del_uph 		= mysql_query("delete from USER_PLAN_HEADER where PlanCode = '$plan_to_be_deleted'");	
	$del_ad   		= mysql_query("delete from APPOINTMENT_DETAILS where PlanCode = '$plan_to_be_deleted'");
	$del_ah 		= mysql_query("delete from APPOINTMENT_HEADER where PlanCode = '$plan_to_be_deleted'");
	$del_dd 		= mysql_query("delete from DIET_DETAILS where PlanCode = '$plan_to_be_deleted'");
	$del_dh 		= mysql_query("delete from DIET_HEADER where PlanCode = '$plan_to_be_deleted'");
	$del_ed 		= mysql_query("delete from EXERCISE_DETAILS where PlanCode = '$plan_to_be_deleted'");
	$del_eh 		= mysql_query("delete from EXERCISE_HEADER where PlanCode = '$plan_to_be_deleted'");
	$del_id 		= mysql_query("delete from INSTRUCTION_DETAILS where PlanCode = '$plan_to_be_deleted'");
	$del_ih 		= mysql_query("delete from INSTRUCTION_HEADER where PlanCode = '$plan_to_be_deleted'");
	$del_ltd 		= mysql_query("delete from LAB_TEST_DETAILS1 where PlanCode = '$plan_to_be_deleted'");
	$del_icl 		= mysql_query("delete from IVR_CALL_LOGS where PlanCode = '$plan_to_be_deleted'");
	$del_lth 		= mysql_query("delete from LAB_TEST_HEADER1 where PlanCode = '$plan_to_be_deleted'");
	$del_md 		= mysql_query("delete from MEDICATION_DETAILS where PlanCode = '$plan_to_be_deleted'");
	$del_mh 		= mysql_query("delete from MEDICATION_HEADER where PlanCode = '$plan_to_be_deleted'");
	$del_std 		= mysql_query("delete from SELF_TEST_DETAILS where PlanCode = '$plan_to_be_deleted'");
	$del_sth 		= mysql_query("delete from SELF_TEST_HEADER where PlanCode = '$plan_to_be_deleted'");
	$del_uad 		= mysql_query("delete from USER_APPOINTMENT_DETAILS where PlanCode = '$plan_to_be_deleted'");
	$del_uah 		= mysql_query("delete from USER_APPOINTMENT_HEADER where PlanCode = '$plan_to_be_deleted'");
	$del_udd 		= mysql_query("delete from USER_DIET_DETAILS where PlanCode = '$plan_to_be_deleted'");
	$del_udh 		= mysql_query("delete from USER_DIET_HEADER where PlanCode = '$plan_to_be_deleted'");
	$del_uidfc 		= mysql_query("delete from USER_INSTRUCTION_DATA_FROM_CLIENT where PlanCode = '$plan_to_be_deleted'");
	$del_uid 		= mysql_query("delete from USER_INSTRUCTION_DETAILS where PlanCode = '$plan_to_be_deleted'");
	$del_uih 		= mysql_query("delete from USER_INSTRUCTION_HEADER where PlanCode = '$plan_to_be_deleted'");	
	$del_ultd   	= mysql_query("delete from USER_LAB_TEST_DETAILS1 where PlanCode = '$plan_to_be_deleted'");
	$del_ulth 		= mysql_query("delete from USER_LAB_TEST_HEADER1 where PlanCode = '$plan_to_be_deleted'");
	$del_umdfc 		= mysql_query("delete from USER_MEDICATION_DATA_FROM_CLIENT where PlanCode = '$plan_to_be_deleted'");
	$del_uid 		= mysql_query("delete from USER_MEDICATION_DETAILS where PlanCode = '$plan_to_be_deleted'");
	$del_uih 		= mysql_query("delete from USER_MEDICATION_HEADER where PlanCode = '$plan_to_be_deleted'");
	$del_ultd 		= mysql_query("delete from USER_SELF_TEST_HEADER where PlanCode = '$plan_to_be_deleted'");
	$del_ulth 		= mysql_query("delete from USER_SELF_TEST_DETAILS where PlanCode = '$plan_to_be_deleted'");
	$del_midfc 		= mysql_query("delete from USER_SELF_TEST_DATA_FROM_CLIENT where PlanCode = '$plan_to_be_deleted'");
	$del_mid 		= mysql_query("delete from USER_SELF_PLAN_HEADER where PlanCode = '$plan_to_be_deleted'");
	$del_mih 		= mysql_query("delete from USER_PLAN_MAPPING where PlanCode = '$plan_to_be_deleted'");

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
	<form name="frm_delete_plan" id="frm_delete_plan" method="POST">
	<input type="hidden" value="1" name="hidden_value" id="hidden_value">
	<select name="plan_select" id="plan_select">
		<!-- <option value="0" style="display:none;">Select User</option> -->
	<?php
	$get_all_users = mysql_query("select PlanCode, MerchantID, PlanName  from PLAN_HEADER order by CreatedDate desc");
	$get_user_count = mysql_num_rows($get_all_users);
	//echo $get_user_count;	
	if($get_user_count > 0){
		while ($user_row = mysql_fetch_array($get_all_users)) {
			$plancode  	= $user_row['PlanCode'];
			$merchant 	= $user_row['MerchantID'];
			$name 		= $user_row['PlanName'];
				echo "<option value='$plancode'>$plancode $merchant - $name</option>";
	}
	} else {
		echo "No Plans";
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
			var dltcnfrm = confirm("The Plan and all the related data will be deleted. Click OK to continue.");
			if(dltcnfrm == true){
				$("#frm_delete_plan").submit();
			}else {

			}
		});
	});
	</script>
	</body>
</html>
