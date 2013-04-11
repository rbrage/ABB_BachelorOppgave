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

<section id="raport">
	<div class="page-header">
		<h2>Raport</h2> 
		<a id="createPDF" class="btn" href="/Home/CreatePDF" target="_blanck">Create PDF</a>
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



