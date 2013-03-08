<?php

if($viewmodel->noCoding){
	echo $viewmodel->errmsg;
}
elseif($viewmodel->error){
	if($viewmodel->returnCoding == "json"){
		$response = array("Request" => array("Success" => false, "Error" => true, "Message" => $viewmodel->errmsg));
		echo json_encode($response);
	}
	elseif($viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Request></Request>");
		$xml->addChild("Success", true);
		$xml->addChild("Error", true);
		$xml->addChild("Message", $viewmodel->errmsg);

		echo $xml->asXML();
	}
	else{
		echo $viewmodel->errmsg;
	}
}
else{
	if($viewmodel->returnCoding == "json"){
		$response = array(	"Request" => array("Success" => true, "Error" => $this->viewmodel->error, "Message" => $viewmodel->msg),
							"Register" => array("Size" => ($this->viewmodel->stop - $this->viewmodel->start), "Start" => $this->viewmodel->start, "Stop" => $this->viewmodel->stop));
		
		$points = array();
		for($i = $this->viewmodel->start; $i < $this->viewmodel->stop && $i < $this->viewmodel->list->size(); $i++){
			$item = $this->viewmodel->list->get($i);
			$points[] = array("x" => $item->x, "y" => $item->y, "z" => $item->z, "timestamp" => $item->timestamp, "additionalinfo" => $item->getAdditionalInfo());
		}
		$response["Register"]["Points"] = $points;
		echo json_encode($response);
	}
	elseif($viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Request></Request><Register></Register>");
		$xml->addChild("Success", "true", "Request");
		$xml->addChild("Error", $viewmodel->error, "Request");
		$xml->addChild("Message", $viewmodel->msg, "Request");
		$xml->addChild("Size", $this->viewmodel->stop - $this->viewmodel->start, "Register");
		$xml->addChild("Start", $this->viewmodel->start, "Register");
		$xml->addChild("Stop", $this->viewmodel->stop - $this->viewmodel->start, "Register");
		for($i = $this->viewmodel->start; $i < $this->viewmodel->stop && $i < $this->viewmodel->list->size(); $i++){
			$point = $xml->addChild("Point", "", "Register");
			$item = $this->viewmodel->list->get($i);
			$point->addChild("x", $item->x);
			$point->addChild("y", $item->y);
			$point->addChild("z", $item->z);
			$point->addChild("time", $item->timestamp);
			$additionalinfo = $item->getAditionalInfo();
			$info = $point->addChild("additionalinfo");
			foreach ($additionalinfo as $key => $value){
				$info->addChild($key, $value);
			}
		}

		echo $xml->asXML();
	}
}

?>