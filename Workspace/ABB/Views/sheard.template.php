<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link href="/scripts/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="/scripts/bootstrap.css" rel="stylesheet" media="screen">

<title>ABB Analyseprogram</title>
</head>
<body style="padding-top: 60px;">
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse"
					data-target=".nav-collapse"> <span class="icon-bar"></span> <span
					class="icon-bar"></span> <span class="icon-bar"></span>
				</a> <a class="brand" style="padding-top: 5px; padding-bottom: 5px;"
					href="#"><img src="/img/abbLogo.gif"> </a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li class="active"><a href="#">Home</a></li>
						<li><a href="#about">About</a></li>
						<li><a href="#contact">Contact</a></li>
					</ul>
				</div>
				<!--/.nav-collapse -->
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span2">
				<div class="span2" data-spy="affix">
					<ul class="nav nav-list">
						<li class="nav-header">Meny</li>
						<li class="active"><a href="/test/testing.php">Tabell</a></li>
						<li><a href="#">3D plot</a>
						</li>
					</ul>
				</div>
			</div>

			<div class="span10">
				<?php

				$this->ViewBody();

				?>
			</div>
		</div>
	</div>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="scripts/bootstrap.min.js"></script>

</body>
</html>
