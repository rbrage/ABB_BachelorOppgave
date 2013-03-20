<?php
$this->viewmodel->templatemenu = array("points" => "Available Points");
$this->Template("Shared");
?>

	<section id="points">
		<div class="page-header">
			<h2>Available points</h2>
		</div>
		
		<table class="table table-striped" id="PointTable">
			<thead>
				<tr>
					<th>Number</th>
					<th>X</th>
					<th>Y</th>
					<th>Z</th>
					<th>Cluster</th>
					<th>Time</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				for($i = 0; $i < 50 && $i < $this->viewmodel->cachedarr->size(); $i++){
					$item = $this->viewmodel->cachedarr->get($i);
					echo "
				<tr>
				<td>".$i."</td>
				<td>".$item->x."</td>
				<td>".$item->y."</td>
				<td>".$item->z."</td>
				<td>".$item->cluster."</td>
				<td>".$item->timestamp."</td>
				</tr>";
				}
				?>
			</tbody>
		</table>
		<div class="span4 offset4">
			<button id="MoreResults" class="btn">Get more results</button>
			<script type="text/javascript">
				stop = <?php echo $i; ?>;
				$("#MoreResults").click(function(){
					$("#MoreResults").html("Loading");
					$.getJSON("/Register/Points/json?start=" + stop + "&stop=" + (stop + 50), function(data){
						start = data.Register.Start;
						$.each(data.Register.Points, function(key, value){
							$("#PointTable").find("tbody").append("<tr>" + 
								"<td>" + start + "</td>" +
								"<td>" + value.x + "</td>" +
								"<td>" + value.y + "</td>" +
								"<td>" + value.z + "</td>" +
								"<td>" + value.cluster + "</td>" +
								"<td>" + value.timestamp + "</td>" +
							"</tr>");
							start++;
						});
						stop = start;
						$("#MoreResults").html("Get more results");
					}).error(function(data){
						$("#MoreResults").html("Error");
					});
				});
			</script>
		</div>
	</section>