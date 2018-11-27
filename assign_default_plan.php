<?php
session_start();
include('include/configinc.php');
$logged_merchantid 	= "00091345026943373028";
$logged_userid 		= "00091312984987380909";
if(isset($_REQUEST['plancodeselected']) && (!empty($_REQUEST['plancodeselected']))){

	//print_r($_REQUEST);exit;
	$selectedplancode 						= $_REQUEST['plancodeselected'];
	$_SESSION['current_assigned_plan_code'] = $selectedplancode;
	$doctorsname 							= "Dr. Sam";
	$user 									= $_REQUEST['userid'];
	$_SESSION['current_assigned_user_id'] = $user;
		$get_user_merchant_link = mysql_query("select MerchantID, UserID, RoleID from USER_MERCHANT_MAPPING where MerchantID='$logged_merchantid' and UserID='$user' and RoleID='5'");
		$link_count = mysql_num_rows($get_user_merchant_link);
		//echo $link_count;exit;
		if($link_count > 0){

		} else {
		$user_merchant_query = mysql_query("insert into `USER_MERCHANT_MAPPING` (`MerchantID`,`UserID`, `RoleID`, `Status`, `CreatedDate`, `CreatedBy`) VALUES ('$logged_merchantid', '$user', '5', 'A', now(), '$logged_userid')");			
		}
		$assign_to_user_query = mysql_query("insert into `USER_PLAN_MAPPING` (`UserID`, `PlanCode`, `DependencyID`, `Status`, `CreatedDate`, `CreatedBy`) VALUES ('$user', '$selectedplancode', '0', 'A', now(), '$logged_userid')");
		//echo $assign_to_user_query;exit;
		$assigned_to_user = mysql_affected_rows();

			//MOVE ALL PLAN DETAILS TO USER TABLES
			//GET PLAN HEADER
			$get_plan_header = "select PlanCode, MerchantID, CategoryID, PlanName, PlanDescription, PlanStatus, PlanCurrencyCode, PlanCost, PlanCoverImagePath from PLAN_HEADER where PlanCode='$selectedplancode'";
			//echo $get_plan_header;exit;
			$get_plan_header_run = mysql_query($get_plan_header);
			while ($plan_header = mysql_fetch_array($get_plan_header_run)) {
				$user_plan_code 		= $plan_header['PlanCode'];
				$user_merchant_id 		= $plan_header['MerchantID'];
				$user_category_id 		= $plan_header['CategoryID'];
				$user_plan_name 		= mysql_real_escape_string(trim(htmlspecialchars($plan_header['PlanName'])));
				$user_plan_desc 		= mysql_real_escape_string(trim(htmlspecialchars($plan_header['PlanDescription'])));
				$user_plan_status 		= "A"; //INCOMPLETE PLAN 
				$user_plan_currency		= $plan_header['PlanCurrencyCode'];
				$user_plan_cost			= $plan_header['PlanCost'];
				$user_plan_image 		= "";
				//INSERT TO USER_PLAN_HEADER
				$insert_to_user_plan_header = mysql_query("insert into USER_PLAN_HEADER (UserID, PlanCode, MerchantID, CategoryID, PlanName, PlanDescription, PlanStatus, FreePlan, PlanCurrencyCode, PlanCost, PlanCoverImagePath) values ('$user', '$user_plan_code', '$user_merchant_id', '$user_category_id', '$user_plan_name', '$user_plan_desc', '$user_plan_status', 'Y', '$user_plan_currency', '$user_plan_cost', '$user_plan_image')");
			}
			//GET MEDICATION HEADER
			$get_medication_header = mysql_query("select PlanCode, PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate, CreatedBy from MEDICATION_HEADER where PlanCode = '$selectedplancode'");
			$medication_count = mysql_num_rows($get_medication_header);
			//echo $medication_count;
			if($medication_count > 0){
				while ($medication_header 	= mysql_fetch_array($get_medication_header)) {
					$user_mh_plan_code  	= $medication_header['PlanCode'];
					$user_mh_presc_no   	= $medication_header['PrescriptionNo'];
					$user_mh_presc_name 	= mysql_real_escape_string(trim(htmlspecialchars($medication_header['PrescriptionName'])));
					$user_mh_doc_name   	= mysql_real_escape_string(trim(htmlspecialchars($medication_header['DoctorsName'])));
					$user_mh_created_date  	= $medication_header['CreatedDate'];
					$user_mh_created_by  	= $medication_header['CreatedBy'];
					//INSERT INTO USER MEDICATION HEADER
			$insert_to_user_med_header = mysql_query("insert into USER_MEDICATION_HEADER (UserID, PlanCode, PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate, CreatedBy) values ('$user', '$user_mh_plan_code', '$user_mh_presc_no', '$user_mh_presc_name', '$doctorsname', '$user_mh_created_date', '$user_mh_created_by')");		
				}
			} else {
				//NO MEDICATION ADDED FOR THIS PLAN
			}
			//GET MEDICATION DETAILS
			$get_medication_details = mysql_query("select `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `MedicineCount`, `MedicineTypeID`, `When`, `SpecificTime`, `Instruction`,`Link`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `SpecificDate`, `CreatedDate`, `CreatedBy` from MEDICATION_DETAILS where `PlanCode` = '$selectedplancode'");
			$med_details_count  = mysql_num_rows($get_medication_details);
			if($med_details_count > 0){
				while ($medication_details = mysql_fetch_array($get_medication_details)) {
					$user_md_plan_code  		= $medication_details['PlanCode'];
					$user_md_presc_no   		= $medication_details['PrescriptionNo'];
					$user_md_row_no		 		= $medication_details['RowNo'];
					$user_md_med_name   		= mysql_real_escape_string(trim(htmlspecialchars($medication_details['MedicineName'])));
					$user_md_med_count  		= mysql_real_escape_string(trim(htmlspecialchars($medication_details['MedicineCount'])));
					$user_md_type_id		 	= $medication_details['MedicineTypeID'];
					$user_md_when		 		= $medication_details['When'];
					$user_md_sptime		 		= $medication_details['SpecificTime'];
					$user_md_instruction		= $medication_details['Instruction'];
					$user_md_link				= $medication_details['Link'];
					$user_md_frequency		 	= $medication_details['Frequency'];
					$user_md_freq_string		= $medication_details['FrequencyString'];
					$user_md_how_long		 	= $medication_details['HowLong'];
					$user_md_howlong_type		= $medication_details['HowLongType'];
					$user_md_iscritical		 	= $medication_details['IsCritical'];
					$user_md_respreqd		 	= $medication_details['ResponseRequired'];
					$user_md_startflag		 	= $medication_details['StartFlag'];
					$user_md_no_days		 	= $medication_details['NoOfDaysAfterPlanStarts'];
					$user_md_specific_date		= $medication_details['SpecificDate'];
					$user_md_created_date  		= $medication_details['CreatedDate'];
					$user_md_created_by  		= $medication_details['CreatedBy'];
					//INSERT INTO USER MEDICATION DETAILS
					$insert_to_user_med_details = mysql_query("insert into USER_MEDICATION_DETAILS (`UserID`, `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `MedicineCount`, `MedicineTypeID`, `When`, `SpecificTime`, `Instruction`,`Link`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `SpecificDate`, `CreatedDate`,`CreatedBy`) values ('$user', '$user_md_plan_code', '$user_md_presc_no', '$user_md_row_no', '$user_md_med_name', '$user_md_med_count', '$user_md_type_id', '$user_md_when','$user_md_sptime', '$user_md_instruction','$user_md_link','$user_md_frequency','$user_md_freq_string','$user_md_how_long','$user_md_howlong_type','$user_md_iscritical','$user_md_respreqd', '$user_md_startflag', '$user_md_no_days','$user_md_specific_date','$user_md_created_date','$user_md_created_by')");
				}
			} else {
				//NO MEDICATION ADDED FOR THIS PLAN
			}
			//GET INSTRUCTION HEADER
			$get_instruction_header = mysql_query("select PlanCode, PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate, CreatedBy from INSTRUCTION_HEADER where PlanCode = '$selectedplancode'");
			$instruction_count = mysql_num_rows($get_instruction_header);
			if($instruction_count > 0){
				while ($instruction_header 	= mysql_fetch_array($get_instruction_header)) {
					$user_ih_plan_code  	= $instruction_header['PlanCode'];
					$user_ih_presc_no   	= $instruction_header['PrescriptionNo'];
					$user_ih_presc_name 	= mysql_real_escape_string(trim(htmlspecialchars($instruction_header['PrescriptionName'])));
					$user_ih_doc_name   	= mysql_real_escape_string(trim(htmlspecialchars($instruction_header['DoctorsName'])));
					$user_ih_created_date  	= $instruction_header['CreatedDate'];
					$user_ih_created_by  	= $instruction_header['CreatedBy'];
					//INSERT INTO USER INSTRUCTION HEADER
			$insert_to_user_med_header = mysql_query("insert into USER_INSTRUCTION_HEADER (UserID, PlanCode, PrescriptionNo, PrescriptionName, DoctorsName, CreatedDate, CreatedBy) values ('$user', '$user_ih_plan_code', '$user_ih_presc_no', '$user_ih_presc_name', '$doctorsname', '$user_ih_created_date', '$user_ih_created_by')");		
				}
			} else {
				//NO INSTRUCTION ADDED FOR THIS PLAN
			}
			//GET INSTRUCTION DETAILS
			$get_instruction_details = mysql_query("select `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `When`, `SpecificTime`, `Instruction`,`Link`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `SpecificDate`, `CreatedDate`, `CreatedBy` from INSTRUCTION_DETAILS where `PlanCode` = '$selectedplancode'");
			$med_details_count  = mysql_num_rows($get_instruction_details);
			if($med_details_count > 0){
				while ($instruction_details = mysql_fetch_array($get_instruction_details)) {
					$user_id_plan_code  		= $instruction_details['PlanCode'];
					$user_id_presc_no   		= $instruction_details['PrescriptionNo'];
					$user_id_row_no		 		= $instruction_details['RowNo'];
					$user_id_med_name   		= mysql_real_escape_string(trim(htmlspecialchars($instruction_details['MedicineName'])));
					$user_id_when		 		= $instruction_details['When'];
					$user_id_sptime		 		= $instruction_details['SpecificTime'];
					$user_id_instruction		= $instruction_details['Instruction'];
					$user_id_link				= $instruction_details['Link'];
					$user_id_frequency		 	= $instruction_details['Frequency'];
					$user_id_freq_string		= $instruction_details['FrequencyString'];
					$user_id_how_long		 	= $instruction_details['HowLong'];
					$user_id_howlong_type		= $instruction_details['HowLongType'];
					$user_id_iscritical		 	= $instruction_details['IsCritical'];
					$user_id_respreqd		 	= $instruction_details['ResponseRequired'];
					$user_id_startflag		 	= $instruction_details['StartFlag'];
					$user_id_no_days		 	= $instruction_details['NoOfDaysAfterPlanStarts'];
					$user_id_specific_date		= $instruction_details['SpecificDate'];
					$user_id_created_date  		= $instruction_details['CreatedDate'];
					$user_id_created_by  		= $instruction_details['CreatedBy'];
					//INSERT INTO USER INSTRUCTION DETAILS
					$insert_to_user_med_details = mysql_query("insert into USER_INSTRUCTION_DETAILS (`UserID`, `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `When`, `SpecificTime`, `Instruction`,`Link`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `SpecificDate`, `CreatedDate`,`CreatedBy`) values ('$user', '$user_id_plan_code', '$user_id_presc_no', '$user_id_row_no', '$user_id_med_name', '$user_id_when','$user_id_sptime', '$user_id_instruction','$user_id_link','$user_id_frequency','$user_id_freq_string','$user_id_how_long','$user_id_howlong_type','$user_id_iscritical','$user_id_respreqd', '$user_id_startflag', '$user_id_no_days','$user_id_specific_date','$user_id_created_date','$user_id_created_by')");
				}
			} else {
				//NO INSTRUCTION ADDED FOR THIS PLAN
			}
			//GET APPOINTMENT HEADER
			$get_appointment_header = mysql_query("select PlanCode, AppointmentDate, CreatedDate, CreatedBy from APPOINTMENT_HEADER where PlanCode = '$selectedplancode'");
			$appointment_count = mysql_num_rows($get_appointment_header);
			if($appointment_count > 0){
				while ($appointment_header 	= mysql_fetch_array($get_appointment_header)) {
					$user_ah_plan_code  	= $appointment_header['PlanCode'];
					$user_ah_app_date   	= $appointment_header['AppointmentDate'];
					$user_ah_created_date  	= $appointment_header['CreatedDate'];
					$user_ah_created_by  	= $appointment_header['CreatedBy'];
					//INSERT INTO USER APPOINTMENT HEADER
			$insert_to_user_appo_header = mysql_query("insert into USER_APPOINTMENT_HEADER (UserID, PlanCode, AppointmentDate, CreatedDate, CreatedBy) values ('$user', '$user_ah_plan_code', '$user_ah_app_date', '$user_ah_created_date','$user_ah_created_by')");
				}
			} else {
				//NO APPOINTMENTS ADDED FOR THIS PLAN
			}
			//GET APPOINTMENT DETAILS
			$get_appointment_details = mysql_query("select `PlanCode`, `AppointmentDate`, `AppointmentTime`, `AppointmentShortName`, `DoctorsName`, `AppointmentRequirements`, `CreatedDate`, `CreatedBy` from APPOINTMENT_DETAILS where `PlanCode` = '$selectedplancode'");
			$appo_details_count  = mysql_num_rows($get_appointment_details);
			if($appo_details_count > 0){
				while ($appointment_details = mysql_fetch_array($get_appointment_details)) {
					$user_ad_plan_code  		= $appointment_details['PlanCode'];
					$user_ad_appo_date   		= $appointment_details['AppointmentDate'];
					$user_ad_appo_time		 	= $appointment_details['AppointmentTime'];
					$user_ad_appo_sname   		= mysql_real_escape_string(trim(htmlspecialchars($appointment_details['AppointmentShortName'])));
					$user_ad_docname		 	= mysql_real_escape_string(trim(htmlspecialchars($appointment_details['DoctorsName'])));
					$user_ad_requirements		= mysql_real_escape_string(trim(htmlspecialchars($appointment_details['AppointmentRequirements'])));
					$user_ad_created_date		= $appointment_details['CreatedDate'];
					$user_ad_created_by			= $appointment_details['CreatedBy'];
					//INSERT INTO USER MEDICATION DETAILS
					$insert_to_user_appo_details = mysql_query("insert into USER_APPOINTMENT_DETAILS (`UserID`, `PlanCode`, `AppointmentDate`, `AppointmentTime`, `AppointmentShortName`, `DoctorsName`, `AppointmentRequirements`, `CreatedDate`, `CreatedBy`) values ('$user', '$user_ad_plan_code', '$user_ad_appo_date', '$user_ad_appo_time', '$user_ad_appo_sname', '$user_ad_docname', '$user_ad_requirements','$user_ad_created_date','$user_ad_created_by')");
				}
			} else {
				//NO APPOINTMENTS ADDED FOR THIS PLAN
			}
			//GET SELF TEST HEADER
			$get_selftest_header = mysql_query("select PlanCode, SelfTestID, CreatedDate, CreatedBy from SELF_TEST_HEADER where PlanCode = '$selectedplancode'");
			$selftest_count = mysql_num_rows($get_selftest_header);
			if($selftest_count > 0){
				while ($selftest_header 	= mysql_fetch_array($get_selftest_header)) {
					$user_ah_plan_code  	= $selftest_header['PlanCode'];
					$user_ah_selftest_id   	= $selftest_header['SelfTestID'];
					$user_ah_created_date  	= $selftest_header['CreatedDate'];
					$user_ah_created_by  	= $selftest_header['CreatedBy'];
					//INSERT INTO USER APPOINTMENT HEADER
			$insert_to_user_selftest_header = mysql_query("insert into USER_SELF_TEST_HEADER (UserID, PlanCode, SelfTestID, CreatedDate, CreatedBy) values ('$user', '$user_ah_plan_code', '$user_ah_selftest_id', '$user_ah_created_date','$user_ah_created_by')");
				}
			} else {
				//NO SELF TEST ADDED FOR THIS PLAN
			}
			//GET SELF TEST DETAILS
			$get_selftest_details = mysql_query("select `PlanCode`, `SelfTestID`, `RowNo`, `TestName`, `DoctorsName`, `TestDescription`, `Instruction`,`Link`, `Frequency`,`FrequencyString`, `HowLong`, `HowLongType`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `SpecificDate`, `CreatedDate`, `CreatedBy` from SELF_TEST_DETAILS where `PlanCode` = '$selectedplancode'");
			$selftest_details_count  = mysql_num_rows($get_selftest_details);
			if($selftest_details_count > 0){
				while ($selftest_details = mysql_fetch_array($get_selftest_details)) {
					$user_sd_plan_code  			= $selftest_details['PlanCode'];
					$user_sd_selftest_id   			= $selftest_details['SelfTestID'];
					$user_sd_self_row		 		= $selftest_details['RowNo'];
					$user_sd_self_name   			= mysql_real_escape_string(trim(htmlspecialchars($selftest_details['TestName'])));
					$user_sd_docname		 		= mysql_real_escape_string(trim(htmlspecialchars($selftest_details['DoctorsName'])));
					$user_sd_test_desc				= mysql_real_escape_string(trim(htmlspecialchars($selftest_details['TestDescription'])));
					$user_sd_instruction			= mysql_real_escape_string(trim(htmlspecialchars($selftest_details['Instruction'])));
					$user_sd_link					= $selftest_details['Link'];
					$user_sd_frequency				= $selftest_details['Frequency'];
					$user_sd_freq_string			= $selftest_details['FrequencyString'];
					$user_sd_howlong				= $selftest_details['HowLong'];
					$user_sd_howlong_type			= $selftest_details['HowLongType'];
					$user_sd_resp_reqd				= $selftest_details['ResponseRequired'];
					$user_sd_start_flag				= $selftest_details['StartFlag'];
					$user_sd_no_of_days				= $selftest_details['NoOfDaysAfterPlanStarts'];
					$user_sd_specific_date			= $selftest_details['SpecificDate'];
					$user_sd_created_date			= $selftest_details['CreatedDate'];
					$user_sd_created_by				= $selftest_details['CreatedBy'];
					//INSERT INTO USER MEDICATION DETAILS
					$insert_to_user_appo_details = mysql_query("insert into USER_SELF_TEST_DETAILS (`UserID`, `PlanCode`, `SelfTestID`, `RowNo`, `TestName`, `DoctorsName`, `TestDescription`, `InstructionID`,`Link`, `Frequency`,`FrequencyString`,`HowLong`, `HowLongType`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `SpecificDate`, `CreatedDate`, `CreatedBy`) values ('$user', '$user_sd_plan_code', '$user_sd_selftest_id', '$user_sd_self_row', '$user_sd_self_name', '$user_sd_docname', '$user_sd_test_desc','$user_sd_instruction','$user_sd_link','$user_sd_frequency','$user_sd_freq_string','$user_sd_howlong','$user_sd_howlong_type','$user_sd_resp_reqd','$user_sd_start_flag','$user_sd_no_of_days','$user_sd_specific_date','$user_sd_created_date','$user_sd_created_by')");
				}
			} else {
				//NO SELF TEST ADDED FOR THIS PLAN
			}
			//GET LAB TEST HEADER
			$get_labtest_header = mysql_query("select PlanCode, LabTestID, CreatedDate, CreatedBy from LAB_TEST_HEADER1 where PlanCode = '$selectedplancode'");
			$labtest_count = mysql_num_rows($get_labtest_header);
			if($labtest_count > 0){
				while ($labtest_header 	= mysql_fetch_array($get_labtest_header)) {
					$user_ah_plan_code  	= $labtest_header['PlanCode'];
					$user_ah_labtest_id   	= $labtest_header['LabTestID'];
					$user_ah_created_date  	= $labtest_header['CreatedDate'];
					$user_ah_created_by  	= $labtest_header['CreatedBy'];
					//INSERT INTO USER APPOINTMENT HEADER
			$insert_to_user_labtest_header = mysql_query("insert into USER_LAB_TEST_HEADER1 (UserID, PlanCode, LabTestID, CreatedDate, CreatedBy) values ('$user', '$user_ah_plan_code', '$user_ah_labtest_id', '$user_ah_created_date','$user_ah_created_by')");
				}
			} else {
				//NO LAB TEST ADDED FOR THIS PLAN
			}
			//GET LAB TEST DETAILS
			$get_labtest_details = mysql_query("select `PlanCode`, `LabTestID`, `RowNo`, `TestName`, `DoctorsName`, `LabTestRequirements`, `CreatedDate`, `CreatedBy` from LAB_TEST_DETAILS1 where `PlanCode` = '$selectedplancode'");
			$labtest_details_count  = mysql_num_rows($get_labtest_details);
			if($labtest_details_count > 0){
				while ($labtest_details = mysql_fetch_array($get_labtest_details)) {
					$user_sd_plan_code  			= $labtest_details['PlanCode'];
					$user_sd_labtest_id   			= $labtest_details['LabTestID'];
					$user_sd_self_row		 		= $labtest_details['RowNo'];
					$user_sd_self_name   			= mysql_real_escape_string(trim(htmlspecialchars($labtest_details['TestName'])));
					$user_sd_docname		 		= mysql_real_escape_string(trim(htmlspecialchars($labtest_details['DoctorsName'])));
					$user_sd_test_req				= mysql_real_escape_string(trim(htmlspecialchars($labtest_details['LabTestRequirements'])));
					$user_sd_created_date			= $labtest_details['CreatedDate'];
					$user_sd_created_by				= $labtest_details['CreatedBy'];
					//INSERT INTO USER LAB TEST DETAILS
					$insert_to_user_appo_details = mysql_query("insert into USER_LAB_TEST_DETAILS1 (`UserID`, `PlanCode`, `LabTestID`, `RowNo`, `TestName`, `DoctorsName`, `LabTestRequirements`, `CreatedDate`, `CreatedBy`) values ('$user', '$user_sd_plan_code', '$user_sd_labtest_id', '$user_sd_self_row', '$user_sd_self_name', '$user_sd_docname', '$user_sd_test_req','$user_sd_created_date','$user_sd_created_by')");
				}
			} else {
				//NO LAB TEST ADDED FOR THIS PLAN
			}
			//GET DIET HEADER
			$get_diet_header = mysql_query("select PlanCode, DietNo, DietPlanName, AdvisorName, DietDurationDays, CreatedDate, CreatedBy from DIET_HEADER where PlanCode = '$selectedplancode'");
			$diet_count = mysql_num_rows($get_diet_header);
			if($diet_count > 0){
				while ($diet_header 	= mysql_fetch_array($get_diet_header)) {
					$user_dh_plan_code  	= $diet_header['PlanCode'];
					$user_dh_dietno   		= $diet_header['DietNo'];
					$user_dh_diet_plan  	= mysql_real_escape_string(trim(htmlspecialchars($diet_header['DietPlanName'])));
					$user_dh_advisor  		= mysql_real_escape_string(trim(htmlspecialchars($diet_header['AdvisorName'])));
					$user_dh_duration  		= $diet_header['DietDurationDays'];
					$user_dh_created_date  	= $diet_header['CreatedDate'];
					$user_dh_created_by  	= $diet_header['CreatedBy'];
					//INSERT INTO USER APPOINTMENT HEADER
			$insert_to_user_diet_header = mysql_query("insert into USER_DIET_HEADER (UserID, PlanCode, DietNo, DietPlanName, AdvisorName, DietDurationDays, CreatedDate, CreatedBy) values ('$user', '$user_dh_plan_code', '$user_dh_dietno', '$user_dh_diet_plan','$user_dh_advisor','$user_dh_duration','$user_dh_created_date','$user_dh_created_by')");
				}
			} else {
				//NO DIET ADDED FOR THIS PLAN
			}
			//GET DIET DETAILS
			$get_diet_details = mysql_query("select PlanCode, DietNo, DayNo, SNo, InstructionID, MealDescription, SpecificTime, CreatedDate, CreatedBy from DIET_DETAILS where PlanCode = '$selectedplancode'");
			$diet_details_count  = mysql_num_rows($get_diet_details);
			if($diet_details_count > 0){
				while ($diet_details = mysql_fetch_array($get_diet_details)) {
					$user_dd_plan_code  			= $diet_details['PlanCode'];
					$user_dd_diet_no	   			= $diet_details['DietNo'];
					$user_dd_day_no 		 		= $diet_details['DayNo'];
					$user_dd_sno         			= $diet_details['SNo'];
					$user_dd_instruction	 		= $diet_details['InstructionID'];
					$user_dd_meal_desc				= mysql_real_escape_string(trim(htmlspecialchars($diet_details['MealDescription'])));
					$user_dd_spec_time				= $diet_details['SpecificTime'];
					$user_dd_created_date			= $diet_details['CreatedDate'];
					$user_dd_created_by				= $diet_details['CreatedBy'];
					//INSERT INTO USER LAB TEST DETAILS
					$insert_to_user_diet_details = mysql_query("insert into USER_DIET_DETAILS (UserID, PlanCode, DietNo, DayNo, SNo, InstructionID, MealDescription, SpecificTime, CreatedDate, CreatedBy) values ('$user', '$user_dd_plan_code', '$user_dd_diet_no', '$user_dd_day_no', '$user_dd_sno', '$user_dd_instruction', '$user_dd_meal_desc','$user_dd_spec_time','$user_dd_created_date','$user_dd_created_by')");
				}
			} else {
				//NO DIET ADDED FOR THIS PLAN
			}
		}
if($logged_merchantid)
        {
            echo "{".json_encode('PLANPIPER_SIGNUP').':'.json_encode("1")."}";
        }
        else
        {
            echo "{".json_encode('PLANPIPER_SIGNUP').':'.json_encode("0")."}";
        }
?>