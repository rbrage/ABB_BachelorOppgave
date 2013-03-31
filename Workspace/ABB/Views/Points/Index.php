<?php
$this->viewmodel->templatemenu = array("export" => "Export pointset", "points" => "Available Points");
$this->Template("Shared");
?>

<section id="export">
	<div class="page-header">
		<h2>Export pointset</h2>
	</div>
	<h4>Export to CSV file</h4>

	<div class="form-horizontal">
		<div class="control-group">
			<label class="control-label">Start point</label>
			<div class="controls">
				<input type="text" id="startpoint">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label">Stop point</label>
			<div class="controls">
				<input type="text" id="stoppoint">
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button class="btn" id="exportPointsButton">Export</button>
			</div>
		</div>
		
	</div>
	
	<script type="text/javascript">
		$(function(){
			$("#exportPointsButton").click(function(){
				var start = $("#startpoint").val();
				var stop = $("#stoppoint").val();

				$.getJSON("/Export/PointsToCSV/json?start=" + start + "&stop=" + stop, function(data){
					if(data.success){
						window.open(data.link, "_self");
					}
				});
			});
		});
	</script>
	
</section>
<section id="points">
		<div class="page-header">
			<h2>Available points</h2>
		</div>
		
		<table class="table table-striped" id="PointTable">
			<thead>
				<tr>
					<th>#</th>
					<th>X</th>
					<th>Y</th>
					<th>Z</th>
					<th>Cluster</th>
					<th>Time</th>
					<th>Additional Info</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				for($i = 0; $i < 50 && $i < $this->viewmodel->cachedarr->size(); $i++){
					$item = $this->viewmodel->cachedarr->get($i);
					echo "
				<tr id=". $i .">
				<td>".$i."</td>
				<td>".$item->x."</td>
				<td>".$item->y."</td>
				<td>".$item->z."</td>
				<td>".$item->cluster."</td>
				<td>".$item->timestamp."</td>";
					
					$moreinfo = $item->getAdditionalInfo();
					if(count($moreinfo)){
						echo "<td>";
						foreach($item->getAdditionalInfo() as $key => $value){
							echo "" . $key . ": " . $value . "<br>";
						}
						echo "</td>";
					}
					else{
						echo "<td></td>";
					}
					
				echo "</tr>";
				}
				?>
			</tbody>
		</table>
		<div class="span4 offset4">
			<button id="MoreResults" class="btn" data-loading-text="Loading...">Get more results</button>
			<script type="text/javascript">
				$(function(){
				var stop = <?php echo $i; ?>;
				var updatePointTable = function(){
					//$("#MoreResults").html("Loading");
					$.getJSON("/Register/Points/json?start=" + stop + "&stop=" + (stop + 50), function(data){
						start = data.Register.Start;
						$.each(data.Register.Points, function(key, value){
							$("#PointTable > tbody").append("<tr id=" + start + ">" + 
								"<td id=\"num\">" + start + "</td>" +
								"<td id=\"x\">" + value.x + "</td>" +
								"<td id=\"y\">" + value.y + "</td>" +
								"<td id=\"z\">" + value.z + "</td>" +
								"<td id=\"cluster\">" + value.cluster + "</td>" +
								"<td id=\"time\">" + value.timestamp + "</td>" +
								"<td id=\"additionalinfo\"></td></tr>");
								$.each(value.additionalinfo, function(key, value){
									$("#" + start + " > #additionalinfo").append(key + ": " + value + "<br>");
								});
							start++;
						});
						stop = start;
						$("#MoreResults").html("Get more results");
					}).error(function(data){
						$("#MoreResults").html("Error");
					});
				};
				
				$("#MoreResults").click(updatePointTable);

				$(window).scroll(function(){
					if($(window).scrollTop() + $(window).height() == $(document).height()) {
						updatePointTable();
					}
				});
				});
			</script>
		</div>
	</section>