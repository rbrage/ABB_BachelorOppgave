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
			echo "x: " . $obj->x . "	 y: " . $obj->y . "	 z: " . $obj->z . "	 time: " . $obj->timestamp . "\n <br \>";

			
		//Prøver å få til å sortere etter tiden
			//print_r($data);
			//usort($data, array($this,"cmp"));
			//echo "Sorted: \n<br \> x: " . $obj->x . "	 y: " . $obj->y . "	 z: " . $obj->z . "	 time: " . $obj->timestamp . "\n <br \>";
		}
	}
	//Prøver å få til å sortere etter tiden
	function cmp($a, $b)
	{
		return strcmp($a->timestamp, $b->timestamp);
	}

}

?>