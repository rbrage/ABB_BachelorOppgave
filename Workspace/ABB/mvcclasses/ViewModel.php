<?php

class ViewModel {
	
	private $data = array();

	public function Add($name, &$value){
		$this->$name = &$value;
	}
	
	public function __get($name){
		if(array_key_exists($name, $this->data))
			return $this->data[$name];
		else
			return "No value";
	}
	
	public function __set($name, $value){
		$this->data[$name] = $value;
	}
	
	public function __isset($name){
		return isset($this->data[$name]);
	}
	
	public function __unset($name){
		unset($this->data[$name]);
	}

}

?>
