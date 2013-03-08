<?php

include_once("Models/Cache.php");
include_once("Models/TriggerPoint.php");

class TriggerPointRegister {
	
	private $cache;
	private $useLock = false;
	private $waitingForStoreCommand = false;
	const STORAGENAME = "TriggerPoints";
	
	public function __construct(){
		$this->cache = new Cache();
	}
	
	public function lockCacheBetweenFetchAndStore($lock){
		if(!$waitingForStoreCommand)
			$this->useLock = $lock;
	}
	
	public function getTriggerRegister(){
		if($this->useLock){
			$this->waitingForStoreCommand = true;
			$this->cache->lock();
		}
		
		$data = $this->cache->getCacheData(self::STORAGENAME);
		
		if(!is_array($data))
			$data = array();
		
		return $data;
	}
	
	public function storeTriggerRegister($data){
		if(!is_array($data)) return false;
		$response = $this->cache->setCacheData(self::STORAGENAME, $data);
		
		if($this->useLock){
			$this->waitingForStoreCommand = false;
			$this->cache->unlock();
		}
		
		return $response;
	}
	
	public function clearRegister(){
		return $this->cache->removeCacheData(self::STORAGENAME);
	}
	
	public function addTriggerPointToRegister($triggerPoint){
		$this->cache->lock();
		$data = $this->cache->getCacheData(self::STORAGENAME);
		if(!is_array($data))
			$data = array();
		
		$data[] = $triggerPoint;
		$this->cache->setCacheData(self::STORAGENAME, $data);
		$this->cache->unlock();
	}
	
	public function getCache(){
		return $this->cache;
	}
}