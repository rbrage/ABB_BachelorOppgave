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
		<div id="3DPlotDiv" style="float: left; width: 900px; height: 600px; background:#edebeb;">
			
		</div>
	</div>

</section>


<script type="text/javascript">

			var point3DPlot;

			setUp();
			
			function setUp()
			{

			var points = new Array();
			<?php 
	      			$list = $this->viewmodel->arr->getCachedArrayList();
	      			$size = $list->size();
	      			for ($i = 0; $i<=$size-1 ;$i++){
	      				?>points[<?php echo $i ?>] = new point(<?php echo $list->get($i)->x ?>,<?php echo $list->get($i)->y?>,<?php echo $list->get($i)->z?>,<?php echo $list->get($i)->timestamp?>);
	      				<?php 
	      			}
	      			?>

	      		var data = {width: 900, height: 600, axisSize: 350};
	      		var container = document.getElementById("3DPlotDiv");
	      		
	      		point3DPlot = new PlotWebGLCanvas(document.getElementById("3DPlotDiv"), points, data);
				


			};
			
				
				function point(x, y, z, t)
			      
			      {       
			        return [x, y, z, t]; 
			    };
				

			</script>


