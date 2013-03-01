<?php

require_once("Models/CachedArrayList.php");
require_once("Models/TriggerPoint.model.php");

class Points extends Controller {
	
	public function index(){
		$this->viewmodel->cachedarr = new CachedArrayList();
		$this->View();
	}
	
}

?>