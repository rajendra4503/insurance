<?php 
session_start();
ini_set("display_errors","0");
include_once('include/configinc.php');
include('include/session.php');
$plancode = $_SESSION['plancode_for_current_plan'];

mysql_set_charset('utf8');
  if( !empty( $_SESSION['LANGUAGE']) && $_SESSION['LANGUAGE'] != '' ){
    $table = $_SESSION['LANGUAGE'];
    if(table_exists($table)){
      $query  = mysql_query("SELECT FieldNo ,$table  FROM $table WHERE ScreenNo='012'");
      while ( $result = mysql_fetch_assoc($query) ) {
        ${$result['FieldNo']} = $result[$table];
      } 
    }
    
  }


?>
<!DOCTYPE html>
<html lang="en">
<body>
  <div>
   <form name="frm_plan_appointments" id="frm_plan_appointments" method="post" action="plan_appo_new.php">
      <!--<div class="appointmentNameBar">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="center" style="background-color:#BFBFBF;">
          <span>Doctor's Name:</span>
          <input type="text" name="doctorName" placeholder="Enter Doctor Name.." size="25px" maxlength="25" id="doctorName" class="forminputs">
        </div>
      </div>-->
      <span style="float:left;width:3%;">
        <table id="aslno" style="display:none;">
          <tr><th>#</th></tr>
          <tr><td>1</td></tr>
          <tr><td>2</td></tr>
          <tr><td>3</td></tr>
        </table>
      </span>
      <div class="table-responsive" style="float:left;width:95%;padding-bottom:100px;">
      <table id="adata" class="table table-striped">
      <tr id="aheader">

          <th><?php if(isset($Appointmentshortname) &&  $Appointmentshortname !=''){echo $Appointmentshortname;} else{echo 'Appointment short name ';}?></th>

          <th style="width:180px;"><?php if(isset($DoctorName) &&  $DoctorName !=''){echo $DoctorName;} else{echo 'Doctors Name';}?></th>

          <th style="width:130px;"> <?php if(isset($DateTime) &&  $DateTime !=''){echo $DateTime;} else{echo 'Date & Time';}?></th>

          <th style="width:100px;"><?php if(isset($Duration) &&  $Duration !=''){echo $Duration;} else{echo 'Duration ';}?></th>

          <th> <?php if(isset($Venue) &&  $Venue !=''){echo $Venue;} else{echo 'Venue';}?></th>

          <th> <?php if(isset($Requirements) &&  $Requirements !=''){echo $Requirements;} else{echo 'Requirements';}?></th>

          <th></th>
        </tr>
        <?php 
          $appo_count = 0;
          $appo_count_string = "";
          $get_appo = "select  PlanCode, AppointmentShortName, DoctorsName, AppointmentDate, AppointmentTime, AppointmentRequirements, AppointmentDuration, AppointmentPlace from APPOINTMENT_DETAILS where PlanCode = '$plancode'";
         // echo $get_appo;exit;
          $get_appo_run = mysql_query($get_appo);
          $get_appo_count = mysql_num_rows($get_appo_run);
          if($get_appo_count > 0){
            while ($appo_row = mysql_fetch_array($get_appo_run)) {
              $appo_count++;
              $appo_count_string    .= $appo_count.",";
              $appo_short_name      = $appo_row['AppointmentShortName'];
              $appo_doc_name        = $appo_row['DoctorsName'];
              $appo_date            = date('d-M-Y',strtotime($appo_row['AppointmentDate']));
              $appo_time            = date('h:i A',strtotime($appo_row['AppointmentTime']));
              $appo_req             = $appo_row['AppointmentRequirements'];
              $appo_dur             = $appo_row['AppointmentDuration'];
              $dur_array            = explode(":", $appo_dur);
              $dur_inh              = $dur_array[0];
              $dur_inm              = $dur_array[1];
              $appo_ven             = $appo_row['AppointmentPlace'];
              ?>
              <tr>

                <td><input type="text" name="appointmentName<?php echo $appo_count;?>" placeholder="<?php if(isset($EnterAppointmentName) &&  $EnterAppointmentName !=''){echo $EnterAppointmentName;} else{echo 'Enter Appointment Name..';}?>" maxlength="25" id="appointmentName<?php echo $appo_count;?>" class="forminputs2" value="<?php echo $appo_short_name;?>"></td>

                <td><input type="text" name="doctorName<?php echo $appo_count;?>" placeholder="<?php if(isset($EnterDoctorName) &&  $EnterDoctorName !=''){echo $EnterDoctorName;} else{echo 'Enter Doctor Name..';}?>" maxlength="25" id="doctorName<?php echo $appo_count;?>" class="forminputs2" value="<?php echo $appo_doc_name;?>"></td>

                <td><input type="text" name="appointment_date<?php echo $appo_count;?>" placeholder="<?php if(isset($Date) &&  $Date !=''){echo $Date;} else{echo 'Date';}?>" maxlength="25" id="appointment_date<?php echo $appo_count;?>" readonly class="forminputs2 appoinmentdate" value="<?php echo $appo_date;?>" style='height:30px;'>

                <div class="input-append bootstrap-timepicker"><span class="add-on"><input type="text" name="appointment_time<?php echo $appo_count;?>" placeholder=" <?php if(isset($Time) &&  $Time !=''){echo $Time;} else{echo 'Time';}?>" maxlength="25" id="appointment_time<?php echo $appo_count;?>" readonly class="forminputs2 appointmenttime" value="<?php echo $appo_time;?>" style='height:30px;width:100%;'></span></div>
                </td>
                <td>
                  <select name="duration_inhours<?php echo $appo_count;?>" id="duration_inhours<?php echo $appo_count;?>" class="forminputs2 durationinhours" style='height:30px;width:100%;'>
                    <option value="00" <?php if($dur_inh == "00"){echo "selected";}?>>0 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="01" <?php if($dur_inh == "01"){echo "selected";}?>>1 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="02" <?php if($dur_inh == "02"){echo "selected";}?>>2 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="03" <?php if($dur_inh == "03"){echo "selected";}?>>3 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="04" <?php if($dur_inh == "04"){echo "selected";}?>>4 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="05" <?php if($dur_inh == "05"){echo "selected";}?>>5 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="06" <?php if($dur_inh == "06"){echo "selected";}?>>6 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="07" <?php if($dur_inh == "07"){echo "selected";}?>>7 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="08" <?php if($dur_inh == "08"){echo "selected";}?>>8 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="09" <?php if($dur_inh == "09"){echo "selected";}?>>9 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="10" <?php if($dur_inh == "10"){echo "selected";}?>>10 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="11" <?php if($dur_inh == "11"){echo "selected";}?>>11 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="12" <?php if($dur_inh == "12"){echo "selected";}?>>12 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                  </select>
                  <select name="duration_inmins<?php echo $appo_count;?>" id="duration_inmins<?php echo $appo_count;?>" class="forminputs2 durationinmins" style='height:30px;width:100%;'>
                    <option value="00" <?php if($dur_inm == "00"){echo "selected";}?>>0 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="05" <?php if($dur_inm == "05"){echo "selected";}?>>5 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="10" <?php if($dur_inm == "10"){echo "selected";}?>>10 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="15" <?php if($dur_inm == "15"){echo "selected";}?>>15 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="20" <?php if($dur_inm == "20"){echo "selected";}?>>20 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="25" <?php if($dur_inm == "25"){echo "selected";}?>>25 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="30" <?php if($dur_inm == "30"){echo "selected";}?>>30 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="35" <?php if($dur_inm == "35"){echo "selected";}?>>35 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="40" <?php if($dur_inm == "40"){echo "selected";}?>>40 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="45" <?php if($dur_inm == "45"){echo "selected";}?>>45 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="50" <?php if($dur_inm == "50"){echo "selected";}?>>50 mins</option>
                    <option value="55" <?php if($dur_inm == "55"){echo "selected";}?>>55 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                  </select>
                </td>
                <td><textarea name="venue<?php echo $appo_count;?>" id="venue<?php echo $appo_count;?>" placeholder="<?php if(isset($EnterVenueappointment) &&  $EnterVenueappointment !=''){echo $EnterVenueappointment;} else{echo 'Enter Venue for the appointment.. ';}?>" class="forminputs2 venuedetails" maxlength='250'><?php echo $appo_ven;?></textarea></td>
                <td><textarea name="requirements<?php echo $appo_count;?>"  placeholder="<?php if(isset($EnterRequirementsappointment) &&  $EnterRequirementsappointment !=''){echo $EnterRequirementsappointment;} else{echo 'Enter Requirements for the appointment..';}?>" class="forminputs2"><?php echo $appo_req;?></textarea></td>
                <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="<?php echo $appo_count;?>"></td>
              </tr>
              <?php
            }
          } else {
            $appo_count_string = "1,2,3,";
            $appo_count = "3";
            for ($j=1; $j <= 3; $j++) { 
              ?>
        <tr>
          <td><input type="text" name="appointmentName<?php echo $j;?>" placeholder="<?php if(isset($EnterAppointmentName) &&  $EnterAppointmentName !=''){echo $EnterAppointmentName;} else{echo 'Enter Appointment Name..';}?>" maxlength="25" id="appointmentName<?php echo $j;?>" class="forminputs2"></td>
          <td><input type="text" name="doctorName<?php echo $j;?>" placeholder="<?php if(isset($EnterDoctorName) &&  $EnterDoctorName !=''){echo $EnterDoctorName;} else{echo 'Enter Doctor Name..';}?>" maxlength="25" id="doctorName<?php echo $j;?>" class="forminputs2"></td>
          <td><input type="text" name="appointment_date<?php echo $j;?>" placeholder="<?php if(isset($Date) &&  $Date !=''){echo $Date;} else{echo 'Date';}?>" maxlength="25" id="appointment_date<?php echo $j;?>" readonly class="forminputs2 appoinmentdate" style='height:30px;'>
          <div class="input-append bootstrap-timepicker"><span class="add-on"> <input type="text" name="appointment_time<?php echo $j;?>" placeholder="<?php if(isset($Time) &&  $Time !=''){echo $Time;} else{echo 'Time';}?>" maxlength="25" id="appointment_time<?php echo $j;?>" readonly class="forminputs2 appointmenttime"  style='height:30px;width:100%;'></span></div>
          </td>
          <td>
                  <select name="duration_inhours<?php echo $j;?>" id="duration_inhours<?php echo $j;?>" class="forminputs2 durationinhours" style='height:30px;width:100%;'>
                    <option value="00">0 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="01">1 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="02">2 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="03">3 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="04">4 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="05">5 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="06">6 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="07">7 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="08">8 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="09">9 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="10">10 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="11">11 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                    <option value="12">12 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option>
                  </select>
                  <select name="duration_inmins<?php echo $j;?>" id="duration_inmins<?php echo $j;?>" class="forminputs2 durationinmins" style='height:30px;width:100%;'>
                    <option value="00">0 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="05">5 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="10" selected>10 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="15">15 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="20">20 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="25">25 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="30">30 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="35">35 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="40">40 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="45">45 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="50">50 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                    <option value="55">55 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option>
                  </select>
                </td>
          <td><textarea name="venue<?php echo $j;?>" id="venue<?php echo $j;?>" placeholder="<?php if(isset($EnterVenueappointment) &&  $EnterVenueappointment !=''){echo $EnterVenueappointment;} else{echo 'Enter Venue for the appointment.. ';}?>" class="forminputs2 venuedetails" maxlength='250'></textarea></td>
          <td><textarea name="requirements<?php echo $j;?>"  placeholder="<?php if(isset($EnterRequirementsappointment) &&  $EnterRequirementsappointment !=''){echo $EnterRequirementsappointment;} else{echo 'Enter Requirements for the appointment..';}?>" class="forminputs2"></textarea></td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="<?php echo $j;?>"></td>
        </tr>

              <?php
            }
          }
        ?>
      </table>
      </div>
      <input type="hidden" name="usedappointmentcount" id="usedappointmentcount" value="<?php echo $appo_count_string;?>">
      <input type="hidden" name="appointmentcount" id="appointmentcount" value="<?php echo $appo_count;?>">
      <input type="hidden" name="propercount" id="propercount" value="<?php echo $appo_count;?>">
      <input type="hidden" name="hidden_value" id="hidden_value" value="Y">
    </form>
    <div id="ActionBar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="position: fixed;bottom: 0;background-color: #fff;text-align: center;margin-left: -5%;width:90%;">
        <button type="button" id="addappointment" class="btns formbuttons"><?php if(isset($ADDANAPPOINTMENT) &&  $ADDANAPPOINTMENT !=''){echo $ADDANAPPOINTMENT;} else{echo 'ADD AN APPOINTMENT';}?></button>
        <button type="button" id="saveAndEdit" class="btns formbuttons"><?php if(isset($SAVE) &&  $SAVE !=''){echo $SAVE;} else{echo 'SAVE';}?></button>
        <!--<button type="button" id="saveTemplate" class="btns formbuttons">SAVE THIS AS TEMPLATE</button>-->
      </div>
  </div>
