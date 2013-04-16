<?php

require_once 'Models/ListNames.php';
require_once 'Models/CachedArrayList.php';
require_once 'Models/TriggerPoint.php';
require_once 'Models/ClusterAlgorithm.php';

class Master extends Controller {

	private $list;
	private $clusterlist;

	/**
	 * Returns all of the masterpoints that is pressent in the cache. Answers in either json or xml.
	 * @param String $id
	 */
	function Points($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;

		$this->viewmodel->list = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$this->viewmodel->msg = "";
		$this->viewmodel->success = true;
		
		return $this->View();
	}

	/**
	 * Adds a masterpoint to the cache. Answers in either json or xml. It needs the URL-parameters x, y and z.
	 * @param String $id
	 */
	function Add($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->list = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$x = floatval(@$this->urlvalues["x"]);
		$y = floatval(@$this->urlvalues["y"]);
		$z = floatval(@$this->urlvalues["z"]);
		
		if(!is_numeric($x) || !is_numeric($y) || !is_numeric($z)){
			$this->viewmodel->success = false;
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "One or more of the coordinates is missing. New masterpoint was not added to the list.";
			return $this->View();
		}
		
		$point = new TriggerPoint($x, $y, $z);
		$this->viewmodel->success = $this->list->add($point);
		if($this->viewmodel->success){
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "Couldn't put the new point into cache.";
		}
		else{
			$this->viewmodel->msg = "New master point successfully added to the list.";
		}
		
		return $this->View();
	}

	/**
	 * Sets new values on a masterpoint. Answers in either json or xml. It needs the URL-parameters pointid, x, y and z.
	 * @param String $id
	 */
	function Set($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->list = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$x = floatval(@$this->urlvalues["x"]);
		$y = floatval(@$this->urlvalues["y"]);
		$z = floatval(@$this->urlvalues["z"]);
		$number = intval(@$this->urlvalues["pointid"]);
		
		if($number < 0 || $number >= $this->list->size()){
			$this->viewmodel->error = true;
			$this->viewmodel->success = false;
			$this->viewmodel->msg = "The point ID was outside the list range. No new data is set.";
			return $this->View();
		}
		
		$point = $this->list->get($number, true);
		
		$point->x = $x;
		$point->y = $y;
		$point->z = $z;
		
		$this->list->set($number, $point, true);
		$this->viewmodel->success = true;
		$this->viewmodel->msg = "New data stored in given point.";
		
		return $this->View();
	}

	/**
	 * Removes a masterpoint from the cache. Answers in either json or xml. Needs the URL-parameter pointid.
	 * @param String $id
	 */
	function Remove($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->list = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$number = intval(@$this->urlvalues["pointid"]);
		
		if($number >= 0 && $number < $this->list->size()){
			$this->list->remove($number);
			$this->viewmodel->success = true;
			$this->viewmodel->msg = "The point is now removed from the list.";
			return $this->View();
		}
		else{
			$this->viewmodel->error = true;
			$this->viewmodel->success = false;
			$this->viewmodel->msg = "Couldn't remove the point from the list. Point ID was out of range.";
			return $this->View();
		}
	}

	/**
	 * Clears the masterpoint cache. Answers in either json or xml.
	 * @param String $id
	 */
	function Clear($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->list = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$this->list->clear();

		$this->viewmodel->success = true;
		$this->viewmodel->msg = "The master point list is now cleared";
		return $this->View();
	}

	/**
	 * Finds the nearest cluster to each masterpoint. Answers in either json or xml.
	 * @param String $id
	 */
	function AsignToCluster($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->list = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		
		if($this->clusterlist->isEmpty()){
			$this->viewmodel->error = true;
			$this->viewmodel->success = false;
			$this->viewmodel->msg = "There is no clusters to compare against. Run the cluster analysis first.";
			return $this->View();
		}

		for($i = 0; $i < $this->list->size(); $i++){
			$master = $this->list->get($i, true);
			$shortestcluster = 0;
			$shortestdistance = -1;
			foreach ($this->clusterlist->iterator() as $c => $cluster){
				$distance = ClusterAlgorithm::distance($master, $cluster);
				if($distance < $shortestdistance || $shortestdistance < 0){
					$shortestcluster = $c;
					$shortestdistance = $distance;
				}
			}
			
			$master->setAsignedCluster($shortestcluster);
			$this->list->set($i, $master, true);
		}
		
		$this->viewmodel->success = true;
		$this->viewmodel->msg = "Master points now asigned to clusters.";
		return $this->View();
	}
}

?>