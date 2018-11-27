<?php
//ini_set("display_errors","0");
include_once('include/configinc.php');
include_once('include/session.php');
?>
   <div class="sidebar-nav">
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
					<a href="create_plan.php"><div id="lefthotkeyselected"><img src="images/hotkeys/after/newplan.png" id="hotkeyicon"><h6>NEW PLAN</h6></div></a></li>
          <li><a href="plan_medication.php"><div id="lefthotkey"><img src="images/hotkeys/before/newclient.png" id="hotkeyicon"><h6>NEW CLIENT</h6></div></a>
          </li>
					
            <li>
              
            </li>
            <li>
				<a href="planlist.php"><div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 paddingrl0" id="lefthotkey"><img src="images/hotkeys/before/plans.png" id="hotkeyicon"><h6>PLANS</h6></div></a>
				<a href="client_list.php"><div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 paddingrl0" id="lefthotkey"><img src="images/hotkeys/before/clients.png" id="hotkeyicon"><h6>CLIENTS</h6></div></a>
			</div>            	
            </li>
            <li>
				<div id="leftnavrow">
					<a class="assign" id="assign"><div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 paddingrl0" id="lefthotkey"><img src="images/hotkeys/before/assign.png" id="hotkeyicon" title="Assign a plan to a client"><h6>ASSIGN</h6></div></a>
					<a href="profile_settings.php"><div class="lcol-lg-6 col-md-6 col-sm-6 col-xs-6 paddingrl0" id="lefthotkey"><img src="images/hotkeys/before/settings.png" id="hotkeyicon"><h6>PROFILE</h6></div></a>
				</div>            	
            </li>
            <li>
				<div id="leftnavrow">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 paddingrl0" id="lefthotkeyinactive" title="Not Available in this version"><img src="images/hotkeys/before/notifications.png" id="hotkeyicon"><h6>NOTIFICATIONS</h6></div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 paddingrl0" id="lefthotkeyinactive" title="Not Available in this version"><img src="images/hotkeys/before/plancodes.png" id="hotkeyicon"><h6>PLAN CODES</h6></div>
				</div>         	
            </li>
            <li>
				<div id="leftnavrow">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="lefthotkeydots"><h6>...</h6></div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="lefthotkeydots"><h6>...</h6></div>
				</div>    	
            </li>        	
           </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
