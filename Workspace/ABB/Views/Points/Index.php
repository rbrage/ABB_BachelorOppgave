<?php
$this->viewmodel->templatemenu = array("export" => "Export pointset", "masterpoints" => "Master points", "markedpoints" => "Outlying points", "points" => "Available Points");
$this->Template("Shared");
?>

<section id="export">
	<br>
	<div class="page-header">
		<h2>Export pointset</h2>
	</div>
	
	<div class="alert hide"></div>
	
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
			<label class="control-label">Dataset</label>
			<div class="controls">
				<select name="dataset" id="dataset">
					<option value="<?php echo CachedArrayList::ARRAYLISTPREFIX; ?>">Regular points</option>
					<option value="<?php echo ListNames::CLUSTERLISTNAME; ?>">Cluster points</option>
					<option value="<?php echo ListNames::MASTERPOINTLISTNAME; ?>">Master points</option>
					<option value="<?php echo ListNames::OUTLYINGPOINTLISTNAME; ?>">Outlying points</option>
				</select>
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
			var closeButton = "<button type=\"button\" class=\"close\" onclick=\"$('#export > .alert').fadeOut(800);\">&times;</button>";
			$("#exportPointsButton").click(function(){
				var start = $("#startpoint").val();
				var stop = $("#stoppoint").val();
				var dataset = $("#dataset").val();

				$.getJSON("/Export/PointsToCSV/json?start=" + start + "&stop=" + stop + "&dataset=" + dataset, function(data){
					if(data.Request.Success){
						$("#export > .alert").removeClass("alert-error").addClass("alert-success").html(closeButton + "File successfully created. The download will start automatic. If the download don't start click <a href=\"" + data.link + "\">here</a>.").fadeIn(800);
						window.open(data.Request.Link, "_self");
						}
					else{
						$("#export > .alert").removeClass("alert-success").addClass("alert-error").html(closeButton + "An error occured. Message: " + data.Request.Message).fadeIn(800);
					}
				}).error(function(){
					$("#export > .alert").removeClass("alert-success").addClass("alert-error").html(closeButton + "An error occured. The response form the server was not valid.").fadeIn(800);
				});;
			});
		});
	</script>
	
</section>

<section id="masterpoints">
	<br>
	<div class="page-header">
		<h2>Master points</h2>
	</div>
	
	<div class="alert hide"></div>
	<div>
	<button id="AssignMasterpointsToClustersButton" class="btn">Assign master points To Clusters</button>
	<button id="ClearMasterpointsButton" class="btn">Clear master points</button>
	
	<script type="text/javascript">

		$(function(){

			$("#AssignMasterpointsToClustersButton").click(function(){
				$.getJSON("/master/AsignToCluster/json", function(data){
					$("#masterpoints > .alert").html(data.Request.Message).fadeIn(800);
					updatemasterpoints();
				});
			});
			
			$("#ClearMasterpointsButton").click(function(){
				if(confirm("After using the clear option you will not be able to get any points back. Are you sure you want to clear all master points?")){
					$.getJSON("/master/clear/json", function(data){
						$("#masterpoints > .alert").html(data.Request.Message).fadeIn(800);
						$("#MasterPointTable > tbody").html("<tr><td colspan=\"7\">There is no points to show you.</td></tr>");
					});
				}
			});

			$("i.close").click(function(){
				pointid = $(this).parent().parent().find("#num").text();
				$.getJSON("/master/remove/json?pointid=" + pointid, function(data){
					$("#masterpoints > .alert").html(data.Request.Message).fadeIn(800);
					updatemasterpoints();
				});
			});

			updatemasterpoints = function(){
				$.getJSON("/master/points/json", function(data){
					$("#MasterPointTable > tbody").empty();
					start = 0;
					$.each(data.Master.Points, function(key, value){
						$("#MasterPointTable > tbody").append("<tr id=" + start + ">" + 
								"<td id=\"num\">" + start + "</td>" +
								"<td style=\"text-align: right;\" id=\"x\">" + value.x + "</td>" +
								"<td style=\"text-align: right;\" id=\"y\">" + value.y + "</td>" +
								"<td style=\"text-align: right;\" id=\"z\">" + value.z + "</td>" +
								"<td id=\"cluster\">" + value.cluster + "</td>" +
								"<td id=\"time\">" + value.timestamp + "</td>" +
								"<td id=\"additionalinfo\"></td></tr>");
						$.each(value.additionalinfo, function(key, value){
							$("#MasterPointTable > tbody > #" + start + " > #additionalinfo").append(key + ": " + value + "<br>");
						});
						start++;
					});
				});
			}
			
			$("#GetMasterPointButton").click(updatemasterpoints);
			
		});

	</script>
	</div>
	<br>
	<table class="table table-striped" id="MasterPointTable">
			<thead>
				<tr>
					<th>#</th>
					<th style="text-align: right;">X</th>
					<th style="text-align: right;">Y</th>
					<th style="text-align: right;">Z</th>
					<th>Cluster</th>
					<th>Time</th>
					<th>Additional Info</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if(!$this->viewmodel->masterlist->isEmpty()){
					for($i = 0; $i < 50 && $i < $this->viewmodel->masterlist->size(); $i++){
						$item = $this->viewmodel->masterlist->get($i);
						echo "
				<tr id=". $i .">
				<td id=\"num\">".$i."</td>
				<td style=\"text-align: right;\" id=\"x\">".number_format(floatval($item->x), 2, ".", "")."</td>
				<td style=\"text-align: right;\" id=\"y\">".number_format(floatval($item->y), 2, ".", "")."</td>
				<td style=\"text-align: right;\" id=\"z\">".number_format(floatval($item->z), 2, ".", "")."</td>
				<td id=\"cluster\">".$item->cluster."</td>
				<td id=\"time\">".$item->timestamp."</td>";
					
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
						
					echo "<td><i class=\"close\">&times;</i></td></tr>";
					}
				}
				else{
					$i = 0;
					echo "<tr><td colspan=\"7\">There is no points to show you.</td></tr>";
				}
				?>
			</tbody>
		</table>
		<div class="span4 offset4">
			<button id="GetMasterPointButton" class="btn" data-loading-text="Loading...">Update master points</button>
		</div>
