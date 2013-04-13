<?php

require_once 'Models/CachedArrayList.php';
require_once 'Models/CachedSettings.php';
require_once 'Models/TriggerPoint.php';
require_once 'Models/KMeans.php';

class Outlier extends Controller {

	private $list;
	private $pointlist;
	private $settings;
	
	/**
	 * Returns all of the outlierpoints that is pressent in the cache. Answers in either json or xml.
	 * @param String $id
	 */
	public function Points($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;

		$this->viewmodel->list = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);
		$this->viewmodel->pointlist = new CachedArrayList();
		$this->viewmodel->msg = "";
		
		$start = 0;
		if(isset($this->urlvalues["start"]))
			$start = $this->urlvalues["start"] + 0;
		
		$stop = 0;
		if(isset($this->urlvalues["stop"]))
			$stop = $this->urlvalues["stop"] + 0;
		
		if(($stop - $start) > 1000){
			$stop = $start + 1000;
//  			$this->viewmodel->error = true;
			$this->viewmodel->msg = "Only possible to get 1000 points each time. Request order sized down to 1000 points.";
		}
		
		if($stop > $this->viewmodel->list->size()){
			$stop = $this->viewmodel->list->size();
			$this->viewmodel->msg = "Your request is outside the cached size. Request order is sized down.";
		}
		
		if($start > $this->viewmodel->list->size()){
			$start = $this->viewmodel->list->size();
			$this->viewmodel->msg = "Your request is outside the cached size. Request order is sized down.";
		}
		
		$this->viewmodel->start = $start;
		$this->viewmodel->stop  = $stop;
		
		return $this->View();
	}
	
	/**
	 * Runs the outlier analysis. Answers in either json or xml.
	 * @param String $id
	 */
	public function RunAnalysis($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->settings = new CachedSettings();
		$this->pointlist = new CachedArrayList();
		$this->list = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);
		$this->list->clear();
		
		$threshold = $this->settings->getSetting(CachedSettings::OUTLIERCONTROLLDISTANCE);
		
		foreach($this->pointlist->iterator() as $i => $point){
			$distance = @$point->getAdditionalInfo(KMeans::DISTANCETOCLUSTER);
			
			if(!is_numeric($distance))
				continue;
			
			if($distance > $threshold){
				$this->list->add($i);
			}
		}

		$this->viewmodel->success = true;
		$this->viewmodel->msg = "The outlier analysis is now done.";
		return $this->View();
	}
	
	/**
	 * Clears the outlierpoint cache. Answers in either json or xml.
	 * @param String $id
	 */
	public function Clear($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->list = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);
		$this->list->clear();

		$this->viewmodel->success = true;
		$this->viewmodel->msg = "The outlier list is now cleared.";
		return $this->View();
	}
}

?>