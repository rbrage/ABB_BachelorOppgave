<?php
$this->viewmodel->templatemenu = array("options" => "Options", "points" => "Clusterpoints");
$this->template("Shared");

?>

<section id="options">
	<div class="page-header">
		<h2>Options</h2>
	</div>
	
	<div class="alert hide"></div>
	
	<h4>Runtime variables</h4>
	<?php $settings = $this->viewmodel->settings; ?>
	<p>Number of clusters: <?php echo $settings->getSetting(CachedSettings::NUMBEROFCLUSTERS); ?></p>
	<p>Max points to use: <?php echo $settings->getSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS); ?></p>
	<p>Run analysis while submition: <?php echo (($settings->getSetting(CachedSettings::ANALYSECLUSTERSWHILESUBMITION))?"Yes":"No"); ?></p>
	<p>Random initial clusterpoints: <?php echo (($settings->getSetting(CachedSettings::RANDOMINITIALCLUSTERPOINTS))?"Yes":"No"); ?></p>
	
	<h4>Actions</h4>
	<button class="btn" id="runButton">Run analysis</button>
	<button class="btn" id="reasignButton">Reasign all points</button>
	<button class="btn" id="forceNewButton">Force new analysis</button>
	<button class="btn" id="clearButton">Clear clusterpoints</button>
	<script type="text/javascript">
		$(function(){
			var closeButton = "<button type=\"button\" class=\"close\" onclick=\"$('#options > .alert').fadeOut(800);\">&times;</button>";
			$("#runButton").click(function(){
				$("#options > .alert").removeClass("alert-success").html(closeButton + "New analysis is started. Don't refresh the browser!").fadeIn(800);
				$.getJSON("/cluster/run/json", function(data){
					$("#options > .alert").addClass("alert-success").html(closeButton + data.msg).fadeIn(800);
					updateClusterPoints();
				});
			});
			
			$("#forceNewButton").click(function(){
				if(confirm("This will remove the clusters that are present now, and a new calculation will start. Are you sure you want to do this?")){
					$("#options > .alert").removeClass("alert-success").html(closeButton + "New analysis is started. Don't refresh the browser!").fadeIn(800);
					$.getJSON("/cluster/force/json", function(data){
						$("#options > .alert").addClass("alert-success").html(closeButton + data.msg).fadeIn(800);
						updateClusterPoints();
					});
				}
			});
			
			$("#clearButton").click(function(){
				if(confirm("This will remove all clusterdata thats present. Are you sure you want to do this?")){
					$.getJSON("/cluster/reset/json", function(data){
						$("#options > .alert").addClass("alert-success").html(closeButton + data.msg).fadeIn(800);
						updateClusterPoints();
					});
				}
			});

			$("#reasignButton").click(function(){
				$.getJSON("/cluster/reasign/json", function(data){
						$("#options > .alert").addClass("alert-success").html(closeButton + data.msg).fadeIn(800);
						updateClusterPoints();
					});
			});

			function updateClusterPoints(){
				$.getJSON("/Cluster/Points/json", function(data){
					var tbody = $("#points > table > tbody");
					if(data.Cluster.Size != 0){
						tbody.html("");
					}
					else{
						tbody.html("<tr><td colspan=\"5\">There is no points to define the clusters. Run the analysis first.</td></tr>");
					}
					
					$.each(data.Cluster.Points, function(key, value){
						tbody.append(
								"<tr>" +
									"<td>" + value.clusterID + "</td>" + 
									"<td>" + value.x + "</td>" + 
									"<td>" + value.y + "</td>" + 
									"<td>" + value.z + "</td>" + 
									"<td>" + value.connections + "</td>" + 
								"</tr>");
					});
				});
			}
		});
	</script>
</section>
<section id="points">
	<div class="page-header">
		<h2>Clusterpoints</h2>
	</div>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>X</th>
					<th>Y</th>
					<th>Z</th>
					<th>Connections</th>
				</tr>
			</thead>
			<tbody>
			<?php 
			$list = $this->viewmodel->clusterlist;
			
			if($list->size() > 0){
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
			}
			else{
				echo "<tr><td colspan=\"5\">There is no points to define the clusters. Run the analysis first.</td></tr>";
			}
			?>
			</tbody>
		</table>		
</section>

