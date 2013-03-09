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
		$id = $this->urlvalues["id"];
		$this->viewmodel = new ViewModel();
		return $this->{$this->action}($id);
	}

	private $templateAlreadyRun = false;
	protected function Template($template){
		Debuger::RegisterPoint("Called template method.", "MVC");
		if(!$this->templateAlreadyRun){
			Debuger::RegisterPoint("Loading the " . $template . " template.", "MVC");
			$this->templateAlreadyRun = true;
			$this->templateloc = "Views/" . $template . ".template.php";
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
		$this->viewloc = "Views/" . get_class($this) . "/" . $this->action . ".php";
		$viewmodel = &$this->viewmodel;
		$ViewModel = &$this->viewmodel;
		$viewModel = &$this->viewmodel;
		require_once($this->viewloc);
	}

	protected function RedirectTo($url){
		if(!headers_sent())
			header("Location: " . $url);
		else
			throw new Exception("Header has already been sent. Can not redirect.");
	}

}

?>
