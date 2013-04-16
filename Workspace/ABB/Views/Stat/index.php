<?php
$list = $this->viewmodel->clusterlist;
if($list->size() > 0){
	$menulist = array();
		for($i = 0; $i < $list->size(); $i++){
			$menulist["clusterinformation$i"] = "Cluster $i";
		};
		
	$this->viewmodel->templatemenu = $menulist;
};
$this->template("Stat");
?>

<section id="stat">
	<br>
	<div class="page-header">
		<h2>Statistics</h2>
	</div>
	<div class="alert hide"></div>
	<div>
		<h4>Actions</h4>
		<button id="RunStatistics" class="btn" >Run Statistics</button> 
		<button id="ClearStatistics" class="btn">Clear Statistics</button> 
		<br><br>
		<h4>Trigger Report</h4>
		<p>Export statistical data to a PDF-file.</p>
		<form action="/stat/CreatePDF/" method="GET" class="form-horizontal" target="_blank">
			<div class="control-group">
				<label class="control-label">Comments in to the report:</label>
				<div class="controls">
					<textarea rows="5" name="ReportComment"></textarea>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<input type="submit" value="Create PDF" class="btn" />
				</div>
			</div>
		</form>
		
	</div>
	<script type="text/javascript">
		$(function(){

			$("#ClearStatistics").click(function(){
				if(confirm("After using the clear option you will not be able to get any data back. Are you sure you want to clear all statistics?")){
					$.getJSON("/stat/clear/json", function(data){
						$("#stat > .alert").html(data.Request.Message).fadeIn(800);
					});
				}
			});
			
			$("#RunStatistics").click(function(){
				$("#stat > .alert").removeClass("alert-error").removeClass("alert-success").html(<?php echo (($this->viewmodel->clusterlist->size() > 20000)?"\"The server is crushing some big numbers at the moment! It might take a while, have an apple.\"":"\"Calculation is started!\""); ?>).fadeIn(800);
				$.getJSON("/Stat/RunAnalysis/json", function(data){
					$("#stat > .alert").removeClass("alert-error").addClass("alert-success").html(data.Request.Message).fadeIn(800);
					window.setTimeout(function(){location.reload();}, 3000);
					$(window).delay(5000);
					
				}).error(function(){
					$("#stat > .alert").removeClass("alert-success").addClass("alert-error").html("An error occured.").fadeIn(800);
				});
			});
		});
	</script>
