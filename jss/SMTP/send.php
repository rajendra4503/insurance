<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>PHPMailer - sendmail test</title>
</head>
<body>
<?php
require 'PHPMailerAutoload.php';
//require 'class.smtp.php';
//require 'class.phpmailer.php';

//Create a new PHPMailer instance
$mail = new PHPMailer();
// Set PHPMailer to use the sendmail transport
$mail->isSMTP();
//Set who the message is to be sent from
$mail->setFrom('rimith@raj.com', 'Rimith');
//Set an alternative reply-to address
//$mail->addReplyTo('rimith@appmantras.com', 'Rimith');
//Set who the message is to be sent to
$mail->addAddress('indu@appmantras.com', 'indu');
//Set the subject line
$mail->Subject = 'PHPMailer SMTP test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents('test.html'), dirname(__FILE__));


$message="<html>
			<head>
			<title></title>
			</head>
			<style type='text/css'>
			body {
			  margin: 0;
			  padding: 0;
			}
			</style>
			<body>
				<table style='width:100%;background-color:#E77168;height:150px;'>
					<tr>
						<td width='43%'>
							<img src='images/bni_logo.png'  style='float:left;height:90;width:90;padding-right:350px;padding-left:10px;'>
						</td>
						<td width='19%' align='center'>
							<img src='images/wasta_logo.png' style='text-align:center;height:90;width:90;padding-right:300px;'></td>
						<td width='38%' style='text-align:center;padding-right:10px;color:#FFFFFF;font-size:24px'>
							<b>Powered by wasta</b>
						</td>
					</tr>
				</table>
				<table style='width:100%;'>
					<tr >
						<td></td>
						<td align='center'>
							<p class= 'label1' style='font-size:50px'><b>BNI BEDAZZLERS</b></p>
							<p class= 'label1' style='font-size:50px'><b>MEGA VISITORS DAY</b></p>
							<p class= 'label2' style='font-size:40px'>THU, FEB 13TH 2014</p>
							<p class= 'label2' style='font-size:40px'>HOTEL HYATT, MG ROAD</p>
						</td>
						<td></td>
					</tr>
				</table>
				<table style='width:100%;height:200px;background-color:#E5E7E7'>
					<tr>
						<td align='center'>
							<p class= 'label4' style='font-size:30px;'>Wasta was made by your friendly neighbourhood</p>
							<p style='font-size:30px;'>
								<span class='label3'>app developers, </span>
								<span class='label4'>APP MANTRAS</span>.
							</p>
							<p style='font-size:30px;'>
								<span class='label3'>Your business needs an app. </span>
								<span class='label4'>Get in touch with us!</span>
							</p>
						</td>
					</tr>
				</table>
			</body>
			</html>";
			
$mail->msgHTML($message, dirname(__FILE__));

//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.gif');

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
?>
</body>
</html>
