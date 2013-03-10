<?php

class TriggerPoint {
	
	public $x;
	public $y; 
	public $z;
	
	public $timestamp;
	
	public $additionalInfo = array();
	
	public function __construct($x = -1, $y = -1, $z = -1, $timestamp = -1){
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		
		$this->timestamp = $timestamp;
	}
	
	public function getX(){
		return $this->x;
	}
	
	public function getY(){
		return $this->y;
	}
	
	public function getZ(){
		return $this->z;
	}
	
	public function getTimestamp(){
		return $this->timestamp;
	}
	
	public function setX($x){
		$this->x = $x;
	}
	
	public function setY($y){
		$this->y = $y;
	}
	
	public function setZ($z){
		$this->z = $z;
	}
	
	public function setTimestamp($timestamp){
		$this->timestamp = $timestamp;
	}
	
	public function addAdditionalInfo($key, $value){
		$this->additionalInfo[$key] = $value;
	}
	
	public function getAdditionalInfo($key = null){
		if($key == null){
			return $this->additionalInfo;
		}
		else {
			return $this->additionalInfo[$key];
		}
	}
	
}