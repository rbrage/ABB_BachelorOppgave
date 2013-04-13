<?php

require_once("Models/CachedSettings.php");

class Settings extends Controller {

	public $settings;

	/**
	 * Gives the settings page. 
	 */
	public function Index(){
		$this->settings = new CachedSettings();
		$this->viewmodel->settings = $this->settings;
		$this->View();
	}

	/**
	 * Saves new cluster settings.
	 * @param String $id
	 */
	public function Cluster($id){
		$randomInitClusters = @$this->urlvalues[CachedSettings::RANDOMINITIALCLUSTERPOINTS];
		$clusterAtRuntime = @$this->urlvalues[CachedSettings::ANALYSECLUSTERSWHILESUBMITION];
		$maxPoints = @$this->urlvalues[CachedSettings::MAXPOINTSINCLUSTERANALYSIS];
		$numClusters = @$this->urlvalues[CachedSettings::NUMBEROFCLUSTERS];
		$this->settings = new CachedSettings();

		if($randomInitClusters == "on"){
			$this->settings->setSetting(CachedSettings::RANDOMINITIALCLUSTERPOINTS, true);
		}
		else{
			$this->settings->setSetting(CachedSettings::RANDOMINITIALCLUSTERPOINTS, false);
		}
		if($clusterAtRuntime == "on"){
			$this->settings->setSetting(CachedSettings::ANALYSECLUSTERSWHILESUBMITION, true);
		}
		else{
			$this->settings->setSetting(CachedSettings::ANALYSECLUSTERSWHILESUBMITION, false);
		}

		if(is_numeric($numClusters))
			if($numClusters > 0)
				$this->settings->setSetting(CachedSettings::NUMBEROFCLUSTERS, $numClusters);

		if(is_numeric($maxPoints))
			if($maxPoints >= $this->settings->getSetting(CachedSettings::NUMBEROFCLUSTERS))
				$this->settings->setSetting(CachedSettings::MAXPOINTSINCLUSTERANALYSIS, $maxPoints);

		return $this->RedirectTo("./");
	}

	/**
	 * Saves new msterpoint settings.
	 * @param String $id
	 */
	public function MasterPoint($id){
		$background = @$this->urlvalues[CachedSettings::RUNMASTERCODEINBACKGROUND];
		$code = @$this->urlvalues[CachedSettings::KODETOMASTERPOINTTRIGGERING];
		$this->settings = new CachedSettings();

		if($background == "on"){
			$this->settings->setSetting(CachedSettings::RUNMASTERCODEINBACKGROUND, true);
		}
		else{
			$this->settings->setSetting(CachedSettings::RUNMASTERCODEINBACKGROUND, false);
		}

		$this->settings->setSetting(CachedSettings::KODETOMASTERPOINTTRIGGERING, $code);

		return $this->RedirectTo("./");
	}

	/**
	 * Saves new settings for the masterprogram.
	 * @param String $id
	 */
	public function Triggerprogram($id){
		$background = @$this->urlvalues[CachedSettings::RUNTRIGGERPROGRAMINBACKGROUND];
		$code = @$this->urlvalues[CachedSettings::KODETOTRIGGERPROGRAMSTART];
		$this->settings = new CachedSettings();

		if($background == "on"){
			$this->settings->setSetting(CachedSettings::RUNTRIGGERPROGRAMINBACKGROUND, true);
		}
		else{
			$this->settings->setSetting(CachedSettings::RUNTRIGGERPROGRAMINBACKGROUND, false);
		}

		$this->settings->setSetting(CachedSettings::KODETOTRIGGERPROGRAMSTART, $code);

		return $this->RedirectTo("./");
	}
	
	/**
	 * Saves new settings for outlying points.
	 * @param String $id
	 */
	public function Outlier($id){
		$threshold = @$this->urlvalues[CachedSettings::OUTLIERCONTROLLDISTANCE];
		$this->settings = new CachedSettings();

		$this->settings->setSetting(CachedSettings::OUTLIERCONTROLLDISTANCE, $threshold);
		
		return $this->RedirectTo("./");
	}
}

?>