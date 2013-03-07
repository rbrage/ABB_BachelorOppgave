<?php

require_once("Models/CachedSettings.php");

class Settings extends Controller {
	
	public $settings;
	
	public function Index(){
		$this->settings = new CachedSettings();
		$this->viewmodel->settings = $this->settings;
		$this->View();
	}
	
	public function Cluster(){
		$maxPoints = $this->urlvalues[CachedSettings::MAXPOINTSINCLUSTERANALYSIS];
		$numClusters = $this->urlvalues[CachedSettings::NUMBEROFCLUSTERS];
		$this->settings = new CachedSettings();
		
		if(is_numeric($numClusters))
			if($numClusters > 0)
				$this->settings->setSetting(CachedSettings::NUMBEROFCLUSTERS, $numClusters);

		if(is_numeric($maxPoints))
			if($maxPoints >= $this->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS))
				$this->settings->setSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS, $maxPoints);
		
		return $this->RedirectTo("./");
	}
}

?>