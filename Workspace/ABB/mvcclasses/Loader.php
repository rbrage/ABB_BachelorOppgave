<?php

class Loader {

	private $controller;
	private $action;
	private $urlvalues;

	public function __construct($urlvalues){
		Debuger::RegisterPoint("Loader constructed", "MVC");
		$this->urlvalues = $urlvalues;
		if($this->urlvalues["controller"] == "")
			$this->controller = "Home";
		else
			$this->controller = ucfirst(strtolower($this->urlvalues["controller"]));
		
		if($this->urlvalues["action"] == "")
			$this->action = "Index";
		else
			$this->action = ucfirst(strtolower($this->urlvalues["action"]));
	}

	public function CreateController(){
		Debuger::RegisterPoint("Creating controller.", "MVC");
		if(class_exists($this->controller)){
			Debuger::RegisterPoint("Found a class consistent with the request.", "MVC");
			$parents = class_parents($this->controller);
			if(in_array("Controller", $parents)){
				Debuger::RegisterPoint("The class is a contoller class.", "MVC");
				if(method_exists($this->controller, $this->action)){
					Debuger::RegisterPoint("Found a method that is consistent with the request and start the controller.", "MVC");
					return new $this->controller($this->action, $this->urlvalues);
				}
				else{
					if(class_exists("Err"))
						return new Err("Err404", $this->urlvalues);
					die("MVC error:\nError: MVC is missing the ErrController.\nSolution: Please place back ErrController in your controller structure!");
				}
			}
			else{
				if(class_exists("Err"))
					return new Err("Err404", $this->urlvalues);
				die("MVC error:\nError: MVC is missing the ErrController.\nSolution: Please place back ErrController in your controller structure!");
			}
		}
		else{
			if(class_exists("Err"))
				return new Err("Err404", $this->urlvalues);
			die("MVC error:\nError: MVC is missing the ErrController.\nSolution: Please place back ErrController in your controller structure!");
		}
	}

}

function __autoload($class){
	Debuger::RegisterPoint("Attemting to autoload " . $class . " class", "MVC");
	$path = "./Controllers/" . $class . ".controller.php";
	if(file_exists($path)){
		Debuger::RegisterPoint("Found the correct file and loades it in to php.", "MVC");
		require_once($path);
	}
}

?>
