<?php
//ini_set("display_errors","0");
include_once('include/configinc.php');
include_once('include/session.php');
$userdp = "";
if(($logged_userdp != "")&&($logged_userdp != NULL)){
  $userdp = "uploads/profile_pictures/".$logged_userdp;
}else {
  $userdp = "images/dp.png";
}
?>
  <!-- <div class="sidebar-nav">
      <div class="navbar navbar-default" role="navigation" style="height: 100%;">
        <div class="navbar-header">
        <div align="left">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
       </div>
        
          <span class="visible-xs navbar-brand">Menu</span>
        </div>
        <div class="navbar-collapse collapse sidebar-navbar-collapse">
          <ul class="nav navbar-nav">
            <li id="navusername" style="text-transform:uppercase;">
            	<div style="width:100%;" align="center">
            		<div id="dpdiv" align="center"><img src="images/dp.png"  style="width:100%;height:auto;"></div>
            	</div>
            	<div id="namediv" align="center">
	     			<?php echo $logged_firstname." ".$logged_lastname;?>
	     		</div>
            </li>
            <li>
					<a href="create_plan.php"><img src="images/hotkeys/after/newplan.png" id="hotkeyicon"><h6>NEW PLAN</h6></a></li>
          <li><a href="plan_medication.php"><img src="images/hotkeys/before/newclient.png" id="hotkeyicon"><h6>NEW CLIENT</h6></a>
          </li>
					
            <li>
              <li id='bazaar' class='navbar_li'><a href='bazaar.php' class='navbar_href'><img src="images/hotkeys/after/newclient.png" id="hotkeyicon">Bazaar</a></li>
            </li>
      	
           </ul>
        </div>/.nav-collapse 
      </div>
    </div>-->

       <div class="sidebar-nav" style="height:100%;">
      <div class="navbar navbar-default" role="navigation" style="height: 100%;">
        <div class="navbar-header">
        <div align="left">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
         
          <!--<span class="visible-xs navbar-brand">Menu</span>-->
        </div>
        <div class="navbar-collapse collapse sidebar-navbar-collapse">
          <ul class="nav navbar-nav sidebarsmallscreen">
            <li id="navusername" style="text-transform:uppercase;" class="hidden-xs">
              <div style="width:100%;" align="center">
                <div id="dpdiv" align="center"><img src="<?php echo  $userdp;?>"  style="width:100%;height:auto;" id="dpdivimg"></div>
              </div>
              <div id="namediv" align="center">
            <?php echo $logged_firstname." ".$logged_lastname;?>
          </div>
            </li>
            <!--<li id="newplan" class="navbar_li"><a href="create_plan.php" class="navbar_href">NEW PLAN</a></li>
            <li id="newuser" class="navbar_li"><a href="new_user.php" class="navbar_href">NEW USER</a></li>-->
            <li id="dashboard" class="navbar_li"><a href="dashboard.php" class="navbar_href">DASHBOARD</a></li>
            <li id="plans" class="navbar_li"><a href="plan_list.php" class="navbar_href">PLAN MANAGEMENT</a></li>
            <?php
            if(($logged_usertype!='I'))
            {
            ?>
            <li id="medicine" class="navbar_li"><a href="medicine_directory.php" class="navbar_href">MEDICINE DIRECTORY</a></li>
            <?php }
            if(($logged_usertype=='I') && ($logged_roleid !=4) && ($logged_roleid !=3))
            {
            }
            else
            {
            ?>
            <?php if(($logged_roleid !=4) && ($logged_roleid !=3)){?>
            <li id="users" class="navbar_li"><a href="user_list.php" class="navbar_href">HEALTHCARE PROVIDERS</a></li>
            <?php }
            }
            ?>
            <!--<li id="clients" class="navbar_li"><a href="new_user.php" class="navbar_href">NEW PLAN USER</a></li>-->
            <?php
            if(($logged_usertype=='I') && ($logged_roleid !=4) && ($logged_roleid !=3))
            {
            ?>
            <li id="planusers" class="navbar_li"><a href="plan_users.php" class="navbar_href">FAMILY MEMBERS</a></li>
            <?php
            }
            else
            {
            ?>
            <li id="planusers" class="navbar_li"><a href="plan_users.php" class="navbar_href">PATIENTS</a></li>
            <?php
            }
            ?>
            
            <li id="assign" class="navbar_li"><a href="assign_plan.php" class="navbar_href">ASSIGN PLANS</a></li>
            <li id="notification" class="navbar_li"><a href="push_notification.php" class="navbar_href">SEND NOTIFICATION</a></li>
            <?php if(($logged_roleid !=4) && ($logged_roleid !=3)){?><li id="profile" class="navbar_li"><a href="profile.php" class="navbar_href">PROFILE MANAGEMENT</a></li><?php }?>
           <!--  <li id="reports" class="navbar_li"><a href="reports.php" class="navbar_href">REPORTS</a></li> -->
            <li id="logout" class="navbar_li"><a href="logout.php" class="navbar_href">LOGOUT</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
