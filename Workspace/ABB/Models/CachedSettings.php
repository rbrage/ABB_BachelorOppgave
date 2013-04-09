<?php

require_once("Models/Cache.php");

class CachedSettings {
	
	const SETTINGSPREFIX = "Settings_";
	const SETTINGSLOADEDTOCACHE = "LoadedToCache";
	const NUMBEROFCLUSTERS = "Clusters";
	const MAXPOINTSINCLUSTERANALYSIS = "MaxClusterPointsInAnalysis";
	const KODETOMASTERPOINTTRIGGERING = "MasterpointTriggeringkode";
	const RUNMASTERCODEINBACKGROUND = "MastercodeInBackground";
	const KODETOTRIGGERPROGRAMSTART = "TriggerProgramStartkode";
	const RUNTRIGGERPROGRAMINBACKGROUND = "TriggerprogramInBackground";
	const ANALYSECLUSTERSWHILESUBMITION = "RuntimeClustering";
	const RANDOMINITIALCLUSTERPOINTS = "RandomInitialClusterPoints";
	const NEXTPOINTASMASTERPOINT = "NextPointAsMaster";
	const OUTLIERCONTROLLDISTANCE = "OutlierControllDistance";
	
	private $defaltvalues = array(
			self::NUMBEROFCLUSTERS => 1,
			self::MAXPOINTSINCLUSTERANALYSIS => 100,
			self::KODETOMASTERPOINTTRIGGERING => "",
			self::KODETOTRIGGERPROGRAMSTART => "",
			self::ANALYSECLUSTERSWHILESUBMITION => false,
			self::RUNMASTERCODEINBACKGROUND => false,
			self::RUNTRIGGERPROGRAMINBACKGROUND => false,
			self::RANDOMINITIALCLUSTERPOINTS => false
			);
	
	private $cache;
	
	public function __construct(){
		$this->cache = new Cache();
		if (!$this->cache->getCacheData(self::SETTINGSPREFIX . self::SETTINGSLOADEDTOCACHE)){
			foreach ($this->defaltvalues as $key => $value){
				$this->cache->setCacheData(self::SETTINGSPREFIX . $key, $value);
			}
			
			$this->cache->setCacheData(self::SETTINGSPREFIX . self::SETTINGSLOADEDTOCACHE, true);
		}
	}
	
	public function getSetting($name){
		return $this->cache->getCacheData(self::SETTINGSPREFIX . $name);
	}
	
	public function setSetting($name, $value){
		return $this->cache->setCacheData(self::SETTINGSPREFIX . $name, $value);
	}
	
	public function getDefaultSettings(){
		return $this->defaltvalues;
	}
	
}

?>