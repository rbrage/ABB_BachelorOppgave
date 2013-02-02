<?php

// ide: apc-arraylist.

class Cache {
	
	public $apcInstalled;
	public $wincacheInstalled;
	private $lockname = "_lock";
	
	public function __construct(){
		Debuger::SetSendInfoToBrowser("Cache", true);
		if(function_exists("extension_loaded")){
			Debuger::RegisterPoint("Checks if the apc extension is loaded.", "Cache");
			$this->apcInstalled = extension_loaded('apc');
			Debuger::RegisterPoint("Apc extension is " . ($this->apcInstalled ? "" : "not ") . "loaded.", "Cache");
		}
		else 
			Debuger::RegisterPoint("Couldnt check if any extension is loaded.", "Cache");
	}
	
	public function lock(){
		Debuger::RegisterPoint("Locking Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		while(!apc_add($this->lockname, true)){
			usleep(500);
		}
	}
	
	public function unlock(){
		Debuger::RegisterPoint("Unlocking Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		apc_delete($this->lockname);
	}
	
	public function getCacheData($key){
		Debuger::RegisterPoint("Gets data from Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		$sucess = false;
		$data = apc_fetch($key, $sucess);
		return ($sucess) ? ($data) : (null);
	}
	
	public function setCacheData($key, $data){
		Debuger::RegisterPoint("Sets data in Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		return apc_store($key, $data);
	}
	
	public function removeCacheData($key){
		Debuger::RegisterPoint("Removes data from Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		if(!apc_key_exists($key)) return true;
		return apc_delete($key);
	}
	
}

?>