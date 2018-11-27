<?php
include_once('include/configinc.php');
date_default_timezone_set("Asia/Kolkata");
//echo "<pre>";print_r($_SESSION);exit;
if(isset($_SESSION['logged_email']))
{
      $logged_userid                      = $_SESSION['logged_userid'];
      $logged_merchantid                  = $_SESSION['logged_merchantid'];
      $logged_companyname                 = $_SESSION['logged_companyname'];
      $logged_mobile                      = $_SESSION['logged_mobile'];
      $logged_email                       = $_SESSION['logged_email'];  
      $logged_usertype                    = $_SESSION['logged_usertype'];   
      $logged_roleid                      = $_SESSION['logged_roleid'];
      $logged_firstname                   = $_SESSION['logged_firstname'];
      $logged_lastname                    = $_SESSION['logged_lastname'];
      $logged_userstatus                  = $_SESSION['logged_userstatus'];
      $logged_companycountryid            = $_SESSION['logged_companycountryid'];
      $logged_userdp                      = $_SESSION['logged_userdp'];
      $plan_to_customize                  = (empty($_SESSION['current_assigned_plan_code']))      ? '' : $_SESSION['current_assigned_plan_code'];
      //echo $plan_to_customize;exit;
     
      /*DYNAMIC TABS*/
      $get_modules        = "select t1.ModuleID,t1.ModuleName,t1.ModuleURL,t1.CustomizeModuleURL
                             from PLANPIPER_MODULES as t1,MAPPING_MERCHANT_WITH_MODULES as t2
                              where t1.ModuleID=t2.ModuleID and t2.MerchantID='$logged_merchantid' and t1.ModuleStatus=t2.Status
                              and t1.ModuleStatus='A' order by ModuleID";
        $get_modules_query  = mysql_query($get_modules);
        $get_module_count   = mysql_num_rows($get_modules_query);
        $modules            = "";
        $active             = "";
            if($get_modules_query)
            {
                while($module_rows = mysql_fetch_array($get_modules_query))
                {
                    $module_id  = $module_rows['ModuleID'];
                    $module_name= $module_rows['ModuleName'];
                    if($plan_to_customize==""){
                    $url        = (empty($module_rows['ModuleURL']))          ? '' : $module_rows['ModuleURL'];
                    }
                    else{
                    $url        = (empty($module_rows['CustomizeModuleURL'])) ? '' : $module_rows['CustomizeModuleURL'];
                    }
                    $modules    .= "<li role='presentation' class='navbartoptabs' id='$module_id'><a href=$url>$module_name</a></li>"; 
                }
            }
      /*END OF DYNAMIC TABS*/
      /*GET URL TO REDIRECT BASED ON LOGGED MERCHANT*/
      $get_url              = "select min(t1.ModuleID) as Min_ModuleID,t1.ModuleURL
                             from PLANPIPER_MODULES as t1,MAPPING_MERCHANT_WITH_MODULES as t2
                              where t1.ModuleID=t2.ModuleID and t2.MerchantID='$logged_merchantid' and t1.ModuleStatus=t2.Status
                              and t1.ModuleStatus='A'";
        $get_url_query  = mysql_query($get_url);
        $get_url_count   = mysql_num_rows($get_url_query);
        $url            = "";
        $active             = "";
            if($get_url_query)
            {
                while($url_rows = mysql_fetch_array($get_url_query))
                {
                    $module_id  = $url_rows['Min_ModuleID'];
                    $header_url = (empty($url_rows['ModuleURL']))          ? '' : $url_rows['ModuleURL']; 
                }
            }

       /*END OF GET URL TO REDIRECT BASED ON LOGGED MERCHANT*/

       //FETCH SMS PARAMETERS FROM DATABASE
        $get_sms_configuration      = "select MerchantID, URL, UserParam, UserParamValue, PwdParam, PwdParamValue, SenderIDParam, SenderIDParamValue, MessageParam, MobileParam from SMS_GATEWAY_DETAILS where MerchantID='$logged_merchantid'";
            $get_sms_configuration_query  = mysql_query($get_sms_configuration);
            $sms_config_count   = mysql_num_rows($get_sms_configuration_query);
            if($sms_config_count == 1)
            {
              while($row = mysql_fetch_array($get_sms_configuration_query)){
                $smsurl                 = $row['URL'];
                $username_parameter     = $row['UserParam'];
                $username_value         = $row['UserParamValue'];
                $password_parameter     = $row['PwdParam'];
                $password_value         = $row['PwdParamValue'];
                $sender_parameter       = $row['SenderIDParam'];
                $sender_value           = $row['SenderIDParamValue'];
                $message_parameter      = $row['MessageParam'];
                $number_parameter       = $row['MobileParam'];
              }
            }
            else{
              $smsurl=""; $username_parameter=""; $username_value=""; $password_parameter=""; $password_value=""; 
              $sender_parameter=""; $sender_value=""; $message_parameter=""; $number_parameter=""; 
            }
        //END OF FETCH SMS PARAMETERS FROM DATABASE

} 
else
{
  header("Location:login.php");
}
?>