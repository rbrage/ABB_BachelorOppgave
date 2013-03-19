<?php

require_once 'Models/Cache.php';
require_once 'Models/CachedArrayList.php';
require_once 'Models/TriggerPoint.php';

class KMeans {
	
	private $k = 1;
	private $pointsToCrush = 100;
	private $cache;
	private $clusterlist;
	private $pointlist;
	
	const CLUSTERLISTNAME = "CLUSTERANALYSIS";
	const CLUSTERANALYSISRUNNINGNAME = "CLUSTERANALYSISRUNNING";
	const CLUSTERTOUCHEDNAME = "Touched";
	const CLUSTERCOUNTNAME = "CLUSTERCOUNT";
// 	const CLUSTERARRAYNAME = "Clusterarray";
// 	const CLUSTERHASINITIALCLUSTER = "ClusterHasInitialClusters";
	
	
	public function __construct($k){
		Debuger::SetupNewDebugID("KMeans");
		Debuger::SetSendInfoToBrowser("KMeans", false);
		$this->k = $k;
		$this->cache = new Cache();
		$this->clusterlist = new CachedArrayList(self::CLUSTERLISTNAME);
		$this->pointlist = new CachedArrayList();
	}
	
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

				$this->updateClusterCenter();
				$this->reasignPointsToClusters();
					
				$tuched = false;
				foreach($this->clusterlist->iterator() as $cluster){
					if($cluster->getAdditionalInfo(self::CLUSTERTOUCHEDNAME)){
						$tuched = true;
						break;
					}
				}
					
				if(!$tuched)
					break;

			}
			
			$this->cache->removeCacheData(self::CLUSTERANALYSISRUNNINGNAME);
		}
		else{
			Debuger::RegisterPoint("Clusteranalysis is already running.", "KMeans");
		}
	}
	
	public function setNumberOfPointsToDeterminClusters($numberOfPoints){
		$this->pointsToCrush = $numberOfPoints;
	}
	
	private function asignInitialCluster(){
		for($i = $this->clusterlist->size(); $i < $this->k && $i < $this->pointlist->size(); $i++){
			Debuger::RegisterPoint("Asigning cluster " . $i, "KMeans");
			$point = $this->pointlist->get($i, true);
			$point->setAsignedCluster($i);
			$this->pointlist->set($i, $point, true);
			
			$point->addAdditionalInfo(self::CLUSTERTOUCHEDNAME, false);
			$point->addAdditionalInfo(self::CLUSTERCOUNTNAME, 0);
// 			$point->addAdditionalInfo(self::CLUSTERARRAYNAME, array());
			$this->clusterlist->set($i, $point);
		}
	}
	
	private function putPointInCluster($pointNumber, $clusterNumber){
		$point = $this->pointlist->get($pointNumber, true);
		$oldclusternumber = $point->getAsignedCluster();
		$point->setAsignedCluster($clusterNumber);
		$this->pointlist->set($pointNumber, $point, true);
		
		// adding point to new cluster
		$clusterpoint = $this->clusterlist->get($clusterNumber);
		if($pointNumber < $this->pointsToCrush)
			$clusterpoint->addAdditionalInfo(self::CLUSTERTOUCHEDNAME, true);
// 		$clusterarray = $clusterpoint->getAdditionalInfo(self::CLUSTERARRAYNAME);
// 		$clusterarray[$pointNumber] = $pointNumber;
// 		$clusterpoint->addAdditionalInfo(self::CLUSTERARRAYNAME, $clusterarray);
		$this->clusterlist->set($clusterNumber, $clusterpoint);
		
		// removing point from old cluster
		$clusterpoint = $this->clusterlist->get($oldclusternumber);
		if($pointNumber < $this->pointsToCrush)
			$clusterpoint->addAdditionalInfo(self::CLUSTERTOUCHEDNAME, true);
// 		$clusterarray = $clusterpoint->getAdditionalInfo(self::CLUSTERARRAYNAME);
// 		$clusterarray[$pointNumber] = -1;
// 		$clusterpoint->addAdditionalInfo(self::CLUSTERARRAYNAME, $clusterarray);
		$this->clusterlist->set($oldclusternumber, $clusterpoint);
	}
	
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
				$this->putPointInCluster($i, $shortestcluster);
			}
		}
	}
	
	public function asignAllPointsToClusters(){
		foreach ($this->pointlist->iterator() as $i => $point){
			Debuger::RegisterPoint("Trying to reasigning point " . $i, "KMeans");
			$shortestcluster = 0;
			$shortestdistance = -1;
			foreach ($this->clusterlist as $c => $cluster){
				$distance = $this->distance($point, cluster);
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
				$this->putPointInCluster($i, $shortestcluster);
			}
		}
	}
	
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
				$clusterpoint->x = $sum["x"]/$countarray[$cluster];
				$clusterpoint->y = $sum["y"]/$countarray[$cluster];
				$clusterpoint->z = $sum["z"]/$countarray[$cluster];
				
				$clusterpoint->addAdditionalInfo(self::CLUSTERCOUNTNAME, $countarray[$cluster]);
			}
			else{
				$clusterpoint->x = 0;
				$clusterpoint->y = 0;
				$clusterpoint->z = 0;
				
				$clusterpoint->addAdditionalInfo(self::CLUSTERCOUNTNAME, 0);
			}
			
			$clusterpoint->addAdditionalInfo(self::CLUSTERTOUCHEDNAME, false);
			$this->clusterlist->set($cluster, $clusterpoint, true);
				
			Debuger::RegisterPoint("Cluster " . $cluster . " has " . $clusterpoint->getAdditionalInfo(self::CLUSTERCOUNTNAME) . " points.", "KMeans");
		}
	}
	
	private function distance($first, $second){
		return sqrt(pow(($first->x - $second->x), 2) + pow(($first->y - $second->y), 2) + pow(($first->z - $second->z), 2));
	}
	
	public function forceNewAnalysis(){
		$this->clusterlist->clear();
		$this->cache->removeCacheData(self::CLUSTERANALYSISRUNNINGNAME);
	}
	
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
		$cluster = $this->clusterlist->get($shortestcluster, true);
		$cluster->addAdditionalInfo(self::CLUSTERCOUNTNAME, $cluster->getAdditionalInfo(self::CLUSTERCOUNTNAME)+1);
		$this->clusterlist->set($shortestcluster, $cluster, true);
			
		if($this->pointlist->size() > $this->pointsToCrush)
			return false;
		return true;
	}
	
}

?>