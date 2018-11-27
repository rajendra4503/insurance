<?php
date_default_timezone_set('Asia/Kolkata');
include('include/configinc.php');
$merchants = array();
$plan= array("IN0000000029","IN0000000028");
foreach($plan as $val)
{
	$get_merchant_id_qry   = "select MerchantID from USER_PLAN_HEADER where PlanCode='$val'";
//echo $get_merchant_id_qry."<br>";
$get_merchant_id       = mysql_query($get_merchant_id_qry); 
$get_mer_count          = mysql_num_rows($get_merchant_id);
    if($get_mer_count>0)
    {
        while($merchant_rows = mysql_fetch_array($get_merchant_id))
        {
            $merchant_id   = $merchant_rows['MerchantID'];
            if(!in_array($merchant_id,$merchants))
            {
                array_push($merchants,$merchant_id);
            }
        }
    }
}

echo "<pre>";print_r($merchants);
?>