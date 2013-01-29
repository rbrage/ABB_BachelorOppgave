<?php

class Test extends Controller {
	
	public function Index(){
		Debuger::RegisterPoint("Index method in Test class called.");
		$this->View();
	}
	
	public function Testing(){
		Debuger::RegisterPoint("Test method in Test class called.");
		$this->View();
	}
}

?>