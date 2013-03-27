<?php

require_once 'Models/KMeans.php';
require_once 'Models/CachedArrayList.php';
require_once 'Models/CachedSettings.php';

class Cluster extends Controller {

	private $clusterlist;
	private $pointlist;
	private $settings;
	private $cluster;

	/**
	 * Loads the main cluster analysis site. All user interaction should be done here. Answers in HTML5.
	 */
	public function index(){
		$this->clusterlist = new CachedArrayList(KMeans::CLUSTERLISTNAME);
		$this->settings = new CachedSettings();
		$this->viewmodel->clusterlist = $this->clusterlist;
		$this->viewmodel->settings = $this->settings;

		return $this->View();
	}

	/**
	 * Clears all clusters that has been calculated before. Answers either in json or xml, given in id, that have to be set.
	 * @param String $id
	 */
	public function reset($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->clusterlist = new CachedArrayList(KMeans::CLUSTERLISTNAME);
		$this->clusterlist->clear();
		
		echo json_encode(array("msg" => "The clusterlist is cleared."));
	}

	/**
	 * Runs a new cluster analysis. If a analysis if already run it will continue on that one. Answers either in json or xml, given in id, that have to be set.
	 * @param String $id
	 */
	public function run($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->settings = new CachedSettings();
		$this->cluster = new KMeans($this->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS));
		$this->cluster->setNumberOfPointsToDeterminClusters($this->settings->getSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS));
		$this->cluster->setRandomSelectionOfInitialCluster($this->settings->getSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS));
		$this->cluster->calculateClusters();
		$this->cluster->asignAllPointsToClusters();
		
		echo json_encode(array("msg" => "The clusters are done calculating."));
	}
	
	/**
	 * Forces a new cluster analysis by clearing all previus information first. Answers either in json or xml, given in id, that have to be set. 
	 * @param unknown_type $id
	 */
	public function force($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->settings = new CachedSettings();
		$this->cluster = new KMeans($this->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS));
		$this->cluster->setNumberOfPointsToDeterminClusters($this->settings->getSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS));
		$this->cluster->setRandomSelectionOfInitialCluster($this->settings->getSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS));
		$this->cluster->forceNewAnalysis();
		$this->cluster->calculateClusters();
		$this->cluster->asignAllPointsToClusters();
		echo json_encode(array("msg" => "The new analysis is done."));
	}
	
	/**
	 * Reasigns all points to the defined clusters. Answers either in json or xml, given in id, that have to be set.
	 * @param String $id
	 */
	public function reasign($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->settings = new CachedSettings();
		$this->cluster = new KMeans($this->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS));
		$this->cluster->asignAllPointsToClusters();
		echo json_encode(array("msg" => "All points are now in nearest cluster."));
	}
	
	/**
	 * Gives all of the points that defines cluster sentrums. Answers either in json or xml, given in id, that have to be set.
	 * @param String $id
	 */
	public function points($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->viewmodel->list = new CachedArrayList(KMeans::CLUSTERLISTNAME);
		$this->viewmodel->msg = "Clusterlist transmitted";
		
		return $this->View();
	}

}

?>