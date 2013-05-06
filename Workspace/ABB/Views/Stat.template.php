<?php 
require_once 'Models/Cache.php';
require_once 'Models/CachedArrayList.php';
require_once 'Models/KMeans.php';
?>
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
<link href="/scripts/jquery.jqplot.min.css" rel="stylesheet" media="screen">
<script src="/scripts/jquery-1.9.0.js" type="text/javascript"></script>
<script src="/scripts/bootstrap.min.js" type="text/javascript"></script>
<script src="/scripts/SSESideInfo.js" type="text/javascript"></script>


<script src="/scripts/jquery.jqplot.min.js" type="text/javascript"></script>
<script src="/scripts/jqplot.barRenderer.min.js" type="text/javascript"></script>
<script src="/scripts/jqplot.categoryAxisRenderer.min.js" type="text/javascript"></script>
<script src="/scripts/jqplot.BezierCurveRenderer.min.js" type="text/javascript"></script>
<script src="/scripts/jqplot.canvasAxisTickRenderer.min.js" type="text/javascript"></script>
<script src="/scripts/jqplot.canvasTextRenderer.min.js" type="text/javascript"></script>
<script src="/scripts/jqplot.highlighter.min.js" type="text/javascript"></script>
<script src="/scripts/jqplot.enhancedLegendRenderer.min.js" type="text/javascript"></script>
<script src="/scripts/jqplot.pointLabels.min.js" type="text/javascript"></script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<title>ABB Analyseprogram</title>

</head>
<body data-spy="scroll" data-target=".bs-docs-sidebar" data-twttr-rendered="true">
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li><a class="brand" style="padding-top: 5px; padding-bottom: 5px; margin-left: 5px;" href="/"><img src="/img/ABB.png"> </a></li>
						<li><a href="/"><i class="icon-home"></i></a></li>
						<li><a href="/Home/Plot"><i class="icon-fullscreen"></i> 3D Plot</a></li>
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
	<div class="container-fluid">
		<div class="row-fluid">

			<div class="span2 bs-docs-sidebar">
					<ul class="nav nav-list bs-docs-sidenav affix">
						<li class="nav-header">Headline</li>
						<?php 
						if(is_array($this->viewmodel->templatemenu))
							foreach ($this->viewmodel->templatemenu as $section => $name)
								echo "<li><a href=\"#" . $section . "\">" . $name . "</a>";
						?>
					</ul>
			</div>
			<div class="span8">
				<?php

				$this->ViewBody();

				?>
			</div>
			<div class="span2">
				<div data-spy="affix" style="padding-right: 10px;">
					<div class="alert alert-info">
						<p class="nav-header">
							Information 
							<button class="btn btn-mini" id="LiveUpdateButton">Live update<i class="icon-pause" id="LiveUpdateIcon"></i></button>
						</p>
						<?php 

						$cache = new Cache();
						$pointlist = new CachedArrayList();
						$clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
						$masterlist = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
						$outlierlist = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);
						
						$cacheinfo = $cache->getCacheInfo();
						
						?>
						<table class="table table-condensed">
							<tbody>
								<tr>
									<td>Number of trigger points:</td>
									<td id="pointsize"><?php echo $pointlist->size(); ?></td>
								</tr>
								<tr>
									<td>Used memory:</td>
									<td id="usedmemory"><?php echo $cacheinfo["mem_size"]/1000 . "k"; ?></td>
								</tr>
								<tr>
									<td>Available memory:</td>
									<td id="availablememory"><?php echo ini_get("apc.shm_size") * 1000 . "k"; ?></td>
								</tr>
								<tr>
									<td>Number of cluster:</td>
									<td id="clustersize"><?php echo $clusterlist->size(); ?></td>
								</tr>
								<tr>
									<td>Number of master points:</td>
									<td id="mastersize"><?php echo $masterlist->size(); ?></td>
								</tr>
								<tr>
									<td>Number of outliers:</td>
									<td id="outliersize"><?php echo $outlierlist->size(); ?></td>
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

