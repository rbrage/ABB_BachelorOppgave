<?php

class Test extends Controller {

	public function Index(){
		Debuger::RegisterPoint("Index method in Test class called.");
		$this->View();
	}

	public function Testing(){
		Debuger::RegisterPoint("Test method in Test class called.");
		$this->View();
	}

	public function Phpinfo(){
		$this->View();
	}

	public function apc(){
		include("Models/Cache.model.php");
		$cache = new Cache();
		$cache->apc_unlock();
		$cache->apc_lock();
		$cache->apc_unlock();
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
		$cache->apc_unlock();
	}

	public function lock(){
		include("Models/Cache.model.php");
		$cache = new Cache();
		$cache->apc_lock();
	}
}

?>