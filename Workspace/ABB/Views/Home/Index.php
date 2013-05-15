<?php 
$this->viewmodel->templatemenu = array("execute" => "Sensor program options", "informasjon" => "Informasjon", "report" => "Report", "last" => "Last 10 Points");

$this->Template("Home");
?>

<section id="execute">
<br>
	<div class="page-header">
		<h2>Sensor program options</h2>
	</div>
	<div class="alert hide"></div>
	<button id="triggerprogramButton" class="btn">Start Triggerprogram</button>
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
	
	<button id="masterpointButton" class="btn">Retrieve masterpoint</button>
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
<?php 

$cache = new Cache();
$pointlist = new CachedArrayList();
$clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
$masterlist = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
$outlierlist = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);

$cacheinfo = $cache->getCacheInfo();

?>
<section id="informasjon">
	<br>
	<div class="page-header">
		<h2>Informasjon</h2> 
	</div>
	<div class="row-fluid">
		<div class="span6">
		<table class="table table-condensed">
			<tbody>
				<tr>
					<td>Number of triggerpoints:</td>
					<td id="pointsize"><?php echo $pointlist->size(); ?></td>
				</tr>
				<tr>
					<td>Number of cluster:</td>
					<td id="clustersize"><?php echo $clusterlist->size(); ?></td>
				</tr>
				<tr>
					<td>Number of masterpoints:</td>
					<td id="mastersize"><?php echo $masterlist->size(); ?></td>
				</tr>
				<tr>
					<td>Number of outlyers:</td>
					<td id="outliersize"><?php echo $outlierlist->size(); ?></td>
				</tr>
			</tbody>
		</table>
		</div>
		<div class="span6">
		<table class="table table-condensed">
			<tbody>
				<tr>
					<td>Number of analysed points:</td>
					<td id="pointsize"><?php  echo $this->viewmodel->settings->getSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS); ?></td>
				</tr>
				<tr>
					<td>Outlyers max distance:</td>
					<td id="clustersize"><?php echo $this->viewmodel->settings->getSetting(CachedSettings::OUTLIERCONTROLLDISTANCE); ?></td>
				</tr>
				<tr>
					<td>Used memory:</td>
					<td id="usedmemory"><?php echo $cacheinfo["mem_size"]/1000 . "k"; ?></td>
				</tr>
				<tr>
					<td>Available memory:</td>
					<td id="availablememory"><?php echo ini_get("apc.shm_size") * 1000 . "k"; ?></td>
				</tr>
				
			</tbody>
		</table>
		</div>
	</div>
</section>
<section id="report">
<br>
	<div class="page-header">
		<h2>Report</h2> 
	</div>
	<div>
		<a id="createPDF" class="btn" href="/stat/CreatePDF" target="_blank">Create PDF</a>
	</div>
</section>
<section id="last">
<br>
	<div class="page-header">
		<h2>Last 10 points</h2>
	</div>
	<?php 
	$list = $this->viewmodel->arr->getCachedArrayList();
	$size = $list->size();

			?>
	<table class="table table-striped" id="LastPointsTable">
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
				if(!$list->isEmpty()){
					for($i = $size -1; ($size - $i) <= 10 && $i >= 0 ;$i--){
						$item = $list->get($i);
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
							echo "</td></tr>";
						}
						else{
							echo "<td></td></tr>";
						}
					}
				}
				else{
					$i = 0;
					echo "<tr><td colspan=\"7\">There is no points to show you.</td></tr>";
				}
				?>
			</tbody>
		</table>
			<script type="text/javascript">

			$(function(){
				addSSEvent("pointsize", function(event){
					$.getJSON("/register/points/json?start=" + (event.data - 10) + "&stop=" + event.data, function(data){
						$("#LastPointsTable > tbody").empty();
						start = data.Register.Start;
						$.each(data.Register.Points, function(key, value){
							$("#LastPointsTable > tbody").prepend("<tr id=" + start + ">" + 
									"<td id=\"num\">" + start + "</td>" +
									"<td style=\"text-align: right;\" id=\"x\">" + value.x + "</td>" +
									"<td style=\"text-align: right;\" id=\"y\">" + value.y + "</td>" +
									"<td style=\"text-align: right;\" id=\"z\">" + value.z + "</td>" +
									"<td id=\"cluster\">" + value.cluster + "</td>" +
									"<td id=\"time\">" + value.timestamp + "</td>" +
									"<td id=\"additionalinfo\"></td></tr>");
							$.each(value.additionalinfo, function(key, value){
								$("#LastPointsTable > tbody > #" + start + " > #additionalinfo").append(key + ": " + value + "<br>");
							});
							start++;
						});
					});
				});
			});

			</script>
</section>