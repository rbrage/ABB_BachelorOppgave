<?php

require_once("Models/CachedArrayList.php");
require_once("Models/TriggerPoint.php");

class Points extends Controller {
	
	public function index(){
		$this->viewmodel->cachedarr = new CachedArrayList();
		$this->View();
	}
	
}

?>