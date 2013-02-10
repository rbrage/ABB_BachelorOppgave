<?php

class Test extends Controller {

	public function Index(){
		Debuger::RegisterPoint("Index method in Test class called.");
		$this->View();
	}

	public function Testing(){
		Debuger::RegisterPoint("Test method in Test class called.");
		require_once("Models/CachedArrayList.php");
		$this->list = new CachedArrayList();
		$this->viewmodel->arr = $this->list->iterator();
		$this->View();
	}

	public function Phpinfo(){
		$this->View();
	}

	public function apc(){
		include("Models/Cache.model.php");
		$cache = new Cache();
		$cache->unlock();
		$cache->lock();
		$cache->unlock();
	}

	public function ext(){
		var_dump(get_loaded_extensions());
	}

	public function shm(){
		$shm_id = shmop_open(0xff3, "c", 0644, 100);
		if (!$shm_id) {
			echo "Couldn't create shared memory segment\n";
		}
	}

	public function unlock(){
		include("Models/Cache.model.php");
		$cache = new Cache();
		$cache->unlock();
	}

	public function lock(){
		include("Models/Cache.model.php");
		$cache = new Cache();
		$cache->lock();
	}

	public function exec(){
		exec("start");
	}

	public function addTP(){
		include("Models/TriggerPointRegister.php");
		$tpreg = new TriggerPointRegister();
		$tp = new TriggerPoint();
		$tpreg->addTriggerPointToRegister($tp);
		echo sizeof($tpreg->getTriggerRegister());
	}

	public function fillTP(){
		echo microtime() . "\n";
		include("Models/TriggerPointRegister.php");
		$tpreg = new TriggerPointRegister();
		$tp = new TriggerPoint();
		for($i = 0; $i < 1000; $i++)
		$tpreg->addTriggerPointToRegister($tp);
		echo microtime();
	}
	
	public function memUse(){
		$info = apc_cache_info("user", true);
		print_r(($info["mem_size"]/1000) . "k");
	}
	
	public function listsize(){
		require_once("Models/CachedArrayList.php");
		$list = new CachedArrayList();
		echo $list->size() . "<br />";
		$this->memUse();
	}
}

?>