<?php

class Master extends Controller {
	
	private $list;
	
	function Points($id){
		$this->list = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
	}
	
	function Add($id){
		$this->list = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$x = $this->urlvalues["x"];
		$y = $this->urlvalues["y"];
		$z = $this->urlvalues["z"];
	}
	
	function Set($id){
		$this->list = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$x = $this->urlvalues["x"];
		$y = $this->urlvalues["y"];
		$z = $this->urlvalues["z"];
		$number = $this->urlvalues["pointid"];
	}
	
	function Remove($id){
		$this->list = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$number = $this->urlvalues["pointid"];
	}
	
	function Clear($id){
		$this->list = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$this->list->clear();
	}
	
	function AsignToCluster($id){
		
	}
}

?>