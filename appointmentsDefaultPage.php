<!DOCTYPE html>
<html lang="en">
<body>
  <div>
   <form name="frm_plan_appointments" id="frm_plan_appointments" method="post" action="plan_appointments.php">
      <!--<div class="appointmentNameBar">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="center" style="background-color:#BFBFBF;">
          <span>Doctor's Name:</span>
          <input type="text" name="doctorName" placeholder="Enter Doctor Name.." size="25px" maxlength="25" id="doctorName" class="forminputs">
        </div>
      </div>-->
      <span style="float:left;width:3%;">
        <table id="aslno">
          <tr><th>#</th></tr>
          <tr><td>1</td></tr>
          <tr><td>2</td></tr>
          <tr><td>3</td></tr>
        </table>
      </span>
      <div class="table-responsive" style="float:left;width:95%;padding-bottom:100px;">
      <table id="adata" class="table table-striped">
      <tr id="aheader">
          <th>Appointment short name</th>
          <th style="width:180px;">Doctor's Name</th>
          <th style="width:130px;">Date</th>
          <th style="width:100px;">Time</th>
          <th>Requirements</th>
          <th></th>
        </tr>
        <tr>
          <td><input type="text" name="appointmentName1" placeholder="Enter Appointment Name.." maxlength="25" id="appointmentName1" class="forminputs2"></td>
          <td><input type="text" name="doctorName1" placeholder="Enter Doctor Name.." maxlength="25" id="doctorName1" class="forminputs2"></td>
          <td><input type="text" name="appointment_date1" placeholder="Date" maxlength="25" id="appointment_date1" readonly class="forminputs2 appoinmentdate"></td>
          <td><div class="input-append bootstrap-timepicker"><span class="add-on"> <input type="text" name="appointment_time1" placeholder="Time" maxlength="25" id="appointment_time1" readonly class="forminputs2 appointmenttime"></span></div></td>
          <td><textarea name="requirements1"  placeholder="Enter Requirements for the appointment.." class="forminputs2"></textarea></td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="1"></td>
        </tr>
        <tr>
          <td><input type="text" name="appointmentName2" placeholder="Enter Appointment Name.." maxlength="25" id="appointmentName2" class="forminputs2"></td>
          <td><input type="text" name="doctorName2" placeholder="Enter Doctor Name.." maxlength="25" id="doctorName2" class="forminputs2"></td>
          <td><input type="text" name="appointment_date2" placeholder="Date" maxlength="25" id="appointment_date2" readonly class="forminputs2 appoinmentdate"></td>
          <td><input type="text" name="appointment_time2" placeholder="Time" maxlength="25" id="appointment_time2" readonly class="forminputs2 appointmenttime"></td>
          <td><textarea name="requirements2" id="requirements2" placeholder="Enter Requirements for the appointment.." class="forminputs2"></textarea></td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="2"></td>
        </tr>
        <tr>
          <td><input type="text" name="appointmentName3" placeholder="Enter Appointment Name.." maxlength="25" id="appointmentName3" class="forminputs2"></td>
          <td><input type="text" name="doctorName3" placeholder="Enter Doctor Name.." size="25px" maxlength="25" id="doctorName3" class="forminputs2"></td>
          <td><input type="text" name="appointment_date3" placeholder="Date" maxlength="25" id="appointment_date3" readonly class="forminputs2 appoinmentdate"></td>
          <td><input type="text" name="appointment_time3" placeholder="Time" maxlength="25" id="appointment_time3" readonly class="forminputs2 appointmenttime"></td>
          <td><textarea name="requirements3" id="requirements3"  placeholder="Enter Requirements for the appointment.." class="forminputs2"></textarea></td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="3"></td>
        </tr>
      </table>
      </div>
      <input type="hidden" name="usedappointmentcount" id="usedappointmentcount" value="1,2,3,">
      <input type="hidden" name="appointmentcount" id="appointmentcount" value="3">
      <input type="hidden" name="hidden_value" id="hidden_value" value="Y">
    </form>
    <div id="ActionBar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="position: fixed;bottom: 0;background-color: #fff;text-align: center;margin-left: -5%;width:90%;">
        <button type="button" id="addappointment" class="btns formbuttons">ADD AN APPOINTMENT</button>
        <button type="button" id="saveAndEdit" class="btns formbuttons">SAVE</button>
        <!--<button type="button" id="saveTemplate" class="btns formbuttons">SAVE THIS AS TEMPLATE</button>-->
      </div>
  </div>
</body>
<script type="text/javascript">
  $(document).on('click', '.deleterow', function () {
    var deleted_row_id = $(this).attr('id');  
    //alert(deleted_row_id);
    var appointment_name = $('#appointmentName'+deleted_row_id).val();
   if(appointment_name.replace(/\s+/g, '') == ""){
      $('#aslno tr:last').remove();
      //this.parentNode.parentNode.remove();
      $(this).closest('tr').remove();
      appointmentcount = appointmentcount - 1;
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
    bootbox.confirm("This appointment will be deleted. Click OK to continue.", function(result) {
      deleteconfirm = result;
     // alert(deleteconfirm);
      if(deleteconfirm == true){
         $('#aslno tr:last').remove();
        //this.parentNode.parentNode.remove();
        $(this).closest('tr').remove();
        appointmentcount = appointmentcount - 1;
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
    });

   }
  });
var appointmentcount = 3;
var propercount = 3;
$('#addappointment').click(function(){
        appointmentcount = appointmentcount + 1;
        propercount = propercount + 1;
        $('.deleterow').show();
        var first = "<tr><td><input type='text' name='appointmentName"+propercount+"' placeholder='Enter Appointment Name..' maxlength='25' id='appointmentName"+propercount+"' class='forminputs2'></td><td><input type='text' name='doctorName"+propercount+"' placeholder='Enter Doctor Name..' size='25px' maxlength='25' id='doctorName"+propercount+"' class='forminputs2'></td><td><input type='text' name='appointment_date"+propercount+"' placeholder='Date' maxlength='25' id='appointment_date"+propercount+"' readonly class='forminputs2 appoinmentdate'></td><td><input type='text' name='appointment_time"+propercount+"' placeholder='Time' maxlength='25' id='appointment_time"+propercount+"' readonly class='forminputs2 appointmenttime'></td><td><textarea name='requirements"+propercount+"' id='requirements"+propercount+"' placeholder='Enter Requirements for the appointment..' class='forminputs2'></textarea></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"'></td></tr>";
        var slno  = "<tr><td>"+appointmentcount+"</td></tr>";
        $('#aslno > tbody').append(slno);
        $('#adata > tbody').append(first);
        var current_usedappointmentcount = $('#usedappointmentcount').val();
        var new_usedappointmentcount = current_usedappointmentcount+propercount+",";
        $('#usedappointmentcount').val(new_usedappointmentcount);
        $('#appointmentcount').val(appointmentcount);
      });
</script>
</html>