</body>
<script type="text/javascript">
  $(document).on('click', '.deleterow', function () {
    var deleted_row_id = $(this).attr('id');  
    //alert(deleted_row_id);
            appointmentcount = $('#appointmentcount').val();
        propercount = $('#propercount').val();
    var appointment_name = $('#appointmentName'+deleted_row_id).val();
   if(appointment_name.replace(/\s+/g, '') == ""){
      $('#aslno tr:last').remove();
      //this.parentNode.parentNode.remove();
      $(this).closest('tr').remove();
      //alert(appointmentcount);
      appointmentcount = parseInt(appointmentcount) - 1;
      //alert(appointmentcount);
      if(appointmentcount == 1){
        $('.deleterow').hide();
      }
        var deleted_usedappointment = deleted_row_id+",";
        var current_usedappointmentcount = $('#usedappointmentcount').val();
        var new_usedappointmentcount  = current_usedappointmentcount.replace(deleted_usedappointment, "");
        $('#usedappointmentcount').val(new_usedappointmentcount);
        $('#appointmentcount').val(appointmentcount);
   } else {
    var deleteconfirm ;
    deleteconfirm = confirm("This appointment will be deleted. Click OK to continue.");
     // alert(deleteconfirm);
      if(deleteconfirm == true){
         $('#aslno tr:last').remove();
        //this.parentNode.parentNode.remove();
        $(this).closest('tr').remove();
        appointmentcount = parseInt(appointmentcount) - 1;
        if(appointmentcount == 1){
          $('.deleterow').hide();
        }
          var deleted_usedappointment = deleted_row_id+",";
          var current_usedappointmentcount = $('#usedappointmentcount').val();
          var new_usedappointmentcount  = current_usedappointmentcount.replace(deleted_usedappointment, "");
          $('#usedappointmentcount').val(new_usedappointmentcount);
          $('#appointmentcount').val(appointmentcount);
      } else {

      }      
   }
  });
