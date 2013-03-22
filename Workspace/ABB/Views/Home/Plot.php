<html lang="en">
<head>
<?php 
$this->viewmodel->templatemenu = array("last" => "Last 10 Points", "plot3d" => "3D Plot");
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
				text-align:center;

				background-color: #000;
				margin: 0px;
				overflow: hidden;
			}

			#info {
				position: absolute;
				top: 0px; width: 100%;
				padding: 5px;
				
			}
			.backgroundColor{
				background: rgba(0, 0, 255, 0.2);
			}

			a {

				color: #f00;
			}

</style>

<link href="/scripts/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="/scripts/bootstrap.css" rel="stylesheet" media="screen">
<script src="/scripts/jquery-1.9.0.js" type="text/javascript"></script>
<script src="/scripts/bootstrap.min.js" type="text/javascript"></script>
<script src="/scripts/SSESideInfo.js" type="text/javascript"></script>

<script type="text/javascript" src="/scripts/PlotWebGLCanvas.js"></script>
<script type="text/javascript" src="/scripts/three.min.js"></script>
<script type="text/javascript" src="/scripts/three.js"></script>
<script type="text/javascript" src="/scripts/TrackballControls.js"></script>
<script type="text/javascript" src="/scripts/Detector.js"></script>

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
	
	<div id="info">
		<div class="span4">
			<div class="accordion" id="accordion2">
				<div class="accordion-group backgroundColor">
					<div class="accordion-heading">
						<p class="accordion-toggle nav-header" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
							Information
						</p>
					</div>
					<div id="collapseOne" class="accordion-body collapse in">
						<div class="accordion-inner">
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
								<tr>
									<td>Number of cluster:</td>
									<?php $settings = $this->viewmodel->settings; ?>
									<td><?php echo $settings->getSetting(CachedSettings::NUMBEROFCLUSTERS); ?></td>
								</tr>
								</tbody>
						</table>
						</div>
					</div>
				</div>
				<div class="accordion-group backgroundColor">
					<div class="accordion-heading">
						<p class="accordion-toggle nav-header" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
							Selected point
						</p>
					</div>
					<div id="collapseTwo" class="accordion-body collapse">
						<div class="accordion-inner">
							<table class="table table-condensed">
								<tbody>
									<tr>
										<td> X Y Z:</td><td id="coordinates"></td>
									</tr>
									<tr>
										<td> Time: </td>
										<td id="timestamp"></td>
									</tr>
									<tr>
										<td> Cluster: </td>
										<td id="cluster"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	

	
<script type="text/javascript">

			var point3DPlot;
			var points;
			
				setUp();
			
			function setUp()
			{

			points = new Array();
			
			var container = document.getElementById("3DPlotDiv");
	      	var data = {width: window.innerWidth, height: window.innerHeight, axisSize: 350};
	      		
	      	point3DPlot = new PlotWebGLCanvas(container, points, data);
			loadPoint();
			
		
			}
			
			function loadPoint(){
			
			$.getJSON("/Register/Points/json?start=0&stop=10000", function(data){
						start = data.Register.Start;
						$.each(data.Register.Points, function(key, value){
						points[start] = new point(value.x,value.y,value.z,value.timestamp,value.cluster);
							start++;
						});
				reload(points);
			});
			}
			
				
			function point(x, y, z, t, c){       
				return [x, y, z, t, c]; 
			};
				
			
			window.onresize = function(event) {
				reload();
			};
			
</script>
	

</body>
</html>