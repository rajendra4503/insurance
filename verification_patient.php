<?php
session_start();
include('include/configinc.php');
include('include/session.php');
include('include/functions.php');
/**********************Get user and Hospital Deatils***********************/

	if ($_POST['type'] == 'checked' && $_POST['claimId']) {
		$ID = $_POST['claimId'];
		$status = $_POST['status'];
		if($status == 1){
			echo 'Patient Already Verified Successfully.';
		}else{

		$query = "UPDATE patient_details SET Status = 1 
		WHERE Claim_ID = '$ID'";
		if(mysql_query($query)){
			echo 'Patient Verified Successfully.';
		}
	  }
	}

	if ($_POST['type'] == 'unchecked' && $_POST['claimId']) {
		$ID = $_POST['claimId'];
		$query = "UPDATE patient_details SET Status = 0 
		WHERE Claim_ID = '$ID'";
		if(mysql_query($query)){
			echo 'Patient Unverified Successfully';
		}
	}
	exit;
?>