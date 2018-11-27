<!DOCTYPE html>
<html lang="en">
<style type="text/css">
      .carousel-control{ 
      opacity:1;
    }
    .carousel-control.left {
      left:-0.8em;
    }

    .carousel-indicators {
      display: none;
    }
</style>
<body>
  <div>
   <form name="frm_plan_diet" id="frm_plan_diet" method="post" action="#">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0" style="padding-bottom:100px;">
        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12 mealCol">
          <select name="meal1" id="meal1">
            <option>select meal of the day</option>
            <option>Just after getting up</option>
            <option>Breakfast</option>
            <option>Morning Snack</option>
            <option>Lunch</option>
            <option>After Snack</option>
            <option>Dinner</option>
            <option>Just before sleeping</option>
            <option>At this specific time</option>
          </select>
        </div>
        <div class="col-lg-10 col-md-10 col-sm-6 col-xs-12 paddingrl0">
                   <div class="carousel slide" id="myCarousel">
            <div class="carousel-inner">
              <div class="item active">
                <div class="col-sm-3  col-xs-12 paddingrl0">
                  <div class="thumbnail">
                    <div>Day1</div>
                    <div class="caption"  style="box-shadow:5px 5px 5px #E1E1E1;">
                      <textarea rows="5" placeholder="Enter Meal Description.." class="forminputs2"></textarea>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3  col-xs-12 paddingrl0">
                  <div class="thumbnail">
                    <div>Day2</div>
                    <div class="caption" style="box-shadow:5px 5px 5px #E1E1E1;">
                      <textarea rows="5" placeholder="Enter Meal Description.." class="forminputs2"></textarea>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3  col-xs-12 paddingrl0">
                  <div class="thumbnail">
                    <div>Day3</div>
                    <div class="caption" style="box-shadow:5px 5px 5px #E1E1E1;">
                      <textarea rows="5" placeholder="Enter Meal Description.." class="forminputs2"></textarea>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3  col-xs-12 paddingrl0">
                  <div class="thumbnail">
                    <div>Day4</div>
                    <div class="caption" style="box-shadow:5px 5px 5px #E1E1E1;">
                      <textarea rows="5" placeholder="Enter Meal Description.." class="forminputs2"></textarea>
                    </div>
                  </div>
                </div>
              </div>

              <div class="item">
                <div class="col-sm-3  col-xs-12 paddingrl0">
                  <div class="thumbnail">
                    <div>Day5</div>
                    <div class="caption" style="box-shadow:5px 5px 5px #E1E1E1;">
                      <textarea rows="5" placeholder="Enter Meal Description.." class="forminputs2"></textarea>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3  col-xs-12 paddingrl0">
                  <div class="thumbnail">
                    <div>Day6</div>
                    <div class="caption" style="box-shadow:5px 5px 5px #E1E1E1;">
                      <textarea rows="5" placeholder="Enter Meal Description.." class="forminputs2"></textarea>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3  col-xs-12 paddingrl0">
                  <div class="thumbnail">
                    <div>Day7</div>
                    <div class="caption" style="box-shadow:5px 5px 5px #E1E1E1;">
                      <textarea rows="5" placeholder="Enter Meal Description.." class="forminputs2"></textarea>
                    </div>
                  </div> 
                </div>
                <div class="col-sm-3  col-xs-12 paddingrl0">
                  <div class="thumbnail">
                    <div>Day8</div>
                    <div class="caption" style="box-shadow:5px 5px 5px #E1E1E1;">
                      <textarea rows="5" placeholder="Enter Meal Description.." class="forminputs2"></textarea>
                    </div>
                  </div> 
                </div>
              </div>
            </div>
            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
              <img src="images/circleLeft.png" style="width:2em;height:auto;">
            </a>
            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
              <img src="images/circleRight.png" style="width:2em;height:auto;">
            </a>
          </div>
        </div>
      </div>
      
    </form>
    <div id="ActionBar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="position: fixed;bottom: 0;background-color: #fff;text-align: center;margin-left: -5%;width:90%;">
        <button type="button" id="addmeal" class="btns formbuttons">ADD A MEAL</button>
        <button type="button" id="saveAndEdit" class="btns formbuttons">SAVE</button>
        <!--<button type="button" id="saveTemplate" class="btns formbuttons">SAVE THIS AS TEMPLATE</button>-->
      </div>
  </div>
</body>
<script type="text/javascript">
  $('#myCarousel').carousel({
    interval: false
  }); 
</script>
<script type="text/javascript">
$('#addmeal').click(function(){
  var selectmealhtml = '<select name="meal2"><option>select meal of the day</option><option>Just after getting up</option><option>Breakfast</option>';
      selectmealhtml += '<option>Morning Snack</option><option>Lunch</option><option>After Snack</option><option>Dinner</option><option>Just before sleeping</option>';
      selectmealhtml += '<option>At this specific time</option></select>';

    var carouselmealhtml = '<textarea rows="5" placeholder="Enter Meal Description.." class="forminputs2"></textarea>';
    $('.mealCol').append(selectmealhtml);
    $('.caption').append(carouselmealhtml);
      });
</script>
</html>