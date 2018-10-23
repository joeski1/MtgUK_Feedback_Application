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
	</head>
	<body>
		<div id="page">
  			<br />
  			<br />
  			<br />
  			<h2 align="center">MTGUK Feedback Application</h2><br />
  			<div class="form-group">
  				<div class="input-group">
  					<span class="input-group-addon">Search</span>
  					<input type="text" size="40" name="search_text" id="search_text" placeholder="Search Members" class="form-control" />
  				</div>
  			</div>
  			<br />

				<!--<div class="table-responsive">
						<table class="table bordered" id="users_table">
							<tr>
								<th>Picture</th>
								<th>Name</th>
								<th id="score_header" colspan="2">Score</th>
								<th></th>
							</tr>
							<tr>
								<td><img src="placeholder_profile.png" alt="" border=3 height=50 width=50></img></td>
								<td>Joe Faulls</td>
								<td id="score">10</td>
								<td>
									<table class="table" id="score_table">
										<tr><td id="score_buy">6</td></tr>
										<tr><td id="score_sell">4</td></tr>
									</table>
								</td>
								<td><img src="add_feedback_button.png" alt="Profile Picture" title="Vouch for Joe Faulls" height=20 width=20></img></td>
							</tr>
						</table>-->

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

$(document).ready(function(){

	$(document).on('click','#user_table', function() {
		query = $(this).attr('fbid');
		element = $("[id='trade_results']").eq($(this).index()-1);
		$.ajax({
			url:"fetch_trades.php",
			method:"post",
			data:{query:query},
			success:function(data)
			{
				console.log(data);
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
});
</script>
