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
	
		$this->viewmodel->arr = &$this->list->iterator();

		return $this->View();
		
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