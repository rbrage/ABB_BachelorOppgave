<?php

abstract class Controller {

	protected $action;
	protected $urlvalues;
	protected $viewmodel;

	private $viewloc;
	private $templateloc;

	public function __construct($action, $urlvalues){
		Debuger::RegisterPoint("Controller constructed.", "MVC");
		$this->action = $action;
		$this->urlvalues = $urlvalues;
	}

	public function ExecuteAction(){
		Debuger::RegisterPoint("Executing " . $this->action . " method.", "MVC");
		$this->viewmodel = new ViewModel();
		return $this->{$this->action}();
	}

	private $templateAlreadyRun = false;
	protected function Template($template){
		Debuger::RegisterPoint("Called template method.", "MVC");
		if(!$this->templateAlreadyRun){
			Debuger::RegisterPoint("Loading the " . $template . " template.", "MVC");
			$this->templateAlreadyRun = true;
			$this->templateloc = "views/" . $template . ".template.php";
			require_once($this->templateloc);
			Debuger::RegisterPoint("MVC avslutter", "MVC");
			exit();
		}
	}

	protected function ViewBody(){
		Debuger::RegisterPoint("Running the body of the view.", "MVC");
		require($this->viewloc);
	}

	protected function View(){
		Debuger::RegisterPoint("Moving over to the View section.", "MVC");
		$this->viewloc = "views/" . get_class($this) . "/" . $this->action . ".php";
		$viewmodel = $this->viewmodel;
		require_once($this->viewloc);
	}

}

?>