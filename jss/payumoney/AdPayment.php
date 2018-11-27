<?php
session_start();
//ini_set("display_errors","0");
include_once('../include/configinc.php');
date_default_timezone_set("Asia/Kolkata");
// $source     = "M";
// $logged_firstname  = "Soma";
// $logged_email      = "somashekar@appmantras.com";
// $logged_mobile     = "9740992958";
// $logged_userid     = "123465798";
$source     = (empty($_REQUEST['source']))      ? '' : mysql_real_escape_string(trim($_REQUEST['source'])); /*W - Web or M - Mobile*/
if($source=='W')
{
include('../include/session.php');
$firstname  = (empty($logged_firstname))        ? '' : $logged_firstname;
$email      = (empty($logged_email))            ? '' : $logged_email;
$mobile     = (empty($logged_mobile))           ? '' : $logged_mobile;
$userid     = (empty($_REQUEST['userid']))      ? '' : $logged_userid;
}
elseif($source=='M')
{
$userid     = (empty($_REQUEST['userid']))      ? '' : $_REQUEST['userid'];
// $firstname  = (empty($_REQUEST['first_name']))  ? '' : mysql_real_escape_string(trim($_REQUEST['first_name']));
// $email      = (empty($_REQUEST['email_id']))    ? '' : mysql_real_escape_string(trim($_REQUEST['email_id']));
// $mobile     = (empty($_REQUEST['mobile_no']))   ? '' : mysql_real_escape_string(trim($_REQUEST['mobile_no']));
$get_user_details = mysql_query("select t1.FirstName, t1.LastName, t2.EmailID, substr(t2.MobileNo,6) as MobileNo from USER_DETAILS as t1, USER_ACCESS as t2 where t1.UserID= t2.UserID and t1.UserID = '$userid'");
$user_count = mysql_num_rows($get_user_details);
if($user_count > 0){
    while ($user_details = mysql_fetch_array($get_user_details)) {
        $firstname  = $user_details['FirstName'];
        $email      = $user_details['EmailID'];
        $mobile     = $user_details['MobileNo'];
    }
    } else {
        $firstname  = "";
        $email      = "";
        $mobile     = "";
    }
}


// $source     = "M";
// $firstname  = "Soma";
// $email      = "somashekar@appmantras.com";
// $mobile     = "9740992958";
// $userid     = "123465798";

// $source     = "M";
// $firstname  = "Indu";
// $email      = "indu@appmantras.com";
// $mobile     = "9742230268";
// $userid     = "000919880932490";


// if($source!=('W' || 'M') || $firstname=="" || $email=="" || $mobile=="" || $userid=="")
// {
//     header('Location:../index.html');
// }
//exit;
//GET MONTHY PRICE
$get_price = mysql_query("select PricingNo, NoOfMonths from PRICING_DETAILS where CurrencyCode='INR' order by NoOfMonths");
$price_count = mysql_num_rows($get_price);
$months = "";
if($price_count > 0){
    while ($prices = mysql_fetch_array($get_price)) {
        $pricing_no     = $prices['PricingNo'];
        $no_of_months   = $prices['NoOfMonths'];
        $months         .= "<option value='$no_of_months'>$no_of_months</option>";
    } 
}

// Merchant key here as provided by Payu
$MERCHANT_KEY = "ZaMVSX";
//$MERCHANT_KEY = "xlpc8X";
// Merchant Salt as provided by Payu
$SALT = "VjJuHrmH";
//$SALT = "cC7DouM4";

// End point - change to https://secure.payu.in for LIVE mode
$PAYU_BASE_URL = "https://test.payu.in";
//$PAYU_BASE_URL = "https://secure.payu.in";

$host_server    = "http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
$success_url    = $host_server."/AdSuccess.php";
$failure_url    = $host_server."/AdFailure.php";

//echo $success_url;exit;

$action = '';

$posted = array();
if(!empty($_POST)) {
    //echo "<pre>";print_r($_POST);exit;
  foreach($_POST as $key => $value) {    
    $posted[$key] = $value; 
  }
}

$formError = 0;

if(empty($posted['txnid'])) {
  // Generate random transaction id
  $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
  $txnid = $posted['txnid'];
}

//echo "<pre>";print_r($posted);
//echo sizeof($posted);exit;

