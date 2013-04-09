<?php

require_once 'Models/CachedArrayList.php';
require_once 'Models/TriggerPoint.php';
require_once 'Models/KMeans.php';
require_once 'Models/CachedSettings.php';

class Home extends Controller {

	public $list;
	
	public function Index(){
		$this->list = new CachedArrayList();

		$this->viewmodel->listsize = $this->list->size();
		
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->settings = new CachedSettings();
		$this->viewmodel->clusterlist = $this->clusterlist;
		$this->viewmodel->settings = $this->settings;	
	
		$this->viewmodel->arr = $this->list->iterator();
		return $this->View();

	}

	public function Plot(){
		$this->list = new CachedArrayList();
		
		$this->viewmodel->listsize = $this->list->size();
		$info = apc_cache_info("user", true);
		$this->viewmodel->listmemory = $info["mem_size"]/1000 . "k";
		
		$this->clusterlist = new CachedArrayList(ListNames::CLUSTERLISTNAME);
		$this->settings = new CachedSettings();
		$this->viewmodel->clusterlist = $this->clusterlist;
		$this->viewmodel->settings = $this->settings;	
	
		$this->viewmodel->arr = $this->list->iterator();
		return $this->View();

	}
	/**
	 * Prints out all triggerpoints that have been registered in the cache.
	 */
	public function Tabell()
	{
		$this->list = new CachedArrayList();
		$this->viewmodel->arr = &$this->list->APCIterator();
		$this->View();
	}

}

?>