</section>	
<?php 
	$list = $this->viewmodel->clusterlist;
	
	if($list->size() > 0){
		for($i = 0; $i < $list->size(); $i++){
			$point = $list->get($i);
			?>
<section id="clusterinformation<?php echo $i?>">
	<br>
	<div>
	<div class="page-header">
		<h2>Cluster <?php echo $i?></h2>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<table class="table table-striped">
					<tbody>
						<tr>
							<td><b>X</b></td>
							<td><b><?php echo round($point->x, 2); ?></b></td>
						</tr>
						<tr>
							<td><b>Y</b></td>
							<td><b><?php echo round($point->y, 2); ?></b></td>
						</tr>
						<tr>
							<td><b>Z</b></td>
							<td><b><?php echo round($point->z, 2); ?></b></td>
						</tr>
						<tr>
							<td><b>Points in cluster</b></td>
							<td><b><?php echo $point->getAdditionalInfo(KMeans::CLUSTERCOUNTNAME)?></b></td>
						</tr>
						<tr>
							<td><b>Distance to the most distant point</b></td>
							<td><b><?php $maxDistance = $this->viewmodel->cache->getCacheData(Stat::MAXDISTANCE); echo @$maxDistance[$i]; ?></b></td>
						</tr>
						<tr>
							<td><b>Offset distance from master point</b></td>
							<td><b><?php $masterDistance = $this->viewmodel->cache->getCacheData(Stat::MASTERPOINTDISTANCE); echo @$masterDistance[$i]; ?></b></td>
						</tr>
						<tr>
							<td><b>Standard deviation from cluster center</b></td>
							<td><b>
								<?php 
									$standardDeviation = $this->viewmodel->cache->getCacheData(Stat::STANDARDDEVIATION); 
									echo "" . @$standardDeviation[$i]["x"] . " @ x-axis<br>";
									echo "" . @$standardDeviation[$i]["y"] . " @ y-axis<br>";
									echo "" . @$standardDeviation[$i]["z"] . " @ z-axis<br>";
									echo "" . round(@($standardDeviation[$i]["x"] + $standardDeviation[$i]["y"] + $standardDeviation[$i]["z"])/3, 2) . " @ average<br>";
								?></b>
							</td>
						</tr>
						<tr>
							<td><b>Outlaying Points</b></td>
							<td><b><?php $outliers = $this->viewmodel->cache->getCacheData(Stat::OUTLIERS); echo @$outliers[$i] . " points > " . $this->viewmodel->settings->getSetting(CachedSettings::OUTLIERCONTROLLDISTANCE); ?></b></td>
						</tr>
						<tr>
							<td><b>Average distance</b></td>
							<td><b><?php $averageDistance = $this->viewmodel->cache->getCacheData(Stat::AVERAGEDISTANCE); echo @$averageDistance[$i]; ?></b></td>
						</tr>
					</tbody>
				</table>
			</div>	
			<div class="span6">
				<div id="totaldistancedistrubiation<?php echo $i; ?>" class="jqplot-target"></div>
			</div>	
			<div class="span12">
				<div id="fullaxisdistrubiation<?php echo $i; ?>" class="jqplot-target"></div>
			</div>
			
			<script type="text/javascript">
				<?php 
					$totaldistrubiation = $this->viewmodel->cache->getCacheData(Stat::DISTRIBUTION);
					$fullaxialdistrubiation = $this->viewmodel->cache->getCacheData(Stat::FULLAXIALDISTRIBUTION);
					$outlierdistance = $this->viewmodel->settings->getSetting(CachedSettings::OUTLIERCONTROLLDISTANCE);
				?>
				$(document).ready(function(){
				$.jqplot._noToImageButton = true;
		        $.jqplot.config.enablePlugins = true;
		        var s1 = <?php 
							$output = "[";
							$first = true;
							if(is_array(@$fullaxialdistrubiation[$i])){
								foreach ($fullaxialdistrubiation[$i] as $count){
									if($first)
										$first = false;
									else 
										$output .= ", ";
									
									$output .= $count["x"];
								}
							}
							else{
								$output .= "0";
							}
							$output .= "]";
							echo $output;
   				 		?>;
		        var s2 = <?php 
							$output = "[";
							$first = true;
							if(is_array(@$fullaxialdistrubiation[$i])){
								foreach ($fullaxialdistrubiation[$i] as $count){
									if($first)
										$first = false;
									else 
										$output .= ", ";
									
									$output .= $count["y"];
								}
							}
							else{
								$output .= "0";
							}
							$output .= "]";
							echo $output;
    					?>;
		        var s3 = <?php 
							$output = "[";
							$first = true;
							if(is_array(@$fullaxialdistrubiation[$i])){
								foreach ($fullaxialdistrubiation[$i] as $count){
									if($first)
										$first = false;
									else 
										$output .= ", ";
									
									$output .= $count["z"];
								}
							}
							else{
								$output .= "0";
							}
							$output .= "]";
							echo $output;
    					?>;
		        var s4 = <?php 
							$output = "[";
							$first = true;
							if(is_array(@$totaldistrubiation[$i])){
								foreach ($totaldistrubiation[$i] as $count){
									if($first)
										$first = false;
									else 
										$output .= ", ";
									
									$output .= $count;
								}
							}
							else{
								$output .= "0";
							}
							$output .= "]";
							echo $output;
		        		?>;
		        var totalticks = <?php 
					        $output = "[";
					        $first = true;
					        for($j = 0; $j < Stat::DISTRIBUTIONRESOLUTION; $j++){
								if($first){
									$first = false;
								}
								else{
									$output .= ", ";
								}
								
								$output .= "\"" . ($j * $outlierdistance/Stat::DISTRIBUTIONRESOLUTION) . " - " . (($j + 1) * $outlierdistance/Stat::DISTRIBUTIONRESOLUTION) . "\"";
							}
					        $output .= "]";
					        echo $output;
		        		?>;
				var fullaxialticks = <?php 
					        $output = "[";
					        $first = true;
					        for($j = 0; $j < Stat::DISTRIBUTIONRESOLUTION; $j++){
								if($first){
									$first = false;
								}
								else{
									$output .= ", ";
								}
					
								$output .= "\"" . (($j * $outlierdistance/(Stat::DISTRIBUTIONRESOLUTION/2)) - $outlierdistance) . " - " . ((($j + 1) * $outlierdistance/(Stat::DISTRIBUTIONRESOLUTION/2)) - $outlierdistance) . "\"";
							}
					        $output .= "]";
					        echo $output;
						?>;
         
		        var plot<?php echo $i; ?> = $.jqplot("fullaxisdistrubiation<?php echo $i; ?>", [s1, s2, s3, s1, s2, s3], {
			            // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
			            animate: !$.jqplot.use_excanvas,
			            title: "1D distrubiation chart",
			            seriesDefaults:{
			                pointLabels: { show: false }
				        },
			            axes: {
			                xaxis: {
			                    renderer: $.jqplot.CategoryAxisRenderer,
			                    ticks: fullaxialticks,
			                    tickRenderer: $.jqplot.CanvasAxisTickRenderer,
			                    label:'Distance from cluster center',
			                    tickOptions: {
				                	angle: 45,
				                	fontSize: '8pt'
			                    }
			                },
	                    	yaxis: {
			                    label:'Number of points'
	                    	}
			            },
			            highlighter: { 
			                show: true,
			                sizeAdjust: 1,
			                tooltipOffset: 9,
			                tooltipAxes: "y"
			            },
			            series:[
				            {
				                renderer:$.jqplot.BarRenderer,
				                color: "#ff0000",
				                label: 'X'
				            },
				            {
				                renderer:$.jqplot.BarRenderer,
				                color: "#00ff00",
				                label: 'Y'
					        },
				            {
				                renderer:$.jqplot.BarRenderer,
				                color: "#0000ff",
				                label: 'Z'
					        },
				            {
				                showMarker:false,
				                color: "#ff0000",
				                rendererOptions:{
				                	smooth: true
				                },
				                linePattern: 'dotted',
				                shadow: false,
				                label: 'X line'
					        },
				            {
				                showMarker:false,
				                color: "#00ff00",
				                rendererOptions:{
				                	smooth: true
				                },
				                linePattern: 'dotted',
				                shadow: false,
				                label: 'X line'
					        },
				            {
				                showMarker:false,
				                color: "#0000ff",
				                rendererOptions:{
				                	smooth: true
				                },
				                linePattern: 'dotted',
				                shadow: false,
				                label: 'Z line'
					        }
			            ],
			            legend: {
			                renderer: $.jqplot.EnhancedLegendRenderer,
							show: true,
			            	location: 'ne',
			            	placement: 'outsideGrid'
			       		}
			        });

			    var plot<?php echo $i; ?> = $.jqplot("totaldistancedistrubiation<?php echo $i; ?>", [s4], {
		            // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
		            animate: !$.jqplot.use_excanvas,
		            seriesDefaults:{
		            	pointLabels: { show: true, location: 'n', edgeTolerance: -30 },
		                renderer:$.jqplot.BarRenderer,
		                pointLabels: { 
			                	show: true 
			                }
			            },
			            axes: {
			                xaxis: {
			                    renderer: $.jqplot.CategoryAxisRenderer,
			                    ticks: totalticks,
			                    tickRenderer: $.jqplot.CanvasAxisTickRenderer,
			                    label:'Distance from cluster center',
			                    tickOptions: {
				                	angle: 45,
				                	fontSize: '8pt'
			                    }
			                }
			            },
			            highlighter: { 
			                show: true,
			                sizeAdjust: 1,
			                tooltipOffset: 9,
			                tooltipAxes: "y"
			            }
			        });
					$('.jqplot-highlighter-tooltip').addClass('ui-corner-all');
			    });
			</script>
		</div>
	</div>
</section>		
	<?php
	}
		}
			else{
				echo "<p>There is no points to define the clusters, so no more spesific statistics can be viewed. Run the analysis first.</p>";
			}
			?>
