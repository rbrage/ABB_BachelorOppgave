<?php

require_once 'Models/Cache.php';
require_once 'Models/CachedArrayList.php';
require_once 'Models/TriggerPoint.php';
require_once 'Models/ListNames.php';
require_once 'Models/ClusterAlgorithm.php';

class KMeans extends ClusterAlgorithm {

	private $k = 1;
	private $pointsToCrush = 100;
	private $cache;
	private $clusterlist;
	private $pointlist;
	private $pointMoved;
	private $randomSelection = false;

	/**
	 * Creates a new instance of the KMeans class. Sets the ini time limit of the script to infinite to make it possible to run large clusters.
	 * @param int $k
	 */
	public function __construct($k){
		set_time_limit(0);
		Debuger::SetupNewDebugID("KMeans");
		Debuger::SetSendInfoToBrowser("KMeans", false);
		$this->k = $k;
		$this->cache = new Cache();
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->pointlist = new CachedArrayList();
	}



	/**
	 * Starts the calculation of the clusters.
	 */
	public function calculateClusters(){
		if(!$this->cache->hasKey(self::CLUSTERANALYSISRUNNINGNAME)){
			$this->cache->setCacheData(self::CLUSTERANALYSISRUNNINGNAME, true);
			Debuger::RegisterPoint("Starting clusteranalysis.", "KMeans");

			$this->asignInitialCluster();

			if($this->k > $this->clusterlist->size()){
				$this->cache->removeCacheData(self::CLUSTERANALYSISRUNNINGNAME);
				return;
			}

			while(true){

				$this->pointMoved = false;
				$this->reasignPointsToClusters();
				$this->updateClusterCenter();
				
				if(!$this->pointMoved)
					break;
			}


			$this->cache->removeCacheData(self::CLUSTERANALYSISRUNNINGNAME);
		}
		else{
			Debuger::RegisterPoint("Clusteranalysis is already running.", "KMeans");
		}
	}

	/**
	 * Sets the max numbers of point to define a cluster.
	 * @param int $numberOfPoints
	 */
	public function setNumberOfPointsToDeterminClusters($numberOfPoints){
		$this->pointsToCrush = $numberOfPoints;
	}

	/**
	 * Sets if the initial clusters should be selected at random.
	 * @param boolean $isRandom
	 */
	public function setRandomSelectionOfInitialCluster($randomSelection){
		$this->randomSelection = $randomSelection;
	}

	/**
	 * Asigns the initial clusters.
	 */
	private function asignInitialCluster(){
		for($i = $this->clusterlist->size(); $i < $this->k && $i < $this->pointlist->size(); $i++){
			Debuger::RegisterPoint("Asigning cluster " . $i, "KMeans");
			$pointnumber = $i;
			if($this->randomSelection){
				$pointnumber = rand(0, $this->pointlist->size()-1);
				$point = $this->pointlist->get($pointnumber, true);
			}
			else
				$point = $this->pointlist->get($pointnumber, true);
			$point->setAsignedCluster($i);
			$this->pointlist->set($pointnumber, $point, true);

			$point->addAdditionalInfo(self::CLUSTERCOUNTNAME, 0);
			$this->clusterlist->set($i, $point);
		}
	}

	/**
	 * Puts a point in its asigned cluster defined with the parameters.
	 * @param int $pointNumber
	 * @param int $clusterNumber
	 */
	private function putPointInCluster($pointNumber, $clusterNumber, $distance){

		$this->pointMoved = true;

		$point = $this->pointlist->get($pointNumber, true);
		$oldclusternumber = $point->getAsignedCluster();
		$point->setAsignedCluster($clusterNumber);
		$point->addAdditionalInfo(self::DISTANCETOCLUSTER, round($distance, 2));
		$this->pointlist->set($pointNumber, $point, true);
	}

	/**
	 * Reasigns all the points to nearest cluster limited to the max points it should go through.
	 */
	private function reasignPointsToClusters(){
		for($i = 0; $i < $this->pointsToCrush && $i < $this->pointlist->size(); $i++){
			Debuger::RegisterPoint("Trying to reasigning point " . $i, "KMeans");
			$point = $this->pointlist->get($i);
			$shortestcluster = 0;
			$shortestdistance = -1;
			foreach ($this->clusterlist->iterator() as $c => $cluster){
				$distance = $this->distance($point, $cluster);
				Debuger::RegisterPoint("Distance to cluster " . $c . ": " . $distance, "KMeans");
				if($distance < $shortestdistance || $shortestdistance < 0){
					$shortestcluster = $c;
					$shortestdistance = $distance;
				}
			}

			$oldcluster = $point->getAsignedCluster();
			Debuger::RegisterPoint("Old cluster is cluster " . $oldcluster, "KMeans");
			if($oldcluster != $shortestcluster){
				Debuger::RegisterPoint("Found a shorter distance to cluster " . $shortestcluster, "KMeans");
				$this->putPointInCluster($i, $shortestcluster, $shortestdistance);
			}
		}
	}

