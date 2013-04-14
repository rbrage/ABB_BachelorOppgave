<?php

require_once 'Models/CachedArrayList.php';
require_once 'Models/ListNames.php';
require_once 'Models/CachedSettings.php';

class Stat extends Controller {
	
	private $clusterlist;
	private $pointlist;
	private $settings;
	private $cluster;
	
	/**
	 * Gives a page with statistics.
	 */
	public function Index(){
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->settings = new CachedSettings();
		$this->viewmodel->clusterlist = $this->clusterlist;
		$this->viewmodel->settings = $this->settings;
		return $this->View();
	}

	/**
	 * Creates a PDF file with a report over the state of the current test and statistics.
	 */
	public function Createpdf(){
		$this->pointlist = new CachedArrayList();
		$this->viewmodel->listsize = $this->list->size();
	
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->settings = new CachedSettings();
		$this->viewmodel->clusterlist = $this->clusterlist;
		$this->viewmodel->settings = $this->settings;
	
		$this->viewmodel->arr = $this->list->iterator();
		return $this->View();
	}
	
	private $clusterlist;
	private $pointlist;
	private $masterpoint;
	private $outlierlist;
	
	private $cache;
	
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
		
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->pointlist = new CachedArrayList();
		$this->masterpoint = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$this->outlierlist = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);
		
		$this->cache = new Cache();
		
		$maxDistance = array();
		
		foreach ($this->pointlist->iterator() as $point){
			
		}
		
		$this->viewmodel->success = true;
		$this->viewmodel->msg = "The cluster list is cleared.";
		return $this->View();
	}
}

?>