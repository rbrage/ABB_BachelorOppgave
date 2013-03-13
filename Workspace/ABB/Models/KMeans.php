<?php

require_once 'Models/Cache.php';

class KMeans {
	
	private $k = 1;
	private $pointsToCrush;
	private $cache;
	
	public function __construct($k){
		$this->k = $k;
		$this->cache = new Cache();
	}
	
	public function calculateClusters(){
		
	}
	
	public function setNumberOfPointsToDeterminClusters($numberOfPoints){
		$this->pointsToCrush = $numberOfPoints;
	}
	
	private function asignInitialCluster(){
		
	}
	
	private function reasignPointsToClusters(){
		
	}
	
	private function updateClusterCenter(){
		
	}
	
	private function distance($first, $second){
		return sqrt(pow(($first->x - $second->x), 2) + pow(($first->y - $second->y), 2) + pow(($first->z - $second->z), 2));
	}
	
	public function addPoint(){
		
	}
	
	public function forceNewAnalysis(){
		
	}
	
}

?>