</section>



<section id="markedpoints">
	<br>
	<div class="page-header">
		<h2>Outlying points</h2>
	</div>
	<div class="alert hide"></div>
	<p>Points will be marked as outliers if they have a distance more than <?php 
		$setting = new CachedSettings();
		echo $setting->getSetting(CachedSettings::OUTLIERCONTROLLDISTANCE);
	?>.</p>
	<button id="RunOutlierAnalysisButton" class="btn">Run analysis</button>
	<button id="ClearOutlierpointsButton" class="btn">Clear outliers</button>
	<br>
	<table class="table table-striped" id="OutlyingTable">
			<thead>
				<tr>
					<th>#</th>
					<th style="text-align: right;">X</th>
					<th style="text-align: right;">Y</th>
					<th style="text-align: right;">Z</th>
					<th>Cluster</th>
					<th>Time</th>
					<th>Additional Info</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if($this->viewmodel->outlierlist->size() > 0){
					for($i = 0; $i < 50 && $i < $this->viewmodel->outlierlist->size(); $i++){
					$itemnumber = $this->viewmodel->outlierlist->get($i);
					if($itemnumber >= $this->viewmodel->pointlist->size()) continue;
					$item = $this->viewmodel->pointlist->get($itemnumber);
					echo "
				<tr id=". $itemnumber .">
				<td>".$itemnumber."</td>
				<td style=\"text-align: right;\">".number_format(floatval($item->x), 2, ".", "")."</td>
				<td style=\"text-align: right;\">".number_format(floatval($item->y), 2, ".", "")."</td>
				<td style=\"text-align: right;\">".number_format(floatval($item->z), 2, ".", "")."</td>
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
				}
				else{
					$i = 0;
					echo "<tr><td colspan=\"7\">There is no points to show you. Run the analysis first.</td></tr>";
				}
				?>
			</tbody>
		</table>
		<div class="span4 offset4">
			<button id="GetMoreOutlyingPoints" class="btn" data-loading-text="Loading...">Get more points</button>
			
	<script type="text/javascript">

		$(function(){
			var outlierstop = <?php echo $i; ?>;

			$("#RunOutlierAnalysisButton").click(function(){
				$.getJSON("/outlier/RunAnalysis/json", function(data){
					$("#markedpoints > .alert").html(data.Request.Message).fadeIn(800);
					outlierstop = 0;
					updateoutlierpoints();
				});
			});
			
			$("#ClearOutlierpointsButton").click(function(){
				if(confirm("After using the clear option you will not be able to get any points back. Are you sure you want to clear all outlying points?")){
					$.getJSON("/outlier/clear/json", function(data){
						$("#markedpoints > .alert").html(data.Request.Message).fadeIn(800);
						$("#OutlyingTable > tbody").html("<tr><td colspan=\"7\">There is no points to show you. Run the analysis first.</td></tr>");
					});
				}
			});

			updateoutlierpoints = function(){
				$.getJSON("/Outlier/Points/json?start=" + outlierstop + "&stop=" + (outlierstop + 50), function(data){
					start = data.Outlier.Start;
					if(start == 0) $("#OutlyingTable > tbody").html("");
					if(data.Outlier.Stop == 0) $("#OutlyingTable > tbody").html("<tr><td colspan=\"7\">There is no points to show you. Run the analysis first.</td></tr>");
					$.each(data.Outlier.Points, function(key, value){
						$("#OutlyingTable > tbody").append("<tr id=" + key + ">" + 
							"<td id=\"num\">" + key + "</td>" +
							"<td style=\"text-align: right;\" id=\"x\">" + value.x + "</td>" +
							"<td style=\"text-align: right;\" id=\"y\">" + value.y + "</td>" +
							"<td style=\"text-align: right;\" id=\"z\">" + value.z + "</td>" +
							"<td id=\"cluster\">" + value.cluster + "</td>" +
							"<td id=\"time\">" + value.timestamp + "</td>" +
							"<td id=\"additionalinfo\"></td></tr>");
							pointid = key;
							$.each(value.additionalinfo, function(key, value){
								$("#OutlyingTable > tbody > #" + pointid + " > #additionalinfo").append(key + ": " + value + "<br>");
							});
						start++;
					});
					outlierstop = start;
					$("#GetMoreOutlyingPoints").text("Get more results");
					}).error(function(data){
						$("#GetMoreOutlyingPoints").text("Error");
					});
				}
			
				$("#GetMoreOutlyingPoints").click(updateoutlierpoints);
			});
			</script>
		</div>
</section>

<section id="points">
	<br>
		<div class="page-header">
			<h2>Available points</h2>
		</div>
		
		<table class="table table-striped" id="PointTable">
			<thead>
				<tr>
					<th>#</th>
					<th style="text-align: right;">X</th>
					<th style="text-align: right;">Y</th>
					<th style="text-align: right;">Z</th>
					<th>Cluster</th>
					<th>Time</th>
					<th>Additional Info</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if($this->viewmodel->pointlist->size() > 0){
					for($i = 0; $i < 50 && $i < $this->viewmodel->pointlist->size(); $i++){
						$item = $this->viewmodel->pointlist->get($i);
						echo "
				<tr id=". $i .">
				<td id=\"num\">".$i."</td>
				<td style=\"text-align: right;\" id=\"x\">".number_format(floatval($item->x), 2, ".", "")."</td>
				<td style=\"text-align: right;\" id=\"y\">".number_format(floatval($item->y), 2, ".", "")."</td>
				<td style=\"text-align: right;\" id=\"z\">".number_format(floatval($item->z), 2, ".", "")."</td>
				<td id=\"cluster\">".$item->cluster."</td>
				<td id=\"time\">".$item->timestamp."</td>";
					
						$moreinfo = $item->getAdditionalInfo();
						if(count($moreinfo)){
							echo "<td id=\"additionalinfo\">";
							foreach($item->getAdditionalInfo() as $key => $value){
								echo "" . $key . ": " . $value . "<br>";
							}
							echo "</td>";
						}
						else{
							echo "<td id=\"additionalinfo\"></td>";
						}
						
					echo "</tr>";
					}
				}
				else{
					$i = 0;
					echo "<tr><td colspan=\"7\">There is no points to show you.</td></tr>";
				}
				?>
			</tbody>
		</table>
		<div class="span4 offset4">
			<button id="GetMorePoints" class="btn" data-loading-text="Loading...">Get more points</button>
			<script type="text/javascript">
				$(function(){
				var stop = <?php echo $i; ?>;
				var updatePointTable = function(){
					//$("#MoreResults").html("Loading");
					$.getJSON("/Register/Points/json?start=" + stop + "&stop=" + (stop + 50), function(data){
						start = data.Register.Start;
						if(start == 0) $("#PointTable > tbody").html("");
						$.each(data.Register.Points, function(key, value){
							$("#PointTable > tbody").append("<tr id=" + start + ">" + 
								"<td id=\"num\">" + start + "</td>" +
								"<td style=\"text-align: right;\" id=\"x\">" + value.x + "</td>" +
								"<td style=\"text-align: right;\" id=\"y\">" + value.y + "</td>" +
								"<td style=\"text-align: right;\" id=\"z\">" + value.z + "</td>" +
								"<td id=\"cluster\">" + value.cluster + "</td>" +
								"<td id=\"time\">" + value.timestamp + "</td>" +
								"<td id=\"additionalinfo\"></td></tr>");
								$.each(value.additionalinfo, function(key, value){
									$("#PointTable > tbody > #" + start + " > #additionalinfo").append(key + ": " + value + "<br>");
								});
							start++;
						});
						stop = start;
						$("#GetMorePoints").html("Get more results");
					}).error(function(data){
						$("#GetMorePoints").html("Error");
					});
				};
				
				$("#GetMorePoints").click(updatePointTable);

				$(window).scroll(function(){
					if($(window).scrollTop() + $(window).height() == $(document).height()) {
						updatePointTable();
					}
				});
				});
			</script>
		</div>
	</section>