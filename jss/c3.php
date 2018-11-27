<?php
session_start();
include('include/configinc.php');
include('include/session.php');
include('include/functions.php');
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
	    <meta charset="utf-8" />
	    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	    <title>Plan Piper - Graph Test</title>
	    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
      <link rel="stylesheet" type="text/css" href="css/jquery.bxslider.css">
	    <link rel="stylesheet" type="text/css" href="css/planpiper.css">
	    <link rel="stylesheet" type="text/css" href="fonts/font.css">
      <link rel="stylesheet" type="text/css" href="css/c3.css">
	    <link rel="shortcut icon" href="images/planpipe_logo.png"/> 
      <style type="text/css">
        .bx-wrapper .bx-pager{
          display: none;
        }
      </style>  
    </head>
  <body style="overflow:hidden;">
  <div id="planpiper_wrapper">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="height:100%;">
       <div class="col-sm-2 paddingrl0" id="sidebargrid">
        <?php include("sidebar.php");?>
     </div>
     <div class="col-sm-10 paddingrl0" id="content_wrapper">
      <?php include_once('top_header.php');?>
        <section>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" id="" style="padding-left:2px;">      
            <div class="slider4" style="width:100%">
                <div class="slide" id="chart"  style="width:25%"></div>
                <div class="slide" id="chart2" style="width:25%"></div>
                <div class="slide" id="chart3" style="width:25%"></div>
                <div class="slide" id="chart4" style="width:25%"></div>
            </div>            
          </div>

        </section>
      </div>
     </div>
    </div>
      <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
      <script type="text/javascript" src="js/jquery.bxslider.min.js"></script>
	  <script type="text/javascript" src="js/bootstrap.min.js"></script>
	  <script type="text/javascript" src="js/modernizr.js"></script>
	  <script type="text/javascript" src="js/placeholders.min.js"></script>
	  <script type="text/javascript" src="js/bootbox.min.js"></script>
    <script type="text/javascript" src="js/c3.js"></script>
    <script type="text/javascript" src="js/d3.v3.min.js" charset="utf-8"></script>
    
	  <script type="text/javascript">
  		$(document).ready(function() {
  			var chart = c3.generate({
            bindto: '#chart',
            data: {
              columns: [
                ['data1', 30, 200, 100, 400, 150, 250],
              ]
            }
        });
        var chart2 = c3.generate({
            bindto: '#chart2',
            data: {
              columns: [
                ['data2', 90, 80, 78, 63, 65, 54]
              ]
            }
        });
        var chart3 = c3.generate({
            bindto: '#chart3',
            data: {
              columns: [
                ['data3', 22, 34, 55, 11, 67, 23]
              ]
            }
        });
        var chart4 = c3.generate({
            bindto: '#chart4',
            data: {
              columns: [
                ['data4', 55, 24, 89, 66, 23, 13]
              ]
            }
        });
          $('.slider4').bxSlider({
            slideWidth: 300,
            minSlides: 1,
            maxSlides: 3,
            moveSlides: 1,
            slideMargin: 10,
            infiniteLoop: false
          });

  		});
  		</script>
    </body>