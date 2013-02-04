<?php

require_once("Models/Cache.model.php");

class CachedArrayList {
	
	private $cache;
	const ARRAYLISTLOADED = "CACHEDARRAYLISTLOADED";
	const SIZEKEYWORD = "CACHEDARRAYLIST_SIZE";
	const ARRAYLISTPREFIX = "";
	
	public function __construct(){
		$this->cache = new Cache();
		if(!$this->cache->hasKey(self::ARRAYLISTLOADED)){
			$this->cache->setCacheData(self::ARRAYLISTLOADED, true);
			$this->clear();
		}
	}
	
	/**
	 * Adds a value to the list and stores it in the shared cache. Its posible to lock the data so it dont get temperd with while your script is running.
	 * @param mixed $data
	 * @param boolean $lock
	 */
	public function add($data, $lock = false){
		$size = $this->size();
		$this->lock(self::ARRAYLISTPREFIX . $size);
		
		$response = $this->cache->setCacheData(self::ARRAYLISTPREFIX . $size, $data);
		if($response)
			$this->cache->increase(self::SIZEKEYWORD);
		
		if(!$lock){
			$this->unlock(self::ARRAYLISTPREFIX . $size);
		}
		
		return $response;
	}
	
	/**
	 * Gets a element in the list from the cache. Its posible to lock the data so it dont get temperd with while your script is running.
	 * @param integer $index
	 * @param boolean $lock
	 */
	public function get($index, $lock = false){
		$this->lock(self::ARRAYLISTPREFIX . $index);
		
		$data = $this->cache->getCacheData(self::ARRAYLISTPREFIX . $index);
		
		if(!$lock)
			$this->unlock(self::ARRAYLISTPREFIX . $index);
		
		return $data;
	}
	
	/**
	 * Sets an element in the list. Its posible to lock the data so it dont get temperd with while your script is running.
	 * @param integer $index
	 * @param mixed $data
	 * @param boolean $lock
	 */
	public function set($index, $data, $lock = false){
		$this->lock(self::ARRAYLISTPREFIX . $index);
		
		$this->cache->setCacheData(self::ARRAYLISTPREFIX . $index, $data);
		
		if(!$lock)
			$this->unlock(self::ARRAYLISTPREFIX . $index);
	}
	
	/**
	 * Not yet implemented
	 * @param unknown_type $index
	 */
	public function hasLock($index){
		
	}

	/**
	 * Locks an element in the list.
	 * @param integer $index
	 */
	public function lock($index){
		$this->cache->lock(self::ARRAYLISTPREFIX . $index);
	}
	
	/**
	 * Unlocks an element in the list. 
	 * @param integer $index
	 */
	public function unlock($index){
		$this->cache->unlock(self::ARRAYLISTPREFIX . $index);
	}
	
	/**
	 * Makes the cachedarraylist to set it's startpoint back to zero. This function doesn't remove any data, but new data will overwrite the old data in the cache. 
	 */
	public function clear(){
		$this->setSize(0);
	}
	
	/**
	 * Checks if the list is empty or not.
	 * @return boolean
	 */
	public function isEmpty(){
		return ($this->size() == 0) ? true : false;
	}
	
	/**
	 * Sets a new size of the list in the cache.
	 * @param integer $size
	 */
	private function setSize($size){
		$this->cache->setCacheData(self::SIZEKEYWORD, $size);
	}

	/**
	 * Gives the current size of the cachedarray
	 * @return integer
	 */
	public function size(){
		return $this->cache->getCacheData(self::SIZEKEYWORD);
	}
	
	/**
	 * Gets the cache as a array. This method is not costefficen. It's recemended to use the iterator instead.
	 * Method not implemented yet.
	 * @return multitype:
	 */
	public function toArray(){
		return array();
	}
	
	/**
	 * Not yet implemented.
	 */
	public function iterator(){
		
	}
	
	/**
	 * Makes the cachedarraylist invisible for new instances. The new instance will not get any data from the previous instance, it gets a clean start. However all data previosly stored will remain in memory until it's cleaned from memory.
	 */
	public function unloadList(){
		$this->cache->removeCacheData(self::ARRAYLISTLOADED);
	}
	
	/**
	 * Removes all data in the cache.
	 * Not yet implemented.
	 */
	public function removeAllDataInCache(){
		
	}
}

?>