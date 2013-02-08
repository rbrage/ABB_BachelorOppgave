<?php

class CacheIterator implements Iterator {
	
	private $position;
	private $cachedArrayList;
	
	public function __construct(CachedArrayList &$cachedArrayList){
		$this->position = 0;
		$this->cachedArrayList = &$cachedArrayList;
	}
	
	public function current(){
		return $this->cachedArrayList->get($this->position);
	}
	
	public function key(){
		return $this->position;
	}
	
	public function next(){
		++$this->position;
	}
	
	public function rewind(){
		$this->position = 0;
	}
	
	public function valid(){
		return ($this->cachedArrayList->size() > $this->position);
	}
	
}