$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0)
{
    if(empty($posted['key'])        || empty($posted['txnid'])          || empty($posted['amount'])     || empty($posted['firstname']) 
        || empty($posted['email'])  || empty($posted['phone'])          || empty($posted['surl'])
        || empty($posted['furl'])   || empty($posted['service_provider']))
    {
    $formError = 1;
    } 
    else
    {
    $posted['productinfo']  = json_encode(json_decode('[{"name":"Planpiper","description":"Health Care","value":"10","isRequired":"false"}]'));
    $hashVarsSeq            = explode('|', $hashSequence);
    $hash_string = '';  
        foreach($hashVarsSeq as $hash_var)
        {
        $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
        $hash_string .= '|';
        }
    $hash_string .= $SALT;
    //echo "<pre>";print_r($posted);exit;
    //header('Location:../index.html');exit;
    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYU_BASE_URL . '/_payment';
    }
}
elseif(!empty($posted['hash']))
{
  $hash = $posted['hash'];
  $action = $PAYU_BASE_URL . '/_payment';
}
?>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>Plan Piper - Payment Page</title>
        <script>
        var hash = '<?php echo $hash ?>';
        function submitPayuForm() {
          if(hash == '') {
            return;
          }
          var payuForm = document.forms.payuForm;
          payuForm.submit();
        }
        </script>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="../css/magicsuggest.css">
        <link rel="stylesheet" type="text/css" href="../css/planpiper.css">
        <link rel="stylesheet" type="text/css" href="../fonts/font.css">
        <link rel="shortcut icon" href="../images/planpipe_logo.png"/>   
        <style type="text/css">
            label {font-size: 18px;}
        </style>
    </head>
    <body onload="submitPayuForm()">
    <br/>
    <?php if($formError) { ?>
      <span style="color:red">Please fill all mandatory fields.</span>
    <?php } ?>
    
        <div class="container">
        <h1 align="center">Payment Details</h1>
            <form action="<?php echo $action; ?>" method="post" name="payuForm" id="payuForm" class="form-horizontal" role="form">
                <div class="errormessages" id="registrationerror"></div>

                <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
                <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
                <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />

                <div class="form-group">
                  <label class="control-label col-sm-3 text-left" for="no_of_months">Select No. Of Months:</label>
                  <div class="col-sm-9">
                    <select class="form-control input-lg" id="no_of_months" name="no_of_months">
                        <option style="display:none;" value="0">Select No. </option>
                        <?php echo $months;?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-sm-3 text-left" for="amount">Amount:</label>
                  <div class="col-sm-9">
                    <input name="amount" id="amount" readonly class="form-control input-lg" style="cursor:none;" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" />
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-sm-3 text-left" for="firstname">First Name:</label>
                  <div class="col-sm-9">
                    <input name="firstname" id="firstname" readonly class="form-control input-lg" style="cursor:none;" value="<?php echo (empty($posted['firstname'])) ? $firstname : $posted['firstname']; ?>" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-sm-3 text-left" for="email">Email:</label>
                  <div class="col-sm-9">
                    <input name="email" id="email" readonly class="form-control input-lg" style="cursor:none;" value="<?php echo (empty($posted['email'])) ? $email : $posted['email']; ?>" />
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-sm-3 text-left" for="phone">Mobile:</label>
                  <div class="col-sm-9">
                    <input name="phone" id="phone" readonly class="form-control input-lg" style="cursor:none;" value="<?php echo (empty($posted['phone'])) ? $mobile : $posted['phone']; ?>" />
                  </div>
                </div>

                <textarea name="productinfo" style="display:none"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea>
                <input type="hidden" name="surl" value="<?php echo (empty($posted['surl'])) ? $success_url : $posted['surl'] ?>" size="64" />
                <input type="hidden" name="furl" value="<?php echo (empty($posted['furl'])) ? $failure_url : $posted['furl'] ?>" size="64" />
                <input type="hidden" name="service_provider" value="payu_paisa" size="64" />
                <input type="hidden" name="udf1" value="<?php echo (empty($posted['udf1'])) ? $source   : $posted['udf1'] ?>" />
                <input type="hidden" name="udf2" value="<?php echo (empty($posted['udf2'])) ? $userid   : $posted['udf2'] ?>" />
                <input type="hidden" name="udf3" id="udf3" value="<?php echo (empty($posted['udf3'])) ? '' : $posted['udf3'] ?>" />
                <input type="hidden" name="udf4" id="udf4" value="<?php echo (empty($posted['udf4'])) ? '' : $posted['udf4'] ?>" />
                <?php if(!$hash) { ?>
                <div class="form-group">        
                  <div class="col-sm-offset-2 col-sm-10 text-center">
                    <button type="submit" class="btn btn-primary" name="frm_submit" id="frm_submit" style="width: 25%;">Submit</button>
                  </div>
                </div>
                <?php } ?>
            </form>
        </div>

        <!-- <form action="<?php echo $action; ?>" method="post" name="payuForm" id="payuForm">
                <div class="errormessages" id="registrationerror"></div>
            <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
            <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
            <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
            <table>
                <tr >
                    <td>
                        Select No. Of Months
                    </td>
                    <td>
                        <select class="selectpicker forminputs3" id="no_of_months" name="no_of_months">
                            <option style="display:none;" value="0">Select No. </option>
                            <?php echo $months;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Amount: </td>
                    <td><input name="amount" id="amount" readonly style="cursor:none;" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" /></td>
                </tr>
                <tr>
                    <td>First Name: </td>
                    <td><input name="firstname" id="firstname" readonly style="cursor:none;" value="<?php echo (empty($posted['firstname'])) ? $firstname : $posted['firstname']; ?>" />
                    </td>
                </tr>
                <tr>
                    <td>Email: </td>
                    <td><input name="email" id="email" readonly style="cursor:none;" value="<?php echo (empty($posted['email'])) ? $email : $posted['email']; ?>" />
                    </td>
                </tr>
                <tr>
                    <td>Phone: </td>
                    <td><input name="phone" id="phone" readonly style="cursor:none;" value="<?php echo (empty($posted['phone'])) ? $mobile : $posted['phone']; ?>" /></td>
                </tr>
                <textarea name="productinfo" style="display:none"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea>
                <input type="hidden" name="surl" value="<?php echo (empty($posted['surl'])) ? $success_url : $posted['surl'] ?>" size="64" />
                <input type="hidden" name="furl" value="<?php echo (empty($posted['furl'])) ? $failure_url : $posted['furl'] ?>" size="64" />
                <input type="hidden" name="service_provider" value="payu_paisa" size="64" />
                <input type="hidden" name="udf1" value="<?php echo (empty($posted['udf1'])) ? $source   : $posted['udf1'] ?>" />
                <input type="hidden" name="udf2" value="<?php echo (empty($posted['udf2'])) ? $userid   : $posted['udf2'] ?>" />
                <input type="hidden" name="udf3" id="udf3" value="<?php echo (empty($posted['udf3'])) ? '' : $posted['udf3'] ?>" />
                <input type="hidden" name="udf4" id="udf4" value="<?php echo (empty($posted['udf4'])) ? '' : $posted['udf4'] ?>" />
                <tr>
                    <?php if(!$hash) { ?>
                    <td colspan="4" align="center"><input type="button" value="Submit" name="frm_submit" id="frm_submit" /></td>
                    <?php } ?>
                </tr>
            </table>
        </form> -->
    </body>
    <script type="text/javascript" src="../js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/modernizr.js"></script>
    <script type="text/javascript" src="../js/placeholders.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
        //alert(123)
        $("#no_of_months").on("change",function(){
        //alert(123)
        var no_of_months=$("#no_of_months").val();
        //alert(no_of_months)
            $.ajax({
                url:'../ajax_validation.php',
                data:{no_of_months:no_of_months,type:"get_price"},
                type: 'post',
                success : function(resp,status){
                //alert(resp)
                var res         = resp.split("~");
                var amnt        = res[0];
                var pricing_no  = res[1];
                //alert(amnt);
                //alert(pricing_no);
                    $("#amount").val(amnt);
                    $("#udf3").val(pricing_no);               
                    $("#udf4").val(no_of_months);   
                },
                error : function(resp){}
            });
        });

        $("#frm_submit").click(function() {
        //alert(123)
        var no_of_months = $('#no_of_months').val();
        if(no_of_months == '0'){
            $('#no_of_months').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please select no. of months.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        var amount = $('#amount').val();
        amount       = amount.replace(/ /g,''); //To check if the variable contains only spaces
        if(amount == ''){
            $('#amount').val('');
            $('#amount').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Amount is required");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        var firstname = $('#firstname').val();
        firstname       = firstname.replace(/ /g,''); //To check if the variable contains only spaces
        if(firstname == ''){
            $('#firstname').val('');
            $('#firstname').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("First name is required.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        var email = $('#email').val();
        email = email.replace(/ /g,''); //To check if the variable contains only spaces
        if(email == ''){
            //$('#email_id').val('');
            $('#email').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Email ID is required.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        else
        {
            var email = $('#email').val();
            if (validateEmail(email)) {
            //alert('Nice!! your Email is valid, now you can continue..');
            }
            else {
            $('#email').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Email ID is invalid.");
            $("#registrationerror").fadeOut(5000);
            return false;
            e.preventDefault();
            }
        }
        var mobilenumber = $('#phone').val();
        mobilenumber       = mobilenumber.replace(/ /g,''); //To check if the variable contains only spaces
        if(mobilenumber == ''){
            $('#phone').val('');
            $('#phone').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Mobile number is required.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }

        if(isNaN(mobilenumber)){
            $('#mobilenumber').val('');
            $('#mobilenumber').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter a valid mobile number.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        if(mobilenumber<999999){
            $('#mobilenumber').val('');
            $('#mobilenumber').focus();
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter a valid mobile number.");
            $("#registrationerror").fadeOut(5000);
            return false;
        }
        if(Number(mobilenumber).toString().length < 7){
            //alert("Please enter a valid mobile number");
            $("#registrationerror").fadeIn();
            $("#registrationerror").text("Please enter a valid mobile number.");
            $("#registrationerror").fadeOut(5000);
            $("#mobilenumber").focus();
            return false;
        } 
    $('#payuForm').submit();
    });

    //Function that validates email address through a regular expression.
    function validateEmail(sEmail)
    {
        var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
        if (filter.test(sEmail))
        {
        return true;
        }
        else
        {
        return false;
        }
    }

    });
    </script>
</html>
