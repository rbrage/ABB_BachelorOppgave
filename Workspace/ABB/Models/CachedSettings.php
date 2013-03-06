<?php

require_once("Models/Cache.model.php");

class CachedSettings {
	
	const SETTINGSPREFIX = "Settings_";
	const NUMBEROFCLUSTERS = "Clusters";
	const MAXPOINTSINCLUSTERANALYSIS = "MaxClusterPointsInAnalysis";
	const KODETOMASTERPOINTTRIGGERING = "MasterpointTriggeringkode";
	const KODETOTRIGGERPROGRAMSTART = "TriggerProgramStartkode";
	const ANALYSECLUSTERSWHILESUBMITION = "";
	
	public $cache;
	
	public function __construct(){
		$this->cache = new Cache();
	}
	
	public function getSetting($name){
		return $this->cache->getCacheData(self::SETTINGSPREFIX . $name);
	}
	
	public function setSetting($name, $value){
		return $this->cache->setCacheData(self::SETTINGSPREFIX . $name, $value);
	}
	
	
	
}

?>