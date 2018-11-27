<?php
//ini_set("display_errors","0");
include_once('../include/configinc.php');
date_default_timezone_set("Asia/Kolkata");

$host_server  = $_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
//echo $host_server;exit;
$status       			= $_POST["status"];
$firstname    			= $_POST["firstname"];
$amount       			= $_POST["amount"];
$txnid        			= $_POST["txnid"];
$posted_hash  			= $_POST["hash"];
$key          			= $_POST["key"];
$productinfo  			= stripslashes($_POST["productinfo"]);
$email        			= $_POST["email"];
$salt         			= "VjJuHrmH";
//$salt 					= "cC7DouM4";
$referene_id			= (empty($_POST['mihpayid']))    	? '' : trim($_POST['mihpayid']);
$transaction_mode  		= (empty($_POST['mode']))    		? '' : trim($_POST['mode']);
$transaction_discount  	= (empty($_POST['discount']))    	? '' : trim($_POST['discount']);
$mobile  				= (empty($_POST['phone']))    		? '' : trim($_POST['phone']);
$error   				= (empty($_POST['error_Message']))  ? '' : trim($_POST['error_Message']);
$pg_type  				= (empty($_POST['PG_TYPE']))    	? '' : trim($_POST['PG_TYPE']);
$bank_ref_no 			= (empty($_POST['bank_ref_num']))   ? '' : trim($_POST['bank_ref_num']);
$unmapped_status		= (empty($_POST['unmappedstatus'])) ? '' : trim($_POST['unmappedstatus']);
$payu_money_id 			= (empty($_POST['payuMoneyId']))    ? '' : trim($_POST['payuMoneyId']);
$udf1 					= $_POST['udf1']; /*Source of Payment (M-Mobile and W-Web)*/
$udf2 					= $_POST['udf2']; /*UserID*/
$udf3 					= $_POST['udf3']; /*Pricing No from Pricing_details Table*/
$udf4 					= $_POST['udf4']; /*Number Of Months*/
$ad_end_date 			= date('Y:m:d', strtotime("+$udf4 months"));

if(isset($_POST["additionalCharges"])) {
    $additionalCharges=$_POST["additionalCharges"];
    $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||'.$udf4.'|'.$udf3.'|'.$udf2.'|'.$udf1.'|'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
}
else{  
    $retHashSeq = $salt.'|'.$status.'|||||||'.$udf4.'|'.$udf3.'|'.$udf2.'|'.$udf1.'|'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
 
}
$hash = hash("sha512", $retHashSeq);

$transaction 		= "insert into TRANSACTION_DETAILS 
						(TransactionID,ReferenceID,PricingNo,TxAmount,TxStatus,TxMode,TxDiscount,ProductInfo,FirstName,EmailID,MobileNo,SourceOfPayment,UserID,HashValue,Error,PGType,BankRefNo,UpMappedStatus,PayUMoneyID,CreatedDate,CreatedBy) values 
            			('$txnid','$referene_id','$udf3','$amount','$status','$transaction_mode','$transaction_discount','$productinfo','$firstname','$email','$mobile','$udf1','$udf2','$posted_hash','$error','$pg_type','$bank_ref_no','$unmapped_status','$payu_money_id',now(),'$udf2')";
$transaction_query 	= mysql_query($transaction);
$transaction_id 	= mysql_insert_id();
$check_insert		= mysql_affected_rows();
if($check_insert)
{
	mysql_query("insert into USER_TRANSACTIONS (UserID,TransactionID,CreatedDate,CreatedBy) values 
				('$udf2','$transaction_id',now(),'$udf2')");
}

if ($hash != $posted_hash) {
	echo "Invalid Transaction. Please try again";
}
else
{
    echo "<h3>Thank You. Your order status is ". $status .".</h3>";
	echo "<h4>Your Transaction ID for this transaction is ".$txnid.".</h4>";
	//echo "<h4>We have received a payment of Rs. " . $amount . "</h4>";
} 
?>
<!--Please enter your website homepagge URL -->

<p><a href="http://<?php echo $host_server;?>/AdPayment.php"> Try Again</a></p>
