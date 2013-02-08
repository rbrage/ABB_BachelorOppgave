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
		$arr = array();
		foreach ($this->list->APCIterator() as $data){
			$arr[] = $data["value"];
		}

// 		usort($arr, array("Home", "cmp"));
		$this->viewmodel->arr = $arr;

		return $this->View();
		
		// 			echo "Sorted: \n<br \> x: " . $obj->x . "	 y: " . $obj->y . "	 z: " . $obj->z . "	 time: " . $obj->timestamp . "\n <br \>";
	}

	/**
	 *  Sort function
	 */
	function cmp(&$a, &$b)
	{
		return strcmp($a->timestamp, $b->timestamp);
	}

}

?>