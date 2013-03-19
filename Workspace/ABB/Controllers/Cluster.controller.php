<?php

require_once 'Models/KMeans.php';
require_once 'Models/CachedArrayList.php';
require_once 'Models/CachedSettings.php';

class Cluster extends Controller {

	private $clusterlist;
	private $pointlist;
	private $settings;
	private $cluster;

	public function index(){
		$this->clusterlist = new CachedArrayList(KMeans::CLUSTERLISTNAME);
		echo "begin\n";
		echo "Size:" . $this->clusterlist->size();
		for($i = 0; $i < $this->clusterlist->size(); $i++){
			print_r($this->clusterlist->get($i));
		}
		echo "end\n";
	}

	public function reset($id){
		$KMeans = new KMeans(0);
		$KMeans->forceNewAnalysis();
	}

	public function run($id){
		$this->settings = new CachedSettings();
		$this->cluster = new KMeans($this->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS));
		$this->cluster->setNumberOfPointsToDeterminClusters($this->settings->getSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS));
		$this->cluster->calculateClusters();
	}

}

?>