var appointmentcount = $('#appointmentcount').val();
var propercount = $('#propercount').val();
$('#addappointment').click(function(){
        appointmentcount = parseInt(appointmentcount) + 1;
        propercount = parseInt(propercount) + 1;
        $('.deleterow').show();
        var first = "<tr><td><input type='text' name='appointmentName"+propercount+"' placeholder='<?php if(isset($EnterAppointmentName) &&  $EnterAppointmentName !=''){echo $EnterAppointmentName;} else{echo 'Enter Appointment Name..';}?>' maxlength='25' id='appointmentName"+propercount+"' class='forminputs2'></td><td><input type='text' name='doctorName"+propercount+"' placeholder='<?php if(isset($EnterDoctorName) &&  $EnterDoctorName !=''){echo $EnterDoctorName;} else{echo 'Enter Doctor Name..';}?>' size='25px' maxlength='25' id='doctorName"+propercount+"' class='forminputs2'></td><td><input type='text' name='appointment_date"+propercount+"' placeholder='<?php if(isset($Date) &&  $Date !=''){echo $Date;} else{echo 'Date';}?>' maxlength='25' id='appointment_date"+propercount+"' readonly class='forminputs2 appoinmentdate' style='height:30px;'><input type='text' name='appointment_time"+propercount+"' placeholder='<?php if(isset($Time) &&  $Time !=''){echo $Time;} else{echo 'Time';}?>' maxlength='25' id='appointment_time"+propercount+"' readonly class='forminputs2 appointmenttime' style='height:30px;width:100%;'></td><td><select name='duration_inhours"+propercount+"' id='duration_inhours"+propercount+"' class='forminputs2 durationinhours' style='height:30px;width:100%;'><option value='00'>0 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option><option value='01'>1 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option><option value='02'>2 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option><option value='03'>3 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option><option value='04'>4 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option><option value='05'>5 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option><option value='06'>6 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option><option value='07'>7 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option><option value='08'>8 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option><option value='09'>9 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option><option value='10'>10 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option><option value='11'>11 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option><option value='12'>12 <?php if(isset($Hrs) &&  $Hrs !=''){echo $Hrs;} else{echo 'Hrs';}?></option></select><select name='duration_inmins"+propercount+"' id='duration_inmins"+propercount+"' class='forminputs2 durationinmins' style='height:30px;width:100%;'><option value='00'>0 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option><option value='05'>5 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option><option value='10' selected>10 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option><option value='15'>15 mins</option><option value='20'>20 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option><option value='25'>25 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option><option value='30'>30 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option><option value='35'>35 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option><option value='40'>40 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option><option value='45'>45 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option><option value='50'>50 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option><option value='55'>55 <?php if(isset($mins) &&  $mins !=''){echo $mins;} else{echo 'mins';}?></option></select></td><td><textarea name='venue"+propercount+"' id='venue"+propercount+"' placeholder='<?php if(isset($EnterVenueappointment) &&  $EnterVenueappointment !=''){echo $EnterVenueappointment;} else{echo 'Enter Venue for the appointment.. ';}?>' class='forminputs2'></textarea></td><td><textarea name='requirements"+propercount+"' id='requirements"+propercount+"' placeholder='<?php if(isset($EnterRequirementsappointment) &&  $EnterRequirementsappointment !=''){echo $EnterRequirementsappointment;} else{echo 'Enter Requirements for the appointment..';}?>' class='forminputs2'></textarea></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"'></td></tr>";
        var slno  = "<tr><td>"+appointmentcount+"</td></tr>";
        $('#aslno > tbody').append(slno);
        $('#adata > tbody').append(first);
        var current_usedappointmentcount = $('#usedappointmentcount').val();
        var new_usedappointmentcount = current_usedappointmentcount+propercount+",";
        $('#usedappointmentcount').val(new_usedappointmentcount);
        $('#appointmentcount').val(appointmentcount);
        $('#propercount').val(propercount);
        $('#appointmentName'+propercount).focus();
      });
</script>
</html>