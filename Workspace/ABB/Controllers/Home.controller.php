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
		foreach ($this->list->iterator() as $data){
			$arr[] = $data["value"];
		}
// 			$obj = $data["value"];
// 			echo "x: " .$obj->x . "	 y: " . $obj->y . "	 z: " . $obj->z . "	 time: " . $obj->timestamp . "\n <br \>";
				
			print_r($arr);
			usort($arr, array($this,"cmp"));
			echo "Sorted: \n<br \> x: " . $obj->x . "	 y: " . $obj->y . "	 z: " . $obj->z . "	 time: " . $obj->timestamp . "\n <br \>";
// 		}
	}
	//Sort function
	function cmp(&$a, &$b)
	{
		return strcmp($a->timestamp, $b->timestamp);
	}

}

?>