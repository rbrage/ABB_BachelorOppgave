<?php

require_once("Models/CachedArrayList.php");
require_once("Models/TriggerPoint.model.php");

class Register extends Controller {
	
	private $list;
	/**
	 * Register a trigger point and puts it in the shared cache.
	 * URL examples:
	 * /register/trigger/json?x=1&y=2&z=3&time=456789
	 * /register/trigger/xml?x=1&y=2&z=3&time=456789
	 * /register/trigger/json?x=1&y=2&z=3&time=456789&img=./folder/file.jpg
	 * /register/trigger/xml?x=1&y=2&z=3&time=456789&img=./folder/file.jpg
	 * @param String $id
	 */
	public function Trigger($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		if(!array_key_exists("x", $this->urlvalues) || !array_key_exists("y", $this->urlvalues) || !array_key_exists("z", $this->urlvalues)){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "Coordinates is missing. Remember to set the x,y and z proberties on the request.";
			return $this->View();
		}
		
		if(!array_key_exists("time", $this->urlvalues)){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "Timestamp is missing. Remember to set the time probertie on the request.";
			return $this->View();
		}
		
		if(!is_numeric($this->urlvalues["x"]) || !is_numeric($this->urlvalues["x"]) || !is_numeric($this->urlvalues["x"])){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "One of the elements in the coordinates is not a number.";
			return $this->View();
		}
		
		if(!is_numeric($this->urlvalues["time"])){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The time is not a number.";
			return $this->View();
		}
		
		$this->list = new CachedArrayList();
		
		if(array_key_exists("img", $this->urlvalues)){
			// please implement a check on the image!
			$data = new TriggerPoint($this->urlvalues["x"], $this->urlvalues["y"], $this->urlvalues["z"], $this->urlvalues["time"], $this->urlvalues["img"]);
		}
		else 
			$data = new TriggerPoint($this->urlvalues["x"], $this->urlvalues["y"], $this->urlvalues["z"], $this->urlvalues["time"]);
		
		$success = $this->list->add($data);
		
		if($success){
			$this->viewmodel->success = true;
			return $this->View();
		}
		else{
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "Couldn't put the triggerpoint in to memory.";
			return $this->View();
		}
		
	}
	
	/**
	 * Sends the size of the cachelist.
	 * URL examples:
	 * /register/size/json
	 * /register/size/xml
	 * @param String $id
	 */
	public function Size($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->list = new CachedArrayList();
		
		$this->viewmodel->listsize = $this->list->size();
		
	}
	
	public function Points($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->viewmodel->list = new CachedArrayList();
		
		$start = 0;
		if(isset($this->urlvalues["start"]))
			$start = $this->urlvalues["start"];
		
		$stop = 0;
		if(isset($this->urlvalues["stop"]))
			$stop = $this->urlvalues["stop"];
		
		if($stop - $start > 1000){
			$stop = $start + 1000;
		}
		
		$this->viewmodel->start = $start;
		$this->viewmodel->stop  = $stop;
		
		$this->View();
		
	}
	
}

?>