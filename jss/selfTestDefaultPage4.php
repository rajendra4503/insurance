<?php 
  session_start();
  //ini_set("display_errors","0");
  include_once('include/configinc.php');
  include('include/session.php');
  //GET INSTRUCTIONS
  $instruction_options = "";
  $get_instruction = mysql_query("select InstructionID, Instruction from INSTRUCTION_MASTER where InstructionID!='20'");
  $instruction_count = mysql_num_rows($get_instruction);
  if($instruction_count > 0){
    while ($instruction = mysql_fetch_array($get_instruction)) {
      $instruction_id  = $instruction['InstructionID'];
      $instructionname = $instruction['Instruction'];
      $instruction_options .= "<option value='$instruction_id'>$instructionname</option>";
    }
  }

    //MEDICAL TESTS SELECT BOX
  $medical_options = "";
  $get_medical_test = mysql_query("select ID, TestName from MEDICAL_TESTS order by TestName");
  $get_medical_test_count = mysql_num_rows($get_medical_test);
  if($get_medical_test_count > 0){
    while ($medical_test = mysql_fetch_array($get_medical_test)) {
      $medical_test_id  = $medical_test['ID'];
      $medical_test_name = $medical_test['TestName'];
      $medical_options .= "<option value='$medical_test_id'>$medical_test_name</option>";      
    }
  //echo $medical_options;exit;
  }
  $plancode = $_SESSION['plancode_for_current_plan'];
  $selfno  = "1";
