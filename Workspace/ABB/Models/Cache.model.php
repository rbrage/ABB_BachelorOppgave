<?php

class Cache {
	
	public $extensionInstalled;
	private $lockname = "_lock";
	
	public function __construct(){
		Debuger::SetSendInfoToBrowser("Cache", true);
		if(function_exists("extension_loaded")){
			Debuger::RegisterPoint("Checks if the extension is loaded.", "Cache");
			$this->extensionInstalled = extension_loaded('wincahce');
			Debuger::RegisterPoint("Is extension loaded? " . ($this->extensionInstalled ? "yes" : "no"), "Cache");
		}
		else 
			Debuger::RegisterPoint("Couldnt check if the extension is loaded.", "Cache");
	}
	
	public function apc_lock(){
		Debuger::RegisterPoint("Locking Apc cache.", "Cache");
		if(!$this->extensionInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		while(!apc_add($this->lockname)){
			usleep(1000);
		}
	}
	
	public function apc_unlock(){
		Debuger::RegisterPoint("Unlocking Apc cache.", "Cache");
		if(!$this->extensionInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		apc_delete($this->lockname);
	}
	
	public function getCacheData($key){
		if(!$this->extensionInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		$sucess = false;
		$data = apc_fetch($key, $sucess);
		return ($sucess) ? ($data) : (null);
	}
	
	public function setCacheData($key, $data){
		if(!$this->extensionInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		return apc_store($key, $data);
	}
	
	public function removeCacheData($key){
		if(!$this->extensionInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		return apc_delete($key);
	}
	
}

?>