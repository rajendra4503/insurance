<?php
session_start();
//echo "<pre>";print_r($_POST);exit;
$status     = $_POST["status"];
$firstname  = $_POST["firstname"];
$amount     = $_POST["amount"];
$txnid      = $_POST["txnid"];
$posted_hash= $_POST["hash"];
$key        = $_POST["key"];
$productinfo= stripslashes($_POST["productinfo"]);
$email      = $_POST["email"];
$salt       = "cC7DouM4";
$udf1 		= (empty($_POST['udf1'])) ? 'mobile' : $_POST['udf1'];

if(isset($_POST["additionalCharges"])) {
       $additionalCharges=$_POST["additionalCharges"];
        $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'||||||||||'.$udf1.'|'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
}
else{	  
      $retHashSeq = $salt.'|'.$status.'||||||||||'.$udf1.'|'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
}
$hash = hash("sha512", $retHashSeq);

// echo "hash1:".$posted_hash."<br>";
// echo "hash2:".$hash."<br>";

// exit;

if ($hash != $posted_hash)
{
	if($udf1=='mobile')
	{
		echo "{".json_encode('PAYMENT_STATUS').':'.json_encode("0")."}";
	}
	else
	{
		echo "Invalid Transaction. Please try again";
	}
}
else
{
	if($udf1=='mobile' && $status=='success')
	{
		echo "{".json_encode('PAYMENT_STATUS').':'.json_encode("1")."}";
	}
	elseif($udf1=='mobile' && $status=='pending')
	{
		echo "{".json_encode('PAYMENT_STATUS').':'.json_encode("2")."}";
	}
	else
	{
		echo "<h3>Thank You. Your order status is ". $status .".</h3>";
		echo "<h4>Your Transaction ID for this transaction is ".$txnid.".</h4>";
		echo "<h4>We have received a payment of Rs. " . $amount . "</h4>";
	}
}         
?>	