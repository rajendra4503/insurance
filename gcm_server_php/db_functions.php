<?php

class DB_Functions {

    private $db;

    //put your code here
    // constructor
    function __construct() {
        include_once 'db_connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->db->connect();
    }

    // destructor
    function __destruct() {

    }

    /*Storing Registration ID into Database When User Logs in for first time */
    public function storeUser($name, $email, $gcm_regid) {
        //$result    = "insert into gcm_users(name, email, gcm_regid, created_at) values ('$name', '$email', '$gcm_regid', now())";
        $get_user      = mysql_query("select UserID from USER_ACCESS where (EmailID='$email' || MobileNo='$email')");
        $get_user_count= mysql_num_rows($get_user);
        if($get_user_count)
        {
            $row    = mysql_fetch_array($get_user);
            $userid = $row['UserID'];
        }

        $result  = "update USER_ACCESS set OSType='A',DeviceID='$gcm_regid' where (EmailID='$email' || MobileNo='$email') and UserID='$userid'";
        // echo ($result);
        mysql_query($result);
        // check for successful store
        if($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    

}

?>