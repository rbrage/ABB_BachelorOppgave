<?php

class Cache {
	
	public $apcInstalled;
	public $wincacheInstalled;
	const LOCKSUFFIX = "_lock";
	
	public function __construct(){
		Debuger::SetSendInfoToBrowser("Cache", false);
		if(function_exists("extension_loaded")){
			Debuger::RegisterPoint("Checks if the apc extension is loaded.", "Cache");
			$this->apcInstalled = extension_loaded('apc');
			Debuger::RegisterPoint("Apc extension is " . ($this->apcInstalled ? "" : "not ") . "loaded.", "Cache");
		}
		else 
			Debuger::RegisterPoint("Couldnt check if any extension is loaded.", "Cache");
	}
	
	/**
	 * Puts a lock on spesific key
	 * @param unknown_type $key
	 * @throws Exception
	 */
	public function lock($key){
		Debuger::RegisterPoint("Locking Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		$i = 0;
		while(!apc_add($key . self::LOCKSUFFIX, true)){
			usleep(500);
			$i++;
			if($i > 2000) $this->removeCacheData($key . self::LOCKSUFFIX);
		}
	}
	
	/**
	 * Removes a lock on a spesific key.
	 * @param unknown_type $key
	 * @throws Exception
	 */
	public function unlock($key){
		Debuger::RegisterPoint("Unlocking Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		$this->removeCacheData($key . self::LOCKSUFFIX);
	}
	
	/**
	 * Gets data from the shared cache.
	 * @param unknown_type $key
	 * @throws Exception
	 * @return Ambigous <NULL, unknown>
	 */
	public function getCacheData($key){
		Debuger::RegisterPoint("Gets data from Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		$sucess = false;
		$data = apc_fetch($key, $sucess);
		return ($sucess) ? ($data) : (null);
	}
	
	/**
	 * Sets new data on the spesific key.
	 * @param unknown_type $key
	 * @param unknown_type $data
	 * @throws Exception
	 */
	public function setCacheData($key, $data){
		Debuger::RegisterPoint("Sets data in Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		$success = apc_store($key, $data);
		if(!$success)
			$this->removeCacheData($key);
		return $success;
	}
	
	/**
	 * Removes data from the shared cache with the spesific key.
	 * @param unknown_type $key
	 * @throws Exception
	 * @return boolean
	 */
	public function removeCacheData($key){
		Debuger::RegisterPoint("Removes data from Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		if(!apc_exists($key)) return true;
		return apc_delete($key);
	}
	
	/**
	 * Checks if there is data with the spesific key on the shared cache.
	 * @param unknown_type $key
	 * @throws Exception
	 */
	public function hasKey($key){
		Debuger::RegisterPoint("Checks if the key exists in the Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		return apc_exists($key);
	}
	
	/**
	 * Increases a numeric value in the shared cache.
	 * @param unknown_type $key
	 * @param unknown_type $step
	 * @throws Exception
	 * @return boolean
	 */
	public function increase($key, $step = 1){
		Debuger::RegisterPoint("Increases a value in Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		$success = false;
		apc_inc($key, $step, $success);
		return $success;
	}
	
	/**
	 * Decreases a numeric value in the shared cache
	 * @param unknown_type $key
	 * @param unknown_type $step
	 * @throws Exception
	 * @return boolean
	 */
	public function decrease($key, $step = 1){
		Debuger::RegisterPoint("Decreases a value in Apc cache.", "Cache");
		if(!$this->apcInstalled) throw new Exception("The Extension APC is not installed. Install APC before using this modelclass.");
		$success = false;
		apc_dec($key, $step, $success);
		return $success;
	}
	
}

?>