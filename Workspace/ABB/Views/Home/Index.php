<?php 
$this->viewmodel->templatemenu = array("execute" => "Run triggering", "report" => "Report", "last" => "Last 10 Points");

$this->Template("Home");
?>

<section id="execute">
<br>
	<div class="page-header">
		<h2>Run triggering</h2>
	</div>
	<div class="alert hide"></div>
	<button id="triggerprogramButton" class="btn">Run Triggerprogram</button>
	<script type="text/javascript">
		$(function(){
			$("#triggerprogramButton").click(function(){
				$.getJSON("/Execute/RunTriggeringProgram/json", function(data){
					$("#execute > .alert").html(data.Request.Message).fadeIn(800);
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
				if(confirm("Is the sensor in place to make a master point?")){
					$.getJSON("/Execute/RunMasterpointTriggering/json", function(data){
						$("#execute > .alert").html(data.Request.Message).fadeIn(800);
					}).error(function(){
						$("#execute > .alert").html("An error occured.").fadeIn(800);
					});
				}
			});
		});
	</script>
</section>

<section id="report">
<br>
	<div class="page-header">
		<h2>Report</h2> 
	</div>
	<a id="createPDF" class="btn" href="/stat/CreatePDF" target="_blank">Create PDF</a>

</section>

<section id="last">
<br>
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



