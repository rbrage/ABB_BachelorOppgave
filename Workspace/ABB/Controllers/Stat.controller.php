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
		$this->viewmodel->listsize = $this->list->size();
	
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->settings = new CachedSettings();
		$this->viewmodel->clusterlist = $this->clusterlist;
		$this->viewmodel->settings = $this->settings;
	
		$this->viewmodel->arr = $this->list->iterator();
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
		$clusterCount = array();
		$standardDeviation = array();
		
		foreach ($this->pointlist->iterator() as $point){
			$distance = $point->getAdditionalInfo(KMeans::DISTANCETOCLUSTER);
			$cluster = $point->cluster;
			
			if($distance > @$maxDistance[$cluster]){
				$maxDistance[$cluster] = $distance;
			}
			
			@$averageDistance[$cluster] += $distance;
			@$clusterCount[$cluster]++;
			
			//@$standardDeviation[$cluster] = $
		}
		
		foreach ($this->clusterlist->iterator() as $i => $cluster){
			$averageDistance[$i] /= $cluster->getAdditionalInfo(KMeans::CLUSTERCOUNTNAME);
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