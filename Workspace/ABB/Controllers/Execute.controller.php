<?php

require_once 'Models/CachedSettings.php';

class Execute extends Controller {
	
	private $settings;
	private $command;
	private $inBackground;
	
	/**
	 * Starts up the triggeringprogram. It will answer in either json or xml.
	 */
	public function RunTriggeringProgram($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->settings = new CachedSettings();
		
		$this->command = $this->settings->getSetting(CachedSettings::KODETOTRIGGERPROGRAMSTART);
		$this->inBackground = $this->settings->getSetting(CachedSettings::RUNTRIGGERPROGRAMINBACKGROUND);
		
		$this->RunCommand();
		
		$this->viewmodel->success = true;
		$this->viewmodel->msg = "Triggering program is started.";
		return $this->View();
	}
	
	/**
	 * Will run the code to retrive a masterpoint, and makes the system ready to recive the next point as a masterpoint. It will answer in either json or xml.
	 */
	public function RunMasterpointTriggering($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->errmsg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->settings = new CachedSettings();
		
		$this->command = $this->settings->getSetting(CachedSettings::KODETOMASTERPOINTTRIGGERING);
		$this->inBackground = $this->settings->getSetting(CachedSettings::RUNMASTERCODEINBACKGROUND);
		
		$this->settings->setSetting(CachedSettings::NEXTPOINTASMASTERPOINT, true);
		
		$this->RunCommand();

		$this->viewmodel->success = true;
		$this->viewmodel->msg = "Master point will be put into the system.";
		return $this->View();
	}
	
	/**
	 * Runs a commandline on the computer. 
	 */
	private function RunCommand(){
		
		$commands = explode(PHP_EOL, $this->command);
		
		if($this->isWindows()){
			pclose(popen("start /b " . $this->command . "", "r"));
		}
		else{
			foreach ($commands as $cmd)
				shell_exec("" . $cmd . " /dev/null" . (($this->inBackground)?" &":""));
		}
	}
	
	/**
	 * Cheacks if the the computer that the system is running on is windows or not.
	 * @return boolean
	 */
	private function isWindows(){
		if(PHP_OS == "WIN32" || PHP_OS == "WINNT")
			return true;
		else 
			return false;
	}
	
}