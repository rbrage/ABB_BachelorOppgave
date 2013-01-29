<?php

class Cache {
	
	public $extensionInstalled = false;
	private $lockname = "_lock";
	
	public function __construct(){
		$this->$extensionInstalled = extension_loaded("apc");
	}
	
	public function apc_lock(){
		if(!$this->extensionInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		while(!apc_add($this->lockname)){
			usleep(1000);
		}
	}
	
	public function apc_unlock(){
		if(!$this->extensionInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		apc_delete($this->lockname);
	}
	
	public function getCacheData(){
		if(!$this->extensionInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		
	}
	
	public function setCacheData($values){
		if(!$this->extensionInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		
	}
	
	public function removeCacheData(){
		if(!$this->extensionInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		
	}
	
}

?>