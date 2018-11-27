<?php 
session_start();
//echo "<pre>";print_r($_SESSION);exit;
ini_set("display_errors","0");
include_once('include/configinc.php');
include('include/session.php');
$plancode = $_SESSION['plancode_for_current_plan'];
  $procedure_list_query = mysql_query("SELECT * FROM  PLAN_PROCEDURE_LIST");
  $list = '';
  $list .='<option value="">Select Procedure</option>';        
  while ( $result = mysql_fetch_array($procedure_list_query)) {
    $id = $result['ID'];
    $name = $result['ProcedureName'];
    $list .= '<option value="'.$id.'">'.$name.'</option>'; 
  }
?>
<!DOCTYPE html>
<html lang="en">
<body>
  
  <div>
   <form name="frm_plan_appointments" id="frm_plan_appointments" method="post" action="plan_procedure.php">


     <select hidden id="procedure_list" class="forminputs2 durationinhours" style='height:30px;width:100%;'>
            <?php echo $list;?>
     </select>
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
          <th>Procedure</th>
          <th style="width:130px;">Date</th>
          <th></th>
        </tr>
        <?php 
          $appo_count = 0;
          $appo_count_string = "";
          $get_appo = "select * from PLAN_PROCEDURE where PlanCode = '$plancode' and  CreatedBy ='$logged_userid'";
         //echo $get_appo;exit;
          $get_appo_run = mysql_query($get_appo);
          $get_appo_count = mysql_num_rows($get_appo_run);
          if($get_appo_count > 0){
            while ($appo_row = mysql_fetch_array($get_appo_run)) {
              $appo_count++;
              $appo_count_string    .= $appo_count.",";
              $appo_date            = date('d-M-Y',strtotime($appo_row['ProDate']));
              ?>
              <tr>
                <td>

                  <select name="procedure<?php echo $appo_count;?>" id="procedure<?php echo $appo_count;?>" class="forminputs2 durationinhours" style='height:30px;width:100%;'>
                     <?php 
                        $procedure_query = mysql_query("SELECT * FROM  PLAN_PROCEDURE_LIST");
                        $list = '';
                        $list .='<option value="">Select Procedure</option>';        
                        while ( $result = mysql_fetch_array($procedure_query)) {
                        $id = $result['ID'];
                        $name = $result['ProcedureName'];
                           if($id == $appo_row['ProId']){
                             $list .= '<option selected value="'.$id.'">'.$name.'</option>'; 
                           }else{
                            $list .= '<option value="'.$id.'">'.$name.'</option>'; 
                           }
                        }
                        echo $list;
                     ?>
                  </select>
                </td>
                <td><input type="text" name="procedure_date<?php echo $appo_count;?>" placeholder="Date" maxlength="25" id="procedure_date<?php echo $appo_count;?>" readonly class="forminputs2 appoinmentdate" value="<?php echo $appo_date;?>" style='height:30px;'>
                </td>
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
          <td>
          <select name="procedure<?php echo $j;?>" id="procedure<?php echo $j;?>" class="forminputs2 durationinhours" style='height:30px;width:100%;'>
            <?php echo $list;?>
            </select>
          </td>

          <td><input type="text" name="procedure_date<?php echo $j;?>" placeholder="Date" maxlength="25" id="procedure_date<?php echo $j;?>" readonly class="forminputs2 appoinmentdate" style='height:30px;'>
          </td>
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
        <button type="button" id="addappointment" class="btns formbuttons">ADD AN PROCEDURE</button>
        <button type="button" id="saveAndEdit" class="btns formbuttons">SAVE</button>
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
    var appointment_name = $('#procedure'+deleted_row_id).val();
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

var procedure_list = $('#procedure_list').html();
var appointmentcount = $('#appointmentcount').val();
var propercount = $('#propercount').val();
$('#addappointment').click(function(){
        appointmentcount = parseInt(appointmentcount) + 1;
        propercount = parseInt(propercount) + 1;
        $('.deleterow').show();
        var first = "<tr><td><select name='procedure"+propercount+"' id='procedure"+propercount+"' class='forminputs2 durationinhours' style='height:30px;width:100%;'>"+procedure_list+"</select></td><td><input type='text' name='procedure_date"+propercount+"' placeholder='Date' maxlength='25' id='procedure_date"+propercount+"' readonly class='forminputs2 appoinmentdate' style='height:30px;'></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"'></td></tr>";
        var slno  = "<tr><td>"+appointmentcount+"</td></tr>";
        $('#aslno > tbody').append(slno);
        $('#adata > tbody').append(first);
        var current_usedappointmentcount = $('#usedappointmentcount').val();
        var new_usedappointmentcount = current_usedappointmentcount+propercount+",";
        $('#usedappointmentcount').val(new_usedappointmentcount);
        $('#appointmentcount').val(appointmentcount);
        $('#propercount').val(propercount);
        $('#procedure'+propercount).focus();
      });
</script>
</html>