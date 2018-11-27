<?php 
session_start();
//ini_set("display_errors","0");
include_once('include/configinc.php');
include('include/session.php');
include('include/functions.php');

$ua=getBrowser();
$browser_name = strtolower(str_replace(" ","",$ua['name']));

//GET DOCTOR SHORT HAND
$shorthand_options = "";
$get_shorthand = mysql_query("select ID, ShortHand from MASTER_DOCTOR_SHORTHAND order by ShortHand desc");
$shorthand_count = mysql_num_rows($get_shorthand);
if($shorthand_count > 0){
  while ($shorthand = mysql_fetch_array($get_shorthand)) {
    $shorthand_id  = $shorthand['ID'];
    $shorthandname = $shorthand['ShortHand'];
    $shorthand_options .= "<option value='$shorthand_id'>$shorthandname</option>";
  }
}

//GET MEDICINES
$get_medicines      = mysql_query("select MedicineID, GenericName, BrandName, Quantity, CompanyName, MedicineType from MEDICINE_LIST order by GenericName");
$medicine_count   = mysql_num_rows($get_medicines);
$medicine_options   = "";
if($medicine_count > 0){
while ($medicines = mysql_fetch_array($get_medicines)) {
        $medicine_id        = $medicines['MedicineID'];
        $generic_name       = $medicines['GenericName'];
        $brand_name         = $medicines['BrandName'];
        $company_name       = $medicines['CompanyName'];
        $quantity           = $medicines['Quantity'];
        $medicine_type      = $medicines['MedicineType'];
        $display_name       = $generic_name." - ".$brand_name;
          if($medicine_type == 2){
             $display_name     .= " (".$quantity." )";
          }
      $display_name     .= " - ".$company_name;
    // $medicine_options   .= "<option value='$medicine_id'>$medicine_name</option>";  
    $medicine_options   .= "<option value='$brand_name'>$display_name</option>";       
}
}

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
?>
<!DOCTYPE html>
<html lang="en">
<body>
<div>
<?php 
  $plancode = $_SESSION['plancode_for_current_plan'];
  $userid   = $_SESSION['userid_for_current_plan'];
  $prescno  = "1";
  $prescription_name = "";
  $doctor_name = "";
  $get_presc_header = mysql_query("select PrescriptionName, DoctorsName from USER_MEDICATION_HEADER where PrescriptionNo = '$prescno' and PlanCode = '$plancode' and UserID='$userid'");
              //echo $get_presc_header;exit;
              $medheader_num = mysql_num_rows($get_presc_header);
              if($medheader_num > 0) {
                while ($headerrow = mysql_fetch_array($get_presc_header)) {
                  $prescription_name  = $headerrow['PrescriptionName'];
                  $doctor_name    = $headerrow['DoctorsName']; 
                }
              }
  ?>
        <form name="frm_plan_prescription" id="frm_plan_prescription" method="post" action="cust_med_new.php" enctype="multipart/form-data">
      <div class="prescriptionNameBar">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="background-color:#BFBFBF;">
          <span>Prescription Template Name:</span>
        <input type="text" name="prescriptionName" id="prescriptionName" placeholder="Enter Prescription Name.." maxlength="25" title="Enter a name for the prescription" value="<?php echo $prescription_name;?>">
        </div>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="background-color:#BFBFBF;">
      <span>Doctor's Name:</span>
    <input type="text" name="doctorName" id="doctorName" placeholder="Enter Doctor Name.." maxlength="25" title="Enter the name of the doctor here.." value="<?php echo $doctor_name;?>">
    </div>
      </div>
      <span style="float:left;width:3%;" class='hidden-xs hidden-sm'>
        <table id="pslno" style="display:none;">
          <tr><th>#</th></tr>
          <tr><td>1</td></tr>
          <tr><td>2</td></tr>
          <tr><td>3</td></tr>
        </table>
      </span>
      <div class="table-responsive" style="float:left;width:99%;padding-bottom:100px;">
      <table id="pdata" class="table">
  <?php
  $get_presc = "select `PlanCode`, `PrescriptionNo`, `RowNo`, `MedicineName`, `MedicineCount`, `MedicineTypeID`, `When`, `ThresholdLimit` , `SpecificTime`, `Instruction`, `Frequency`, `FrequencyString`, `HowLong`, `HowLongType`, `IsCritical`, `ResponseRequired`, `StartFlag`, `NoOfDaysAfterPlanStarts`, `Link`, `OriginalFileName`, `SpecificDate` from USER_MEDICATION_DETAILS where PlanCode='$plancode' and PrescriptionNo = '$prescno' and UserID='$userid'";
  //echo $get_presc;exit;
  $get_presc_run = mysql_query($get_presc);
  $get_presc_count = mysql_num_rows($get_presc_run);
  //echo $get_presc_count;exit;
  $i = 0;
  $med_count_string = "";
  if($get_presc_count > 0){
  while ($prescrow = mysql_fetch_array($get_presc_run)) {
    $i++;
    $med_count_string       .= $i.",";
    $PlanCode               = $prescrow['PlanCode'];
    $PrescriptionNo         = $prescrow['PrescriptionNo'];
    $RowNo                  = $prescrow['RowNo'];
    $MedicineName           = $prescrow['MedicineName'];
    $ThresholdLimit         = $prescrow['ThresholdLimit'];
    $MedicineCount          = $prescrow['MedicineCount'];
    $MedicineTypeID         = $prescrow['MedicineTypeID'];
    $When                   = $prescrow['When'];
     //$SpecificTime        = date('h:i A',strtotime($prescrow['SpecificTime']));
    $SpecificTime           = $prescrow['SpecificTime'];
    $SpecificTimeType       = "type='hidden'";
    if($When== '16'){
      $SpecificTimeType= "type='text'";
    }
    $InstructionSelect     = '';
    if($When== '16'){
      $InstructionSelect     = "disabled style='opacity:0.2;'";
    }
    $Instruction            = $prescrow['Instruction'];
    $Frequency              = $prescrow['Frequency'];
    $FrequencyString        = (empty($prescrow['FrequencyString']))? '' : $prescrow['FrequencyString'];
    $WeeklyString           = "";
    $MonthlyString          = "";
    $WeeklyType             = "type='hidden'";
    $MonthlyType            = "type='hidden'";
    if($Frequency == "Weekly"){
      $WeeklyType= "type='text'";
      $WeeklyString          = $FrequencyString;
      $MonthlyString         = "";
    }
    if($Frequency== "Monthly"){
      $MonthlyType= "type='text'";
      $WeeklyString           = "";
      $MonthlyString          = $FrequencyString;
    }
    $CountSelect1     = "style='width:35px;height:30px;'";
    $CountSelect2     = "style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;";
    if($Frequency == "Once"){
      $CountSelect1     = "disabled style='width:35px;height:30px;opacity:0.2;'";
      $CountSelect2     = "disabled style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;opacity:0.2;'";
    }
    $HowLong          = (empty($prescrow['HowLong']))  ? '' : $prescrow['HowLong'];
    $HowLongType        = $prescrow['HowLongType'];
    $IsCritical         = $prescrow['IsCritical'];
    if($IsCritical== "Y"){
      $CriticalSelect     = "checked";
    } else {
      $CriticalSelect     = "";
    }
    $ThresholdInput       = "disabled style='width:35px;height:30px;opacity:0.2;'";
    $ResponseRequired     = $prescrow['ResponseRequired'];
    if($ResponseRequired== "Y"){
      $ResponseSelect     = "checked";
      $ThresholdInput     = "style='width:35px;height:30px;'";
    } else {
      $ResponseSelect     = "";
    }
    $StartFlag          = $prescrow['StartFlag'];
    $NoOfDaysAfterPlanStarts  = "";
    $SpecificDate       = "";

    if($StartFlag== "PS"){
      $PlanStartRadio   = "checked";
    } else {
      $PlanStartRadio   = "";
    }
    if($StartFlag== "SD"){
      $SpecificDateRadio  = "checked";
      $SpecificDate   = $prescrow['SpecificDate'];
      if(($SpecificDate   == "0000-00-00")||($SpecificDate== "")){
        $SpecificDate   = "";
      } else {
        $SpecificDate = date('d-M-Y',strtotime($SpecificDate));
      }
    } else {
      $SpecificDateRadio  = "";
    }
    if($StartFlag == "ND"){
      $NumOfDaysRadio = "checked";
      $NoOfDaysAfterPlanStarts    = $prescrow['NoOfDaysAfterPlanStarts'];
    } else {
      $NumOfDaysRadio = "";
    }
    
    $Link               = (empty($prescrow['Link']))  ? '' : $prescrow['Link'];
    $OriginalFileName   = (empty($prescrow['OriginalFileName']))  ? '' : $prescrow['OriginalFileName'];
    
// //GET MEDICINES
//     $get_medicines    = mysql_query("select ID, MedicineName from MERCHANT_MEDICINE_LIST where MerchantID in ('0', '$logged_merchantid') order by MedicineName");
//     $medicine_count   = mysql_num_rows($get_medicines);
//     $medicine_options_exist   = "";
//     if($medicine_count > 0){
//       while ($medicines = mysql_fetch_array($get_medicines)) {
//         $medicine_id    = $medicines['ID'];
//         $medicine_name    = $medicines['MedicineName'];
//         if($MedicineName == $medicine_name){
//           $medicine_options_exist   .= "<option value='$medicine_name' selected>$medicine_name</option>";
//         } else {
//           $medicine_options_exist   .= "<option value='$medicine_name'>$medicine_name</option>";      
//         }
//       }
//     }

    //GET MEDICINES
$get_medicines      = mysql_query("select MedicineID, GenericName, BrandName, Quantity, CompanyName, MedicineType from MEDICINE_LIST order by GenericName");
$medicine_count   = mysql_num_rows($get_medicines);
$medicine_options   = "";
if($medicine_count > 0){
while ($medicines = mysql_fetch_array($get_medicines)) {
        $medicine_id        = $medicines['MedicineID'];
        $generic_name       = $medicines['GenericName'];
        $brand_name         = $medicines['BrandName'];
        $company_name       = $medicines['CompanyName'];
        $quantity           = $medicines['Quantity'];
        $medicine_type      = $medicines['MedicineType'];
        $display_name       = $generic_name." - ".$brand_name;
          if($medicine_type == 2){
             $display_name     .= " (".$quantity." )";
          }
      $display_name     .= " - ".$company_name;
      if($MedicineName == $brand_name){
    // $medicine_options   .= "<option value='$medicine_id'>$medicine_name</option>";  
    $medicine_options   .= "<option value='$brand_name'>$display_name</option>";      
    } else {
      $medicine_options   .= "<option value='$brand_name'>$display_name</option>";   
    } 
}
}

    
    //GET DOCTOR SHORT HAND
    $shorthand_options_exist = "";
    $get_shorthand = mysql_query("select ID, ShortHand from MASTER_DOCTOR_SHORTHAND order by ShortHand desc");
    $shorthand_count = mysql_num_rows($get_shorthand);
    if($shorthand_count > 0){
      while ($shorthand = mysql_fetch_array($get_shorthand)) {
        $shorthand_id  = $shorthand['ID'];
        $shorthandname = $shorthand['ShortHand'];
        if($shorthand_id == $When){
          $shorthand_options_exist .= "<option value='$shorthand_id' selected>$shorthandname</option>";
        } else {
          $shorthand_options_exist .= "<option value='$shorthand_id'>$shorthandname</option>";
        }    
      }
    }
    $ShortHandOptions       = $shorthand_options_exist;

    //GET MEDICATION TYPES
    $medicine_type_options1 = "";
    $get_medicine_types = mysql_query("select SNo, MedicineType from MEDICINE_TYPES where SNo != '0'");
    $type_count = mysql_num_rows($get_medicine_types);
    if($type_count > 0){
      while ($medtype = mysql_fetch_array($get_medicine_types)) {
        $medtype_id     = $medtype['SNo'];
        $medtype_name   = $medtype['MedicineType'];
        if($medtype_id == $MedicineTypeID){
        $medicine_type_options1 .= "<option value='$medtype_id' selected>$medtype_name</option>";
      } else {
        $medicine_type_options1 .= "<option value='$medtype_id'>$medtype_name</option>";
        }
    }
    }
    $MedicineTypeOptions        = $medicine_type_options1;

    //INSTRUCTION SELECT BOX
        $instruction_options = "<option value='0' style='display:none;'>select</option>";
        if($Instruction== "Before Food"){
          $instruction_options .= "<option value='Before Food' selected>Before Food</option>";
        } else {
          $instruction_options .= "<option value='Before Food'>Before Food</option>";
        }
        if($Instruction== "With Food"){
          $instruction_options .= "<option value='With Food' selected>With Food</option>";
        } else {
          $instruction_options .= "<option value='With Food'>With Food</option>";
        }
        if($Instruction== "After Food"){
          $instruction_options .= "<option value='After Food' selected>After Food</option>";
        } else {
          $instruction_options .= "<option value='After Food'>After Food</option>";
        }
        if($Instruction== "NA"){
          $instruction_options .= "<option value='NA' selected>Not Applicable</option>";
        } else {
          $instruction_options .= "<option value='NA'>Not Applicable</option>";
        }
      $InstructionOptions       = $instruction_options; 

    //FREQUENCY SELECT BOX
      $frequency_options = "<option value='0' style='display:none;'>select</option>";
        if($Frequency== "Once"){
          $frequency_options .= "<option value='Once' selected>Once</option>";
        } else {
          $frequency_options .= "<option value='Once'>Once</option>";
        }
        if($Frequency== "Daily"){
          $frequency_options .= "<option value='Daily' selected>Daily</option>";
        } else {
          $frequency_options .= "<option value='Daily'>Daily</option>";
        }
        if($Frequency== "Weekly"){
          $frequency_options .= "<option value='Weekly' selected>Weekly</option>";
        } else {
          $frequency_options .= "<option value='Weekly'>Weekly</option>";
        }
        if($Frequency== "Monthly"){
          $frequency_options .= "<option value='Monthly' selected>Monthly</option>";
        } else {
          $frequency_options .= "<option value='Monthly'>Monthly</option>";
        }
      $FrequencyOptions       = $frequency_options;

      //HOW LONG TYPE SELECT BOX
      $howlongtype_options = "<option value='0' style='display:none;'>select</option>";
        if($HowLongType== "Days"){
          $howlongtype_options .= "<option value='Days' selected>Days</option>";
        } else {
          $howlongtype_options .= "<option value='Days'>Days</option>";
        }
        if($HowLongType== "Weeks"){
          $howlongtype_options .= "<option value='Weeks' selected>Weeks</option>";
        } else {
          $howlongtype_options .= "<option value='Weeks'>Weeks</option>";
        }
        if($HowLongType== "Months"){
          $howlongtype_options .= "<option value='Months' selected>Months</option>";
        } else {
          $howlongtype_options .= "<option value='Months'>Months</option>";
        }
      $HowLongTypeOptions       = $howlongtype_options;
      ?>

        <tr style="border-top:4px solid #004f35;">
          <td class="paddingrl5" align="center" colspan="6">
            <!-- <select style="height:35px;width:100%;background-color:#2B6D57;" name="medicine<?php echo $i;?>" id="medicine<?php echo $i;?>" >
            <option value="" style="display:none;">Select a Medicine</option>
            <?php echo $medicine_options_exist;?>
            </select> -->
            <?php
            //echo $browser_name;exit;
            if($browser_name=='applesafari')
            {
            ?>
            <select style="height:35px;width:100%;background-color:#2B6D57;" name="medicine<?php echo $i;?>" id="medicine<?php echo $i;?>" class='forminputs2'>
                <option value="" style="display:none;">Select a Medicine</option>
                <?php
                echo $medicine_options;
                }
                else
                {
                ?>
            </select>   
            <input type="text" list="medicine_list"  id="medicine<?php echo $i;?>" name="medicine<?php echo $i;?>" placeholder="Type Medicine Name Here" class='forminputs2' value='<?php echo $MedicineName;?>'> 
            <datalist id="medicine_list">
            <?php echo $medicine_options;?>
            </datalist>
            <?php
            }
            ?>
          </td>
          <td class="paddingrl5" align="center">
            <div style="border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;">
            <input type="text" maxlength="2" name="medcount<?php echo $i;?>" id="medcount<?php echo $i;?>" style="width:35px;height:30px;" class="forminputs2 roundedinputs countbox" title="Enter the count" value='<?php echo $MedicineCount;?>'>
            <select class=" lightcolorselect" id="medcountType<?php echo $i;?>" name="medcountType<?php echo $i;?>" style="width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;" title="Select Medicine Type">
              <option value="0" style="display:none;">select</option>
              <?php echo $MedicineTypeOptions;?>
            </select>
            </div>
          </td>
          <td style="width:300px;" class="paddingrl5">
            <input type="radio" name="radio<?php echo $i;?>" value="PS" checked class="radio<?php echo $i;?> prescriptionradio" <?php echo $PlanStartRadio;?>> When the plan Starts/Updates<br>
          </td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="<?php echo $i;?>" title="Delete this row"></td>
        </tr>
        <tr>
          <td class="paddingrl5" align="left">When:</td>
          <td class="paddingrl5" align="center">
            <select name="when<?php echo $i;?>" id="when<?php echo $i;?>" title="Select the medicine dosage" class="whenshorthand">
              <option value="0" style="display:none;">select</option>
              <?php echo $ShortHandOptions;?>
            </select>
          </td>
          <td class="paddingrl5" align="center">
            <select name="instruction<?php echo $i;?>" id="instruction<?php echo $i;?>" title="Select the medicine instruction" <?php echo $InstructionSelect;?>>
              <option value="0" style="display:none;">select</option>
              <?php echo $InstructionOptions;?>
            </select>
          </td>
          <td class="paddingrl5" align="center">Frequency :</td>
          <td class="paddingrl5" align="center">
            <select name="frequency<?php echo $i;?>" id="frequency<?php echo $i;?>" title="Select the medicine frequency" class="medfrequency">
              <option value="0" style="display:none;">select</option>
              <?php echo $FrequencyOptions;?>
            </select>
          </td>
          <td class="paddingrl5" align="center">
            Duration :
          </td>
          <td class="paddingrl5" align="center">
             <div style="border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;">
            <input type="text" maxlength="2" name="count<?php echo $i;?>" id="count<?php echo $i;?>" class="forminputs2 roundedinputs countbox" title="Enter the duration" value='<?php echo $HowLong;?>' <?php echo $CountSelect1;?>>
            <select  id="countType<?php echo $i;?>" name="countType<?php echo $i;?>" title="Enter the duration" <?php echo $CountSelect2;?>>
              <option value="0" style="display:none;">select</option>
              <?php echo $HowLongTypeOptions;?>
            </select>
            </div>
          </td>
          <td>
            <input type="radio" name="radio<?php echo $i;?>" value="ND" class="radio<?php echo $i;?> prescriptionradio" <?php echo $NumOfDaysRadio;?>> No. Of days after plan started&nbsp;<input type="text" size="6px" name="numofdays<?php echo $i;?>" id="numofdays<?php echo $i;?>" class="numofdays roundedinputs <?php if($StartFlag!= "ND"){echo "pointernone";}?>" maxlength="2" value='<?php echo $NoOfDaysAfterPlanStarts;?>'>
          </td>
        </tr>
        <tr>
          <td>Critical :<input type="checkbox" name="critical<?php echo $i;?>" id="critical<?php echo $i;?>" class="criticalcheck" title="Check this if the medicine is critical" value="Y" style="margin-left:5px;" <?php echo $CriticalSelect;?>></td>
          <td align="center">Response :<input type="checkbox" name="response<?php echo $i;?>" id="response<?php echo $i;?>" class="responsecheck" title="Check this if a response is required" value="Y" style="margin-left:5px;" <?php echo $ResponseSelect;?>></td>
          <td align="center">Threshold :<input type="text"  maxlength="2" name="threshold<?php echo $i;?>" id="threshold<?php echo $i;?>" <?php echo $ThresholdInput;?> class="forminputs2 roundedinputs countbox" title="Enter the threshold" value='<?php echo $ThresholdLimit;?>'></td>
          <!-- <td colspan="4" style="white-space:nowrap;"><input type="text" name="linkentered<?php echo $i;?>" id="linkentered<?php echo $i;?>"  class="forminputs2" title="Enter Link here" placeholder="Enter Link Here (Optional)" value='<?php echo $Link;?>'></td> -->
          <td colspan="4" style="white-space:nowrap;">
                   <div class="fileinput fileinput-new input-group" data-provides="fileinput" style="width:100%;">
                      <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"><?php echo $OriginalFileName;?></span></div>
                      <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Click To Upload A Document</span><span class="fileinput-exists">Change</span><input type="file" name="uploadedfile<?php echo $i;?>" id="uploadedfile<?php echo $i;?>"></span>
                      <a href="#" class="input-group-addon btn btn-default fileinput-exists removelinkbutton" id="<?php echo $i;?>" data-dismiss="fileinput">Remove</a>
                    </div> 
                    <input type="hidden" name="previouslink<?php echo $i;?>" id="previouslink<?php echo $i;?>"  value='<?php echo $Link;?>'>
                     <input type="hidden" name="originalfilename<?php echo $i;?>" id="originalfilename<?php echo $i;?>"  value='<?php echo $OriginalFileName;?>'>
                     <input type="hidden" name="deletelink<?php echo $i;?>" id="deletelink<?php echo $i;?>"  value='0'>
                </td>
          <td><input type="radio" name="radio<?php echo $i;?>" value="SD" class="radio<?php echo $i;?> prescriptionradio" <?php echo $SpecificDateRadio;?>> On Specific Date:&nbsp;<input type="text" size="10px" name="specificdate<?php echo $i;?>" id="specificdate<?php echo $i;?>" class='specificdate <?php if($StartFlag!= "SD"){echo "pointernone";}?>' value='<?php echo $SpecificDate;?>'></td>
        </tr>
        <tr>
          <td colspan="8">
            <input <?php echo $SpecificTimeType;?> name="specifictime<?php echo $i;?>" id="specifictime<?php echo $i;?>" class="editedspecifictimes forminputs2" title="Click here to edit specific times" value='<?php echo $SpecificTime;?>'>
          </td>
        </tr>
        <tr>
          <td colspan="8">
             <input <?php echo $WeeklyType;?> name="selectedweekdays<?php echo $i;?>" id="selectedweekdays<?php echo $i;?>" class="forminputs2 editselectedweekday" title="Click here to edit frequency" value='<?php echo $WeeklyString;?>'>
            <input <?php echo $MonthlyType;?> name="selectedmonthdays<?php echo $i;?>" id="selectedmonthdays<?php echo $i;?>" value="<?php echo $MonthlyString;?>" class="forminputs2 editselectedmonthday"  title="Click here to edit frequency">
            <div class="bottomshadow" style="width:100%;color:#fff;pointer-events:none;height:12px;"><span style='display:none;'>1</span></div>
          </td>
        </tr>
        <?php }} else {
          $med_count_string = "1,2,3,";
          $i = "3";
        for($j=1;$j<=3;$j++){
          ?>

        <tr style="border-top:4px solid #004f35;">
          <td class="paddingrl5" align="center" colspan="6">
            <!-- <select style="height:35px;width:100%;background-color:#2B6D57;" name="medicine<?php echo $j;?>" id="medicine<?php echo $j;?>" >
            <option value="" style="display:none;">Select a Medicine</option>
            <?php echo $medicine_options;?>
            </select> -->
            <?php
            //echo $browser_name;exit;
            if($browser_name=='applesafari'){
            ?>
            <select style="height:35px;width:100%;background-color:#2B6D57;" name="medicine<?php echo $j;?>" id="medicine<?php echo $j;?>" class='forminputs2'>
                <option value="" style="display:none;">Select a Medicine</option>
            <?php
            echo $medicine_options;
            ?>
            </select>
            <?php
            }
            else {
            ?>
            <input type="text" list="medicine_list"  id="medicine<?php echo $j;?>" name="medicine<?php echo $j;?>" placeholder="Type Medicine Name Here" class='forminputs2'> 
            <datalist id="medicine_list">
            <?php echo $medicine_options;?>
            </datalist>
            <?php
            }
            ?>
          </td>
          <td class="paddingrl5" align="center">
            <div style="border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;">
            <input type="text" maxlength="2" name="medcount<?php echo $j;?>" id="medcount<?php echo $j;?>" style="width:35px;height:30px;" class="forminputs2 roundedinputs countbox" title="Enter the count">
            <select class=" lightcolorselect" id="medcountType<?php echo $j;?>" name="medcountType<?php echo $j;?>" style="width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;" title="Select Medicine Type">
              <option value="0" style="display:none;">select</option>
              <?php echo $medicine_type_options;?>
            </select>
            </div>
          </td>
          <td style="width:300px;" class="paddingrl5">
            <input type="radio" name="radio<?php echo $j;?>" value="PS" checked class="radio<?php echo $j;?> prescriptionradio"> When the plan Starts/Updates<br>
          </td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="<?php echo $j;?>" title="Delete this row"></td>
        </tr>
        <tr>
          <td class="paddingrl5" align="center">When:</td>
          <td class="paddingrl5" align="center">
            <select name="when<?php echo $j;?>" id="when<?php echo $j;?>" title="Select the medicine dosage" class="whenshorthand">
              <option value="0" style="display:none;">select</option>
              <?php echo $shorthand_options;?>
            </select>
          </td>
          <td class="paddingrl5" align="center">
            <select name="instruction<?php echo $j;?>" id="instruction<?php echo $j;?>" title="Select the medicine instruction">
              <option value="0" style="display:none;">select</option>
              <option value="Before Food">Before Food</option>
              <option value="With Food">With Food</option>
              <option value="After Food">After Food</option>
              <option value="NA">Not Applicable</option>
            </select>
          </td>
          <td class="paddingrl5" align="center">Frequency :</td>
          <td class="paddingrl5" align="center">
            <select name="frequency<?php echo $j;?>" id="frequency<?php echo $j;?>" title="Select the medicine frequency" class="medfrequency">
              <option value="0" style="display:none;">select</option>
              <option value="Once">Once</option>
              <option value="Daily">Daily</option>
              <option value="Weekly">Weekly</option>
              <option value="Monthly">Monthly</option>
            </select>
          </td>
          <td class="paddingrl5" align="center">
            Duration :
          </td>
          <td class="paddingrl5" align="center">
             <div style="border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;">
            <input type="text" maxlength="2" name="count<?php echo $j;?>" id="count<?php echo $j;?>" style="width:35px;height:30px;" class="forminputs2 roundedinputs countbox" title="Enter the duration">
            <select  id="countType<?php echo $j;?>" name="countType<?php echo $j;?>" style="width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;" title="Enter the duration">
              <option value="0" style="display:none;">select</option>
              <option value="Days">Days</option>
              <option value="Weeks">Weeks</option>
              <option value="Months">Months</option>
            </select>
            </div>
          </td>
          <td>
            <input type="radio" name="radio<?php echo $j;?>" value="ND" class="radio<?php echo $j;?> prescriptionradio"> No. Of days after plan started&nbsp;<input type="text" size="6px" name="numofdays<?php echo $j;?>" id="numofdays<?php echo $j;?>" class="numofdays roundedinputs pointernone" maxlength="2">
          </td>
        </tr>
        <tr>
          <td align="center">Critical :<input type="checkbox" name="critical<?php echo $j;?>" id="critical<?php echo $j;?>" class="criticalcheck" title="Check this if the medicine is critical" value="Y" style="margin-left:5px;"></td>
          <td align="center">Response :<input type="checkbox" name="response<?php echo $j;?>" id="response<?php echo $j;?>" class="responsecheck" title="Check this if a response is required" value="Y" style="margin-left:5px;"></td>
          <td align="center">Threshold : <input type="text" maxlength="2" name="threshold<?php echo $j;?>" id="threshold<?php echo $j;?>" style="width:40px;height:35px;opacity:0.2;" disabled class="forminputs2 roundedinputs countbox" title="Enter the threshold"></td>
          <!-- <td colspan="4" style="white-space:nowrap;"><input type="text" name="linkentered<?php echo $j;?>" id="linkentered<?php echo $j;?>" value="" class="forminputs2" title="Enter Link here" placeholder="Enter Link Here (Optional)"></td> -->
           <td colspan="4" style="white-space:nowrap;">
                   <div class="fileinput fileinput-new input-group" data-provides="fileinput" style="width:100%;">
                      <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                      <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Click To Upload A Document</span><span class="fileinput-exists">Change</span><input type="file"  name="uploadedfile<?php echo $j;?>" id="uploadedfile<?php echo $j;?>"></span>
                      <a href="#" class="input-group-addon btn btn-default fileinput-exists removelinkbutton" id="<?php echo $j;?>" data-dismiss="fileinput">Remove</a>
                    </div> 
                    <input type="hidden" name="previouslink<?php echo $j;?>" id="previouslink<?php echo $j;?>">
                     <input type="hidden" name="originalfilename<?php echo $j;?>" id="originalfilename<?php echo $j;?>">
                     <input type="hidden" name="deletelink<?php echo $j;?>" id="deletelink<?php echo $j;?>"  value='0'>
                </td>
          <td><input type="radio" name="radio<?php echo $j;?>" value="SD" class="radio<?php echo $j;?> prescriptionradio"> On Specific Date:&nbsp;<input type="text" size="10px" name="specificdate<?php echo $j;?>" id="specificdate<?php echo $j;?>" class='specificdate pointernone' readonly></td>
        </tr>
        <tr>
          <td colspan="8">
            <input type="hidden" name="specifictime<?php echo $j;?>" id="specifictime<?php echo $j;?>" class="editedspecifictimes forminputs2" readonly title="Click here to edit specific times">
          </td>
        </tr>
        <tr>
          <td colspan="8">
             <input type="hidden" name="selectedweekdays<?php echo $j;?>" id="selectedweekdays<?php echo $j;?>" value="" class="forminputs2 editselectedweekday" readonly title="Click here to edit frequency">
            <input type="hidden" name="selectedmonthdays<?php echo $j;?>" id="selectedmonthdays<?php echo $j;?>" value="" class="forminputs2 editselectedmonthday" readonly title="Click here to edit frequency">
            <div class="bottomshadow" style="width:100%;color:#fff;pointer-events:none;height:12px;"><span style='display:none;'>1</span></div>
          </td>
        </tr>
          <?php
        }
        }
        ?>
      </table>
      </div>
    <input type="hidden" name="usedpresciptioncount" id="usedpresciptioncount" value="<?php echo $med_count_string;?>">
    <input type="hidden" name="medicationcount" id="medicationcount" value="<?php echo $i;?>">
    <input type="hidden" name="propercount" id="propercount" value="<?php echo $i;?>">
    <input type="hidden" name="userid_for_current_plan" id="userid_for_current_plan" value="<?php echo $userid;?>">
    </form>
    </div>
    <div id="ActionBar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="position: fixed;bottom: 0;background-color: #fff;text-align: center;margin-left: -5%;width:90%;">
        <button type="button" id="addMedicine" class="btns formbuttons">ADD A MEDICINE</button>
        <button type="button" id="saveAndEdit" class="btns formbuttons">SAVE</button>
        <!--<button type="button" id="saveTemplate" class="btns formbuttons">SAVE THIS AS TEMPLATE</button>-->
      </div>
