// JavaScript Document FOR AJAX COUNTRY STATE CITY CODES
$(document).ready(function(){
			//alert(123)
			$("#country").on("change",function(){
			//alert(123)
			var country=$("#country").val();
			//alert(center)
			$.ajax({
				url:'ajax_country_state_city.php',
				data:{country:country,type:"get_states"},
				type: 'post',
				success : function(resp){
				//alert(resp)
					$("#state").html(resp);
					$("#city").html("<option value='-1'>SELECT CITY</option>");              
				},
				error : function(resp){}
			});
		});

			$("#state").on("change",function(){
			//alert(123)
			var state=$("#state").val();
			//alert(state)
			$.ajax({
				url:'ajax_country_state_city.php',
				data:{state:state,type:"get_cities"},
				type: 'post',
				success : function(resp){
				//alert(resp)
					$("#city").html(resp);               
				},
				error : function(resp){}
			});
		});
		});

