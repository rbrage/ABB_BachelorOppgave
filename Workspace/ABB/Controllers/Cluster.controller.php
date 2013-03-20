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
		$this->settings = new CachedSettings();
		$this->viewmodel->clusterlist = $this->clusterlist;
		$this->viewmodel->settings = $this->settings;

		return $this->View();
	}

	public function reset($id){
		$this->clusterlist = new CachedArrayList(KMeans::CLUSTERLISTNAME);
		$this->clusterlist->clear();
		
		echo json_encode(array("msg" => "The cachelist is cleared."));
	}

	public function run($id){
		$this->settings = new CachedSettings();
		$this->cluster = new KMeans($this->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS));
		$this->cluster->setNumberOfPointsToDeterminClusters($this->settings->getSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS));
		$this->cluster->calculateClusters();
		
		echo json_encode(array("msg" => "The clusters are done calculating."));
	}
	
	public function force($id){
		$this->settings = new CachedSettings();
		$this->cluster = new KMeans($this->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS));
		$this->cluster->forceNewAnalysis();
		$this->cluster->setNumberOfPointsToDeterminClusters($this->settings->getSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS));
		$this->cluster->calculateClusters();
		$KMeans = new KMeans(0);
		$KMeans->forceNewAnalysis();
		echo json_encode(array("msg" => "The new analysis is done."));
	}
	
	public function reasign($id){
		$this->settings = new CachedSettings();
		$this->cluster = new KMeans($this->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS));
		$this->cluster->asignAllPointsToClusters();
		echo json_encode(array("msg" => "All points are now in nearest cluster."));
	}

}

?>