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
	
	private function distance(){
		
	}
	
	public function addPoint(){
		
	}
	
	public function forceNewAnalysis(){
		
	}
	
}

?>