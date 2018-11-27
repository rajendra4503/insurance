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
    <style type="text/css">
      #styled-select select {
    margin-top: 10px;
    background: transparent;
    background-color:#004F35;
    color: #f2bd43;
    width: 218px;
    padding: 5px;
    line-height: 1;
    border: 0;
    border-radius: 0;
    height: 34px;
    -webkit-appearance: none;
  }
  #styled-select select:hover {
    background-color:#004F35;
  }
    </style>

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
            <li id="language" class="navbar_li">
                <div id="styled-select">
                  <select class="language">
              <option>Chosse your language</option>
              <?php 
                $languages = mysql_query("SELECT * FROM LANGUAGE_MASTER ORDER BY ID");
                while ($lang = mysql_fetch_assoc($languages)) { 
                if($_REQUEST['lang'] == $lang['LanguageName']){ ?>
                <option selected><?php echo $lang['LanguageName']; ?></option>
                <?php }else{?>
                <option><?php echo $lang['LanguageName']; ?></option>
                <?php }
                }
               ?>
              </select>
                </div>
            </li>
            <?php 
              mysql_set_charset('utf8');
              if( !empty( $_REQUEST['lang']) && $_REQUEST['lang'] != '' ){
                $table = $_REQUEST['lang'];
                if(table_exists($table)){
                  $query  = mysql_query("SELECT FieldNo ,$table  FROM $table WHERE ScreenNo='003'");
                  while ( $result = mysql_fetch_assoc($query) ) {
                  ${$result['FieldNo']} = $result[$table];
                  } 
                } 
              }


              if( !empty( $_REQUEST['lang']) && $_REQUEST['lang'] != '' ){
                $language_url = $_REQUEST['lang'];
              }  else{
                  $language_url = 'English';
              }

            ?>

            <li id="dashboard" class="navbar_li"><a href="dashboard.php?lang=<?php echo $language_url; ?>" class="navbar_href">DASHBOARD<br><?php if(isset($DASHBOARD) &&  $DASHBOARD !=''){echo '('.$DASHBOARD.')';}?></a></li>
            <li id="plans" class="navbar_li"><a href="plan_list.php?lang=<?php echo $language_url; ?>" class="navbar_href">PLAN MANAGEMENT <br><?php if(isset($PLANMANAGEMENT) &&  $PLANMANAGEMENT !=''){echo '('.$PLANMANAGEMENT.')';}?></a></li>
            <?php
            if(($logged_usertype!='I'))
            {
            ?>
            <li id="medicine" class="navbar_li"><a href="medicine_directory.php?lang=<?php echo $language_url; ?>" class="navbar_href">MEDICINE DIRECTORY <br><?php if(isset($MEDICINEDIRECTORY) &&  $MEDICINEDIRECTORY !=''){echo '('.$MEDICINEDIRECTORY.')';}?></a></li>
            <?php }
            if(($logged_usertype=='I') && ($logged_roleid !=4) && ($logged_roleid !=3))
            {
            }
            else
            {
            ?>
            <?php if(($logged_roleid !=4) && ($logged_roleid !=3)){?>
            <li id="users" class="navbar_li"><a href="user_list.php?lang=<?php echo $language_url; ?>" class="navbar_href">HEALTHCARE PROVIDERS <br><?php if(isset($HEALTHCAREPROVIDERS) &&  $HEALTHCAREPROVIDERS !=''){echo '('.$HEALTHCAREPROVIDERS.')';}?></a></li>
            <?php }
            }
            ?>
            <!--<li id="clients" class="navbar_li"><a href="new_user.php" class="navbar_href">NEW PLAN USER</a></li>-->
            <?php
            if(($logged_usertype=='I') && ($logged_roleid !=4) && ($logged_roleid !=3))
            {
            ?>
            <li id="planusers" class="navbar_li"><a href="plan_users.php?lang=<?php echo $language_url; ?>" class="navbar_href">FAMILY MEMBERS</a></li>
            <?php
            }
            else
            {
            ?>
            <li id="planusers" class="navbar_li"><a href="plan_users.php?lang=<?php echo $language_url; ?>" class="navbar_href">PATIENTS <br><?php if(isset($PATIENTS) &&  $PATIENTS !=''){echo '('.$PATIENTS.')';}?></a></li>
            <?php
            }
            ?>
            
            <li id="assign" class="navbar_li"><a href="assign_plan.php?lang=<?php echo $language_url; ?>" class="navbar_href">ASSIGN PLANS <br><?php if(isset($ASSIGNPLANS) &&  $ASSIGNPLANS !=''){echo '('.$ASSIGNPLANS.')';}?></a></li>

            <li id="notification" class="navbar_li"><a href="push_notification.php?lang=<?php echo $language_url; ?>" class="navbar_href">SEND NOTIFICATION <br><?php if(isset($SENDNOTIFICATION) &&  $SENDNOTIFICATION !=''){echo '('.$SENDNOTIFICATION.')';}?></a></li>

            <?php if(($logged_roleid !=4) && ($logged_roleid !=3)){?><li id="profile" class="navbar_li"><a href="profile.php?lang=<?php echo $language_url; ?>" class="navbar_href">PROFILE MANAGEMENT <br><?php if(isset($PROFILEMANAGEMENT) &&  $PROFILEMANAGEMENT !=''){echo '('.$PROFILEMANAGEMENT.')';}?></a></li><?php }?>

            <li id="patients_claim" class="navbar_li"><a href="patients_claim.php" class="navbar_href">CLAIM SEARCH</a></li>


            <li id="claim_form1" class="navbar_li"><a href="claim_form1.php" class="navbar_href">CLAIM FORM B</a></li>

           <!--  <li id="reports" class="navbar_li"><a href="reports.php" class="navbar_href">REPORTS</a></li> -->
            <li id="logout" class="navbar_li"><a href="logout.php?lang=<?php echo $language_url; ?>" class="navbar_href">LOGOUT <br><?php if(isset($LOGOUT) &&  $LOGOUT !=''){echo '('.$LOGOUT.')';}?></a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
