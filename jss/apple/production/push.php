<?php
// Put your device token here (without spaces):

//$deviceToken = "096477bd030feeeede614f5cf303329107dfb35c5934b5fcfcbfb7bd443a37f5";


// $message 	= "Hi Vishnu...";
// $flag 	 	= "plan_update";
// $userid  	= "";
// $report_id 	= "";	
	
	$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert', 'apple/production/planpiper.pem');
			//stream_context_set_option($ctx, 'ssl', 'local_cert', '/usr/local/www/apache24/data/appmantras/planpiperv1/apple/production/PlanPiperFinalProd.pem');
			//stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
			
			// Open a connection to the APNS server
			$fp = stream_socket_client(
				'ssl://gateway.push.apple.com:2195', $err,
				$errstr, 120, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

			//echo $err."<br>";
			//echo $errstr."<br>";

			if (!$fp)
			{
				//echo "$err";
				exit("Failed to connect: $err $errstr" . PHP_EOL);
			}
			else
				//echo $err."<br>";
			'Connected to APNS' . PHP_EOL;
			
			// Create the payload body
			$body['aps'] = array(
				'alert' 	=> $message,
				'userid' 	=> $userid,
				'report_id'	=> $report_id,
				'flag' 		=> $flag,
				'sound' 	=> 'default'
				);
			//echo "<pre>";print_r($body);exit;
			// Encode the payload as JSON
			$payload = json_encode($body);
			
			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
			
			// Send it to the server
			$result = fwrite($fp, $msg, strlen($msg));
			
			if (!$result)
			{
				//echo "Nopz";
				'Message not delivered' . PHP_EOL;
			}	 
			else
			{
				//echo "Yup";
				//echo "<pre>";print_r($result);
				'Message successfully delivered' . PHP_EOL;
			}
			//echo "{".json_encode('BNI_NOTIFICATION').':'.json_encode("1")."}";
			// Close the connection to the server
			fclose($fp);
	

?>
