
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style type="text/css">
        body {
            padding-top: 60px;
            padding-bottom: 40px;
        }
    </style>
<link href="scripts/bootstrap.css" rel="stylesheet" media="screen">
<link href="scripts/bootstrap.min.css" rel="stylesheet" media="screen">

<title>ABB Analyseprogram</title>
</head>
<body>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse"
					data-target=".nav-collapse"> <span class="icon-bar"></span> <span
					class="icon-bar"></span> <span class="icon-bar"></span>
				</a> <a class="brand" href="#">Project name</a>
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
	<div class="container">
		<div class="row">
			<h1>This is a test!!</h1>
			<table class="table table-striped">
				<thead>
					<tr>
						<th>X</th>
						<th>Y</th>
						<th>Z</th>
						<th>Time</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($viewmodel->arr as &$item){
						
						echo "<tr>
		<td>".$item->x."</td>
		<td>".$item->y."</td>
		<td>".$item->z."</td>
		<td>".$item->timestamp."</td>
		</tr>";
					}
					?>
				</tbody>
			</table>

		</div>

	</div>


	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="scripts/bootstrap.min.js"></script>
</body>
</html>
