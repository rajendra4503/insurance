<?php 
session_start();
//ini_set("display_errors","0");
include_once('include/configinc.php');
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
?>
<!DOCTYPE html>
<html lang="en">
<body>
  <div>
   <form name="frm_plan_prescription" id="frm_plan_prescription" method="post" action="plan_instruction.php">
      <div class="prescriptionNameBar">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="background-color:#BFBFBF;">
          <span>Instruction Template Name:</span>
        <input type="text" name="prescriptionName" id="prescriptionName" placeholder="Template Name.." maxlength="25" title="Enter a name for the template">
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="background-color:#BFBFBF;width:100%;">
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
      <table id="pdata" class="table table-striped">
      <tr id="pheader1">
          <th style="max-width:250px;background-color:#C9C9C9;">Instruction Name</th>
          <th style="background-color:#C9C9C9;">When</th>
          <th style="background-color:#C9C9C9;">Instruction</th>
          <th style="background-color:#C9C9C9;">Frequency</th>
          <th style="background-color:#C9C9C9;">Duration</th>
          <th style="background-color:#C9C9C9;">Critical?</th>
          <th style="background-color:#C9C9C9;">Response?</th>
          <th style="background-color:#C9C9C9;">Start</th>
          <th style="background-color:#C9C9C9;"></th>
        </tr>
        <tr>
          <td><textarea rows="4" name="medicine1" id="medicine1" placeholder="Instruction Name.." maxlength="250" title="Enter Instruction Name here"></textarea></td>
          <td>
            <select name="when1" id="when1" title="Select the instruction frequency" class="whenshorthand">
              <option value="0" style="display:none;">select</option>
              <?php echo $shorthand_options;?>
            </select>
          </td>
          <td>
            <select name="instruction1" id="instruction1" title="Select the timing">
              <option value="0" style="display:none;">select</option>
              <option value="Before Food">Before Food</option>
              <option value="With Food">With Food</option>
              <option value="After Food">After Food</option>
              <option value="NA">Not Applicable</option>
            </select>
          </td>
          <td>
            <select name="frequency1" id="frequency1" title="Select the frequency" class="medfrequency">
              <option value="0" style="display:none;">select</option>
              <option value="Once">Once</option>
              <option value="Daily">Daily</option>
              <option value="Weekly">Weekly</option>
              <option value="Monthly">Monthly</option>
            </select>
          </td>
          <td>
            <div style="border-radius:10px;padding:5px 5px;background-color:#2B6D57;height:50px;width:130px;">
            <input type="text" maxlength="2" name="count1" id="count1" style="width:35px;height:40px;" class="forminputs2 roundedinputs countbox" title="Enter the duration">
            <select  id="countType1" name="countType1" style="width:80px;float:right;height:40px;line-height:35px;" title="Enter the duration">
              <option value="0" style="display:none;">select</option>
              <option value="Days">Days</option>
              <option value="Weeks">Weeks</option>
              <option value="Months">Months</option>
            </select>
            </div>
            
          </td>
          <td><input type="checkbox" name="critical1" id="critical1" class="criticalcheck" title="Check this if the instruction is critical" value="Y"></td>
          <td><input type="checkbox" name="response1" id="response1" class="responsecheck" title="Check this if a response is required" value="Y"></td>
          <td style="text-align:left;font-size : 0.7em;">
            <input type="radio" name="radio1" value="PS" checked class="radio1 prescriptionradio"> When the plan Starts/Updates<br>
            <input type="radio" name="radio1" value="ND" class="radio1 prescriptionradio"> No. Of days after plan started&nbsp;<input type="text" size="6px" name="numofdays1" id="numofdays1" class="numofdays pointernone" maxlength="2"><br>
            <input type="radio" name="radio1" value="SD" class="radio1 prescriptionradio"> On Specific Date:&nbsp;<input type="text" size="10px" name="specificdate1" id="specificdate1" class='specificdate pointernone'>
          </td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="1" title="Delete this row"></td>
        </tr>
        <tr>
          <td colspan="8" style="text-align:left;" align="left">
          <input type="text" name="linkentered1" id="linkentered1" value="" class="forminputs2" title="Enter Link here" placeholder="Enter Link Here (Optional)">
            <input type="hidden" name="selectedweekdays1" id="selectedweekdays1" value="" class="forminputs2 editselectedweekday" readonly title="Click here to edit frequency">
            <input type="hidden" name="selectedmonthdays1" id="selectedmonthdays1" value="" class="forminputs2 editselectedmonthday" readonly title="Click here to edit frequency">
            <input type="hidden" name="specifictime1" id="specifictime1" class="editedspecifictimes forminputs2" readonly title="Click here to edit specific times">
          </td>
        </tr>
        <tr>
          <td><textarea rows="2" name="medicine2" id="medicine2" placeholder="Instruction Name.." title="Enter Instruction Name here" maxlength="250"></textarea></td>
          <td>
            <select name="when2" id="when2" title='Select the dosage' class="whenshorthand">
              <option value="0" style="display:none;">select</option>
              <?php echo $shorthand_options;?>
            </select>
          </td>
          <td>
            <select name="instruction2" id="instruction2" title='Select the timing'>
              <option value="0" style="display:none;">select</option>
              <option value="Before Food">Before Food</option>
              <option value="With Food">With Food</option>
              <option value="After Food">After Food</option>
              <option value="NA">Not Applicable</option>
            </select>
          </td>
          <td>
            <select name="frequency2" id="frequency2" title='Select the frequency' class="medfrequency">
              <option value="0" style="display:none;">select</option>
              <option value="Once">Once</option>
              <option value="Daily">Daily</option>
              <option value="Weekly">Weekly</option>
              <option value="Monthly">Monthly</option>
            </select>
          </td>
          <td>
            <div style="border-radius:10px;padding:5px 5px;background-color:#2B6D57;height:50px;width:130px;">
            <input type="text" maxlength="2" name="count2" id="count2" style="width:35px;height:40px;" class="forminputs2 roundedinputs countbox" title='Enter the duration'>
            <select  id="countType2" name="countType2" style="width:80px;float:right;height:40px;line-height:35px;">
              <option value="0" style="display:none;">select</option>
              <option value="Days">Days</option>
              <option value="Weeks">Weeks</option>
              <option value="Months">Months</option>
            </select>
            </div>
            
          </td>
          <td><input type="checkbox" name="critical2" id="critical2" class="criticalcheck" title='Check this if the instruction is critical' value="Y"></td>
          <td><input type="checkbox" name="response2" id="response2" class="responsecheck" title='Check this if a response is required' value="Y"></td>
          <td style="text-align:left;font-size : 0.7em;">
            <input type="radio" name="radio2" value="PS" checked class="radio2 prescriptionradio"> When the plan Starts/Updates<br>
            <input type="radio" name="radio2" value="ND" class="radio2 prescriptionradio"> No. Of days after plan started&nbsp;<input type="text" size="6px" name="numofdays2" id="numofdays2" class="numofdays pointernone" maxlength="2"><br>
            <input type="radio" name="radio2" value="SD" class="radio2 prescriptionradio"> On Specific Date:&nbsp;<input type="text" size="10px" name="specificdate2" id="specificdate2" class='specificdate pointernone'>
          </td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="2" title="Delete this row"></td>
        </tr>
        <tr>
          <td colspan="8" style="text-align:left;" align="left">
          <input type="text" name="linkentered2" id="linkentered2" value="" class="forminputs2" title="Enter Link here" placeholder="Enter Link Here (Optional)">
            <input type="hidden" name="selectedweekdays2" id="selectedweekdays2" value="" class="forminputs2 editselectedweekday" readonly title="Click here to edit frequency">
            <input type="hidden" name="selectedmonthdays2" id="selectedmonthdays2" value="" class="forminputs2 editselectedmonthday" readonly title="Click here to edit frequency">
            <input type="hidden" name="specifictime2" id="specifictime2" class="editedspecifictimes forminputs2" readonly title="Click here to edit specific times">
          </td>
        </tr>
        <tr>
          <td><textarea rows="2" name="medicine3" id="medicine3" placeholder="Instruction Name.." title="Enter Instruction Name here" maxlength="250"></textarea></td>
          <td>
            <select name="when3" id="when3" title='Select the timing' class="whenshorthand">
              <option value="0" style="display:none;">select</option>
              <?php echo $shorthand_options;?>
            </select>
          </td>
          <td>
            <select name="instruction3" id="instruction3" title='Select the instruction'>
              <option value="0" style="display:none;">select</option>
              <option value="Before Food">Before Food</option>
              <option value="With Food">With Food</option>
              <option value="After Food">After Food</option>
              <option value="NA">Not Applicable</option>
            </select>
          </td>
          <td>
            <select name="frequency3" id="frequency3" title='Select the frequency' class="medfrequency">
              <option value="0" style="display:none;">select</option>
              <option value="Once">Once</option>
              <option value="Daily">Daily</option>
              <option value="Weekly">Weekly</option>
              <option value="Monthly">Monthly</option>
            </select>
          </td>
          <td>
            <div style="border-radius:10px;padding:5px 5px;background-color:#2B6D57;height:50px;width:130px;">
            <input type="text" maxlength="2" name="count3" id="count3" style="width:35px;height:40px;" class="forminputs2 roundedinputs countbox" title='Enter the duration'>
            <select  id="countType3" name="countType3" style="width:80px;float:right;height:40px;line-height:35px;">
              <option value="0" style="display:none;">select</option>
              <option value="Days">Days</option>
              <option value="Weeks">Weeks</option>
              <option value="Months">Months</option>
            </select>
            </div>
            
          </td>
          <td><input type="checkbox" name="critical3" id="critical3" class="criticalcheck" title='Check this if the instruction is critical' value="Y"></td>
          <td><input type="checkbox" name="response3" id="response3" class="responsecheck" title='Check this if a response is required' value="Y"></td>
          <td style="text-align:left;font-size : 0.7em;">
            <input type="radio" name="radio3" value="PS" checked class="radio3 prescriptionradio"> When the plan Starts/Updates<br>
            <input type="radio" name="radio3" value="ND" class="radio3 prescriptionradio"> No. Of days after plan started&nbsp;<input type="text" size="6px" name="numofdays3" id="numofdays3" class="numofdays pointernone" maxlength="2"><br>
            <input type="radio" name="radio3" value="SD" class="radio3 prescriptionradio"> On Specific Date:&nbsp;<input type="text" style="width:100px;" name="specificdate3" id="specificdate3" class='specificdate pointernone'>
          </td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="3" title="Delete this row"></td>
        </tr>
        <tr>
          <td colspan="8" style="text-align:left;" align="left">
          <input type="text" name="linkentered3" id="linkentered3" value="" class="forminputs2" title="Enter Link here" placeholder="Enter Link Here (Optional)">
            <input type="hidden" name="selectedweekdays3" id="selectedweekdays3" value="" class="forminputs2 editselectedweekday" readonly title="Click here to edit frequency">
            <input type="hidden" name="selectedmonthdays3" id="selectedmonthdays3" value="" class="forminputs2 editselectedmonthday" readonly title="Click here to edit frequency">
           <input type="hidden" name="specifictime3" id="specifictime3" class="editedspecifictimes forminputs2" readonly title="Click here to edit specific times">
          </td>
        </tr>
      </table>
      </div>
    <input type="hidden" name="usedpresciptioncount" id="usedpresciptioncount" value="1,2,3,">
    <input type="hidden" name="medicationcount" id="medicationcount" value="3">
    </form>
    </div>
    <div id="ActionBar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="position: fixed;bottom: 0;background-color: #fff;text-align: center;margin-left: -5%;width:90%;">
        <button type="button" id="addMedicine" class="btns formbuttons">ADD AN INSTRUCTION</button>
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
      $(this).closest('tr').remove();
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
    var deleteconfirm = confirm("This instruction will be deleted. Click OK to continue.");
    if(deleteconfirm == true){
      $('#pslno tr:last').remove();
      $(this).closest('tr').next().remove();//To remove the next tr
      $(this).closest('tr').remove();
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
var medicationcount = 3;
var propercount = 3;
var shorthand_options = "<?php echo $shorthand_options;?>";
$('#addMedicine').click(function(){
        medicationcount = medicationcount + 1;
        propercount     = propercount + 1;
        $('.deleterow').show();
        var first = "<tr><td><textarea rows='2' name='medicine"+propercount+"' placeholder='Instruction Name..' id='medicine"+propercount+"' title='Enter Instruction Name here' maxlength='250'></textarea></td><td><select name='when"+propercount+"' id='when"+propercount+"' title='Select the instruction frequency' class='whenshorthand'><option value='0' style='display:none;'>select</option>"+shorthand_options+"</select></td><td><select name='instruction"+propercount+"' id='instruction"+propercount+"' title='Select the timing'><option value='0' style='display:none;'>select</option><option value='Before Food'>Before Food</option><option value='With Food'>With Food</option><option value='After Food'>After Food</option><option value='NA'>Not Applicable</option></select></td><td><select name='frequency"+propercount+"' id='frequency"+propercount+"' title='Select the frequency' class='medfrequency'><option value='0' style='display:none;'>select</option><option value='Once'>Once</option><option value='Daily'>Daily</option><option value='Weekly'>Weekly</option><option value='Monthly'>Monthly</option></select></td><td><div style='border-radius:10px;padding:5px 5px;background-color:#2B6D57;height:50px;width:130px;'><input type='text' maxlength='2' name='count"+propercount+"' id='count"+propercount+"' style='width:35px;height:40px;' class='forminputs2 roundedinputs countbox' title='Enter the duration'><select class='form-control' id='countType"+propercount+"' name='countType"+propercount+"'  style='width:80px;float:right;height:40px;line-height:35px;'><option value='0' style='display:none;'>select</option><option value='Days'>Days</option><option value='Weeks'>Weeks</option><option value='Months'>Months</option></select></div></td><td><input type='checkbox' name='critical"+propercount+"' id='critical"+propercount+"' class='criticalcheck' title='Check this if the instruction is critical' value='Y'></td><td><input type='checkbox' name='response"+propercount+"' id='response"+propercount+"' class='responsecheck' title='Check this if a response is required' value='Y'></td><td style='text-align:left;font-size : 0.7em;'><input type='radio' name='radio"+propercount+"' value='PS' checked class='radio"+propercount+" prescriptionradio'> When the plan Starts/Updates<br><input type='radio' name='radio"+propercount+"' class='radio"+propercount+" prescriptionradio' value='ND'> No. Of days after plan started&nbsp;<input type='text' size='6px' name='numofdays"+propercount+"' id='numofdays"+propercount+"' class='numofdays pointernone' maxlength='2'><br><input type='radio' name='radio"+propercount+"' value='SD' class='radio"+propercount+" prescriptionradio'> On Specific Date:&nbsp;<input type='text' size='10px' name='specificdate"+propercount+"' id='specificdate"+propercount+"' class='specificdate pointernone'></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"' title='Delete this row'></td></tr><tr><td colspan='8' style='text-align:left;' aling='left'><input type='text' name='linkentered"+propercount+"' id='linkentered"+propercount+"' class='forminputs2' title='Enter Link here' placeholder='Enter Link Here (Optional)'><input type='hidden' name='selectedweekdays"+propercount+"' id='selectedweekdays"+propercount+"' value='' class='forminputs2 editselectedweekday' readonly  title='Click here to edit frequency'><input type='hidden' name='selectedmonthdays"+propercount+"' id='selectedmonthdays"+propercount+"' value='' class='forminputs2 editselectedmonthday' readonly title='Click here to edit frequency'><input type='hidden' name='specifictime"+propercount+"' id='specifictime"+propercount+"' class='editedspecifictimes forminputs2' readonly title='Click here to edit specific times'></td></tr>";
        var slno  = "<tr><td>"+medicationcount+"</td></tr>";
        $('#pslno > tbody').append(slno);
        $('#pdata > tbody').append(first);
        var current_usedprescriptioncount = $('#usedpresciptioncount').val();
        var new_usedprescriptioncount = current_usedprescriptioncount+propercount+",";
        $('#usedpresciptioncount').val(new_usedprescriptioncount);
        $('#medicationcount').val(medicationcount);
      });
</script>
</html>