<?php

require_once 'Models/CachedArrayList.php';
require_once 'Models/CachedSettings.php';
require_once 'Models/TriggerPoint.php';
require_once 'Models/KMeans.php';

class Outlier extends Controller {

	private $list;
	private $pointlist;
	private $settings;
	
	public function Points($id){
		$this->list = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);
	}
	
	public function RunAnalysis($id){
		$this->settings = new CachedSettings();
		$this->pointlist = new CachedArrayList();
		$this->list = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);
		$this->list->clear();
		
		$threshold = $this->settings->getSetting(CachedSettings::OUTLIERCONTROLLDISTANCE);
		
		foreach($this->pointlist->iterator() as $i => $point){
			$distance = @$point->getAdditionalInfo(KMeans::DISTANCETOCLUSTER);
			
			if(!is_numeric($distance))
				continue;
			
			if($distance > $threshold){
				$this->list->add($i);
			}
		}
	}

	public function Clear($id){
		$this->list = new CachedArrayList(ListNames::OUTLYINGPOINTLISTNAME);
		$this->list->clear();
	}
}

?>