</body>
<script type="text/javascript">
  $(".countbox,.numofdays").keydown(function(event) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ( $.inArray(event.keyCode,[46,8,9,27,13]) !== -1 ||
       // Allow: Ctrl+A
      (event.keyCode == 65 && event.keyCode == 67 && event.keyCode == 86 || event.ctrlKey === true) ||
       // Allow: home, end, left, right
      (event.keyCode >= 35 && event.keyCode <= 39)) {
         // let it happen, don't do anything
         return;
    }
    else {
      // Ensure that it is a number and stop the keypress
      if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
        event.preventDefault();
      }
    }
  });
  $(document).on('keydown', '.countbox,.numofdays', function () {
    // Allow: backspace, delete, tab, escape, enter and .
    if ( $.inArray(event.keyCode,[46,8,9,27,13]) !== -1 ||
       // Allow: Ctrl+A
      (event.keyCode == 65 && event.keyCode == 67 && event.keyCode == 86 || event.ctrlKey === true) ||
       // Allow: home, end, left, right
      (event.keyCode >= 35 && event.keyCode <= 39)) {
         // let it happen, don't do anything
         return;
    }
    else {
      // Ensure that it is a number and stop the keypress
      if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
        event.preventDefault();
      }
    }
  });
  $(document).on('click', '.deleterow', function () {
    var deleted_row_id = $(this).attr('id');  
    //this.parentNode.remove();
    var medicine_name = $('#medicine'+deleted_row_id).val();
   if(medicine_name.replace(/\s+/g, '') == ""){
        $('#pslno tr:last').remove();
      $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').remove();
      medicationcount = $('#medicationcount').val();
      medicationcount = medicationcount - 1;
      if(medicationcount == 1){
        $('.deleterow').hide();
      }
      var deleted_usedprescription = deleted_row_id+",";
      var current_usedprescriptioncount = $('#usedpresciptioncount').val();
      var new_usedprescriptioncount  = current_usedprescriptioncount.replace(deleted_usedprescription, "");
      $('#usedpresciptioncount').val(new_usedprescriptioncount);
      $('#medicationcount').val(medicationcount);
   } else {
    var deleteconfirm = confirm("This medicine will be deleted. Click OK to continue.");
    if(deleteconfirm == true){
      $('#pslno tr:last').remove();
      $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').remove();
      medicationcount = $('#medicationcount').val();
      medicationcount = medicationcount - 1;
      if(medicationcount == 1){
        $('.deleterow').hide();
      }
      var deleted_usedprescription = deleted_row_id+",";
      var current_usedprescriptioncount = $('#usedpresciptioncount').val();
      var new_usedprescriptioncount  = current_usedprescriptioncount.replace(deleted_usedprescription, "");
      $('#usedpresciptioncount').val(new_usedprescriptioncount);
      $('#medicationcount').val(medicationcount);
    } else {

    }
   }

  });
