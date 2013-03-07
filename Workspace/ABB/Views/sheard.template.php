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
<link href="/scripts/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="/scripts/bootstrap.css" rel="stylesheet" media="screen">
<script src="/scripts/jquery-1.9.0.js" type="text/javascript"></script>
<script src="/scripts/bootstrap.min.js" type="text/javascript"></script>
<script src="/scripts/SSESideInfo.js" type="text/javascript"></script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<!--[if IE]><script type="text/javascript" src="../scripts/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="/scripts/PlotWebGLCanvas.js"></script>
<script src="/scripts/three.min.js"></script>
<script src="/scripts/TrackballControls.js"></script>
<script src="/scripts/Detector.js"></script>
<


<title>ABB Analyseprogram</title>
</head>
<body style="padding-top: 60px;">
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
			<div class="span11 offset3">
				
				<div class="nav-collapse">
					<ul class="nav">
						<li><a class="brand" style="padding-top: 5px; padding-bottom: 5px; margin-left: 5px;"	href="#"><img src="/img/abbLogo.gif"> </a></li>
						<li><a href="/"><i class="icon-home"></i></a></li>
						<li><a href="/points/"><i class="icon-th-list"></i> All Points</a></li>
						<li><a href="/cluster/"><i class="icon-th-large"></i> Clusters</a></li>
						<li><a href="/stat/"><i class="icon-indent-left"></i> Statistics</a></li>
					</ul>
					<ul class="nav pull-right">
						<li><a href="/settings/"><i class="icon-wrench"></i> Settings</a></li>
					</ul>
				</div>
				<!--/.nav-collapse -->
			</div>
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<div class="row-fluid">

			<div class="span2">
				<div data-spy="affix">
					<ul class="nav nav-list">
						<li class="nav-header">Menu</li>
						<?php 
						if(is_array($this->viewmodel->templatemenu))
							foreach ($this->viewmodel->templatemenu as $section => $name)
								echo "<li><a href=\"#" . $section . "\">" . $name . "</a>";
						?>
					</ul>
				</div>
			</div>
			<div class="span7">
				<?php

				$this->ViewBody();

				?>
			</div>
			<div class="span2">
				<div data-spy="affix">
					<div class="alert alert-info">
						<p class="nav-header">Infomation</p>
						<table class="table table-condensed">
							<tbody>
								<tr>
									<td>Number of triggerpoints:</td>
									<td id="cachesize"><?php echo $this->viewmodel->listsize ?></td>
								</tr>
								<tr>
									<td>Used memory size:</td>
									<td id="memorysize"><?php echo $this->viewmodel->listmemory ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

