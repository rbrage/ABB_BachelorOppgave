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
	 * /register/trigger/(json|xml)?x=1&y=2&z=3&time=456789&optionaldelimiter=,&optionalkey=key1,key2,key3&optionalvalue=value1,value2,value3
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
		
		$data = new TriggerPoint($this->urlvalues["x"], $this->urlvalues["y"], $this->urlvalues["z"], $this->urlvalues["time"]);
		if(array_key_exists("optionalkey", $this->urlvalues) && array_key_exists("optionalvalue", $this->urlvalues)){
			$keystring = $this->urlvalues["optionalkey"];
			$valuestring = $this->urlvalues["optianlvalue"];
			if(array_key_exists("optionaldelimiter", $this->urlvalues)){
				$delimiter = $this->urlvalues["optionaldelimiter"];
			}
			else{
				$delimiter = ",";
			}
			
			$keys = explode($delimiter, $keystring);
			$values = explode($delimiter, $valuestring);
			
			$i = 0;
			foreach ($keys as $key){
				$value = $values[$i];
				$data->addAdditionalInfo($key, $value);
				$i++;
			}
		}
		
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
	
	/**
	 * Sends requested amount of triggerpoint to user/system, from defined startpoint.
	 * URL examples:
	 * /register/points/json 	"gets zero points, shows coding options"
	 * /register/points/xml 	"gets zero points, shows coding options"
	 * /register/points/(json|xml)?start=[number] 	"Gets points from start number to end of list. Max 1000 points"
	 * /register/points/(json|xml)?stop=[number] 	"Gets points from zero to stop number. Max 1000 points"
	 * /register/points/(json|xml)?start=[number]&stop=[number] 	"Gets points from start number to stop number. Max 1000 points"
	 * @param String $id
	 */
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
		$this->viewmodel->msg = "";
		
		$start = 0;
		if(isset($this->urlvalues["start"]))
			$start = $this->urlvalues["start"] + 0;
		
		$stop = 0;
		if(isset($this->urlvalues["stop"]))
			$stop = $this->urlvalues["stop"] + 0;
		
		if(($stop - $start) > 1000){
			$stop = $start + 1000;
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "Only possible to get 1000 points each time. Request order sized down to 1000 points.";
		}
		
		if($stop >= $this->viewmodel->list->size())
			$stop = $this->viewmodel->list->size();
		
		$this->viewmodel->start = $start;
		$this->viewmodel->stop  = $stop;
		
		$this->View();
		
	}
	
}

?>