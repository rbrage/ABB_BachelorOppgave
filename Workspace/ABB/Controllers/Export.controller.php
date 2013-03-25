<?php

require_once 'Models/CachedArrayList.php';
require_once 'Models/TriggerPoint.php';

class Export extends Controller {
	
	private $pointlist;
	private $filepointer;
	private $filename;
	
	public function csv($id){
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
		
		if($start == $stop){
			$start = 0;
			$stop = $this->pointlist->size();
		}
		
		$this->filename ="Files" . DIRECTORY_SEPARATOR . date("dmYHis") . ".csv";
		$this->filepointer = fopen($this->filename, "x+");
		
		if(!is_resource($this->filepointer)){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "Couldn't make file.";
			return $this->View();
		}
		
		foreach($this->pointlist->iterator() as $point){
			fwrite($this->filepointer, $point->toString() . PHP_EOL);
		}
		
		fclose($this->filepointer);
		
		echo json_encode(array("msg" => "Pointlist created.", "link" => $this->filename));
		
	}
		
	public function dumpfile($id){
		
	}
	
}

?>