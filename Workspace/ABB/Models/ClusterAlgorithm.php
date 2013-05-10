<?php

abstract class ClusterAlgorithm {
	
	const CLUSTERLISTNAME = "CLUSTERANALYSIS";
	const CLUSTERANALYSISRUNNINGNAME = "CLUSTERANALYSISRUNNING";
	const CLUSTERCOUNTNAME = "CLUSTERCOUNT";
	const DISTANCETOCLUSTER = "DistanceToCluster";
	
	/**
	 * Starts the calculation of the clusters.
	 */
	public abstract function calculateClusters();
	
	/**
	 * Sets the max numbers of point to define a cluster.
	 * @param int $numberOfPoints
	 */
	public abstract function setNumberOfPointsToDeterminClusters($numberOfPoints);
	
	/**
	 * Reasigns all the points to nearest cluster not limited to the max points it should go through.
	 */
	public abstract function asignAllPointsToClusters();
	
	/**
	 * Clears any analysis that has been run earlier by clearing any defined clusters and put all points that has been asigned back to cluster 0 limited by max points it should go through.
	 */
	public abstract function clearAnalysis();
	
	/**
	 * Asigns a new point to a defined nearast cluster. Return a boolean to tell if a new analysis should be run.
	 * @param TriggerPoint $point
	 * @return boolean - If you should do a new analysis or not.
	 */
	public abstract function asignCluster(&$point);

	/**
	 * Gives the distance between two points.
	 * @param TriggerPoint $first
	 * @param TriggerPoint $second
	 * @return float
	 */
	public static function distance($first, $second){
		return sqrt(pow(($first->x - $second->x), 2) + pow(($first->y - $second->y), 2) + pow(($first->z - $second->z), 2));
	}
	
}

?>