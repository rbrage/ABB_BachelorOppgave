<?php

require_once 'Models/CachedArrayList.php';
require_once 'Models/ListNames.php';
require_once 'Models/CachedSettings.php';
require_once 'Models/TriggerPoint.php';
require_once 'Models/ClusterAlgorithm.php';

class Stat extends Controller {
	
	private $clusterlist;
	private $pointlist;
	private $settings;
	
	private $cluster;
	private $reportName;
	
	private $masterpoint;
	private $outlierlist;
	
	private $cache;
	
	const MAXDISTANCE = "Statistics_Max_Distance";
	const MASTERPOINTDISTANCE = "Statistics_Distance_To_Masterpoint";
	const OUTLIERS = "Statistics_Outliers";
	const AVERAGEDISTANCE = "Statistics_Average_Distance";
	const STANDARDDEVIATION = "Statistics_Standard_Deviation";
	const DISTRIBUTION = "Statistics_Distribution";
	const FULLAXIALDISTRIBUTION = "Statistics_Fullaxial_Distribution";
	const DISTRIBUTIONRESOLUTION = 20;
	
	const STATISTICSISRUN = "Statistics_Is_Run";
	
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
	public function Createpdf($id){
		$this->pointlist = new CachedArrayList();
		$this->viewmodel->listsize = $this->pointlist->size();
	
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->settings = new CachedSettings();
		$this->viewmodel->clusterlist = $this->clusterlist;
		$this->viewmodel->settings = $this->settings;
	
		$this->viewmodel->cache = new Cache();
		$this->viewmodel->arr = $this->pointlist->iterator();
		$date = new DateTime();

		$this->viewmodel->reportName = "Trigger Report";
		$this->viewmodel->reportTime = $date->format('d-m-Y H:i');
		$this->viewmodel->comment = @$this->urlvalues["ReportComment"];
		
		return $this->View();
	}
	
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
		$this->settings = new CachedSettings();
		
		
		
		$outlierdistance = $this->settings->getSetting(CachedSettings::OUTLIERCONTROLLDISTANCE);
		
		$maxDistance = array();
		$distanceFromMaster = array();
		$outliers = array();
		$averageDistance = array();
		$standardDeviation = array();
		$distribution = array();
		$fullaxialdistribution = array();
		
		for($i = 0; $i < $this->clusterlist->size(); $i++){
			$maxDistance[$i] = 0;
			$distanceFromMaster[$i] = "No point";
			$outliers[$i] = 0;
			$averageDistance[$i] = 0;
			$standardDeviation[$i] = array("x" => 0, "y" => 0, "z" => 0);
			
			$distribution[$i] = array();
			
			for($j = 0; $j < self::DISTRIBUTIONRESOLUTION; $j++){
				$distribution[$i][$j] = 0;
			}
			
			$fullaxialdistribution[$i] = array();
			
			for($j = 0; $j < self::DISTRIBUTIONRESOLUTION; $j++){
				$fullaxialdistribution[$i][$j] = array("x" => 0, "y" => 0, "z" => 0);
			}
		}
		
		foreach ($this->pointlist->iterator() as $point){
			$distance = $point->getAdditionalInfo(ClusterAlgorithm::DISTANCETOCLUSTER);
			$cluster = $point->cluster;
			
			if($distance === false) continue;
			if($cluster >= $this->clusterlist->size()) continue;
			
			if($distance > $maxDistance[$cluster]){
				$maxDistance[$cluster] = $distance;
			}
			
			$averageDistance[$cluster] += $distance;
			
			$clusterpoint = $this->clusterlist->get($cluster);
			$standardDeviation[$cluster]["x"] += pow($point->x - $clusterpoint->x, 2);
			$standardDeviation[$cluster]["y"] += pow($point->y - $clusterpoint->y, 2);
			$standardDeviation[$cluster]["z"] += pow($point->z - $clusterpoint->z, 2);
			
			$xDistance = $point->x - $clusterpoint->x;
			$yDistance = $point->y - $clusterpoint->y;
			$zDistance = $point->z - $clusterpoint->z;
			if($xDistance < $outlierdistance && $xDistance > -$outlierdistance){
				$i = ($xDistance / $outlierdistance) * (self::DISTRIBUTIONRESOLUTION / 2);
				$fullaxialdistribution[$cluster][$i + (self::DISTRIBUTIONRESOLUTION / 2)]["x"]++;
			}
			if($yDistance < $outlierdistance && $yDistance > -$outlierdistance){
				$i = $yDistance / $outlierdistance * (self::DISTRIBUTIONRESOLUTION / 2);
				$fullaxialdistribution[$cluster][$i + (self::DISTRIBUTIONRESOLUTION / 2)]["y"]++;
			}
			if($zDistance < $outlierdistance && $zDistance > -$outlierdistance){
				$i = $zDistance / $outlierdistance * (self::DISTRIBUTIONRESOLUTION / 2);
				$fullaxialdistribution[$cluster][$i + (self::DISTRIBUTIONRESOLUTION / 2)]["z"]++;
			}
			
			if($distance < $outlierdistance){
				$i = $distance / $outlierdistance * self::DISTRIBUTIONRESOLUTION; 
				$distribution[$cluster][$i]++;
			}
			else {
				$outliers[$cluster]++;
			}
		}
		
