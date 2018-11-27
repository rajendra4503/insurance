<?php 
session_start();
//ini_set("display_errors","0");
include_once('include/configinc.php');
$plancode = $_SESSION['plancode_for_current_plan'];
$userid   = $_SESSION['userid_for_current_plan'];
?>
<!DOCTYPE html>
<html lang="en">
<body>
  <div>
   <form name="frm_plan_labtest" id="frm_plan_labtest" method="post" action="cust_lab_new.php">
      <!--<div class="prescriptionNameBar" align="center">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" align="center" style="background-color:#BFBFBF;">
          <span>Doctor's Name:</span>
          <input type="text" name="doctorName" placeholder="Enter Doctor Name.." size="25px" maxlength="25" id="doctorName" class="forminputs">
        </div>
      </div>-->
      <span style="float:left;width:3%;display:none;">
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
          <th>Lab Test Name</th>
          <th style="width:180px;">Doctor's Name</th>
          <th>Requirements</th>
          <th></th>
        </tr>
        <?php 
          $lab_count = 0;
          $lab_count_string = "";
          $get_lab = "select PlanCode, LabTestID, RowNo, TestName, DoctorsName, LabTestRequirements from USER_LAB_TEST_DETAILS1 where PlanCode = '$plancode' and UserID='$userid' and LabTestDate is null";
         // echo $get_lab;exit;
          $get_lab_run = mysql_query($get_lab);
          $get_lab_count = mysql_num_rows($get_lab_run);
          if($get_lab_count > 0){
            while ($lab_row = mysql_fetch_array($get_lab_run)) {
              $lab_count++;
              $lab_count_string    .= $lab_count.",";
              $labtest_id     = $lab_row['LabTestID'];
              $labtest_row    = $lab_row['RowNo'];
              $labtest_name   = $lab_row['TestName'];
              $labtest_doc    = $lab_row['DoctorsName'];
              $labtest_code   = $lab_row['PlanCode'];
              $labtest_req    = $lab_row['LabTestRequirements'];
              ?>
              <tr>
                <tr>
                <td><input type="text" name="labtestName<?php echo $lab_count;?>" placeholder="Enter Lab test Name.." maxlength="25" id="labtestName<?php echo $lab_count;?>" class="forminputs2" value="<?php echo $labtest_name;?>"></td>
                <td><input type="text" name="doctorName<?php echo $lab_count;?>" placeholder="Enter Doctor Name.." maxlength="25" id="doctorName<?php echo $lab_count;?>" class="forminputs2" value="<?php echo $labtest_doc?>"></td>
                <td><textarea name="requirements<?php echo $lab_count;?>"  placeholder="Enter Requirements for the Lab test.." class="forminputs2"><?php echo $labtest_req; ?></textarea></td>
                <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="<?php echo $lab_count;?>"></td>
              </tr>
              </tr>
              <?php
            }
          } else {
            $lab_count_string = "1,2,3,";
            $lab_count = "3";
            for ($j=1; $j <= 3; $j++) { 
              ?>

        <tr>
          <td><input type="text" name="labtestName<?php echo $j;?>" placeholder="Enter Lab test Name.." maxlength="25" id="labtestName<?php echo $j;?>" class="forminputs2"></td>
          <td><input type="text" name="doctorName<?php echo $j;?>" placeholder="Enter Doctor Name.." maxlength="25" id="doctorName<?php echo $j;?>" class="forminputs2"></td>
          <td><textarea name="requirements<?php echo $j;?>"  placeholder="Enter Requirements for the Lab test.." class="forminputs2"></textarea></td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="<?php echo $j;?>"></td>
        </tr>
        <?php } }?>
      </table>
      </div>
      <input type="hidden" name="usedlabtestcount" id="usedlabtestcount" value="<?php echo $lab_count_string;?>">
      <input type="hidden" name="labtestcount" id="labtestcount" value="<?php echo $lab_count;?>">
      <input type="hidden" name="propercount" id="propercount" value="<?php echo $lab_count;?>">
      <input type="hidden" name="hidden_value" id="hidden_value" value="Y">
      <input type="hidden" name="userid_for_current_plan" id="userid_for_current_plan" value="<?php echo $userid;?>">
    </form>
    <div id="ActionBar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="position: fixed;bottom: 0;background-color: #fff;text-align: center;margin-left: -5%;width:90%;">
        <button type="button" id="addLabTest" class="btns formbuttons">ADD A TEST</button>
        <button type="button" id="saveAndEdit" class="btns formbuttons">SAVE</button>
        <!--<button type="button" id="saveTemplate" class="btns formbuttons">SAVE THIS AS TEMPLATE</button>-->
      </div>
  </div>
