<?php 
$this->viewmodel->templatemenu = array("execute" => "Run triggering", "plot3d" => "3D Plot", "last" => "Last 10 Points");

$this->Template("Home");
?>

<section id="execute">
	<div class="page-header">
		<h2>Run triggering</h2>
	</div>
	<div class="alert hide"></div>
	<button id="triggerprogramButton" class="btn">Run Triggerprogram</button>
	<script type="text/javascript">
		$(function(){
			$("#triggerprogramButton").click(function(){
				$.getJSON("/Execute/RunTriggeringProgram/json", function(data){
					$("#execute > .alert").html(data.msg).fadeIn(800);
				}).error(function(){
					$("#execute > .alert").html("An error occured.").fadeIn(800);
				});
			});
		});
	</script>
	
	<button id="masterpointButton" class="btn">Retrive masterpoint</button>
	<script type="text/javascript">
		$(function(){
			$("#masterpointButton").click(function(){
				$.getJSON("/Execute/RunMasterpointTriggering/json", function(data){
					$("#execute > .alert").html(data.msg).fadeIn(800);
				}).error(function(){
					$("#execute > .alert").html("An error occured.").fadeIn(800);
				});
			});
		});
	</script>
</section>

<section id="plot3d">
	<div class="page-header">
	
		<h2>3D plot
		
			<button class="btn btn-primary pull-right" type="button" href="/Home/Plot">Full Screan <i class="icon-fullscreen"></i></button> 
			
			<button class="btn btn-success pull-right" type="button" onclick="loadPoint()">Reload <i class="icon-refresh"></i></button>
		
		</h2> 
		
			<div id="3DPlotDiv" style="border:1px solid; background-color:#F2F2F2">
			<a href="/Home/Plot"><i class="icon-fullscreen pull-right"></i></a>
			
		</div>
		
	</div>

</section>

<section id="last">
	<div class="page-header">
		<h2>Last 10 points</h2>
	</div>
	<?php 
	$list = $this->viewmodel->arr->getCachedArrayList();
	$size = $list->size();

	if($size != 0){
			?>
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
			<?php 
			for ($i = $size -1; ($size - $i) <= 10 && $i >= 0 ;$i--){
						echo "
		<tr>
		<td>".$list->get($i)->x."</td>
		<td>".$list->get($i)->y."</td>
		<td>".$list->get($i)->z."</td>
			<td>".$list->get($i)->timestamp." ms</td>
			</tr>";
					}
					?>
		</tbody>
	</table>
	<?php 
			}
			else{
			?>
	<p id="empty">Can't find any points in the cache.</p>
	<?php 
			}
			?>
</section>

<script type="text/javascript">

			var point3DPlot;
			var points;
			$(function(){
				
				setUp();
			
				function setUp()
				{	

				points = new Array();
			
				var container = document.getElementById("3DPlotDiv");
		      	var data = {width: container.offsetWidth-15, height: 600, axisSize: 350};
	      		
		      	point3DPlot = new PlotWebGLCanvas(container, points, data);
				loadPoint();
			
		
				};
			
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
			});
</script>


