<?php

require_once("Models/CachedArrayList.php");
require_once("Models/TriggerPoint.php");

class Points extends Controller {
	
	public function Index(){
		$this->viewmodel->cachedarr = new CachedArrayList();
		$this->View();
	}
	
}

?>