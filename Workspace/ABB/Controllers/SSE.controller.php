<?php

require_once("Models/CachedArrayList.php");

class SSE extends Controller {
	
	public function __construct($action, $urlvalues){
		parent::__construct($action, $urlvalues);
		@set_time_limit(0);
		
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		ob_implicit_flush();
		
	}

	public function BasicInfo(){
		$this->list = new CachedArrayList();
		
		$info = apc_cache_info("user", true);
		$this->viewmodel->listmemory = $info["mem_size"]/1000 . "k";
		
		$oldsize = -1;
		$oldmemory = -1;
		while (true){
			$size = $this->list->size();
			if($oldsize != $size){
				$oldsize = $size;
				echo "event: cachesize" . PHP_EOL;
				echo "data: " . $size . PHP_EOL;
				echo PHP_EOL;
				ob_flush();
				flush();
			}
			
			$info = apc_cache_info("user", true);
			$memory = $info["mem_size"]/1000;
			
			if($memory != $oldmemory){
				$oldmemory = $memory;
				echo "event: memorysize" . PHP_EOL;
				echo "data: " . $memory . PHP_EOL;
				echo PHP_EOL;
				ob_flush();
				flush();
			}
			
			sleep(1);
		}
	}
}

?>