	/**
	 * Reasigns all the points to nearest cluster not limited to the max points it should go through.
	 */
	public function asignAllPointsToClusters(){
		$clustercount = array();
		foreach ($this->pointlist->iterator() as $i => $point){
			Debuger::RegisterPoint("Trying to reasigning point " . $i, "KMeans");
			$shortestcluster = 0;
			$shortestdistance = -1;
			foreach ($this->clusterlist->iterator() as $c => $cluster){
				$distance = $this->distance($point, $cluster);
				Debuger::RegisterPoint("Distance to cluster " . $c . ": " . $distance, "KMeans");
				if($distance < $shortestdistance || $shortestdistance < 0){
					$shortestcluster = $c;
					$shortestdistance = $distance;
				}
			}

			$oldcluster = $point->getAsignedCluster();

			$this->putPointInCluster($i, $shortestcluster, $shortestdistance);

			$clustercount[$shortestcluster] = @$clustercount[$shortestcluster] + 1;
		}

		if(!$this->clusterlist->isEmpty()){
			foreach ($clustercount as $i => $count){
				$cluster = $this->clusterlist->get($i, true);
				$cluster->addAdditionalInfo(self::CLUSTERCOUNTNAME, $count);
				$this->clusterlist->set($i, $cluster, true);
			}
		}
	}

	/**
	 * Calculates the center of a cluster.
	 */
	private function updateClusterCenter(){
		$sumarray = array();
		$countarray = array();

		for($i = 0; $i < $this->k && $i < $this->clusterlist->size(); $i++){
			$sumarray[$i] = array("x" => 0, "y" => 0, "z" => 0);
			$countarray[$i] = 0;
		}

		Debuger::RegisterPoint("Finds the sum of all points.", "KMeans");
		for($i = 0; $i < $this->pointsToCrush && $i < $this->pointlist->size(); $i++){
			$point = $this->pointlist->get($i);
			$cluster = $point->getAsignedCluster();

			Debuger::RegisterPoint("Point " . $i . " is in cluster " . $cluster, "KMeans");
			$sumarray[$cluster]["x"] += $point->x;
			$sumarray[$cluster]["y"] += $point->y;
			$sumarray[$cluster]["z"] += $point->z;

			$countarray[$cluster] += 1;
		}

		Debuger::RegisterPoint("Divides the sum with number of points", "KMeans");
		foreach($sumarray as $cluster => $sum){
			$clusterpoint = $this->clusterlist->get($cluster, true);
			if($countarray[$cluster] > 0){
				$x = $sum["x"]/$countarray[$cluster];
				$y = $sum["y"]/$countarray[$cluster];
				$z = $sum["z"]/$countarray[$cluster];
				
				if($clusterpoint->x != $x || $clusterpoint->y != $y || $clusterpoint->z != $z){
					$this->pointMoved = true;
				}
				
				$clusterpoint->x = $x;
				$clusterpoint->y = $y;
				$clusterpoint->z = $z;

				$clusterpoint->addAdditionalInfo(self::CLUSTERCOUNTNAME, $countarray[$cluster]);
				// 				$clusterpoint->addAdditionalInfo(self::CLUSTERTOUCHEDNAME, false);

			}
			else{
				$clusterpoint = $this->pointlist->get($this->pointlist->size() - 1);
				$clusterpoint->addAdditionalInfo(self::CLUSTERCOUNTNAME, 0);
				// 				$clusterpoint->addAdditionalInfo(self::CLUSTERTOUCHEDNAME, true);

			}

			$this->clusterlist->set($cluster, $clusterpoint, true);

			Debuger::RegisterPoint("Cluster " . $cluster . " has " . $clusterpoint->getAdditionalInfo(self::CLUSTERCOUNTNAME) . " points.", "KMeans");
		}
	}

	/**
	 * Clears any analysis that has been run earlier by clearing any defined clusters and put all points that has been asigned back to cluster 0 limited by max points it should go through.
	 */
	public function forceNewAnalysis(){
		$this->clusterlist->clear();
		$this->cache->removeCacheData(self::CLUSTERANALYSISRUNNINGNAME);
		for($i = 0; $i < $this->pointsToCrush && $i < $this->pointlist->size(); $i++){
			$point = $this->pointlist->get($i, true);
			$point->cluster = 0;
			$this->pointlist->set($i, $point, true);
		}
	}

	/**
	 * Asigns a new point to a defined nearast cluster. Return a boolean to tell if a new analysis should be run.
	 * @param TriggerPoint $point
	 * @return boolean - If you should do a new analysis or not.
	 */
	public function asignCluster(&$point){

		if($this->k > $this->clusterlist->size()){
			return true;
		}
			
		$shortestcluster = 0;
		$shortestdistance = -1;
		foreach ($this->clusterlist->iterator() as $c => $cluster){
			$distance = $this->distance($point, $cluster);
			if($distance < $shortestdistance || $shortestdistance < 0){
				$shortestcluster = $c;
				$shortestdistance = $distance;
			}
		}
		$point->setAsignedCluster($shortestcluster);
		$point->addAdditionalInfo(self::DISTANCETOCLUSTER, $shortestdistance);
		$cluster = $this->clusterlist->get($shortestcluster, true);
		$cluster->addAdditionalInfo(self::CLUSTERCOUNTNAME, $cluster->getAdditionalInfo(self::CLUSTERCOUNTNAME)+1);
		$this->clusterlist->set($shortestcluster, $cluster, true);
			
		if($this->pointlist->size() > $this->pointsToCrush)
			return false;
		return true;
	}

}

?>