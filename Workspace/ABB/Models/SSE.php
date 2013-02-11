<?php

class SSE {
	
	private $started = false;
	
	public function __construct(){
		@set_time_limit(0);
	}
	
	public function start(){
		$this->started = true;
		
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		ob_implicit_flush();
	}
	
	public function sendData($event, $data){
		// the output has to be started before any data is sent.
		if(!$this->started) return;
		
		echo "event: " . $event . PHP_EOL;
		echo "data: " . $data . PHP_EOL;
		echo PHP_EOL;
		ob_flush();
		flush();
	}
	
}

?>