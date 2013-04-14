<?php
$list = $this->viewmodel->clusterlist;
if($list->size() > 0){
	$menulist = array();
		for($i = 0; $i < $list->size(); $i++){
			$menulist["clusterinformation$i"] = "Cluster $i";
		};
		
	$this->viewmodel->templatemenu = $menulist;
};
$this->template("Shared");
?>

<section id="stat">
	<div class="page-header">
			<h2>Statistics</h2>
			<button id="runStat" class="btn">Run Statistics</button> 
			<a id="createPDF" class="btn" href="/stat/CreatePDF" target="_blank">Create PDF</a>
	</div>
</section>	
<?php 
	$list = $this->viewmodel->clusterlist;
	
	if($list->size() > 0){
		for($i = 0; $i < $list->size(); $i++){
			$point = $list->get($i);
			?>
			<section id="clusterinformation<?php echo $i?>">
			<div>
			
				<h3>Cluster <?php echo $i?></h3>
				<div class="row">
					<div class="span6">
						<table class="table table-striped">
							<tbody>
								<tr>
									<th>Points in cluster</th>
									<th><?php echo $point->getAdditionalInfo(KMeans::CLUSTERCOUNTNAME)?></th>
								</tr>
								<tr>
									<th>Max. distance</th>
									<th><?php $maxDistance = $this->viewmodel->cache->getCacheData(Stat::MAXDISTANCE); echo @$maxDistance[$i]; ?></th>
								</tr>
								<tr>
									<th>Distance from the master point</th>
									<th><?php $masterDistance = $this->viewmodel->cache->getCacheData(Stat::MASTERPOINTDISTANCE); echo @$masterDistance[$i]; ?></th>
								</tr>
								<tr>
									<th>Standard deviation</th>
									<th>#</th>
								</tr>
								<tr>
									<th>Outlaying Points</th>
									<th><?php $outliers = $this->viewmodel->cache->getCacheData(Stat::MASTERPOINTDISTANCE); echo @$outliers[$i] . " points > " . $this->viewmodel->settings->getSetting(CachedSettings::OUTLIERCONTROLLDISTANCE); ?></th>
								</tr>
								<tr>
									<th>Average distance</th>
									<th><?php $averageDistance = $this->viewmodel->cache->getCacheData(Stat::AVERAGEDISTANCE); echo @$averageDistance[$i]; ?></th>
								</tr>
							</tbody>
						</table>
					</div>	
					<div class="span6">
						<p>kommer bilde</p>
					</div>	
					<div class="span12">
				<div id="total" class="jqplot-target"></div>
					</div>
			</div>
		</div>
</section>		
	<?php
	}
		}
			else{
				echo "<tr><td colspan=\"5\">There is no points to define the clusters. Run the analysis first.</td></tr>";
			}
			?>
</section>

<script type="text/javascript">
	$(document).ready(function(){
		
        $.jqplot.config.enablePlugins = true;
        var s1 = [20, 58, 70, 88, 115, 103, 85, 74, 62, 35];
        var s2 = [15, 61, 74, 85, 110, 95, 83, 76, 60, 30];
        var s3 = [17.5, 65, 69, 86, 100, 100, 8, 75, 58, 20];
        var ticks = ['0-5', '5-10', '10-15', '15-20', '20-25', '25-30', '30-35', '35-40', '40-45', '45-50'];
         
        var plot1 = $.jqplot('total', [s1, s2, s3], {
            // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
            animate: !$.jqplot.use_excanvas,
            seriesDefaults:{
                renderer:$.jqplot.BarRenderer,
                pointLabels: { show: true }
            },
            axes: {
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: ticks
                }
            },
            highlighter: { show: false }
        });

    	$(window).resize(function(){
        	plot1.replot();
        	plot1.redraw();
        });
    });
	</script>

<!--
<section id="total">
	<div id="total" class="jqplot-target"></div>
	
</section>

-->