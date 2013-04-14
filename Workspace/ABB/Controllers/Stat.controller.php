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
		$this->list = new CachedArrayList();
		$this->viewmodel->listsize = $this->list->size();
	
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->settings = new CachedSettings();
		$this->viewmodel->clusterlist = $this->clusterlist;
		$this->viewmodel->settings = $this->settings;
	
		$this->viewmodel->arr = $this->list->iterator();
		return $this->View();
	}
}

?>