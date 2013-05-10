<?php

require_once 'Models/ListNames.php';
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
	public function Index(){
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->settings = new CachedSettings();
		$this->viewmodel->clusterlist = $this->clusterlist;
		$this->viewmodel->settings = $this->settings;

		return $this->View();
	}

	/**
	 * Clears all clusters that has been calculated before. Answers either in json or xml, given in id, that have to be set.
	 * @param String $id
	 */
	public function Clear($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		//$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		//$this->clusterlist->clear();

		$this->cluster = new KMeans(1);
		$this->cluster->clearAnalysis();

		$this->viewmodel->success = true;
		$this->viewmodel->msg = "The cluster list is cleared.";
		return $this->View();
	}

	/**
	 * Runs a new cluster analysis. If a analysis if already run it will continue on that one. Answers either in json or xml, given in id, that have to be set.
	 * @param String $id
	 */
	public function RunAnalysis($id){
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

		$this->viewmodel->success = true;
		$this->viewmodel->msg = "The cluster analysis is done.";
		return $this->View();
	}
	
	/**
	 * Forces a new cluster analysis by clearing all previus information first. Answers either in json or xml, given in id, that have to be set. 
	 * @param String $id
	 */
	public function ForceAnalysis($id){
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
		$this->cluster->clearAnalysis();
		$this->cluster->calculateClusters();
		$this->cluster->asignAllPointsToClusters();
		
		$this->viewmodel->success = true;
		$this->viewmodel->msg = "The new cluster analysis is done.";
		return $this->View();
	}
	
	/**
	 * Reasigns all points to the defined clusters. Answers either in json or xml, given in id, that have to be set.
	 * @param String $id
	 */
	public function ReasignPoints($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;

		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		
		if($this->clusterlist->isEmpty()){
			$this->viewmodel->error = true;
			$this->viewmodel->success = false;
			$this->viewmodel->msg = "Run the cluster analysis first.";
			return $this->View();
		}
		
		$this->settings = new CachedSettings();
		$this->cluster = new KMeans($this->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS));
		$this->cluster->asignAllPointsToClusters();

		$this->viewmodel->success = true;
		$this->viewmodel->msg = "All points is now asigned to the nearest cluster.";
		return $this->View();
	}
	
	/**
	 * Gives all of the points that defines cluster sentrums. Answers either in json or xml, given in id, that have to be set.
	 * @param String $id
	 */
	public function Points($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->viewmodel->list = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->viewmodel->msg = "Clusterlist transmitted";
		
		return $this->View();
	}

}

?>