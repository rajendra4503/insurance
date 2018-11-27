<!DOCTYPE html>
<html lang="en">
<body>
  <div>
   <form name="frm_plan_labtest" id="frm_plan_labtest" method="post" action="plan_labtest.php">
      <!--<div class="prescriptionNameBar" align="center">
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
          <th>Lab Test Name</th>
          <th style="width:180px;">Doctor's Name</th>
          <th>Requirements</th>
          <th></th>
        </tr>
        <tr>
          <td><input type="text" name="labtestName1" placeholder="Enter Lab test Name.." maxlength="25" id="labtestName1" class="forminputs2"></td>
          <td><input type="text" name="doctorName1" placeholder="Enter Doctor Name.." maxlength="25" id="doctorName1" class="forminputs2"></td>
          <td><textarea name="requirements1"  placeholder="Enter Requirements for the Lab test.." class="forminputs2"></textarea></td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="1"></td>
        </tr>
        <tr>
          <td><input type="text" name="labtestName2" placeholder="Enter Lab test Name.." maxlength="25" id="labtestName2" class="forminputs2"></td>
          <td><input type="text" name="doctorName2" placeholder="Enter Doctor Name.." maxlength="25" id="doctorName2" class="forminputs2"></td>
          <td><textarea name="requirements2" id="requirements2" placeholder="Enter Requirements for the Lab test.." class="forminputs2"></textarea></td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="2"></td>
        </tr>
        <tr>
          <td><input type="text" name="labtestName3" placeholder="Enter Lab test Name.." maxlength="25" id="labtestName3" class="forminputs2"></td>
          <td><input type="text" name="doctorName3" placeholder="Enter Doctor Name.." size="25px" maxlength="25" id="doctorName3" class="forminputs2"></td>
          <td><textarea name="requirements3" id="requirements3"  placeholder="Enter Requirements for the Lab test.." class="forminputs2"></textarea></td>
          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="3"></td>
        </tr>
      </table>
      </div>
      <input type="hidden" name="usedlabtestcount" id="usedlabtestcount" value="1,2,3,">
      <input type="hidden" name="labtestcount" id="labtestcount" value="3">
      <input type="hidden" name="hidden_value" id="hidden_value" value="Y">
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
    var labtest_name = $('#labtestName'+deleted_row_id).val();
   if(labtest_name.replace(/\s+/g, '') == ""){
      $('#aslno tr:last').remove();
      //this.parentNode.parentNode.remove();
      $(this).closest('tr').remove();
      labtestcount = labtestcount - 1;
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
      labtestcount = labtestcount - 1;
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
  });var labtestcount = 3;
var propercount = 3;
$('#addLabTest').click(function(){
        labtestcount = labtestcount + 1;
        propercount = propercount + 1;
        $('.deleterow').show();
        var first = "<tr><td><input type='text' name='labtestName"+propercount+"' placeholder='Enter labtest Name..' maxlength='25' id='labtestName"+propercount+"' class='forminputs2'></td><td><input type='text' name='doctorName"+propercount+"' placeholder='Enter Doctor Name..' size='25px' maxlength='25' id='doctorName"+propercount+"' class='forminputs2'></td><td><textarea name='requirements"+propercount+"' id='requirements"+propercount+"' placeholder='Enter Requirements for the labtest..' class='forminputs2'></textarea></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"'></td></tr>";
        var slno  = "<tr><td>"+labtestcount+"</td></tr>";
        $('#aslno > tbody').append(slno);
        $('#adata > tbody').append(first);
        var current_usedlabtestcount = $('#usedlabtestcount').val();
        var new_usedlabtestcount = current_usedlabtestcount+propercount+",";
        $('#usedlabtestcount').val(new_usedlabtestcount);
        $('#labtestcount').val(labtestcount);
      });
</script>
</html>