?>
<!DOCTYPE html>
<html lang="en">
<body>
  <div>
   <form name="frm_plan_selftest" id="frm_plan_selftest" method="post" action="plan_self_new.php" enctype="multipart/form-data">
      <span style="float:left;width:3%;">
        <table id="stslno" style="display:none;">
          <tr><th>#</th></tr>
          <tr><td>1</td></tr>
          <tr><td>2</td></tr>
        </table>
      </span>
      <div class="table-responsive" style="float:left;width:99%;padding-bottom:100px;">
      <table id="pdata" class="table">
        <?php
  $get_self = "select PlanCode, SelfTestID, RowNo, MedicalTestID, TestName, DoctorsName, TestDescription, Instruction, Frequency, FrequencyString, HowLong, HowLongType, Link, OriginalFileName, ResponseRequired, StartFlag, NoOfDaysAfterPlanStarts, SpecificDate from SELF_TEST_DETAILS where PlanCode='$plancode' and SelfTestID = '$selfno'";
  //echo $get_self;exit;
  $get_self_run = mysql_query($get_self);
  $get_self_count = mysql_num_rows($get_self_run);
  //echo $get_self_count;exit;
  $i = 0;
  $self_count_string = "";
  if($get_self_count > 0){
  while ($selfrow = mysql_fetch_array($get_self_run)) {
    $i++;
    $self_count_string       .= $i.",";
    $PlanCode               = $selfrow['PlanCode'];
    $SelfTestID             = $selfrow['SelfTestID'];
    $RowNo                  = $selfrow['RowNo'];
    $MedicalTestID          = $selfrow['MedicalTestID'];
    $TestName               = $selfrow['TestName'];
    $TestNameStyle          = "style='height:40px;width:100%;'";
    if($MedicalTestID != "0"){
      $TestName               = "";
      $TestNameStyle          = "disabled style='height:40px;width:100%;opacity:0.2;'";
    }
    $DoctorsName            = $selfrow['DoctorsName'];
    $TestDescription        = $selfrow['TestDescription'];
    $Instruction            = $selfrow['Instruction'];
    $Frequency              = $selfrow['Frequency'];
    $FrequencyString      = (empty($selfrow['FrequencyString']))? '' : $selfrow['FrequencyString'];
    $WeeklyType         = "type='hidden'";
    $MonthlyType        = "type='hidden'";
    if($Frequency== "Weekly"){
      $WeeklyType= "type='text'";
    }
    if($Frequency== "Monthly"){
      $MonthlyType= "type='text'";
    }
    $CountSelect1     = "style='width:35px;height:30px;'";
    $CountSelect2     = "style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;";
    if($Frequency == "Once"){
    $CountSelect1     = "disabled style='width:35px;height:30px;opacity:0.2;'";
    $CountSelect2     = "disabled style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;opacity:0.2;'";
    }
    $HowLong          = (empty($selfrow['HowLong']))  ? '' : $selfrow['HowLong'];
    $HowLongType        = $selfrow['HowLongType'];
    $StartFlag          = $selfrow['StartFlag'];
    $NoOfDaysAfterPlanStarts  = "";
    $SpecificDate       = "";
    $NumOfDaysRadio = "";
    $SpecificDateRadio  = "";
    $PlanStartRadio   = "";
    if($StartFlag== "PS"){
      $PlanStartRadio   = "checked";
    } else {
      $PlanStartRadio   = "";
    }

    if($StartFlag== "SD"){
      $SpecificDateRadio  = "checked";
      $SpecificDate   = $selfrow['SpecificDate'];
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
      $NoOfDaysAfterPlanStarts    = $selfrow['NoOfDaysAfterPlanStarts'];
    } else {
      $NumOfDaysRadio = "";
    }
    
    $Link           = (empty($selfrow['Link']))  ? '' : $selfrow['Link'];
    $OriginalFileName = (empty($selfrow['OriginalFileName'])) ? '' : $selfrow['OriginalFileName'];

       //MEDICAL TESTS SELECT BOX
  $MedicalTestOptions = "";
  $get_medical_test = mysql_query("select ID, TestName from MEDICAL_TESTS order by TestName");
  $get_medical_test_count = mysql_num_rows($get_medical_test);
  if($get_medical_test_count > 0){
    while ($medical_test = mysql_fetch_array($get_medical_test)) {
      $medical_test_id  = $medical_test['ID'];
      $medical_test_name = $medical_test['TestName'];
      if($MedicalTestID == $medical_test_id){
            $MedicalTestOptions .= "<option value='$medical_test_id' selected>$medical_test_name</option>";
      } else {
            $MedicalTestOptions .= "<option value='$medical_test_id'>$medical_test_name</option>";
      }
      
    }
  }

    //INSTRUCTION SELECT BOX
  $InstructionOptions = "";
  $get_instruction = mysql_query("select InstructionID, Instruction from INSTRUCTION_MASTER where InstructionID!='20'");
  $instruction_count = mysql_num_rows($get_instruction);
  if($instruction_count > 0){
    while ($instruction = mysql_fetch_array($get_instruction)) {
      $instruction_id  = $instruction['InstructionID'];
      $instructionname = $instruction['Instruction'];
      if($Instruction == $instruction_id){
            $InstructionOptions .= "<option value='$instruction_id' selected>$instructionname</option>";
      } else {
            $InstructionOptions .= "<option value='$instruction_id'>$instructionname</option>";
      }
      
    }
  }
    if($MedicalTestID == "5"){
    $InstructionOptions = "";
        if($Instruction == "5"){
          $InstructionOptions .= "<option value='5' selected>After Breakfast</option>";
        } else {
          $InstructionOptions .= "<option value='5'>After Breakfast</option>";
        }
        if($Instruction == "9"){
          $InstructionOptions .= "<option value='9' selected>After Lunch</option>";
        } else {
          $InstructionOptions .= "<option value='9'>After Lunch</option>";
        }
        if($Instruction == "18"){
          $InstructionOptions .= "<option value='18' selected>After Dinner</option>";
        } else {
          $InstructionOptions .= "<option value='18'>After Dinner</option>";
        }
      }
//echo $instruction_id;
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
          <td style="padding:5px;" align="center" colspan="2">
          <select name="selftestName<?php echo $i;?>" id="selftestName<?php echo $i;?>" style="width:100%;" class='testnameselect'>
            <option style="display:none;" value="select">Select Test Name</option>
            <?php echo $MedicalTestOptions;?>
           </select>
           </td>
           <td style="padding:5px;" align="center" colspan="2">
            <textarea rows="1" name="selftestOther<?php echo $i;?>" id="selftestOther<?php echo $i;?>" placeholder="Enter Self Test Name.." maxlength="100" title="Enter Self Test Name" <?php echo $TestNameStyle;?>><?php echo $TestName;?></textarea>
          </td>
           <td style="padding:5px;" align="center" colspan="3">
            <textarea rows="1" name="doctorName<?php echo $i;?>" id="doctorName<?php echo $i;?>" placeholder="Enter Doctor Name.." maxlength="25" title="Enter Self Test Name" style="height:40px;width:100%;"><?php echo $DoctorsName;?></textarea>
          </td>
          <td style="width:300px;" style="padding:5px;">
            <input type="radio" name="radio<?php echo $i;?>" value="PS" checked class="radio<?php echo $i;?> prescriptionradio" <?php echo $PlanStartRadio;?>> When the plan Starts/Updates<br>
          </td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="<?php echo $i;?>" title="Delete this row"></td>
        </tr>
        <tr>
          
          <td style="padding:5px;" align="center">
            
          </td>
          <td style="padding:5px;" align="left">Instruction:</td>
          <td style="padding:5px;" align="center">
              <select name="when<?php echo $i;?>" id="when<?php echo $i;?>">
                <option style="display:none;" value="0">select</option>
                <?php echo $InstructionOptions;?>
              </select>
              </td>
          <td style="padding:5px;" align="center">Frequency:</td>
          <td style="padding:5px;" align="center">
            <select name="frequency<?php echo $i;?>" id="frequency<?php echo $i;?>" title="Select the selfttest frequency" class="testfrequency">
              <option value="0" style="display:none;">select</option>
              <?php echo $FrequencyOptions;?>
            </select>
          </td>
          <td style="padding:5px;" align="center">
            Duration :
          </td>
          <td style="padding:5px;" align="center">
             <div style="border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;">
            <input type="text" maxlength="2" name="count<?php echo $i;?>" id="count<?php echo $i;?>" class="forminputs2 roundedinputs countbox" title="Enter the duration" value='<?php echo $HowLong;?>' <?php echo $CountSelect1;?>>
            <select id="countType<?php echo $i;?>" name="countType<?php echo $i;?>" title="Enter the duration"  <?php echo $CountSelect2;?>>
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
          <td style="padding:5px;" align="center" colspan="7">
            <textarea rows="1" name="selftestdesc<?php echo $i;?>" placeholder="Enter Requirements to selftest.." class="forminputs2" id="selftestdesc<?php echo $i;?>" style="height:40px;width:100%;"><?php echo $TestDescription;?></textarea>
          </td>
           <td><input type="radio" name="radio<?php echo $i;?>" value="SD" class="radio<?php echo $i;?> prescriptionradio" <?php echo $SpecificDateRadio;?>> On Specific Date:&nbsp;<input type="text" size="10px" name="specificdate<?php echo $i;?>" id="specificdate<?php echo $i;?>" class='specificdate <?php if($StartFlag!= "SD"){echo "pointernone";}?>' value='<?php echo $SpecificDate;?>'></td>
        </tr>
        <tr>
          <td>
            <td  style="padding:5px;">Link :</td>
           <!-- <td colspan="5"  style="padding:5px;"><input type="text" name="linkentered<?php echo $i;?>" id="linkentered<?php echo $i;?>"  class="forminputs2" title="Enter Link here" placeholder="Enter Link Here (Optional)" value='<?php echo $Link;?>'></td> -->
           <td colspan="5" style="padding:5px;">
             <div class="fileinput fileinput-new input-group" data-provides="fileinput" style="width:100%;">
                      <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"><?php echo  $OriginalFileName;?></span></div>
                      <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Click To Upload A Document</span><span class="fileinput-exists">Change</span><input type="file"  name="uploadedfile<?php echo $i;?>" id="uploadedfile<?php echo $i;?>"></span>
                      <a href="#" class="input-group-addon btn btn-default fileinput-exists removelinkbutton" id="<?php echo $i;?>" data-dismiss="fileinput">Remove</a>
                    </div> 
                    <input type="hidden" name="previouslink<?php echo $i;?>" id="previouslink<?php echo $i;?>"  value='<?php echo $Link;?>'>
                     <input type="hidden" name="originalfilename<?php echo $i;?>" id="originalfilename<?php echo $i;?>"  value='<?php echo $OriginalFileName;?>'>
                     <input type="hidden" name="deletelink<?php echo $i;?>" id="deletelink<?php echo $i;?>"  value='0'>
           </td>
          </td>
           <td colspan="2" style="padding:5px;"></td>
        </tr>
        <tr>
          <td colspan="8">
             <input <?php echo $WeeklyType;?> name="selectedweekdays<?php echo $i;?>" id="selectedweekdays<?php echo $i;?>" class="forminputs2 editselectedweekday" readonly title="Click here to edit frequency" value='<?php echo $FrequencyString;?>'>
            <input <?php echo $MonthlyType;?> name="selectedmonthdays<?php echo $i;?>" id="selectedmonthdays<?php echo $i;?>" value="<?php echo $FrequencyString;?>" class="forminputs2 editselectedmonthday" readonly title="Click here to edit frequency">
            <div class="bottomshadow" style="width:100%;color:#fff;pointer-events:none;height:12px;"><span style='display:none;'>1</span></div>
          </td>
        </tr>
        <?php }} else {
          $self_count_string = "1,2,";
          $i = "2";
        for($j=1;$j<=2;$j++){
          ?>
<tr style="border-top:4px solid #004f35;">
          <td style="padding:5px;" align="center" colspan="2">
          <select name="selftestName<?php echo $j;?>" id="selftestName<?php echo $j;?>" style="width:100%;" class='testnameselect'>
            <option style="display:none;" value="select">Select Test Name</option>
                  <?php echo $medical_options;?>
           </select>
           </td>
           <td style="padding:5px;" align="center" colspan="2">
            <textarea rows="1" name="selftestOther<?php echo $j;?>" id="selftestOther<?php echo $j;?>" placeholder="Enter Self Test Name.." maxlength="100" title="Enter Self Test Name" style="height:40px;width:100%;" disabled></textarea>
          </td>
           <td style="padding:5px;" align="center" colspan="3">
            <textarea rows="1" name="doctorName<?php echo $j;?>" id="doctorName<?php echo $j;?>" placeholder="Enter Doctor Name.." maxlength="25" title="Enter Self Test Name" style="height:40px;width:100%;"></textarea>
          </td>
          <td style="width:300px;" style="padding:5px;">
            <input type="radio" name="radio<?php echo $j;?>" value="PS" checked class="radio<?php echo $j;?> prescriptionradio"> When the plan Starts/Updates<br>
          </td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="<?php echo $j;?>" title="Delete this row"></td>
        </tr>
        <tr>
          
          <td style="padding:5px;" align="center">
            
          </td>
          <td style="padding:5px;" align="left">Instruction:</td>
          <td style="padding:5px;" align="center">
              <select name="when<?php echo $j;?>" id="when<?php echo $j;?>">
                <option style="display:none;" value="0">select</option>
                <?php echo $instruction_options;?>
              </select>
              </td>
          <td style="padding:5px;" align="center">Frequency:</td>
          <td style="padding:5px;" align="center">
            <select name="frequency<?php echo $j;?>" id="frequency<?php echo $j;?>" title="Select the selfttest frequency" class="testfrequency">
              <option value="0" style="display:none;">select</option>
              <option value="Once">Once</option>
              <option value="Daily">Daily</option>
              <option value="Weekly">Weekly</option>
              <option value="Monthly">Monthly</option>
            </select>
          </td>
          <td style="padding:5px;" align="center">
            Duration :
          </td>
          <td style="padding:5px;" align="center">
             <div style="border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;">
            <input type="text" maxlength="2" name="count<?php echo $j;?>" id="count<?php echo $j;?>" style="width:35px;height:30px;" class="forminputs2 roundedinputs countbox" title="Enter the duration">
            <select id="countType<?php echo $j;?>" name="countType<?php echo $j;?>" style="width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;" title="Enter the duration">
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
          <td style="padding:5px;" align="center" colspan="7">
            <textarea rows="1" name="selftestdesc<?php echo $j;?>" placeholder="Enter Requirements to selftest.." class="forminputs2" id="selftestdesc<?php echo $j;?>" style="height:40px;width:100%;"></textarea>
          </td>
          <td><input type="radio" name="radio<?php echo $j;?>" value="SD" class="radio<?php echo $j;?> prescriptionradio" > On Specific Date:&nbsp;<input type="text" size="10px" name="specificdate<?php echo $j;?>" id="specificdate<?php echo $j;?>" class='specificdate pointernone' readonly></td>
        </tr>
        <tr>
          <td>
            <td  style="padding:5px;">Link :</td>
          <!--  <td colspan="5"  style="padding:5px;"><input type="text" name="linkentered<?php echo $j;?>" id="linkentered<?php echo $j;?>"  class="forminputs2" title="Enter Link here" placeholder="Enter Link Here (Optional)"></td> -->
          <td colspan="5" style="padding:5px;">
             <div class="fileinput fileinput-new input-group" data-provides="fileinput" style="width:100%;">
                      <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                      <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Click To Upload A Document</span><span class="fileinput-exists">Change</span><input type="file"  name="uploadedfile<?php echo $j;?>" id="uploadedfile<?php echo $j;?>"></span>
                      <a href="#" class="input-group-addon btn btn-default fileinput-exists removelinkbutton" id="<?php echo $j;?>" data-dismiss="fileinput">Remove</a>
                    </div> 
                     <input type="hidden" name="previouslink<?php echo $j;?>" id="previouslink<?php echo $j;?>" >
                     <input type="hidden" name="originalfilename<?php echo $j;?>" id="originalfilename<?php echo $j;?>" >
                     <input type="hidden" name="deletelink<?php echo $j;?>" id="deletelink<?php echo $j;?>"  value='0'>
           </td>
           <td colspan="2" style="padding:5px;"></td>
        </tr>
        <tr>
          <td colspan="8">
             <input type='hidden' name="selectedweekdays<?php echo $j;?>" id="selectedweekdays<?php echo $j;?>" class="forminputs2 editselectedweekday" readonly title="Click here to edit frequency">
            <input type='hidden' name="selectedmonthdays<?php echo $j;?>" id="selectedmonthdays<?php echo $j;?>"  class="forminputs2 editselectedmonthday" readonly title="Click here to edit frequency">
            <div class="bottomshadow" style="width:100%;color:#fff;pointer-events:none;height:12px;"><span style='display:none;'>1</span></div>
          </td>
        </tr>
        
          <?php
        }
          } ?>
      </table>
      </div>
      

      <div id="ActionBar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="position: fixed;bottom: 0;background-color: #fff;text-align: center;margin-left: -5%;width:90%;">
        <button type="button" id="addselftest" class="btns formbuttons">ADD A TEST</button>
        <button type="button" id="saveAndEdit" class="btns formbuttons">SAVE</button>
        <!--<button type="button" id="saveTemplate" class="btns formbuttons">SAVE THIS AS TEMPLATE</button>-->
      </div>
       <input type="hidden" name="usedselftestcount" id="usedselftestcount" value="<?php echo $self_count_string;?>">
       <input type="hidden" name="selftestcount" id="selftestcount" value="<?php echo $i;?>">
       <input type="hidden" name="propercount" id="propercount" value="<?php echo $i;?>">
       <input type="hidden" name="hidden_value" id="hidden_value" value="1">
    </form>
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
    var selftest_name = $('#selftestName'+deleted_row_id).val();
    //alert(selftest_name);
       if(selftest_name.replace(/\s+/g, '') == 'select'){
        //alert(deleted_row_id);
         $('#stslno tr:last').remove();
        //this.parentNode.parentNode.parentNode.parentNode.remove();
        $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').remove();
        selftestcount = parseInt(selftestcount) - 1;
        if(selftestcount == 1){
          $('.deleterow').hide();
        }
      var deleted_usedselftestcount = deleted_row_id+",";
       var current_usedselftestcount = $('#usedselftestcount').val();
      var new_usedselftestcount  = current_usedselftestcount.replace(deleted_usedselftestcount, "");
      $('#usedselftestcount').val(new_usedselftestcount);
      $('#selftestcount').val(selftestcount);
   } else {
    var deleteconfirm = confirm("This test will be deleted. Click OK to continue.");
    if(deleteconfirm == true){
      $('#stslno tr:last').remove();
        $(this).closest('tr').next().remove();//To remove the next tr
        $(this).closest('tr').next().remove();//To remove the next tr
        $(this).closest('tr').next().remove();//To remove the next tr
        $(this).closest('tr').next().remove();//To remove the next tr
        $(this).closest('tr').remove();
        selftestcount = parseInt(selftestcount) - 1;
        if(selftestcount == 1){
          $('.deleterow').hide();
        }
      var deleted_usedselftestcount = deleted_row_id+",";
       var current_usedselftestcount = $('#usedselftestcount').val();
      var new_usedselftestcount  = current_usedselftestcount.replace(deleted_usedselftestcount, "");
      $('#usedselftestcount').val(new_usedselftestcount);
      $('#selftestcount').val(selftestcount);
      $('#propercount').val(propercount);
    } else {

    }
   }

    
  });
