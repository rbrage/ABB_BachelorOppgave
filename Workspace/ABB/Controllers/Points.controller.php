<?php

require_once "Models/CachedArrayList.php";
require_once 'Models/CachedSettings.php';
require_once "Models/TriggerPoint.php";
require_once 'Models/ListNames.php';

class Points extends Controller {
	
	/**
	 * Gives the site that gives a overview over all point in the cache.
	 */
	public function Index(){
		$this->viewmodel->pointlist = new CachedArrayList();
		$this->viewmodel->masterlist = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$this->viewmodel->outlierlist = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);
		$this->View();
	}
	
}

?>