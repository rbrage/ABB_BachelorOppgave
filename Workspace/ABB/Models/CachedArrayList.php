<?php

require_once("Models/Cache.php");
require_once("Models/CacheIterator.php");

class CachedArrayList implements arrayaccess {
	
	private $cache;
	private $listprefix;
	const ARRAYLISTLOADED = "LOADED";
	const SIZEKEYWORD = "SIZE";
	const ARRAYLISTPREFIX = "CACHEDARRAYLIST";
	
	public function __construct($listname = self::ARRAYLISTPREFIX){
		$this->listprefix = $listname;
		$this->cache = new Cache();
		if(!$this->cache->hasKey($this->listprefix . "_" . self::ARRAYLISTLOADED)){
			$this->cache->setCacheData($this->listprefix . "_" . self::ARRAYLISTLOADED, true);
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
		$this->lock($this->listprefix . "_" . $size);
		
		$response = $this->cache->setCacheData($this->listprefix . "_" . $size, $data);
		if($response)
			$this->cache->increase($this->listprefix . "_" . self::SIZEKEYWORD);
		
		if(!$lock){
			$this->unlock($this->listprefix . "_" . $size);
		}
		
		return $response;
	}
	
	/**
	 * Gets a element in the list from the cache. Its posible to lock the data so it dont get temperd with while your script is running.
	 * @param integer $index
	 * @param boolean $lock
	 */
	public function get($index, $lock = false){
		$this->lock($this->listprefix . "_" . $index);
		
		$data = $this->cache->getCacheData($this->listprefix . "_" . $index);
		
		if(!$lock)
			$this->unlock($this->listprefix . "_" . $index);
		
		return $data;
	}
	
	/**
	 * Sets an element in the list. Its posible to lock the data so it dont get temperd with while your script is running.
	 * @param integer $index
	 * @param mixed $data
	 * @param boolean $lock
	 */
	public function set($index, $data, $lock = false){
		$this->lock($this->listprefix . "_" . $index);
		
		$this->cache->setCacheData($this->listprefix . "_" . $index, $data);
		
		if(!$lock)
			$this->unlock($this->listprefix . "_" . $index);
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
		$this->cache->lock($this->listprefix . "_" . $index);
	}
	
	/**
	 * Unlocks an element in the list. 
	 * @param integer $index
	 */
	public function unlock($index){
		$this->cache->unlock($this->listprefix . "_" . $index);
	}
	
	/**
	 * Makes the cachedarraylist to set it's startpoint back to zero. This function doesn't remove any data, but new data will overwrite the old data in the cache. 
	 */
	public function clear(){
		return $this->setSize(0);
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
		return $this->cache->setCacheData($this->listprefix . "_" . self::SIZEKEYWORD, $size);
	}

	/**
	 * Gives the current size of the cachedarray
	 * @return integer
	 */
	public function size(){
		return $this->cache->getCacheData($this->listprefix . "_" . self::SIZEKEYWORD);
	}
	
	/**
	 * Returns a CacheIterator that can iterate over the list. This iterator gives the list in the correct order.
	 * @return CacheIterator
	 */
	public function iterator(){
		return new CacheIterator($this);
	}
	
	/**
	 * Returns a APCIterator that can iterate over the list. This comes with apc info, and the list may not come in the correct order. The iterator gives what it fined first in memory.
	 * @return APCIterator
	 */
	public function APCIterator(){
		return new APCIterator('user', '/^' . $this->listprefix . "_" . '[\d]*$/', APC_ITER_ALL, $this->size());
	}
	
	/**
	 * Makes the cachedarraylist invisible for new instances. The new instance will not get any data from the previous instance, it gets a clean start. However all data previosly stored will remain in memory until it's cleaned from memory.
	 */
	public function unloadList(){
		$this->cache->removeCacheData($this->listprefix . "_" . self::ARRAYLISTLOADED);
	}
	
	/**
	 * Removes all data in the cache.
	 * Not yet implemented.
	 */
	public function removeAllDataInCache(){
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset){
		return ($this->size() > $offset);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset){
		return ($this->size() > $offset) ? $this->get($offset) : null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value){
		if(is_null($offset)){
			$this->add($value);
		}
		else{
			$this->set($offset, $value);
		}
	}
	
	/**
	 * Not yet implemented
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset){
		
	}
	
}

?>