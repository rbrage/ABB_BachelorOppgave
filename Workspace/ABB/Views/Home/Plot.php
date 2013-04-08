<html lang="en">
<head>
<?php 
	$this->viewmodel->templatemenu = array("last" => "Last 10 Points", "plot3d" => "3D Plot");
	$list = $this->viewmodel->clusterlist;
	$cache = new Cache();
	$pointlist = new CachedArrayList();
	$clusterlist = new CachedArrayList(KMeans::CLUSTERLISTNAME);

	$cacheinfo = $cache->getCacheInfo();
?>

<title>ABB Analyseprogram</title>

<meta charset="utf-8">
<meta name="viewport"
	content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

<style type="text/css">
iframe.dealply-toast {
	right: -99999px !important;
}

iframe.dealply-toast.fastestext-revealed {
	right: 0px !important;
}
</style>
<style>
			body {
				color: #000;
				font-family:Monospace;
				font-size:13px;

				background-color: #000;
				margin: 0px;
				overflow: hidden;
			}

			#info {
				position: absolute;
				top: 40px;
				padding: 5px;
				
			}
			#stats { position: absolute; bottom: 0; left: 0; }
			#stats #fps { background: transparent !important }
			#stats #fps #fpsText { color: #aaa !important }
			#stats #fps #fpsGraph { display: none }
			#stats #ms { background: transparent !important }
			#stats #ms #msText { color: #aaa !important }
			#stats #ms #msGraph { display: none }
			
			#exit {
				position:absolute;
				right: 10px;
				top: 40px;
				padding: 5px;
				
			}
			.backgroundColor{
				background-color: rgba(250, 250, 250, 0.7);
				background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(242, 242, 242, 0.7));
			}
			
			.header{
				color:rgb(51, 51, 51);
				font-variant: small-caps;
				font-size:20px;
			}
			
			.text{
				color:rgb(51, 51, 51);
				font-variant: small-caps;
				font-size:13px;
			}
			
			a {

				color: #f00;
			}
			

</style>


<link href="/scripts/bootstrap.min.css" rel="stylesheet" media="screen">
<script src="/scripts/jquery-1.9.0.js" type="text/javascript"></script>
<script src="/scripts/bootstrap.min.js" type="text/javascript"></script>
<script src="/scripts/SSESideInfo.js" type="text/javascript"></script>

<style>
	body{
		line-height: 15px;
	}
	.accordion {
					margin-bottom: 0px;
			}
	.dropdown-menu{
					min-width: 20px;
				}

</style>

<script type="text/javascript" src="/scripts/PlotWebGLCanvas.js"></script>
<script type="text/javascript" src="/scripts/three.min.js"></script>
<script type="text/javascript" src="/scripts/three.js"></script>
<script type="text/javascript" src="/scripts/TrackballControls.js"></script>
<script type="text/javascript" src="/scripts/Detector.js"></script>
<script type="text/javascript" src="/scripts/stats.min.js"></script>

<script type="x-shader/x-vertex" id="vertexshader">
			attribute float size;
			attribute vec3 ca;

			varying vec3 vColor;

			void main() {

				vColor = ca;

				vec4 mvPosition = modelViewMatrix * vec4( position, 1.0 );

				//gl_PointSize = size;
				gl_PointSize = size * ( 300.0 / length( mvPosition.xyz ) );

				gl_Position = projectionMatrix * mvPosition;
				

			}

		</script>	
<script type="x-shader/x-fragment" id="fragmentshader">

			uniform vec3 color;
			uniform sampler2D texture;

			varying vec3 vColor;

			void main() {

				gl_FragColor = vec4(vColor, 1.0 );
				gl_FragColor = gl_FragColor * texture2D( texture, gl_PointCoord );

			}

		</script>


