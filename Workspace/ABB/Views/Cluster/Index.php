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
				$("#options > .alert").removeClass("alert-success").removeClass("alert-error").html(closeButton + "New analysis is started. Don't refresh the browser!").fadeIn(800);
				$.getJSON("/cluster/RunAnalysis/json", function(data){
					$("#options > .alert").addClass("alert-success").html(closeButton + data.Request.Message).fadeIn(800);
					updateClusterPoints();
				}).error(function(){
					$("#options > .alert").addClass("alert-error").html(closeButton + "Ops! An error occured.").fadeIn(800);
				});
			});
			
			$("#forceNewButton").click(function(){
				if(confirm("This will remove the clusters that are present now, and a new calculation will start. Are you sure you want to do this?")){
					$("#options > .alert").removeClass("alert-success").removeClass("alert-error").html(closeButton + "New analysis is started. Don't refresh the browser!").fadeIn(800);
					$.getJSON("/cluster/ForceAnalysis/json", function(data){
						$("#options > .alert").addClass("alert-success").html(closeButton + data.Request.Message).fadeIn(800);
						updateClusterPoints();
					}).error(function(){
						$("#options > .alert").addClass("alert-error").html(closeButton + "<b>Oh no!</b> <br> The dragon that usually live in abandoned caves in the outermost corners on the internett, " + 
								"showed himself today. It is now believed that the area has been marked as his habitat. Even though that seems bad enough, it now seems like something " +
								"more terrible has happend! Your good friend, sir request, that went out, safe at heart and confident, on a trip to perform the adventure you had given him, never " +
								"arrived at his destination.<br><br> The only one that made it today was a old, disturbed man that had a incredible, but terrifying, tale to tell. He told a story " +
								"about how he was walking down a path to do a very important task for his master, but can't remember what his quest was.<br><br> For a while he had felt like something was keeping a eye on him, and " + 
								"sometimes he thought he saw something flew by and dimmed the sun a bit. He told himself that it was just his imagination that played with him and this is a " + 
								"peacefull land. He was walking for a little while more, and then he was terrified by a incredible loud and sharp screaming behind him. At that time, he told, " + 
								"everything went extremely bright and then just all dark. He woke up, a small walk from his destination, and managed to crawl rest of the way.<br><br> It seems like the " + 
								"once peacefull and beautiful land is now not longer so. Your adventure you wanted done, and should be a easy one, never did get done. You migth need a mighty and fearless " + 
								"warrior to complete your adventure!").fadeIn(800);
					});
				}
			});
			
			$("#clearButton").click(function(){
				if(confirm("This will remove all clusterdata thats present. Are you sure you want to do this?")){
					$.getJSON("/cluster/clear/json", function(data){
						$("#options > .alert").removeClass("alert-error").addClass("alert-success").html(closeButton + data.Request.Message).fadeIn(800);
						updateClusterPoints();
					}).error(function(){
						$("#options > .alert").removeClass("alert-success").addClass("alert-error").html(closeButton + "Ops! An error occured.").fadeIn(800);
					});
				}
			});

			$("#reasignButton").click(function(){
				$("#options > .alert").removeClass("alert-success").removeClass("alert-error").html(closeButton + "A reasignment of all points have started. Don't refresh the browser!").fadeIn(800);
				$.getJSON("/cluster/reasignpoints/json", function(data){
						$("#options > .alert").removeClass("alert-error").addClass("alert-success").html(closeButton + data.Request.Message).fadeIn(800);
						updateClusterPoints();
					}).error(function(){
						$("#options > .alert").removeClass("alert-success").addClass("alert-error").html(closeButton + "Ops! An error occured.").fadeIn(800);
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
					<td>".round($point->x, 2)."</td>
					<td>".round($point->y, 2)."</td>
					<td>".round($point->z, 2)."</td>
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

