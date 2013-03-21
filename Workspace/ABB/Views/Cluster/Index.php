<?php
$this->viewmodel->templatemenu = array("options" => "Options", "points" => "Clusterpoints");
$this->template("Shared");

?>

<section id="options">
	<div class="page-header">
		<h2>Options</h2>
	</div>
	
	<div class="alert hide">
  <button type="button" class="close" data-dismiss="alert">&times;</button></div>
	
	<h4>Runtime variables</h4>
	<?php $settings = $this->viewmodel->settings; ?>
	<p>Number of clusters: <?php echo $settings->getSetting(CachedSettings::NUMBEROFCLUSTERS); ?></p>
	<p>Max points to use: <?php echo $settings->getSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS); ?></p>
	<p>Run analysis while submition: <?php echo (($settings->getSetting(CachedSettings::ANALYSECLUSTERSWHILESUBMITION))?"Yes":"No"); ?></p>
	
	<h4>Actions</h4>
	<button class="btn" id="runButton">Run analysis</button>
	<button class="btn" id="reasignButton">Reasign all points</button>
	<button class="btn" id="forceNewButton">Force new analysis</button>
	<button class="btn" id="clearButton">Clear clusterpoints</button>
	<script type="text/javascript">
		$(function(){
			$("#runButton").click(function(){
				$("#options > .alert").html("New analysis is started. Don't refresh the browser!").fadeIn(800);
				$.getJSON("/cluster/run/json", function(data){
					$("#options > .alert").text(data.msg);
				});
			});
			
			$("#forceNewButton").click(function(){
				if(confirm("This will remove the clusters that are present now, and a new calculation will start. Are you sure you want to do this?")){
					$.getJSON("/cluster/force/json", function(data){
						$("#options > .alert").text(data.msg).fadeIn(800);
					});
				}
			});
			
			$("#clearButton").click(function(){
				if(confirm("This will remove all clusterdata thats present. Are you sure you want to do this?")){
					$.getJSON("/cluster/reset/json", function(data){
						$("#options > .alert").text(data.msg).fadeIn(800);
					});
				}
			});

			$("#reasignButton").click(function(){
				$.getJSON("/cluster/reasign/json", function(data){
						$("#options > .alert").text(data.msg).fadeIn(800);
					});
			});
		});
	</script>
</section>
<section id="points">
	<div class="page-header">
		<h2>Clusterpoints</h2>
	</div>
	
		<?php 
		$list = $this->viewmodel->clusterlist;
		
		if($list->size() > 0){ 
			
		?>
		<?php 
			for($i = 0; $i < $list->size(); $i++){
			$point = $list->get($i);
		?>
	<h4>
		Clusterpoint
		<?php echo $i;?>
	</h4>
	<table class="table table-striped">
		<tbody>
			<tr>
				<td>X</td>
				<td><?php echo round($point->x, 3); ?></td>
			</tr>
			<tr>
				<td>Y</td>
				<td><?php echo round($point->y, 3); ?></td>
			</tr>
			<tr>
				<td>Z</td>
				<td><?php echo round($point->z, 3); ?></td>
			</tr>
			<tr>
				<td>Connections</td>
				<td><?php echo $point->getAdditionalInfo(KMeans::CLUSTERCOUNTNAME); ?>
				</td>
			</tr>
		</tbody>
	</table>
	<?php 
				}
			?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Number</th>
					<th>X</th>
					<th>Y</th>
					<th>Z</th>
					<th>Connections</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				for($i = 0; $i < $list->size(); $i++){
					$point = $list->get($i);
					
					echo "
				<tr>
					<td>".$i."</td>
					<td>".round($point->x, 3)."</td>
					<td>".round($point->y, 3)."</td>
					<td>".round($point->z, 3)."</td>
					<td>".$point->getAdditionalInfo(KMeans::CLUSTERCOUNTNAME)."</td>
				</tr>";
				}
			?>
			</tbody>
		</table>

		<?php 
		}
		else{ 
		?>
		
		<p>There is no points to define the clusters. Run the analysis first.</p>
		
		<?php 	
		}
		?>
		
</section>

