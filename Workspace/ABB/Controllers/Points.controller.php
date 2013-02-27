<?php

require_once("Models/CachedArrayList.php");

class Points extends Controller {
	
	public function index(){
		$this->viewmodel->cachedarr = new CachedArrayList();
	}
	
}

?>