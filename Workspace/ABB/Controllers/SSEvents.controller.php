<?php

require_once("Models/CachedArrayList.php");
require_once("Models/SSE.php");

class SSEvents extends Controller {

	private $list;
	private $sse;
	
	public function BasicInfo(){
		$this->list = new CachedArrayList();
		$this->sse = new SSE();
		
		$info = apc_cache_info("user", true);
		
		$this->sse->start();
		
		$oldsize = -1;
		$oldmemory = -1;
		while (true){
			$size = $this->list->size();
			if($oldsize != $size){
				$oldsize = $size;
				$this->sse->sendData("cachesize", $size);
			}
			
			$info = apc_cache_info("user", true);
			$memory = $info["mem_size"]/1000;
			
			if($memory != $oldmemory){
				$oldmemory = $memory;
				$this->sse->sendData("memorysize", $memory);
			}
			
			// sover i 1 sec
			sleep(1);
		}
	}
}

?>