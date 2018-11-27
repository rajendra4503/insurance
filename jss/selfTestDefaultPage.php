<?php 
  session_start();
  //ini_set("display_errors","0");
  include_once('include/configinc.php');
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
?>
<!DOCTYPE html>
<html lang="en">
<body>
  <div>
   <form name="frm_plan_selftest" id="frm_plan_selftest" method="post" action="plan_selftest.php">
         <span style="float:left;width:3%;">
        <table id="stslno" style="display:none;">
          <tr><th>#</th></tr>
          <tr><td>1</td></tr>
          <tr><td>2</td></tr>
        </table>
      </span>
      <table id="pdata" style="float:left;width:95%;padding-bottom:100px;">
        <tr id="aheader">
          <th style="max-width:250px;background-color:#C9C9C9;text-align:left;">Name & Requirements:</th>
          <th style="max-width:250px;background-color:#C9C9C9;text-align:left;">Doctor's Name:</th>
          <th style="background-color:#C9C9C9;text-align:left;">Instruction</th>
          <th style="background-color:#C9C9C9;text-align:left;">Frequency</th>
          <th style="background-color:#C9C9C9;text-align:left;">Duration</th>
          <th style="background-color:#C9C9C9;text-align:left;"></th>
          <th style="background-color:#C9C9C9;text-align:left;"></th>
        </tr>
      </table>
      <div class="stdatadiv table-responsive" style="float:left;width:99%;padding-bottom:100px;">
        <table id="pdata">
          <tr>
            <td><input type="text" name="selftestName1" id="selftestName1" placeholder="Enter Test Name.." class="forminputs2" style="max-width:250px;" maxlength="100"></td>
            <td><input type="text" name="doctorName1" id="doctorName1" placeholder="Enter Doctor Name.." class="forminputs2" style="max-width:250px;" maxlength="25"></td>
            <td>
              <select name="when1" id="when1">
                <option style="display:none;" value="0">select</option>
                <?php echo $instruction_options;?>
              </select>
            </td>
            <td>
              <select name="frequency1" id="frequency1" title="Select the selfttest frequency" class="testfrequency">
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
            <td><input type="checkbox" name="response1" id="response1" title='Check this if a response is required' value="Y" checked style="display:none;"></td>
            <td><img src="images/closeRow.png" width="30px" height="auto" class="deleterow" id="1"></td>
          </tr>

          <tr>
            <td colspan="3" style="padding:0px"><textarea name="selftestdesc1" placeholder="Enter Requirements to selftest.." class="forminputs2" id="selftestdesc1"></textarea></td>
            <td colspan="4" style="padding:5px;"> <input type="hidden" name="selectedweekdays1" id="selectedweekdays1" value="" class="forminputs2 editselectedweekday" readonly title="Click here to edit frequency">
            <input type="hidden" name="selectedmonthdays1" id="selectedmonthdays1" value="" class="forminputs2 editselectedmonthday" readonly title="Click here to edit frequency"></td>
          </tr>
          <tr>
            <td colspan="6">
            <input type="text" name="linkentered1" id="linkentered1" value="" class="forminputs2" title="Enter Link here" placeholder="Enter Link Here (Optional)" style="width:100%;">            
            </td>
          </tr>
          <tr style="border-bottom:4px solid #000;">
            <td colspan="7" style="padding:0px;text-align:left">
              <label>Start:</label>&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio1" value="PS" checked class="radio1 prescriptionradio"> When the plan Starts/Updates
            <input type="radio" name="radio1" value="ND" class="radio1 prescriptionradio"> No. Of days after plan started&nbsp;<input type="text" size="6px" name="numofdays1" id="numofdays1" class="numofdays pointernone" maxlength="2">
            <input type="radio" name="radio1" value="SD" class="radio1 prescriptionradio"> At Specific Date:&nbsp;<input type="text" size="10px" name="specificdate1" id="specificdate1" class='specificdate pointernone'>
            </td>
          </tr>
        </table>

        <table id="pdata">
          <tr>
            <td><input type="text" name="selftestName2" id="selftestName2" placeholder="Enter Test Name.." class="forminputs2" style="max-width:250px;" maxlength="100"></td>
            <td><input type="text" name="doctorName2" id="doctorName2" placeholder="Enter Doctor Name.." class="forminputs2" style="max-width:250px;" maxlength="25"></td>
            <td>
              <select name="when2" id="when2">
                <option style="display:none;" value="0">select</option>
                <?php echo $instruction_options;?>
              </select>
            </td>
            <td>
               <select name="frequency2" id="frequency2" title="Select the selfttest frequency" class="testfrequency">
                <option value="0" style="display:none;">select</option>
              <option value="Once">Once</option>
              <option value="Daily">Daily</option>
              <option value="Weekly">Weekly</option>
              <option value="Monthly">Monthly</option>
              </select>
            </td>
            <td>
            <div style="border-radius:10px;padding:5px 5px;background-color:#2B6D57;height:50px;width:130px;">
            <input type="text" maxlength="2" name="count2" id="count2" style="width:35px;height:40px;" class="forminputs2 roundedinputs countbox" title="Enter the duration">
            <select  id="countType2" name="countType2" style="width:80px;float:right;height:40px;line-height:35px;" title="Enter the duration">
              <option value="0" style="display:none;">select</option>
              <option value="Days">Days</option>
              <option value="Weeks">Weeks</option>
              <option value="Months">Months</option>
            </select>
            </div>
            
          </td>
            <td><input type="checkbox" name="response2" id="response2" title='Check this if a response is required' value="Y" checked style="display:none;"></td>
            <td><img src="images/closeRow.png" width="30px" height="auto" class="deleterow" id="2"></td>
          </tr>

          <tr>
            <td colspan="3" style="padding:0px"><textarea name="selftestdesc2" placeholder="Enter Requirements to selftest.." class="forminputs2" id="selftestdesc2"></textarea></td>
            <td colspan="4" style="padding:5px;"> <input type="hidden" name="selectedweekdays2" id="selectedweekdays2" value="" class="forminputs2 editselectedweekday" readonly title="Click here to edit frequency">
            <input type="hidden" name="selectedmonthdays2" id="selectedmonthdays2" value="" class="forminputs2 editselectedmonthday" readonly title="Click here to edit frequency"></td>
          </tr>
          <tr>
            <td colspan="6">
            <input type="text" name="linkentered2" id="linkentered2" value="" class="forminputs2" title="Enter Link here" placeholder="Enter Link Here (Optional)" style="width:100%;">            
            </td>
          </tr>
          <tr style="border-bottom:4px solid #000;">
            <td colspan="7" style="padding:0px;text-align:left">
              <label>Start:</label>&nbsp;&nbsp;&nbsp;
              <input type="radio" name="radio2" value="PS" checked class="radio2 prescriptionradio"> When the plan Starts/Updates
            <input type="radio" name="radio2" value="ND" class="radio2 prescriptionradio"> No. Of days after plan started&nbsp;<input type="text" size="6px" name="numofdays2" id="numofdays2" class="numofdays pointernone" maxlength="2">
            <input type="radio" name="radio2" value="SD" class="radio2 prescriptionradio"> At Specific Date:&nbsp;<input type="text" size="10px" name="specificdate2" id="specificdate2" class='specificdate pointernone'>
            </td>
          </tr>
        </table>
      </div>
      

      <div id="ActionBar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="position: fixed;bottom: 0;background-color: #fff;text-align: center;margin-left: -5%;width:90%;">
        <button type="button" id="addselftest" class="btns formbuttons">ADD A TEST</button>
        <button type="button" id="saveAndEdit" class="btns formbuttons">SAVE</button>
        <!--<button type="button" id="saveTemplate" class="btns formbuttons">SAVE THIS AS TEMPLATE</button>-->
      </div>
       <input type="hidden" name="usedselftestcount" id="usedselftestcount" value="1,2,">
       <input type="hidden" name="selftestcount" id="selftestcount" value="2">
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

       if(selftest_name.replace(/\s+/g, '') == ""){
        //alert(deleted_row_id);
         $('#stslno tr:last').remove();
        this.parentNode.parentNode.parentNode.parentNode.remove();
        selftestcount = selftestcount - 1;
        if(selftestcount == 1){
          $('.deleterow').hide();
        }
      var deleted_usedselftestcount = deleted_row_id+",";
       var current_usedselftestcount = $('#usedselftestcount').val();
      var new_usedselftestcount  = current_usedselftestcount.replace(deleted_usedselftestcount, "");
      $('#usedselftestcount').val(new_usedselftestcount);
      $('#selftestcount').val(selftestcount);
   } else {
    var deleteconfirm = confirm("This medicine will be deleted. Click OK to continue.");
    if(deleteconfirm == true){
      $('#stslno tr:last').remove();
        this.parentNode.parentNode.parentNode.parentNode.remove();
        selftestcount = selftestcount - 1;
        if(selftestcount == 1){
          $('.deleterow').hide();
        }
      var deleted_usedselftestcount = deleted_row_id+",";
       var current_usedselftestcount = $('#usedselftestcount').val();
      var new_usedselftestcount  = current_usedselftestcount.replace(deleted_usedselftestcount, "");
      $('#usedselftestcount').val(new_usedselftestcount);
      $('#selftestcount').val(selftestcount);
    } else {

    }
   }

    
  });
