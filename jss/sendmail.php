<?php
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php');
include ('SMTP/PHPMailerAutoload.php');
include ('SMTP/class.phpmailer.php');
include ('SMTP/class.smtp.php');
//echo "<pre>";print_r($_SESSION);exit;
//$useremail	= "rimith@appmantras.com";
//$username 	= "Sudhir";


if($email != ""){
	function mailresetlink($useremail,$username)
	{
	//Create a new PHPMailer instance
	$mail = new PHPMailer();
	// Set PHPMailer to use the sendmail transport
	$mail->isSMTP();
	//Set who the message is to be sent from
	$mail->setFrom('administrator@appmantras.com', 'Admin');
	//Set who the message is to be sent to
	$mail->addAddress($useremail,$username);
	//Set the subject line
	$mail->Subject = 'Planpiper';

	$message = "
	<html>
	<head>
	<title> Android APK File</title>
	</head>
	<body>
	<p>Hi $username,</p>
	<p>You are now successfully registered with Planpiper.Please Find Attached the Android APK File for the Planpiper app.</p>
	<p>While you cannot reply to this email, please feel free to write to us with any queries at admin@appmantras.com</p>	
	</body>
	</html>
	";	
			
	$mail->msgHTML($message, dirname(__FILE__));

	//send the message, check for errors
		if(!$mail->send()){
			
		} 
		else{
			
		}
	}
//echo $email." ".$name;exit;
mailresetlink($email,$name);
}
?>