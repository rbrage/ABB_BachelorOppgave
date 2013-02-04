<?php

require_once("Models/CachedArrayList.php");
require_once("Models/TriggerPoint.model.php");

class Home extends Controller {
	
	public $list;
	/**
	 * Prints out all triggerpoints that have been registered in the cache.
	 */
	public function Index(){
		$this->list = new CachedArrayList();
		foreach ($this->list->iterator() as $data){
			$obj = $data["value"];
			echo "x: " . $obj->x . "	 y: " . $obj->y . "	 z: " . $obj->z . "	 time: " . $obj->timestamp . "\n";
		}
	}
	
}

?>