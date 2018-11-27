//CHECKING FOR A SAME DEVICE ID FOR MULTIPLE PERSONS
        if($deviceid!="" && $ostype!="")
        {
        $get_duplicate_device_ids 		= "select UserID,DeviceID from USER_DETAILS where DeviceID='$deviceid' and (EmailID!='$username' or  MobileNo='$username')";
        //echo $get_duplicate_device_ids;exit;
        $get_duplicate_device_ids_qry	= mysql_query($get_duplicate_device_ids);
            if($get_duplicate_device_ids_qry)
            {
                while($duplicate_rows = mysql_fetch_array($get_duplicate_device_ids_qry))
                {
                    $duplicate_user_id 		= $duplicate_rows['UserID'];
                    $duplicate_device_id 	= $duplicate_rows['DeviceID'];

                    mysql_query("update USER_DETAILS set DeviceID='',OSType='' where UserID='$duplicate_user_id'");
                }
            }
        }



        mysql_query("update USER_DETAILS set OSType='$ostype',DeviceID='$deviceid' where UserID='$user_id'");

         /* $get_merchants  = "select t1.MerchantID,t1.RoleID,t2.CompanyName,t3.RoleName 
                               from USER_MERCHANT_MAPPING as t1, MERCHANT_DETAILS as t2,ROLE_MASTER as t3
                               where t1.UserID='$user_id' and t1.MerchantID=t2.MerchantID and t1.Status='A' and t1.RoleID='5'
                               and t1.RoleID=t3.RoleID";
            //echo $get_merchants;exit;
            $get_merchant_qry = mysql_query($get_merchants);
            $get_merchant_count=mysql_num_rows($get_merchant_qry);
                if($get_merchant_count>1)
                {
                    while($merchant_rows = mysql_fetch_array($get_merchant_qry))
                    {
                        $res1['MerchantID']     = $merchant_rows['MerchantID'];
                        $res1['CompanyName']    = $merchant_rows['CompanyName'];
                    array_push($merchants,$res1);
                    }
                }*/


                ****************************

                 $files                      = "http://".$host_server.'/'.$plan_header.$plan_cover_image_name;
                            function reduce_image_size($dest_folder,$image_name,$files)
                            {
                                //REDUCE IMAGE RESOLUTION
                                if($files)
                                {
                                    //echo 123;exit;
                                    $dest   = $dest_folder.$image_name;
                                    $width  = 300;
                                    $height = 300;
                                    list($width_orig, $height_orig) = getimagesize($files);
                                    $ratio_orig = $width_orig/$height_orig;
                                    if ($width/$height > $ratio_orig)
                                    {
                                       $width = $height*$ratio_orig;
                                    }
                                    else
                                    {
                                       $height = $width/$ratio_orig;
                                    }
                                    $image_p    = imagecreatetruecolor($width, $height);
                                    $image      = imagecreatefromjpeg($files);
                                    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
                                    imagejpeg($image_p,$dest, 100);
                                    ImageDestroy ($image_p);
                                }
                                //END OF REDUCING IMAGE RESOLUTION
                            }

                            if(getimagesize($files) !== false)
                            {
                                //echo "yes";
                                reduce_image_size($reduced_plan_header,$plan_cover_image_name,$files);
                            }






                            NEW QUERY
                            $get_detailed_info = "select t1.PlanCode,t1.MerchantID,t1.CategoryID,t4.CategoryName,t1.PlanName,t1.PlanDescription,t1.PlanStatus,
                                          t1.PlanCurrencyCode,t1.PlanCost,t1.PlanCoverImagePath,t1.CreatedDate,t2.RoleID,t2.Status,
                                          t3.CompanyName,t3.CompanyEmailID,t3.CompanyMobileNo,t3.CompanyAddressLine1,t3.CompanyAddressLine2,
                                          t3.CompanyPinCode,'Bangalore' as CompanyCityName,'Karnataka' as CompanyStateName,
                                          'India' as CompanyCountryName
                                          from PLAN_HEADER as t1,USER_MERCHANT_MAPPING as t2,MERCHANT_DETAILS as t3,CATEGORY_MASTER as t4
                                          where t1.MerchantID=t2.MerchantID and t2.MerchantID=t3.MerchantID and t2.UserID='$user_id' 
                                          and t1.CategoryID=t4.CategoryID and t1.PlanCode='$plancode'";