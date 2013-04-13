<?php
$this->viewmodel->templatemenu = array("CacheSettings" => "Cache settings", "ClusterAnalysis" => "Cluster analysis", "OutlyingPoints" => "Outlying Points", "MasterPointSettings" => "Master Point Triggering", "TriggerprogramSettings" => "Triggerprogram");
$this->Template("Shared");
?>

<!-- Cache settings  -->

<section id="CacheSettings">
	<div class="page-header">
		<h2>Cache settings</h2>
		<p>qergeg</p>
	</div>
	
	<div class="alert hide" id="alertmsg"></div>
	
	<div class="form-horizontal">
		<h4>Clear point cache</h4>
		<p>This option can be used to clear the cache for trigger points. If
			this is used the pointlist will be put back to zero points. This does
			not clear the memory, but old points will be overwriten as new points
			gets submitted.		
		<p>
		
		<div class="alert alert-block alert-error">
			<h4>Warning!</h4>
			<p>This will remove all trigger point data that is present in the cache at the moment. You will not be able to get all the points back!</p>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn" id="ClearPointlist" value="Clear cache">
			</div>
		</div>
		<script type="text/javascript">$(function(){
				$("#ClearPointlist").click(function(){
					if(confirm("Are you sure about clearing the cache?")){
						$.getJSON("/register/clear/json", function(data){
							$("#CacheSettings > #alertmsg").html(data.Request.Message).fadeIn(800);
						});
					}
				});
			});
		</script>
	</div>
	
	<div class="form-horizontal">
		<h4>Clear cluster cache</h4>
		<p>This option can be used to clear the cache for cluster points. If
			this is used the cluster pointlist will be put back to zero points. This does
			not clear the memory, but old points will be overwriten as new points
			gets submitted.		
		<p>
		
		<div class="alert alert-block alert-error">
			<h4>Warning!</h4>
			<p>This will remove all cluster point data that is present in the cache at the moment. You will not be able to get all the points back!</p>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn" id="ClearClusterPointlist" value="Clear cache">
			</div>
		</div>
		<script type="text/javascript">$(function(){
				$("#ClearClusterPointlist").click(function(){
					if(confirm("Are you sure about clearing the cache?")){
						$.getJSON("/cluster/clear/json", function(data){
							$("#CacheSettings > #alertmsg").html(data.Request.Message).fadeIn(800);
						});
					}
				});
			});
		</script>
	</div>
	
	<div class="form-horizontal">
		<h4>Clear master point cache</h4>
		<p>This option can be used to clear the cache for master points. If
			this is used the master pointlist will be put back to zero points. This does
			not clear the memory, but old points will be overwriten as new points
			gets submitted.		
		<p>
		
		<div class="alert alert-block alert-error">
			<h4>Warning!</h4>
			<p>This will remove all master point data that is present in the cache at the moment. You will not be able to get all the points back!</p>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn" id="ClearMasterPointlist" value="Clear cache">
			</div>
		</div>
		<script type="text/javascript">$(function(){
				$("#ClearMasterPointlist").click(function(){
					if(confirm("Are you sure about clearing the cache?")){
						$.getJSON("/master/clear/json", function(data){
							$("#CacheSettings > #alertmsg").html(data.Request.Message).fadeIn(800);
						});
					}
				});
			});
		</script>
	</div>
	
	<div class="form-horizontal">
		<h4>Clear outlier cache</h4>
		<p>This option can be used to clear the cache for outlier data. If
			this is used the outlier data will be put back to zero points. This does
			not clear the memory, but old points will be overwriten as new points
			gets submitted.		
		<p>
		
		<div class="alert alert-block alert-error">
			<h4>Warning!</h4>
			<p>This will remove all outlier data that is present in the cache at the moment. You will not be able to get all the data back!</p>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<input type="submit" class="btn" id="Clearoutlierdata" value="Clear cache">
			</div>
		</div>
		<script type="text/javascript">$(function(){
				$("#Clearoutlierdata").click(function(){
					if(confirm("Are you sure about clearing the cache?")){
						$.getJSON("/outlier/clear/json", function(data){
							$("#CacheSettings > #alertmsg").html(data.Request.Message).fadeIn(800);
						});
					}
				});
			});
		</script>
	</div>
</section>

<!-- Cluster settings -->