var medicationcount = $('#medicationcount').val();
var propercount = $('#propercount').val();
var shorthand_options = "<?php echo $shorthand_options;?>";
var medicine_options  = "<?php echo $medicine_options;?>";
$('#addMedicine').click(function(){
        medicationcount = $('#medicationcount').val();
        propercount     = $('#propercount').val();
        medicationcount = parseInt(medicationcount) + 1;
        propercount     = parseInt(propercount) + 1;
        $('.deleterow').show();
        var mednamedisplay = "";
        var browser_name = '<?php echo $browser_name; ?>';
        //alert(browser_name);
      if(browser_name == "applesafari"){
      mednamedisplay = "<select style='height:35px;width:100%;background-color:#2B6D57;' name='medicine"+propercount+"' id='medicine"+propercount+"' class='forminputs2'><option value='' style='display:none;'>Select a Medicine</option><?php echo $medicine_options;?></select>";
      }else {
      mednamedisplay = "<input type='text' list='medicine_list'  id='medicine"+propercount+"' name='medicine"+propercount+"' placeholder='Type Medicine Name Here' class='forminputs2'><datalist id='medicine_list'><?php echo $medicine_options;?></datalist>";
      }
        //<td class='paddingrl5' align='right'>Threshold:</td><td class='paddingrl5' align='center'><input type='text' maxlength='2' name='threshold"+propercount+"' id='threshold"+propercount+"' style='width:40px;height:35px;' class='forminputs2 roundedinputs countbox' title='Enter the threshold'></td>

        var first = "<tr style='border-top:4px solid #004f35;'><td class='paddingrl5' align='center' colspan='6'>"+mednamedisplay+"</td><td class='paddingrl5' align='center'><div style='border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;'><input type='text' maxlength='2' name='medcount"+propercount+"' id='medcount"+propercount+"' style='width:35px;height:30px;' class='forminputs2 roundedinputs countbox' title='Enter the count'><select class=' lightcolorselect' id='medcountType"+propercount+"' name='medcountType"+propercount+"' style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;' title='Select Medicine Type'><option value='0' style='display:none;'>select</option><?php echo $medicine_type_options;?></select></div></td><td style='width:300px;' class='paddingrl5'><input type='radio' name='radio"+propercount+"' value='PS' checked class='radio"+propercount+" prescriptionradio'> When the plan Starts/Updates<br></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"' title='Delete this row'></td></tr><tr><td class='paddingrl5' align='center'>When:</td><td class='paddingrl5' align='center'><select name='when"+propercount+"' id='when"+propercount+"' title='Select the medicine dosage' class='whenshorthand'><option value='0' style='display:none;'>select</option><?php echo $shorthand_options;?></select></td><td class='paddingrl5' align='center'><select name='instruction"+propercount+"' id='instruction"+propercount+"' title='Select the medicine instruction'><option value='0' style='display:none;'>select</option><option value='Before Food'>Before Food</option><option value='With Food'>With Food</option><option value='After Food'>After Food</option><option value='NA'>Not Applicable</option></select></td><td class='paddingrl5' align='center'>Frequency :</td><td class='paddingrl5' align='center'><select name='frequency"+propercount+"' id='frequency"+propercount+"' title='Select the medicine frequency' class='medfrequency'><option value='0' style='display:none;'>select</option><option value='Once'>Once</option><option value='Daily'>Daily</option><option value='Weekly'>Weekly</option><option value='Monthly'>Monthly</option></select></td><td class='paddingrl5' align='center'>Duration :</td><td class='paddingrl5' align='center'><div style='border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;'><input type='text' maxlength='2' name='count"+propercount+"' id='count"+propercount+"' style='width:35px;height:30px;' class='forminputs2 roundedinputs countbox' title='Enter the duration'><select class='' id='countType"+propercount+"' name='countType"+propercount+"' style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;' title='Enter the duration'><option value='0' style='display:none;'>select</option><option value='Days'>Days</option><option value='Weeks'>Weeks</option><option value='Months'>Months</option></select></div></td><td><input type='radio' name='radio"+propercount+"' value='ND' class='radio"+propercount+" prescriptionradio'> No. Of days after plan started&nbsp;<input type='text' size='6px' name='numofdays"+propercount+"' id='numofdays"+propercount+"' class='numofdays roundedinputs pointernone' maxlength='2'></td></tr><tr><td>Critical :<input type='checkbox' name='critical"+propercount+"' id='critical"+propercount+"' class='criticalcheck' title='Check this if the medicine is critical' value='Y' style='margin-left:5px;'></td><td align='center'>Response :<input type='checkbox' name='response"+propercount+"' id='response"+propercount+"' class='responsecheck' title='Check this if a response is required' value='Y' style='margin-left:5px;'></td><td align='center'>Threshold:<input type='text' maxlength='2' name='threshold"+propercount+"' id='threshold"+propercount+"' style='width:35px;height:30px;opacity:0.2;' disabled class='forminputs2 roundedinputs countbox' title='Enter the threshold'></td><td colspan='4' style='white-space:nowrap;'><div class='fileinput fileinput-new input-group' data-provides='fileinput' style='width:100%;'><div class='form-control' data-trigger='fileinput'><i class='glyphicon glyphicon-file fileinput-exists'></i><span class='fileinput-filename'></span></div><span class='input-group-addon btn btn-default btn-file'><span class='fileinput-new'>Click To Upload A Document</span><span class='fileinput-exists'>Change</span><input type='file'  name='uploadedfile"+propercount+"' id='uploadedfile"+propercount+"'></span><a href='#' class='input-group-addon btn btn-default fileinput-exists removelinkbutton' id='"+propercount+"' data-dismiss='fileinput'>Remove</a></div> <input type='hidden' name='previouslink"+propercount+"' id='previouslink"+propercount+"'><input type='hidden' name='originalfilename"+propercount+"' id='originalfilename"+propercount+"'><input type='hidden' name='deletelink"+propercount+"' id='deletelink"+propercount+"'  value='0'></td><td><input type='radio' name='radio"+propercount+"' value='SD' class='radio"+propercount+" prescriptionradio'> On Specific Date:&nbsp;<input type='text' size='10px' name='specificdate"+propercount+"' id='specificdate"+propercount+"' class='specificdate pointernone' readonly></td></tr><tr><td colspan='8'><input type='hidden' name='specifictime"+propercount+"' id='specifictime"+propercount+"' class='editedspecifictimes forminputs2' readonly title='Click here to edit specific times'></td></tr><tr><td colspan='8'> <input type='hidden' name='selectedweekdays"+propercount+"' id='selectedweekdays"+propercount+"' value='' class='forminputs2 editselectedweekday' readonly title='Click here to edit frequency'><input type='hidden' name='selectedmonthdays"+propercount+"' id='selectedmonthdays"+propercount+"' value='' class='forminputs2 editselectedmonthday' readonly title='Click here to edit frequency'><div class='bottomshadow' style='width:100%;color:#fff;pointer-events:none;height:12px;'><span style='display:none;'>1</span></div></td></tr>";
        var slno  = "<tr><td>"+medicationcount+"</td></tr>";
        $('#pslno > tbody').append(slno);
        $('#pdata > tbody').append(first);
        var current_usedprescriptioncount = $('#usedpresciptioncount').val();
        var new_usedprescriptioncount = current_usedprescriptioncount+propercount+",";
        $('#usedpresciptioncount').val(new_usedprescriptioncount);
        $('#medicationcount').val(medicationcount);
        $('#propercount').val(propercount);
        $('#medicine'+propercount).focus();
      });
</script>
</html>