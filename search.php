<?php
include('include/configinc.php');

//Prmary  Diagnosis

if(!empty($_POST["type"]) == 'primary') {

    $id = $_POST["selectId"];

    if(!empty($_POST["keyword"])) {

      $query ="SELECT DISTINCT ICD10CM FROM diagnosis_master WHERE ICD10CM like '%".$_POST["keyword"]."%'";

      $result = mysql_query($query);

      if(!empty($result)) {
      ?>
      <ul id="country-list" class="list">
        <?php while($row=mysql_fetch_assoc($result)) { ?>
          <li onClick="selectValue('<?php echo $row["ICD10CM"]; ?>',<?php echo $id;?>);"><?php echo $row["ICD10CM"]; ?></li>
        <?php } ?>
      </ul>   
 <?php } } 

 if(!empty($_POST["description"])) {

      $query ="SELECT DISTINCT ICD10CMDescription FROM diagnosis_master WHERE ICD10CM = '".$_POST["description"]."'";

      $result = mysql_query($query);

      if(!empty($result)) {
        while($row=mysql_fetch_assoc($result)) { 
           echo $row["ICD10CMDescription"];
        }  
      } 
    } 

    if(!empty($_POST["procedure"])) {

      $query ="SELECT DISTINCT ICD10PCS FROM diagnosis_master WHERE ICD10CM like '".$_POST["procedure"]."'";
      $result = mysql_query($query);
      if(!empty($result)) {
      ?>
        <?php while($row=mysql_fetch_assoc($result)) { ?>
        <option value="<?php echo $row["ICD10PCS"]; ?>">
          <?php echo $row["ICD10PCS"]; ?>
        </option>
        <?php } 
     
       } } 

 if(!empty($_POST["procedure_description"])) {
      $query ="SELECT DISTINCT ICD10PCSDescription FROM diagnosis_master WHERE ICD10PCS = '".$_POST["procedure_description"]."'";
      $result = mysql_query($query);
      if(!empty($result)) {
        while($row=mysql_fetch_assoc($result)) { 
           echo $row["ICD10PCSDescription"];
        }  
      } 
    }
    exit;
  } 
?>