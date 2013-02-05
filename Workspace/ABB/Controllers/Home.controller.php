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
			
			//Prøver å få til å sortere etter tiden, men gir opp nå for i dag:)
			//print_r($data);
			//usort($obj, array("Home", 'cmp'));
			//print_r($data);
		}
	}
	//Prøver å få til å sortere etter tiden, men gir opp nå for i dag:)
	function cmp($a, $b)
	{
		print_r($b);
		$obj1 = $a["value"];
		$obj2 = $b["value"];
		
		if(  $obj1->timestamp ==  $obj2->timestamp ){
			return 0 ;
		}
		return ($obj1->timestamp < $obj2->timestamp) ? -1 : 1;
	}

}

?>