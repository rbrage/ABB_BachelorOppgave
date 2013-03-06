<?php
$this->Template("sheard");
?>

<div class="row span9">
	<section id="ClusterSettings">
		<div id="page-header">
			<h2>Cluster analysis</h2>
			<p>qergeg</p>
		</div>
		<div>
		
			<h4>K-Means Algoritm</h4>
			
			<form action="cluster" method="GET" class="form-horizontal">
				<p>It is possible to calculate clusters while the </p>
				<div class="control-group">
					<div class="controls">
					<label class="checkbox">
						<input type="checkbox" name="<?php echo CachedSettings::ANALYSECLUSTERSWHILESUBMITION; ?>" <?php if($this->viewmodel->settings->getSetting(CachedSettings::ANALYSECLUSTERSWHILESUBMITION)) echo "checked"; ?> />
						Calculate on each point submittion
					</label>
					</div>
				</div>
				<p>The k-means algoritm needs to know how many clusters it should be looking for, so its very important to put in the correct number of clusters. </p>
				<div class="control-group">
					<label class="control-label" for="<?php echo CachedSettings::NUMBEROFCLUSTERS; ?>">Number of clusters</label>
					<div class="controls">
						<input 	type="text"
								name="<?php echo CachedSettings::NUMBEROFCLUSTERS; ?>"
								id="<?php echo CachedSettings::NUMBEROFCLUSTERS; ?>"
								value="<?php echo $this->viewmodel->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS); ?>" />
					</div>
				</div>
				<p>How many points until the sentrum of the clusters will be locked in.</p>
				<div class="control-group">
					<label class="control-label" for="<?php echo CachedSettings::MAXPOINTSINCLUSTERANALYSIS; ?>">Max number of points</label>
					<div class="controls">
						<input 	type="text"
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
	<section id="MasterPointSettings">
		<div id="page-header">
			<h2>Master Point Triggering</h2>
		</div>
		<div>
			<form action="masterpoint" method="GET">
				<p>
					Number of clusters to be found: <input type="text"
						name="<?php echo CachedSettings::NUMBEROFCLUSTERS; ?>"
						value="<?php echo $this->viewmodel->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS); ?>" />
				</p>
				<p>
					Max number of points to be prosessed: <input type="text"
						name="<?php echo CachedSettings::MAXPOINTSINCLUSTERANALYSIS; ?>"
						value="<?php echo $this->viewmodel->settings->getSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS); ?>" />
				</p>
				<input type="submit" value="Save Settings" class="right" />
			</form>
		</div>
	</section>
</div>
