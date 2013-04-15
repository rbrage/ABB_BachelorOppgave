<?php

require_once 'Models/CachedArrayList.php';
require_once 'Models/ListNames.php';
require_once 'Models/CachedSettings.php';
require_once 'Models/TriggerPoint.php';
require_once 'Models/KMeans.php';

class Stat extends Controller {
	
	private $clusterlist;
	private $pointlist;
	private $settings;
	private $cluster;
	private $reportName;
	
	/**
	 * Gives a page with statistics.
	 */
	public function Index(){
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->settings = new CachedSettings();
		$this->viewmodel->clusterlist = $this->clusterlist;
		$this->viewmodel->settings = $this->settings;
		$this->viewmodel->cache = new Cache();
		
		return $this->View();
	}

	/**
	 * Creates a PDF file with a report over the state of the current test and statistics.
	 */
	public function Createpdf(){
		$this->pointlist = new CachedArrayList();
		$this->viewmodel->listsize = $this->pointlist->size();
	
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->settings = new CachedSettings();
		$this->viewmodel->clusterlist = $this->clusterlist;
		$this->viewmodel->settings = $this->settings;
	
		$this->viewmodel->cache = new Cache();
		$this->viewmodel->arr = $this->pointlist->iterator();
		
		$this->viewmodel->reportName = $this->settings->getSetting(CachedSettings::REPORTNAME);
		
		
		return $this->View();
	}
	
	private $masterpoint;
	private $outlierlist;
	
	private $cache;
	
	public function RunAnalysis($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->pointlist = new CachedArrayList();
		$this->masterpoint = new CachedArrayList(ListNames::MASTERPOINTLISTNAME);
		$this->outlierlist = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);
		
		$this->cache = new Cache();
		
		$maxDistance = array();
		$distanceFromMaster = array();
		$outliers = array();
		$averageDistance = array();
		$standardDeviation = array();
		
		for($i = 0; $i < $this->clusterlist->size(); $i++){
			$maxDistance[$i] = 0;
			$distanceFromMaster[$i] = 0;
			$outliers[$i] = 0;
			$averageDistance[$i] = 0;
			$standardDeviation[$i] = array("x" => 0, "y" => 0, "z" => 0);
		}
		
		foreach ($this->pointlist->iterator() as $point){
			$distance = $point->getAdditionalInfo(KMeans::DISTANCETOCLUSTER);
			$cluster = $point->cluster;
			
			if($cluster >= $this->clusterlist->size()) continue;
			
			if($distance > $maxDistance[$cluster]){
				$maxDistance[$cluster] = $distance;
			}
			
			$averageDistance[$cluster] += $distance;
			
			$clusterpoint = $this->clusterlist->get($cluster);
			$standardDeviation[$cluster]["x"] += pow($point->x - $clusterpoint->x, 2);
			$standardDeviation[$cluster]["y"] += pow($point->y - $clusterpoint->y, 2);
			$standardDeviation[$cluster]["z"] += pow($point->z - $clusterpoint->z, 2);
		}
		
		foreach ($this->clusterlist->iterator() as $i => $cluster){
			$averageDistance[$i] = round($averageDistance[$i] / $cluster->getAdditionalInfo(KMeans::CLUSTERCOUNTNAME), 2);
			
			$standardDeviation[$i]["x"] = round(sqrt($standardDeviation[$i]["x"]/$cluster->getAdditionalInfo(KMeans::CLUSTERCOUNTNAME)), 2);
			$standardDeviation[$i]["y"] = round(sqrt($standardDeviation[$i]["y"]/$cluster->getAdditionalInfo(KMeans::CLUSTERCOUNTNAME)), 2);
			$standardDeviation[$i]["z"] = round(sqrt($standardDeviation[$i]["z"]/$cluster->getAdditionalInfo(KMeans::CLUSTERCOUNTNAME)), 2);
		}
		
		foreach ($this->outlierlist->iterator() as $outlierpoint){
			$point = $this->pointlist->get($outlierpoint);
			$outliers[$point->cluster]++;
		}
		
		foreach ($this->masterpoint->iterator() as $masterpoint){
			$cluster = $masterpoint->cluster;
			$distanceFromMaster[$cluster] = KMeans::distance($this->clusterlist->get($cluster), $masterpoint);
		}
		
		$this->cache->setCacheData(self::MAXDISTANCE, $maxDistance);
		$this->cache->setCacheData(self::MASTERPOINTDISTANCE, $distanceFromMaster);
		$this->cache->setCacheData(self::OUTLIERS, $outliers);
		$this->cache->setCacheData(self::AVERAGEDISTANCE, $averageDistance);
		$this->cache->setCacheData(self::STANDARDDEVIATION, $standardDeviation);
		
		$this->viewmodel->success = true;
		$this->viewmodel->msg = "Statistics is now calculated.";
		return $this->View();
	}
	
	const MAXDISTANCE = "Statistics_Max_Distance";
	const MASTERPOINTDISTANCE = "Statistics_Distance_To_Masterpoint";
	const OUTLIERS = "Statistics_Outliers";
	const AVERAGEDISTANCE = "Statistics_Average_Distance";
	const STANDARDDEVIATION = "Statistics_Standard_Deviation";
}

?>