var selftestcount = $('#selftestcount').val();
var propercount = $('#propercount').val();
$('#addselftest').click(function(){
        selftestcount = $('#selftestcount').val();
        propercount     = $('#propercount').val();
        var instruction_options = "<?php echo $instruction_options;?>";
        var medical_options = "<?php echo $medical_options;?>";
        selftestcount = parseInt(selftestcount) + 1;
        propercount     = parseInt(propercount) + 1;
        $('.deleterow').show();
        var first = "<tr style='border-top:4px solid #004f35;'><td style='padding:5px;' align='center' colspan='2'><select name='selftestName"+propercount+"' id='selftestName"+propercount+"' style='width:100%;' class='testnameselect'><option style='display:none;' value='select'>Select Test Name</option>"+medical_options+"</select> </td> <td style='padding:5px;' align='center' colspan='2'><textarea rows='1' name='selftestOther"+propercount+"' id='selftestOther"+propercount+"' placeholder='Enter Self Test Name..' maxlength='100' title='Enter Self Test Name' style='height:40px;width:100%;' disabled></textarea></td> <td style='padding:5px;' align='center' colspan='3'><textarea rows='1' name='doctorName"+propercount+"' id='doctorName"+propercount+"' placeholder='Enter Doctor Name..' maxlength='25' title='Enter Self Test Name' style='height:40px;width:100%;'></textarea></td><td style='width:300px;' style='padding:5px;'><input type='radio' name='radio"+propercount+"' value='PS' checked class='radio"+propercount+" prescriptionradio'> When the plan Starts/Updates<br></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"' title='Delete this row'></td></tr><tr><td style='padding:5px;' align='center'></td><td style='padding:5px;' align='left'>Instruction:</td><td style='padding:5px;' align='center'><select name='when"+propercount+"' id='when"+propercount+"'><option style='display:none;' value='0'>select</option>"+instruction_options+"</select></td><td style='padding:5px;' align='center'>Frequency:</td><td style='padding:5px;' align='center'><select name='frequency"+propercount+"' id='frequency"+propercount+"' title='Select the selfttest frequency' class='testfrequency'><option value='0' style='display:none;'>select</option><option value='Once'>Once</option><option value='Daily'>Daily</option><option value='Weekly'>Weekly</option><option value='Monthly'>Monthly</option></select></td><td style='padding:5px;' align='center'>Duration :</td><td style='padding:5px;' align='center'> <div style='border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;'><input type='text' maxlength='2' name='count"+propercount+"' id='count"+propercount+"' style='width:35px;height:30px;' class='forminputs2 roundedinputs countbox' title='Enter the duration'><select id='countType"+propercount+"' name='countType"+propercount+"' style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;' title='Enter the duration'><option value='0' style='display:none;'>select</option><option value='Days'>Days</option><option value='Weeks'>Weeks</option><option value='Months'>Months</option></select></div></td><td><input type='radio' name='radio"+propercount+"' value='ND' class='radio"+propercount+" prescriptionradio'> No. Of days after plan started&nbsp;<input type='text' size='6px' name='numofdays"+propercount+"' id='numofdays"+propercount+"' class='numofdays roundedinputs pointernone' maxlength='2'></td></tr><tr><td style='padding:5px;' align='center' colspan='7'><textarea rows='1' name='selftestdesc"+propercount+"' placeholder='Enter Requirements to selftest..' class='forminputs2' id='selftestdesc"+propercount+"' style='height:40px;width:100%;'></textarea></td><td><input type='radio' name='radio"+propercount+"' value='SD' class='radio"+propercount+" prescriptionradio' > On Specific Date:&nbsp;<input type='text' size='10px' name='specificdate"+propercount+"' id='specificdate"+propercount+"' class='specificdate pointernone' readonly></td></tr><tr><td><td style='padding:5px;'>Link :</td><td colspan='5' style='padding:5px;'><div class='fileinput fileinput-new input-group' data-provides='fileinput' style='width:100%;'><div class='form-control' data-trigger='fileinput'><i class='glyphicon glyphicon-file fileinput-exists'></i><span class='fileinput-filename'></span></div><span class='input-group-addon btn btn-default btn-file'><span class='fileinput-new'>Click To Upload A Document</span><span class='fileinput-exists'>Change</span><input type='file' name='uploadedfile"+propercount+"' id='uploadedfile"+propercount+"'></span><a href='#' class='input-group-addon btn btn-default fileinput-exists removelinkbutton' id='"+propercount+"' data-dismiss='fileinput'>Remove</a></div><input type='hidden' name='previouslink"+propercount+"' id='previouslink"+propercount+"'><input type='hidden' name='originalfilename"+propercount+"' id='originalfilename"+propercount+"'><input type='hidden' name='deletelink"+propercount+"' id='deletelink"+propercount+"'  value='0'></td><td colspan='2' style='padding:5px;'></td></tr><tr><td colspan='8'> <input type='hidden' name='selectedweekdays"+propercount+"' id='selectedweekdays"+propercount+"' class='forminputs2 editselectedweekday' readonly title='Click here to edit frequency'><input type='hidden' name='selectedmonthdays"+propercount+"' id='selectedmonthdays"+propercount+"class='forminputs2 editselectedmonthday' readonly title='Click here to edit frequency'><div class='bottomshadow' style='width:100%;color:#fff;pointer-events:none;height:12px;'><span style='display:none;'>1</span></div></td></tr>";
        var slno  = "<tr><td>"+selftestcount+"</td></tr>";
        $('#stslno > tbody').append(slno);
        $('#pdata').append(first);
        var current_usedselftestcount = $('#usedselftestcount').val();
        var new_usedselftestcount = current_usedselftestcount+propercount+",";
        $('#usedselftestcount').val(new_usedselftestcount);
        $('#selftestcount').val(selftestcount);
        $('#propercount').val(propercount);
        $('#selftestName'+propercount).focus();
      });
</script>
</html>