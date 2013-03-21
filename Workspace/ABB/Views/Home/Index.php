<?php 
$this->viewmodel->templatemenu = array("last" => "Last 10 Points", "plot3d" => "3D Plot");

$this->Template("Shared");
?>

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
	<p>Can't find any points in the cache.</p>
	<?php 
			}
			?>
</section>

<section id="plot3d">
	<div class="page-header">
		<h2>3D plot</h2>
		
			<div id="3DPlotDiv" style="border:1px solid">
			
		</div>
		<button class="btn btn-small btn-primary" type="button" onclick="loadPoint()">Reload</button>
	</div>

</section>


<script type="text/javascript">

			var point3DPlot;
			var points;
			
				setUp();
			
			function setUp()
			{

			points = new Array();
			
			var container = document.getElementById("3DPlotDiv");
	      	var data = {width: container.offsetWidth-15, height: 600, axisSize: 350};
	      		
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