</head>
<body>

	<div id="3DPlotDiv">
	</div>
	
	<script>
	$(function(){
		$('#accordion1').on('show', function(){
							$('#icon1').removeClass('icon-chevron-right').addClass('icon-chevron-down');
							});	
						
		$('#accordion1').on('hide', function(){
							$('#icon1').removeClass('icon-chevron-down').addClass('icon-chevron-right');
							});	
						
		$('#accordion2').on('show', function(){
							$('#icon2').removeClass('icon-chevron-right').addClass('icon-chevron-down');
						});	
						
		$('#accordion2').on('hide', function(){
							$('#icon2').removeClass('icon-chevron-down').addClass('icon-chevron-right');
						});	
		$('#accordion3').on('show', function(){
							$('#icon3').removeClass('icon-chevron-right').addClass('icon-chevron-down');
						});	
						
		$('#accordion3').on('hide', function(){
							$('#icon3').removeClass('icon-chevron-down').addClass('icon-chevron-right');
						});	
					
			});
			
		
	</script>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li><a class="brand" style="padding-top: 5px; padding-bottom: 5px; margin-left: 5px;"	href="/"><img src="/img/ABB.png"> </a></li>
						<li><a href="/"><i class="icon-home"></i></a></li>
						<li><a href="/Home/Plot"><i class="icon-fullscreen"></i> 3D Plott</a></li>
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
	<div id="info">
		<div class="">
		
		<!-- Information collaps-->
			<div class="accordion" id="accordion1">
				<div class="accordion-group backgroundColor">
					<div class="accordion-heading">
						<p class="accordion-toggle header" data-toggle="collapse" data-parent="#accordion1" href="#collapseOne">Information 
						<i id="icon1" class="icon-chevron-down pull-right"></i>
						</p>
					</div>
					<div id="collapseOne" class="accordion-body collapse in">
						<div class="accordion-inner">
							<table class="table table-condensed text">
							<tbody>
								<tr>
									<td>Number of triggerpoints:</td>
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
								</tbody>
						</table>
						</div>
					</div>
				</div>
			</div>
		<!-- Selected point collaps -->
			<div class="accordion" id="accordion2">
				<div class="accordion-group backgroundColor">
					<div class="accordion-heading">
						<p class="accordion-toggle header" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
							Selected point 
							<i id="icon2" class="icon-chevron-right pull-right"></i>
						</p>
					</div>
					<div id="collapseTwo" class="accordion-body collapse">
						<div class="accordion-inner">
							<table class="table table-condensed text">
								<tbody>
									<tr>
										<td> X Y Z:</td><td id="coordinates"></td>
									</tr>
									<tr>
										<td> Time: </td>
										<td id="timestamp"></td>
									</tr>
									<tr>
										<td> Cluster ID: </td>
										<td id="clusterIDSelected"></td>
									</tr>
									<tr>
										<td> X:</td>
										<td id="clusterCoorXSelected"></td>
									</tr>
									<tr>
										<td> Y:</td>
										<td id="clusterCoorYSelected"></td>
									</tr>
									<tr>
										<td> Z:</td>
										<td id="clusterCoorZSelected"></td>
									</tr>
									<tr>
										<td> # of points in cluster:</td>
										<td id="clusterConectionSelected"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		<!-- Cluster collaps -->
			<div class="accordion" id="accordion3">
				<div class="accordion-group backgroundColor">
					<div class="accordion-heading">
						<p class="accordion-toggle header" data-toggle="collapse" data-parent="#accordion3" href="#collapseThree">
							Cluster 
							<i id="icon3" class="icon-chevron-right pull-right"></i>
						</p>
					</div>
					<div id="collapseThree" class="accordion-body collapse">
						<div class="accordion-inner">
							<table class="table table-condensed text">
								<tbody>
										<td> Cluster ID:
											<div class="btn-group">
												<a id="clusterid" class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#"> # <span class=" icon-chevron-down"></span> </a>
												<ul class="dropdown-menu">
													<?php for($i=0; $i<$list->size(); $i++){?>
														<li id="<?php echo $i ?>"onclick="setID(this.id)"><a href="#"><?php echo $i?></a></li>
													<?php }?>
												</ul>
											</div>
										</td>
										<td><i id="icon1" class="icon-eye-open" onclick="moveViewButton()"></i></td>
									</tr>
									<tr>
										<td> X:</td>
										<td id="clusterCoorX"></td>
									</tr>
									<tr>
										<td> Y:</td>
										<td id="clusterCoorY"></td>
									</tr>
									<tr>
										<td> Z:</td>
										<td id="clusterCoorZ"></td>
									</tr>
									<tr>
										<td> # of points in cluster:</td>
										<td id="clusterConection"></td>
									</tr>
								</tbody>
							</table>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="exit">
	<a class="close" href="/Home/">&times;</a>
	</div>

		
		<script type="text/javascript">
			var point3DPlot;
			var points;
			var cluster;
			var clusterPoints;
			var id;
			var stats;
			
				setUp();
			
			function setUp()
			{
			
			points = new Array();
			cluster = new Array();
			
			
			var container = document.getElementById("3DPlotDiv");
	      	var data = {width: window.innerWidth, height: window.innerHeight, axisSize: 350};
	      	
			loadCluster();	
	      	point3DPlot = new PlotWebGLCanvas(container, points, data, cluster);
			
			}
			
			
			$(function(){
				if(typeof(EventSource) !=="undefined"){
					if(ssesource != null){
						ssesource.addEventListener("pointsize", function (event){
							loadPoints(event.data);
						}, true);
					}
				}
			});
			
			$.getJSON("/Register/Size/json", function(data){
					var size = data.Register.Size;
					loadPoints(size);
				});
		
			
				function loadPoints(totalsize){
					$.getJSON("/Register/Points/json?start=" + points.length + "&stop=" + (points.length + 1000), function(data){
						start = data.Register.Start;
						$.each(data.Register.Points, function(key, value){
							points[start++] = new point(value.x, value.y, value.z, value.timestamp, value.cluster);
						});
						
						reload(points);
						if(totalsize > start){
							loadPoints();
							
						}
					});
				}
				
				function loadCluster(){
					$.getJSON("/Cluster/Points/json", function(data){
						start = 0;
						$.each(data.Cluster.Points, function(key, value){
							cluster[start++] = new point(value.x, value.y, value.z, null, value.connections);
						});
					});
				};
				
			function point(x, y, z, t, c){       
				return [x, y, z, t, c]; 
			};
				
			function moveViewButton(){
				console.log(this.id);
				var buttonId = this.id;
				moveView(cluster[buttonId][0],cluster[buttonId][1],cluster[buttonId][2]);
				
			};
			
			function setID(id){
				this.id = id;
				
				var clusterid="" + this.id + " <span class=\"caret\"></span>";
				document.getElementById("clusterid").innerHTML=clusterid;
				
				if(this.clusterList.length>0){
					var clusterX = this.clusterList[this.id][0];
					var clusterY = this.clusterList[this.id][1];
					var clusterZ = this.clusterList[this.id][2];
					var clusterConections = this.clusterList[this.id][4];
					
					var clustertext="" + clusterX;
					document.getElementById("clusterCoorX").innerHTML=clustertext;
					var clustertext="" + clusterY;
					document.getElementById("clusterCoorY").innerHTML=clustertext;
					var clustertext="" + clusterZ;
					document.getElementById("clusterCoorZ").innerHTML=clustertext;
					
					var clustertext="" +clusterConections;
					document.getElementById("clusterConection").innerHTML=clustertext;
		
				};
				
			};
			
			window.onresize = function(event) {
				reload();
			};
			
			
			
			
</script>
	

</body>
</html>