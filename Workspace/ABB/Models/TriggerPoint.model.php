<?php

class TriggerPoint {
	
	public $x;
	public $y; 
	public $z;
	
	public $timestamp;
	public $img;
	
	public function __construct($x = -1, $y = -1, $z = -1, $timestamp = -1, $img = null){
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		
		$this->timestamp = $timestamp;
		$this->img = $img;
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
	
	public function getTimestemp(){
		return $this->timestamp;
	}
	
	public function getImg(){
		return $this->img;
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
	
	public function setTimestemp($timestamp){
		$this->timestamp = $timestamp;
	}
	
	public function setImg($img){
		$this->img = $img;
	}
	
}