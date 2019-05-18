<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>JS Test</title>
    <style><?php include 'mtguk_feedback.css'; ?> </style>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
		<!-- Bootstrap Date-Picker Plugin -->
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
	</head>
	<body>
		<div id="page">
  			<br />
  			<br />
  			<br />
  			<h2 align="center">MTGUK Feedback Application</h2><br />

				<p><a href="#" onClick="logInWithFacebook()">Log In</a></p>

  			<div class="form-group">
  				<div class="input-group">
  					<span class="input-group-addon">Search</span>
  					<input type="text" size="40" name="search_text" id="search_text" placeholder="Search Members" class="form-control" />
  				</div>
  			</div>
  			<br />

			<!-- Modal -->
			<div id="feedback_modal" class="modal fade" role="dialog">
			  <div class="modal-dialog" id="feedback_modal_dialog">

			    <!-- Modal content-->
			    <div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title" id="feedback_form_name">Add Feedback for x</h4>
							<h5 id="feedback_error_msg"></h2>
						</div>
						<div class="modal-body">
							  <div class="form-group">
										<select class="form-control" id="buy_or_sell_sel">
											<option value=bought_from>Bought from</option>
											<option value=sold_to>Sold to</option>
										</select>
								 </div>

								<div class="form-group">
							    <label>On:</label>
									<input class="form-control" id="date" name="date" placeholder="MM/DD/YYY" type="text"/>
							  </div>

							  <button id="feedback_submit" type="button" class="btn btn-default">Submit</button>
						</div>
			    </div>

			  </div>
			</div>

  			<div id="result"></div>
				<div id="results2"></div>

      <div style="clear:both"></div>
  		<br />

  		<br />
  		<br />
  		<br />
		</div>
	</body>
</html>

<script>
	addFeedback = function (id, name, my_id, my_name) {
		document.getElementById('feedback_submit').onclick = function() {
			var seller_id, seller_name, buyer_id, buyer_name, trade_date;
			trade_date = $('#date').val();
			var d1 = new Date(trade_date);
			var today = new Date();
			if(trade_date == '') {
				console.log("empty date");
				$('#feedback_error_msg').html("Error: Please enter a date");
				return;
			} else if(d1 > today) {
				$('#feedback_error_msg').html("Error: Date cannot be in the future");
				return;
			}
			if($('#buy_or_sell_sel').val() == 1) {
				seller_id = my_id;
				seller_name = my_name;
				buyer_id = id;
				buyer_name = name;
			} else {
				seller_id = id;
				seller_name = name;
				buyer_id = my_id;
				buyer_name = my_name;
			}
			$('#feedback_modal').modal('toggle');
			buy_or_sell = $('#buy_or_sell_sel').val();
			$.ajax({
				url:"add_feedback.php",
				method:"post",
				data:{trade_date:trade_date,
					id:id,
					name:name,
					my_id:my_id,
					my_name:my_name,
					buy_or_sell:buy_or_sell},
				success:function(data)
				{
					$('#results2').html(data);
				}
			});
		};
		$('#feedback_form_name').html("Add Feedback for " + name);
		$('#feedback_modal').modal('toggle');
		$('#feedback_error_msg').html("");
	};
</script>

<script>
  logInWithFacebook = function() {
    FB.login(function(response) {
      if (response.authResponse) {
        // Now you can redirect the user or do an AJAX request to
        // a PHP script that grabs the signed request from the cookie.
				displayApp();
      } else {
        alert('User cancelled login or did not fully authorize.');
      }
    }, {
			scope: 'groups_access_member_info'
		});
    return false;
  };

  window.fbAsyncInit = function() {
    FB.init({
      appId: '474355809662556',
      cookie: true,
      version: 'v3.0'
    });
  };

  (function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>

<script>

displayApp = function(){

	$(document).on('click','#user_table', function() {
		query = $(this).attr('fbid');
		element = $("[id='trade_results']").eq($(this).index()/2-1);
		$.ajax({
			url:"fetch_trades.php",
			method:"post",
			data:{query:query},
			success:function(data)
			{
				if(!$.trim(element.html())) {
					element.html(data);
				} else {
					element.html("");
				}
			}
		});
  });

	load_data();
	function load_data(query) {
		$.ajax({
			url:"fetch_users.php",
			method:"post",
			data:{query:query},
			success:function(data)
			{
				$('#result').html(data);
			}
		});
	}

	$('#search_text').keyup(function(){
		var search = $(this).val();
		if(search != '') {
			load_data(search);
		}
		else {
			load_data();
		}

	});
};
</script>

<script>
    $(document).ready(function(){
      var date_input=$('input[name="date"]'); //our date input has the name "date"
      var container=$('.modal-content');
      var options={
        format: 'mm/dd/yyyy',
        container: container,
        todayHighlight: true,
        autoclose: true,
      };
      date_input.datepicker(options);
    })
</script>