<section id="ClusterAnalysis">
	<div class="page-header">
		<h2>Cluster analysis</h2>
		<p>qergeg</p>
	</div>
	<div>

		<h4>K-Means Algoritm</h4>

		<form action="cluster" method="GET" class="form-horizontal">
			<p>It is possible to calculate clusters while the point get submitted. If 
			this option is turned on there will be a done a new analysis of the 
			clusters every time a point get submitted.</p>
			<div class="alert alert-block">
				<h4>Notice!</h4>
				<p>May result in point submittions use up all of the resources on the 
				server and block new submittions. To avoid this from happening try to 
				keep the number of points that lockes in a cluster low.</p>
			</div>
			<div class="control-group">
				<div class="controls">
					<label class="checkbox"> <input type="checkbox"
						name="<?php echo CachedSettings::ANALYSECLUSTERSWHILESUBMITION; ?>"
						<?php if($this->viewmodel->settings->getSetting(CachedSettings::ANALYSECLUSTERSWHILESUBMITION)) echo "checked"; ?> />
						Calculate on each point submittion
					</label>
				</div>
			</div>
			<p>If the analysis dont give you a sufficent result you can use a random 
			assignement of the initial points that are used to calculate clusters around.</p>
			<div class="alert alert-block">
				<h4>Notice!</h4>
				<p>May result result in higher runtime of the analysis.</p>
				<p>If you know that points will come in to the system in the correct 
				order it is recommended not to use random initial points. It is also 
				not recomended to use this if the analysis is run each time a 
				point get submitted.</p>
			</div>
			<div class="control-group">
				<div class="controls">
					<label class="checkbox"> <input type="checkbox"
						name="<?php echo CachedSettings::RANDOMINITIALCLUSTERPOINTS; ?>"
						<?php if($this->viewmodel->settings->getSetting(CachedSettings::RANDOMINITIALCLUSTERPOINTS)) echo "checked"; ?> />
						Random initial clusterpoints
					</label>
				</div>
			</div>
			<p>The k-means algoritm needs to know how many clusters it should be
				looking for, so its very important to put in the correct number of
				clusters.</p>
			<div class="control-group">
				<label class="control-label" for="<?php echo CachedSettings::NUMBEROFCLUSTERS; ?>">
					Number of clusters
				</label>
				<div class="controls">
					<input type="text"
						name="<?php echo CachedSettings::NUMBEROFCLUSTERS; ?>"
						id="<?php echo CachedSettings::NUMBEROFCLUSTERS; ?>"
						value="<?php echo $this->viewmodel->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS); ?>" />
				</div>
			</div>
			<p>How many points until the sentrum of the clusters will be locked in.</p>
			<div class="control-group">
				<label class="control-label"
					for="<?php echo CachedSettings::MAXPOINTSINCLUSTERANALYSIS; ?>">Max
					number of points</label>
				<div class="controls">
					<input type="text"
						name="<?php echo CachedSettings::MAXPOINTSINCLUSTERANALYSIS; ?>"
						id="<?php echo CachedSettings::MAXPOINTSINCLUSTERANALYSIS; ?>"
						value="<?php echo $this->viewmodel->settings->getSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS); ?>" />
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<input type="submit" value="Save Cluster Settings" class="btn" />
				</div>
			</div>

		</form>
	</div>
</section>

<!-- Outlying points settings -->

<section id="OutlyingPoints">
	<div class="page-header">
		<h2>Outlying points</h2>
	</div>
	<form action="/settings/outlier" method="GET" class="form-horizontal">
		<div class="control-group">
			<label class="control-label"
				for="<?php echo CachedSettings::OUTLIERCONTROLLDISTANCE; ?>">Max
				distance</label>
			<div class="controls">
				<input type="text"
					name="<?php echo CachedSettings::OUTLIERCONTROLLDISTANCE; ?>"
					id="<?php echo CachedSettings::OUTLIERCONTROLLDISTANCE; ?>"
					value="<?php echo $this->viewmodel->settings->getSetting(CachedSettings::OUTLIERCONTROLLDISTANCE); ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input type="submit" value="Save Outlier Settings" class="btn" />
			</div>
		</div>
	</form>

</section>

<!-- Master point triggering settings -->

<section id="MasterPointSettings">
	<div class="page-header">
		<h2>Master Point Triggering</h2>
	</div>
	<div>
		<form action="masterpoint" method="GET" class="form-horizontal">
			<p></p>
			<div class="control-group">
				<div class="controls">
					<label class="checkbox"> 
						<input 	type="checkbox"
								name="<?php echo CachedSettings::RUNMASTERCODEINBACKGROUND; ?>"
								<?php if($this->viewmodel->settings->getSetting(CachedSettings::RUNMASTERCODEINBACKGROUND)) echo "checked"; ?> />
						Run in background (has no function on Windows)
					</label>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Code to be run to retrive a masterpoint:</label>
				<div class="controls">
					<textarea rows="5" name="<?php echo CachedSettings::KODETOMASTERPOINTTRIGGERING; ?>"><?php echo $this->viewmodel->settings->getSetting(CachedSettings::KODETOMASTERPOINTTRIGGERING); ?></textarea>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<input type="submit" value="Save Master point code" class="btn" />
				</div>
			</div>
		</form>
	</div>
</section>

<!-- Program startup settings -->

<section id="TriggerprogramSettings">
	<div class="page-header">
		<h2>Triggerprogram</h2>
	</div>
	<div>
		<form action="triggerprogram" method="GET" class="form-horizontal">
			<p></p>
			<div class="control-group">
				<div class="controls">
					<label class="checkbox"> 
						<input 	type="checkbox"
								name="<?php echo CachedSettings::RUNTRIGGERPROGRAMINBACKGROUND; ?>"
								<?php if($this->viewmodel->settings->getSetting(CachedSettings::RUNTRIGGERPROGRAMINBACKGROUND)) echo "checked"; ?> />
						Run in background (has no function on Windows)
					</label>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Code to be run to start the triggeringprogram:</label>
				<div class="controls">
					<textarea rows="5" name="<?php echo CachedSettings::KODETOTRIGGERPROGRAMSTART; ?>"><?php echo $this->viewmodel->settings->getSetting(CachedSettings::KODETOTRIGGERPROGRAMSTART); ?></textarea>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<input type="submit" value="Save triggerprogram settings" class="btn" />
				</div>
			</div>
		</form>
	</div>
</section>
