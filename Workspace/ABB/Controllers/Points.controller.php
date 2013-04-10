<?php

require_once "Models/CachedArrayList.php";
require_once "Models/TriggerPoint.php";
require_once 'Models/ListNames.php';

class Points extends Controller {
	
	public function Index(){
		$this->viewmodel->pointlist = new CachedArrayList();
		$this->viewmodel->masterlist = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$this->viewmodel->outlierlist = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);
		$this->View();
	}
	
}

?>