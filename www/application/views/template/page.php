<html>
	<head>
	  <meta charset="utf-8">
	  <meta http-equiv="X-UA-Compatible" content="IE=edge">
	  <meta name="viewport" content="width=device-width, initial-scale=1">
	  <title></title>
	  <!-- Latest compiled and minified CSS -->
	  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

	  <!-- Optional theme -->
	  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

	  <!-- Font awesome -->
	  <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">


	  <!-- Latest compiled and minified JavaScript -->
	  <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

	  <!-- default styles -->
	  <link rel="stylesheet" href="/assets/css/style.css">

	  <script src="/assets/scripts/parsley.min.js"></script>
	  <script src="/assets/scripts/global.js"></script>

	  <?php

	  	if((isset($scripts)) && (sizeof($scripts) > 0 )){
		  foreach($scripts as $script){
			echo '<script src="/assets/scripts/'.$script.'"></script>';
		  }
		}
	  ?>
	</head>
	<body>
	<?php
		if((isset($nav_options)) && (sizeof($nav_options) > 0)){
	?>
	  <nav class="navbar navbar-default" role="navigation">
		<div class="container-fluid">

		  <div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			  <span class="sr-only">Toggle navigation</span>
			  <span class="icon-bar"></span>
			  <span class="icon-bar"></span>
			  <span class="icon-bar"></span>
			</button>
		  </div>

		  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
			  <?php
			  	foreach($nav_options as $option){
					echo '<li><a href="'.$option['url'].'">'.$option['text'].'</a></li>';
				}
			  ?>
			</ul>
		  </div>
		</div>
	  </nav>
	<?php
		}//if nav_bar
	?>
		<div class="main-container space_medium top bottom container">
		  <div class="row">
			<div class="col-sm-12">
			  <?php echo $main_content; ?>
			</div>
		  </div>
		</div>

		<footer>
		  <div class="container">
			<div class="">
			  <div class="col-sm-12 text-right">
					<a class="btn btn-default" href="mailto:info@desmondliang.com?subject=My thoughts about the check in app."><i class="fa fa-envelope-o"></i> FEEDBACK</a>
			  </div>
			</div>
		  </div>
		</footer>
	</body>
</html>