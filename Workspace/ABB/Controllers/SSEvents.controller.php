<?php

require_once 'Models/Cache.php';
require_once 'Models/CachedArrayList.php';
require_once 'Models/SSE.php';
require_once 'Models/KMeans.php';

class SSEvents extends Controller {

	private $pointlist;
	private $clusterlist;
	private $sse;
	private $cache;
	
	public function BasicInfo(){
		$this->pointlist = new CachedArrayList();
		$this->clusterlist = new CachedArrayList(KMeans::CLUSTERLISTNAME);
		$this->sse = new SSE();
		$this->cache = new Cache();
		
		$info = $this->cache->getCacheInfo();
		
		$this->sse->start();
		
		$oldpointsize = -1;
		$oldmemory = -1;
		$oldclustersize = -1;
		while (true){
			$size = $this->pointlist->size();
			if($oldpointsize != $size){
				$oldpointsize = $size;
				$this->sse->sendData("pointsize", $size);
			}
			
			$size = $this->clusterlist->size();
			if($oldclustersize != $size){
				$oldclustersize = $size;
				$this->sse->sendData("clustersize", $size);
			}
			
			$info = $this->cache->getCacheInfo();
			$memory = round($info["mem_size"]/1000, 2);
			
			if($memory != $oldmemory){
				$oldmemory = $memory;
				$this->sse->sendData("usedmemory", $memory);
			}
			
			// sover i 1 sec
			sleep(1);
		}
	}
}

?>