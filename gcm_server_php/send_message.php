<?php
/*Micromax*/
//$regId 	= "APA91bGbtzbR_USdgasHI2MLHq1FkxwnmSl9jBcoPiM8myb6RXJEdLB5gZCpDIqsh9rVDJN5z5jOwtpb7OBhqcsuVznHroEIbBB7_va9eLQBsrO8fkcd80gBWQ0nxX7d6u-agdUC3-KvsV2nYqFKTSU6WznIvlNm9GrANekqpxTpcO23Hf5jQSI";
/*TAB*/
//$regId  = "APA91bFbfeDP7NjNlcjlEL_NtHN8bEWa3DCEOmKkq95cRq1pi3tT2f05dhli8Y0ExhDsNEfG1d-ExSQ5Edx1IYWFDkzD3wxfAJesChXSTh9KwTBujw1l8b19YyxYwQY4dkxle41Mi4MnThQ22HYN1mg-d59fersIIj6EVHAOjZWnzRXfbsfzeYk";
//$regId  = "APA91bHZT1tYJwNI-8chuihBGR-xNiWdnhhqO1LoW4LUTTAEYAQMa_pkRNsO_6RUd2DpNxsbpQrYnXVBgYR6axEjnLsBMHjiks76L0NHot60d4lwtzHubD9r0YtzxcqrsXw_XG3Ojq4QniJjTDwV944Btjyk-2sidQ";		   
//$message 	= "Hi Somsekhar..Good Morning!!";


//$regId  = "APA91bFsukuEzR96H5IWR9o4hVf4wJGUENr7iwkdHM31mYxY_bk70qBJ953ragj3BrkzqI8KtoUjiWzHcsCzCqEnR9KomJbALiH8V_gGlsof1jNh_-3PPljhZ3pdUBbaizOfsYvZOkkmNWK4VYhHUgIWZ-Vor0Y4yA";		   
//message 	= "Hi Somu..!!";


//$res['message'] = "Hi Somu..!!";
//$res['flag']	= 'C';
//$message		= json_encode($res);

	if ($regId && $message)
	{
	    include_once 'GCM.php';
	    $gcm = new GCM();

	  	$registatoin_ids = array($regId);
	 	$message = array("price" => $message);

	    $result = $gcm->send_notification($registatoin_ids, $message);
	  
	}
?>