var selftestcount = 2;
var propercount = 2;
var instruction_options = "<?php echo $instruction_options;?>";
$('#addselftest').click(function(){
        selftestcount = selftestcount + 1;
        propercount     = propercount + 1;
        $('.deleterow').show();
        var first = "<table id='pdata'><tr><td><input type='text' name='selftestName"+propercount+"' id='selftestName"+propercount+"' placeholder='Enter Test Name..' class='forminputs2' style='max-width:250px;' maxlength='100'></td><td><input type='text' name='doctorName"+propercount+"' id='doctorName"+propercount+"' placeholder='Enter Doctor Name..' class='forminputs2' style='max-width:250px;' maxlength='25'></td><td><select name='when"+propercount+"' id='when"+propercount+"'><option style='display:none;' value='0'>select</option>"+instruction_options+"</select></td><td><select name='frequency"+propercount+"' id='frequency"+propercount+"' class='testfrequency'><option value='0' style='display:none;'>select</option><option value='Once'>Once</option><option value='Daily'>Daily</option><option value='Weekly'>Weekly</option><option value='Monthly'>Monthly</option></select></td><td><div style='border-radius:10px;padding:5px 5px;background-color:#2B6D57;height:50px;width:130px;'><input type='text' maxlength='2' name='count"+propercount+"' id='count"+propercount+"' style='width:35px;height:40px;' class='forminputs2 roundedinputs countbox' title='Enter the duration'><select class='' id='countType"+propercount+"' name='countType"+propercount+"' style='width:80px;float:right;height:40px;line-height:35px;' title='Enter the duration'><option value='0' style='display:none;'>select</option><option value='Days'>Days</option><option value='Weeks'>Weeks</option><option value='Months'>Months</option></select></div></td><td><input type='checkbox' name='response"+propercount+"' id='response"+propercount+"' checked style='display:none;'></td><td><img src='images/closeRow.png' width='30px' height='auto' class='deleterow' id='"+propercount+"'></td></tr><tr><td colspan='3' style='padding:0px'><textarea name='selftestdesc"+propercount+"' placeholder='Enter Requirements to selftest..' class='forminputs2' id='selftestdesc"+propercount+"'></textarea></td><td colspan='4' style='padding:5px;'> <input type='hidden' name='selectedweekdays"+propercount+"' id='selectedweekdays"+propercount+"' value='' class='forminputs2 editselectedweekday' readonly title='Click here to edit frequency'><input type='hidden' name='selectedmonthdays"+propercount+"' id='selectedmonthdays"+propercount+"' value='' class='forminputs2 editselectedmonthday' readonly title='Click here to edit frequency'></td></tr><tr><td colspan='6'><input type='text' name='linkentered"+propercount+"' id='linkentered"+propercount+"' class='forminputs2' title='Enter Link here' placeholder='Enter Link Here (Optional)' style='width:100%;'</td></tr><tr style='border-bottom:4px solid #000;'><td colspan='7' style='padding:0px;text-align:left'><label>Start:</label>&nbsp;&nbsp;&nbsp;<input type='radio' name='radio"+propercount+"' value='PS' checked class='radio"+propercount+" prescriptionradio'> When the plan Starts/Updates<input type='radio' name='radio"+propercount+"' value='ND' class='radio"+propercount+" prescriptionradio'> No. Of days after plan started&nbsp;<input type='text' size='6px' name='numofdays"+propercount+"' id='numofdays"+propercount+"' class='numofdays pointernone' maxlength='2'><input type='radio' name='radio"+propercount+"' value='SD' class='radio"+propercount+" prescriptionradio'> At Specific Date:&nbsp;<input type='text' size='10px' name='specificdate"+propercount+"' id='specificdate"+propercount+"' class='specificdate pointernone'></td></tr></table>";
        var slno  = "<tr><td>"+selftestcount+"</td></tr>";
        $('#stslno > tbody').append(slno);
        $('.stdatadiv').append(first);
        var current_usedselftestcount = $('#usedselftestcount').val();
        var new_usedselftestcount = current_usedselftestcount+propercount+",";
        $('#usedselftestcount').val(new_usedselftestcount);
        $('#selftestcount').val(selftestcount);
      });

  
</script>
</html>