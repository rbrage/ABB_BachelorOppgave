<?php

require_once 'Models/CachedArrayList.php';
require_once 'Models/TriggerPoint.php';

class Export extends Controller {
	
	private $pointlist;
	private $filepointer;
	private $filename;
	
	public function PointsToCSV($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->pointlist = new CachedArrayList();
		$this->viewmodel->msg = "";
		
		$start = 0;
		if(isset($this->urlvalues["start"]))
			$start = $this->urlvalues["start"] + 0;
		
		$stop = 0;
		if(isset($this->urlvalues["stop"]))
			$stop = $this->urlvalues["stop"] + 0;
		
		if($stop > $this->pointlist->size()){
			$stop = $this->pointlist->size();
		}
		
		if($stop < 0){
			$stop = 0;
		}
		
		if($start > $this->pointlist->size()){
			$stop = $this->pointlist->size();
		}
		
		if($start < 0){
			$stop = 0;
		}
		
		if($start == $stop){
			$start = 0;
			$stop = $this->pointlist->size();
		}
		
		if(!is_dir("files")){
			if(!mkdir("files")){
				$this->viewmodel->error = true;
				$this->viewmodel->errmsg = "Internal error. Couldn't get premission to make storage directoy.";
				$this->viewmodel->noCoding = true;
				return $this->View();
			}
		}
		
		$this->filename ="files" . DIRECTORY_SEPARATOR . date("d-m-Y His") . ".csv";
		$this->filepointer = fopen($this->filename, "w+");
		
		if(!is_resource($this->filepointer)){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "Couldn't make file.";
			return $this->View();
		}
		
		for($i = $start; $i < $stop; $i++){
			$point = $this->pointlist->get($i);
			fwrite($this->filepointer, $point->toString() . PHP_EOL);
		}
		
		fclose($this->filepointer);
		
		echo json_encode(array("msg" => "Pointlist created.", "link" => DIRECTORY_SEPARATOR . ($this->filename), "success" => true));
		
	}
		
	public function dumpfile($id){
		
	}
	
}

?>