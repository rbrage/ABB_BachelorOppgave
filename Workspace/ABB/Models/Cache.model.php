<?php

class Cache {
	
	public $apcInstalled;
	public $wincacheInstalled;
	const LOCKSUFFIX = "_lock";
	
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
	
	public function lock($key){
		Debuger::RegisterPoint("Locking Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		while(!apc_add($key . self::LOCKSUFFIX, true)){
			usleep(500);
		}
	}
	
	public function unlock($key){
		Debuger::RegisterPoint("Unlocking Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		apc_delete($key . self::LOCKSUFFIX);
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
	
	public function hasKey($key){
		Debuger::RegisterPoint("Checks if the key exists in the Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		return apc_key_exists($key);
	}
	
	public function increase($key, $step = 1){
		Debuger::RegisterPoint("Increases a value in Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		$success = false;
		apc_inc($key, $step, $success);
		return $success;
	}
	
	public function decrease($key, $step = 1){
		Debuger::RegisterPoint("Decreases a value in Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		$success = false;
		apc_dec($key, $step, $success);
		return $success;
	}
	
}

?>