		foreach ($this->clusterlist->iterator() as $i => $cluster){
			$averageDistance[$i] = round($averageDistance[$i] / $cluster->getAdditionalInfo(ClusterAlgorithm::CLUSTERCOUNTNAME), 2);
			
			$standardDeviation[$i]["x"] = round(sqrt($standardDeviation[$i]["x"]/$cluster->getAdditionalInfo(ClusterAlgorithm::CLUSTERCOUNTNAME)), 2);
			$standardDeviation[$i]["y"] = round(sqrt($standardDeviation[$i]["y"]/$cluster->getAdditionalInfo(ClusterAlgorithm::CLUSTERCOUNTNAME)), 2);
			$standardDeviation[$i]["z"] = round(sqrt($standardDeviation[$i]["z"]/$cluster->getAdditionalInfo(ClusterAlgorithm::CLUSTERCOUNTNAME)), 2);
		}
		
		foreach ($this->masterpoint->iterator() as $masterpoint){
			$cluster = $masterpoint->cluster;
			if($cluster >= $this->clusterlist->size()) continue;
			$distanceFromMaster[$cluster] = round(ClusterAlgorithm::distance($this->clusterlist->get($cluster), $masterpoint), 2);
		}
		
		$this->cache->setCacheData(self::MAXDISTANCE, $maxDistance);
		$this->cache->setCacheData(self::MASTERPOINTDISTANCE, $distanceFromMaster);
		$this->cache->setCacheData(self::OUTLIERS, $outliers);
		$this->cache->setCacheData(self::AVERAGEDISTANCE, $averageDistance);
		$this->cache->setCacheData(self::STANDARDDEVIATION, $standardDeviation);
		$this->cache->setCacheData(self::DISTRIBUTION, $distribution);
		$this->cache->setCacheData(self::FULLAXIALDISTRIBUTION, $fullaxialdistribution);
		
		$this->cache->setCacheData(self::STATISTICSISRUN, true);
		
		$this->viewmodel->success = true;
		$this->viewmodel->msg = "Statistics is now calculated.";
		return $this->View();
	}
	
	function Clear($id){
		$this->viewmodel->error = false;
		$this->viewmodel->noCoding = false;
		
		if($id != "json" && $id != "xml"){
			$this->viewmodel->error = true;
			$this->viewmodel->msg = "The coding you requested is not recognized.";
			$this->viewmodel->noCoding = true;
			return $this->View();
		}
		$this->viewmodel->returnCoding = $id;
		
		$this->cache = new Cache();
		
		$this->cache->removeCacheData(self::MAXDISTANCE);
		$this->cache->removeCacheData(self::MASTERPOINTDISTANCE);
		$this->cache->removeCacheData(self::OUTLIERS);
		$this->cache->removeCacheData(self::AVERAGEDISTANCE);
		$this->cache->removeCacheData(self::STANDARDDEVIATION);
		$this->cache->removeCacheData(self::DISTRIBUTION);
		$this->cache->removeCacheData(self::FULLAXIALDISTRIBUTION);

		$this->cache->removeCacheData(self::STATISTICSISRUN);
		$this->viewmodel->success = true;
		$this->viewmodel->msg = "The statistics list is now cleared.";
		return $this->View();
	}
}

?>