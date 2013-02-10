<?php

require_once("Models/CachedArrayList.php");

class SSE extends Controller {

	public function CachedListSize(){
		$this->list = new CachedArrayList();

		$this->viewmodel->listsize = $this->list->size();

		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		ob_flush();
		flush();

		$old = -1;
		while (true){
			$size = $this->list->size();
			if($old != $size){
				$old = $size;
				echo "data: " . $size . PHP_EOL;
				ob_flush();
				flush();
			}
			sleep(1);
		}

		//$this->View();
	}

	protected function View(){

		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');

		parent::View();
	}
}

?>