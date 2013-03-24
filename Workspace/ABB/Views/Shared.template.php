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
<link href="/scripts/bootstrap.css" rel="stylesheet" media="screen">
<script src="/scripts/jquery-1.9.0.js" type="text/javascript"></script>
<script src="/scripts/bootstrap.js" type="text/javascript"></script>
<script src="/scripts/SSESideInfo.js" type="text/javascript"></script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<title>ABB Analyseprogram</title>

</head>
<body style="padding-top: 60px;">
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li><a class="brand" style="padding-top: 5px; padding-bottom: 5px; margin-left: 5px;"	href="#"><img src="/img/ABB.png"> </a></li>
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
			<div class="span8">
				<?php

				$this->ViewBody();

				?>
			</div>
			<div class="span2" style="magin-left: 10px;">
				<div data-spy="affix">
					<div class="alert alert-info">
						<p class="nav-header">Information</p>
						<?php 

						$cache = new Cache();
						$pointlist = new CachedArrayList();
						$clusterlist = new CachedArrayList(KMeans::CLUSTERLISTNAME);
						
						$cacheinfo = $cache->getCacheInfo();
						
						?>
						<table class="table table-condensed">
							<tbody>
								<tr>
									<td>Number of triggerpoints:</td>
									<td id="pointsize"><?php echo $pointlist->size(); ?></td>
								</tr>
								<tr>
									<td>Used memory:</td>
									<td id="usedsize"><?php echo $cacheinfo["mem_size"]/1000 . "k"; ?></td>
								</tr>
								<tr>
									<td>Available memory:</td>
									<td id="availablesize"><?php echo ini_get("apc.shm_size") * 1000 . "k"; ?></td>
								</tr>
								<tr>
									<td>Number of cluster:</td>
									<td id="clustersize"><?php echo $clusterlist->size(); ?></td>
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

