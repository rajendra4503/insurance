<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="fonts/font.css">
	<link rel="icon" href="favicon.ico"/>
	<title>Planpiper | client dashboard</title>
	<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/flowtype.js"></script>  
  <style type="text/css">
    .carousel-control{ 
      opacity:1;
    }
    .carousel-control.left {
      left:-0.8em;
      top:9.2em;
    }
    .carousel-control.right {
      top:9.2em;
    }

    .carousel-indicators {
      display: none;
    }
  </style>
</head>

<body>
  <div class="container-fluid" style="padding-left:0px;padding-right:0px">

    <div class="row" id="headerBar">
    	<div class="col-lg-6 col-md-6 col-sm-8 col-xs-12" style="text-align:center;">
    		<input type="text" name="clientSearch" id="clientSearch" placeholder="Search patient by name/mobile/email...">
    	</div>
      <div class="col-lg-3 col-md-3 col-sm-4 hidden-xs" id="cdPhoneAndEmail">
        <img src="images/clientDashboard/cdphone.png">&nbsp;&nbsp;&nbsp;
          <span><!-- phoneNo with country code -->
            +91 1234567890
          </span>
          <br>
        <img src="images/clientDashboard/cdemail.png">&nbsp;
        <span><!-- emailID-->
          chinnikrishna@gmail.com
        </span>
      </div>
      <div class="col-lg-3 hidden-md hidden-sm hidden-xs" id="cdAddress">
        <img src="images/clientDashboard/cdaddress.png">&nbsp;
        <span><!-- addressLine1 -->
          5th floor, "Place On Earth"
        </span><br>
        <span><!-- addressLine2 -->
          #100 Pai Layout, Old Madras Road
        </span><br>
        <span><!-- city, state pincode -->
          Bangalore, Karnataka 560038
        </span>
      </div>
    </div>

    <div class="row" id="cdAllData">
      <div class="col-lg-4 col-lg-offset-0 col-md-6 col-md-offset-0 col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-1" id="pma">
        <div id="plans">
          <div class = "plandetailsbox" style="background-color:#3F464E;">
            <div id="planCategory">
              HealthCare(Pregnancy)
            </div>
            <div class="planTitle">
              9 Months Pregnancy Plan
            </div>
            <div id="planDescription">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit...
            </div>
            <div id="createdOnBy">
              <span>Created On: 15-12-2014</span>
            </div>
          </div>
        </div>
        <div id="medication">
          <div id="medicationHeader">
            <img src="images/clientDashboard/cdedit.png" id="cdmedit">
            Medication
            <img src="images/clientDashboard/cdadd.png" id="cdmadd">
          </div>
          <div id="medicationBody">
            <div class="prescription">Prescription6(19th Dec 2014)
              <img src="images/clientDashboard/cdprescriptionCollapsed.png">
            </div>
            <div class="medicines">
              <div class="table-responsive">          
                <table class="table table-bordered">
                  <tbody id="cdclientTableBody">
                   <tr>
                     <td>1.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                   <tr>
                     <td>2.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                   <tr>
                     <td>3.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="prescription">Prescription5(19th Nov 2014)
              <img src="images/clientDashboard/cdprescriptionCollapsed.png">
            </div>
            <div class="medicines">
              <div class="table-responsive">          
                <table class="table table-bordered">
                  <tbody id="cdclientTableBody">
                   <tr>
                     <td>1.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                   <tr>
                     <td>2.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                   <tr>
                     <td>3.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="prescription">Prescription4(19th Oct 2014)
              <img src="images/clientDashboard/cdprescriptionCollapsed.png">
            </div>
            <div class="medicines">
              <div class="table-responsive">          
                <table class="table table-bordered">
                  <tbody id="cdclientTableBody">
                   <tr>
                     <td>1.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                   <tr>
                     <td>2.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                   <tr>
                     <td>3.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="prescription">Prescription3(19th Sep 2014)
              <img src="images/clientDashboard/cdprescriptionCollapsed.png">
            </div>
            <div class="medicines">
              <div class="table-responsive">          
                <table class="table table-bordered">
                  <tbody id="cdclientTableBody">
                   <tr>
                     <td>1.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                   <tr>
                     <td>2.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                   <tr>
                     <td>3.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="prescription">Prescription4(19th Aug 2014)
              <img src="images/clientDashboard/cdprescriptionCollapsed.png">
            </div>
            <div class="medicines">
              <div class="table-responsive">          
                <table class="table table-bordered">
                  <tbody id="cdclientTableBody">
                   <tr>
                     <td>1.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                   <tr>
                     <td>2.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                   <tr>
                     <td>3.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="prescription">Prescription1(19th July 2014)
              <img src="images/clientDashboard/cdprescriptionCollapsed.png">
            </div>
            <div class="medicines">
              <div class="table-responsive">          
                <table class="table table-bordered">
                  <tbody id="cdclientTableBody">
                   <tr>
                     <td>1.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                   <tr>
                     <td>2.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                   <tr>
                     <td>3.</td>
                     <td>Calpol D 100mg</td>
                   </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
        <div id="appointments">
          <div id="appointmentHeader">
            <img src="images/clientDashboard/cdedit.png" id="cdaedit">
            Appointments
            <img src="images/clientDashboard/cdadd.png" id="cdaadd">
          </div>
          <div id="appointmentBody">
            <div class="appointmentDate">19th Dec 2014
              <img src="images/clientDashboard/cdprescriptionCollapsed.png">
            </div>
            <div class="appointmentTimes">
              <div class="table-responsive">          
                <table class="table table-bordered">
                  <tbody id="cdclientTableBody">
                   <tr>
                     <td>12:00 PM.</td>
                     <td>Dr.Vishnu</td>
                     <td>Sugar Checkup</td>
                   </tr>
                   <tr>
                     <td>02:00 PM.</td>
                     <td>Dr.Saptak</td>
                     <td>BP Checkup</td>
                   </tr>
                   <tr>
                     <td>05:00 PM.</td>
                     <td>Dr.Chinni</td>
                     <td>Health Checkup</td>
                   </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="appointmentDate">19th Nov 2014
              <img src="images/clientDashboard/cdprescriptionCollapsed.png">
            </div>
            <div class="appointmentTimes">
              <div class="table-responsive">          
                <table class="table table-bordered">
                  <tbody id="cdclientTableBody">
                   <tr>
                     <td>12:00 PM.</td>
                     <td>Dr.Vishnu</td>
                     <td>Sugar Checkup</td>
                   </tr>
                   <tr>
                     <td>02:00 PM.</td>
                     <td>Dr.Saptak</td>
                     <td>BP Checkup</td>
                   </tr>
                   <tr>
                     <td>05:00 PM.</td>
                     <td>Dr.Chinni</td>
                     <td>Health Checkup</td>
                   </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="appointmentDate">19th Oct 2014
              <img src="images/clientDashboard/cdprescriptionCollapsed.png">
            </div>
            <div class="appointmentTimes">
              <div class="table-responsive">          
                <table class="table table-bordered">
                  <tbody id="cdclientTableBody">
                   <tr>
                     <td>12:00 PM.</td>
                     <td>Dr.Vishnu</td>
                     <td>Sugar Checkup</td>
                   </tr>
                   <tr>
                     <td>02:00 PM.</td>
                     <td>Dr.Saptak</td>
                     <td>BP Checkup</td>
                   </tr>
                   <tr>
                     <td>05:00 PM.</td>
                     <td>Dr.Chinni</td>
                     <td>Health Checkup</td>
                   </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="appointmentDate">19th Sep 2014
              <img src="images/clientDashboard/cdprescriptionCollapsed.png">
            </div>
            <div class="appointmentTimes">
              <div class="table-responsive">          
                <table class="table table-bordered">
                  <tbody id="cdclientTableBody">
                   <tr>
                     <td>12:00 PM.</td>
                     <td>Dr.Vishnu</td>
                     <td>Sugar Checkup</td>
                   </tr>
                   <tr>
                     <td>02:00 PM.</td>
                     <td>Dr.Saptak</td>
                     <td>BP Checkup</td>
                   </tr>
                   <tr>
                     <td>05:00 PM.</td>
                     <td>Dr.Chinni</td>
                     <td>Health Checkup</td>
                   </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="appointmentDate">19th Aug 2014
              <img src="images/clientDashboard/cdprescriptionCollapsed.png">
            </div>
            <div class="appointmentTimes">
              <div class="table-responsive">          
                <table class="table table-bordered">
                  <tbody id="cdclientTableBody">
                   <tr>
                     <td>12:00 PM.</td>
                     <td>Dr.Vishnu</td>
                     <td>Sugar Checkup</td>
                   </tr>
                   <tr>
                     <td>02:00 PM.</td>
                     <td>Dr.Saptak</td>
                     <td>BP Checkup</td>
                   </tr>
                   <tr>
                     <td>05:00 PM.</td>
                     <td>Dr.Chinni</td>
                     <td>Health Checkup</td>
                   </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="appointmentDate">19th July 2014
              <img src="images/clientDashboard/cdprescriptionCollapsed.png">
            </div>
            <div class="appointmentTimes">
              <div class="table-responsive">          
                <table class="table table-bordered">
                  <tbody id="cdclientTableBody">
                   <tr>
                     <td>12:00 PM.</td>
                     <td>Dr.Vishnu</td>
                     <td>Sugar Checkup</td>
                   </tr>
                   <tr>
                     <td>02:00 PM.</td>
                     <td>Dr.Saptak</td>
                     <td>BP Checkup</td>
                   </tr>
                   <tr>
                     <td>05:00 PM.</td>
                     <td>Dr.Chinni</td>
                     <td>Health Checkup</td>
                   </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="col-lg-8 col-lg-offset-0 col-md-6 col-md-offset-0 col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-1" id="par" style="padding-left:0px;">
        <div id="analytics">
          <div class="carousel slide" id="myCarousel">
            <div class="carousel-inner">
              <div class="item active">
                <div class="col-lg-4" style="padding-left:0px;padding-right:0px;">
                  <div class="thumbnail">
                    <div class="graph" id="graph1">
                      
                    </div>
                    <div class="caption" >
                      Blood Sugar Level <img src="images/clientDashboard/cdshare.png">
                    </div>
                  </div>
                </div>
                <div class="col-lg-4" style="padding-left:0px;padding-right:0px;">
                  <div class="thumbnail">
                    <div class="graph" id="graph2">
                      
                    </div>
                    <div class="caption" >
                      Weight Measurement <img src="images/clientDashboard/cdshare.png">
                    </div>
                  </div>
                </div>
                <div class="col-lg-4" style="padding-left:0px;padding-right:0px;">
                  <div class="thumbnail">
                    <div class="graph" id="graph3">
                      
                    </div>
                    <div class="caption" >
                      Blood Pressure <img src="images/clientDashboard/cdshare.png">
                    </div>
                  </div>
                </div>
              </div>

              <div class="item">
                <div class="col-lg-4" style="padding-left:0px;padding-right:0px;">
                  <div class="thumbnail">
                    <div class="graph" id="graph1">
                      
                    </div>
                    <div class="caption" >
                      BSL <img src="images/clientDashboard/cdshare.png">
                    </div>
                  </div>
                </div>
                <div class="col-lg-4" style="padding-left:0px;padding-right:0px;">
                  <div class="thumbnail">
                    <div class="graph" id="graph2">
                      
                    </div>
                    <div class="caption" >
                      WM <img src="images/clientDashboard/cdshare.png">
                    </div>
                  </div>
                </div>
                <div class="col-lg-4" style="padding-left:0px;padding-right:0px;">
                  <div class="thumbnail">
                    <div class="graph" id="graph3">
                      
                    </div>
                    <div class="caption" >
                      BP <img src="images/clientDashboard/cdshare.png">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
              <img src="images/clientDashboard/cdleft.png">
            </a>
            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
              <img src="images/clientDashboard/cdright.png">
            </a>
          </div>
        </div>
        <div id="reports">
          <div class="col-lg-6" id="allReports">
            <div class="table-responsive">          
               <table class="table table-bordered">
                 <thead id="reportsTableHeader">
                   <tr>
                     <th>#</th>
                     <th>Title</th>
                     <th>Given By</th>
                     <th>Date&nbsp;&nbsp;&nbsp;<img src="images/clientDashboard/cdsortingDown.png"></th>
                   </tr>
                 </thead>
                 <tbody id="reportsTableBody">
                   <tr>
                     <td>1</td>
                     <td>Blood sugar level report<img src="images/view.png"></td>
                     <td>Dr. Ajay Kumar</td>
                     <td>24/Dec/2014</td>
                   </tr>
                   <tr>
                     <td>2</td>
                     <td>Blood sugar level report<img src="images/view.png"></td>
                     <td>Dr. Vishnu Kumar</td>
                     <td>24/Nov/2014</td>
                   </tr>
                   <tr>
                     <td>3</td>
                     <td>Blood sugar level report<img src="images/view.png"></td>
                     <td>Dr. Ranga Rao</td>
                     <td>24/Oct/2014</td>
                   </tr>
                   <tr>
                     <td>4</td>
                     <td>Blood sugar level report<img src="images/view.png"></td>
                     <td>Dr. V.V.Reddy</td>
                     <td>24/Sep/2014</td>
                   </tr>
                   <tr>
                     <td>5</td>
                     <td>Blood sugar level report<img src="images/view.png"></td>
                     <td>Dr. Kumar</td>
                     <td>24/Aug/2014</td>
                   </tr>

                 </tbody>
               </table>
             </div>

             <div id="reportsSearchDiv">
               <input type="text" name="reportsSearch" id="reportsSearch" placeholder="Search by Report Name/Doctor Name...">
               <img src="images/clientDashboard/cdadd.png">
             </div>
          </div>
          <div class="col-lg-6" id="reportView">
            <div id="reportname">
               Blood Sugar Level Report and Test &nbsp;&nbsp;<img src="images/clientDashboard/cdfullscreen.png">
            </div>
            <div id="reportpage">
              
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
          
  <script type="text/javascript">
    $(document).ready(function(){
      $('body').flowtype({
         minimum   : 500,
         maximum   : 1200,
         minFont   : 10,
         maxFont   : 40,
         fontRatio : 30
      });

      mainHeader = 0;//include main header height also
      cdHeaderBarHeight = $("#headerBar").outerHeight(true);
      totalUsedHeight = cdHeaderBarHeight + mainHeader;
      remainingHeight = ($(window).innerHeight()-totalUsedHeight)
      plansHeight = remainingHeight * (0.25);
      medicationHeight = remainingHeight * (0.35);
      appointmentsHeight = remainingHeight * (0.35);
      $('#pma').css({height: remainingHeight});
      $('#plans').css({height: plansHeight});
      $('#medication').css({height: medicationHeight});
      $('#appointments').css({height: appointmentsHeight});

      analyticsHeight = remainingHeight * (0.35);
      reportsHeight = remainingHeight * (0.60);
      $('#par').css({height: remainingHeight});
      $('#analytics').css({height: analyticsHeight});
      $('#reports').css({height: reportsHeight});

      graphHeight = analyticsHeight * (0.74);
      graphCaptionHeight = analyticsHeight * (0.2);
      $('.graph').css({height:graphHeight});
      $('.caption').css({height:graphCaptionHeight});

      $('#allReports').css({height:reportsHeight});
      $('#reportView').css({height:reportsHeight});

      cdreportnameHeight = $("#reportname").outerHeight(true);
      $('#reportpage').css({height:(reportsHeight-cdreportnameHeight)});

      $('#cdmedit, #cdmadd').click(function(){
        alert('edit and add buttons will redirect to medication page');
      });

      $('#cdaedit, #cdaadd').click(function(){
        alert('edit and add buttons will redirect to medication page');
      });

      $(".prescription, .appointmentDate").click(function(){
        if($(this).children().attr("src") ==  "images/clientDashboard/cdprescriptionCollapsed.png"){
          $(this).css({"color":"#004F35","background-color":"#EEEEEE"});
          $(this).children().attr("src","images/clientDashboard/cdprescriptionExpanded.png");
        }else{
          $(this).css({"color":"#676767","background-color":"#E0E0E0"});
          $(this).children().attr("src","images/clientDashboard/cdprescriptionCollapsed.png");
        }
        $(this).next().slideToggle("slow");
      });

      $("#cdAllData .thumbnail .caption img").click(function(){
        alert("sharing this graph");
      });

      $("#reports #allReports #reportsTableHeader tr th img").click(function(){
        if($(this).attr("src") ==  "images/clientDashboard/cdsortingDown.png"){
          $(this).attr("src","images/clientDashboard/cdsortingUp.png");
          alert("list in assending order");
        }else{
          $(this).attr("src","images/clientDashboard/cdsortingDown.png");
          alert("list in descending order");
        }
      });

      $("#reports #allReports #reportsSearchDiv img").click(function(){
        alert("report add page");
      });

      $("#reports #allReports #reportsTableBody tr").click(function(){
        alert("opening the report");
      });

      $("#reports #reportView #reportname img").click(function(){
        alert("report will open in fullscreen");
      });


      $(window).resize(function(){
        mainHeader = 0;//include main header height also
        cdHeaderBarHeight = $("#headerBar").outerHeight(true);
        totalUsedHeight = cdHeaderBarHeight + mainHeader;
        remainingHeight = ($(window).innerHeight()-totalUsedHeight)
        plansHeight = remainingHeight * (0.25);
        medicationHeight = remainingHeight * (0.35);
        appointmentsHeight = remainingHeight * (0.35);
        $('#pma').css({height: remainingHeight});
        $('#plans').css({height: plansHeight});
        $('#medication').css({height: medicationHeight});
        $('#appointments').css({height: appointmentsHeight});

        analyticsHeight = remainingHeight * (0.35);
        reportsHeight = remainingHeight * (0.60);
        $('#par').css({height: remainingHeight});
        $('#analytics').css({height: analyticsHeight});
        $('#reports').css({height: reportsHeight});

        graphHeight = analyticsHeight * (0.74);
        graphCaptionHeight = analyticsHeight * (0.2);
        $('.graph').css({height:graphHeight})
        $('.caption').css({height:graphCaptionHeight})
        });


    }); 
    $('#myCarousel').carousel({
      interval: false
    }); 


  </script>


</body>
</html>