</body>
<script type="text/javascript">
  $(document).on('click', '.deleterow', function () {
    var deleted_row_id = $(this).attr('id');  
    //alert(deleted_row_id);
    labtestcount = $('#labtestcount').val();
    //alert(labtestcount);
    propercount = $('#propercount').val();
    //alert(deleted_row_id);
    var labtest_name = $('#labtestName'+deleted_row_id).val();
   if(labtest_name.replace(/\s+/g, '') == ""){
      $('#aslno tr:last').remove();
      //this.parentNode.parentNode.remove();
      $(this).closest('tr').remove();
      labtestcount = parseInt(labtestcount) - 1;
      if(labtestcount == 1){
        $('.deleterow').hide();
      }
        var deleted_usedlabtest = deleted_row_id+",";
        var current_usedlabtestcount = $('#usedlabtestcount').val();
        var new_usedlabtestcount  = current_usedlabtestcount.replace(deleted_usedlabtest, "");
        $('#usedlabtestcount').val(new_usedlabtestcount);
        $('#labtestcount').val(labtestcount);
   } else {
    var deleteconfirm = confirm("This Lab test will be deleted. Click OK to continue.");
    if(deleteconfirm == true){
       $('#lslno tr:last').remove();
      //this.parentNode.parentNode.remove();
      $(this).closest('tr').remove();
      //alert(labtestcount);
      labtestcount = parseInt(labtestcount) - 1;
      //alert(labtestcount);
      if(labtestcount == 1){
        $('.deleterow').hide();
      }
        var deleted_usedlabtest = deleted_row_id+",";
        var current_usedlabtestcount = $('#usedlabtestcount').val();
        var new_usedlabtestcount  = current_usedlabtestcount.replace(deleted_usedlabtest, "");
        $('#usedlabtestcount').val(new_usedlabtestcount);
        $('#labtestcount').val(labtestcount);
    } else {

    }
   }
  });var labtestcount = $('#labtestcount').val();
var propercount = $('#propercount').val();
$('#addLabTest').click(function(){
  var labtestcount = $('#labtestcount').val();
  var propercount = $('#propercount').val();
        labtestcount = parseInt(labtestcount) + 1;
        propercount = parseInt(propercount) + 1;
        $('.deleterow').show();
        var first = "<tr><td><input type='text' name='labtestName"+propercount+"' placeholder='Enter labtest Name..' maxlength='25' id='labtestName"+propercount+"' class='forminputs2'></td><td><input type='text' name='doctorName"+propercount+"' placeholder='Enter Doctor Name..' size='25px' maxlength='25' id='doctorName"+propercount+"' class='forminputs2'></td><td><textarea name='requirements"+propercount+"' id='requirements"+propercount+"' placeholder='Enter Requirements for the labtest..' class='forminputs2'></textarea></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"'></td></tr>";
        var slno  = "<tr><td>"+labtestcount+"</td></tr>";
        $('#aslno > tbody').append(slno);
        $('#adata > tbody').append(first);
        var current_usedlabtestcount = $('#usedlabtestcount').val();
        var new_usedlabtestcount = current_usedlabtestcount+propercount+",";
        $('#usedlabtestcount').val(new_usedlabtestcount);
        $('#labtestcount').val(labtestcount);
        $('#propercount').val(propercount);
        $('#labtestName'+propercount).focus();
      });
</script>
</html>