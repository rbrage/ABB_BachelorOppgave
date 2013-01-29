<?php

class Cache {
	
	public $extensionInstalled = false;
	private $lockname = "_lock";
	
	public function __construct(){
		$this->$extensionInstalled = extension_loaded("apc");
	}
	
	public function lock(){
		while(!apc_add($this->lockname)){
			usleep(1000);
		}
	}
	
	public function apc_unlock(){
		apc_delete($this->lockname);
	}
	
	public function getCacheData(){
		
	}
	
	public function setCacheData($values){
		
	}
	
	public function removeCacheData(){
		
	}
	
}

?>