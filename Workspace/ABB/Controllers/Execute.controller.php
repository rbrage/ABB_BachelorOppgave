<?php

require_once 'Models/CachedSettings.php';

class Execute extends Controller {
	
	private $settings;
	private $command;
	private $inBackground;
	
	public function RunTriggeringProgram(){
		$this->settings = new CachedSettings();
		
		$this->command = $this->settings->getSetting(CachedSettings::KODETOTRIGGERPROGRAMSTART);
		$this->inBackground = $this->settings->getSetting(CachedSettings::RUNTRIGGERPROGRAMINBACKGROUND);
		
		$this->RunCommand();
		
		echo json_encode(array("msg" => "Triggering program is started."));
	}
	
	public function RunMasterpointTriggering(){
		$this->settings = new CachedSettings();
		
		$this->command = $this->settings->getSetting(CachedSettings::KODETOMASTERPOINTTRIGGERING);
		$this->inBackground = $this->settings->getSetting(CachedSettings::RUNMASTERCODEINBACKGROUND);
		
		$this->RunCommand();
	}
	
	private function RunCommand(){
		if($this->isWindows()){
			pclose(popen("start /b " . $this->command, "r"));
		}
		else{
			pclose(popen("" . $this->command . " /dev/null" . (($this->inBackground)?" &":""), "r"));
		}
	}
	
	private function isWindows(){
		if(PHP_OS == "WIN32" || PHP_OS == "WINNT")
			return true;
		else 
			return false;
	}
	
}