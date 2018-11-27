<?php 
$message = "Mr X has not taken the medicine";
$userid  = "00091327942967327157";
$i = 1;
if($i == 1){
?>

<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
  <script type="text/javascript">
  $(document).ready(function(){
    var message = "<?php echo $message;?>";
    var userid = "<?php echo $userid;?>";
    notifyMe(message,userid);
  });
  <?php include('js/notification.js');?>
  </script>
  <?php
}
?>
