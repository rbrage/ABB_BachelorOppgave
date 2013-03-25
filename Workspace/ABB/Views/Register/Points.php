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
		$xml->addChild("Success", false);
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
			$points[] = array("x" => floatval($item->x), "y" => floatval($item->y), "z" => floatval($item->z), "cluster" => intval($item->cluster), "timestamp" => doubleval($item->timestamp), "additionalinfo" => $item->getAdditionalInfo());
		}
		$response["Register"]["Points"] = $points;
		echo json_encode($response);
	}
	elseif($viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Request></Request>");
		$xml->addChild("Success", "true");
		$xml->addChild("Error", $viewmodel->error);
		$xml->addChild("Message", $viewmodel->msg);
		$reg = $xml->addChild("Register");
		$reg->addAttribute("Size", $this->viewmodel->stop - $this->viewmodel->start);
		$reg->addAttribute("Start", $this->viewmodel->start);
		$reg->addAttribute("Stop", $this->viewmodel->stop - $this->viewmodel->start);
		for($i = $this->viewmodel->start; $i < $this->viewmodel->stop && $i < $this->viewmodel->list->size(); $i++){
			$point = $reg->addChild("Point");
			$item = $this->viewmodel->list->get($i);
			if(get_class($item) == "TriggerPoint"){
				$point->addChild("x", $item->x);
				$point->addChild("y", $item->y);
				$point->addChild("z", $item->z);
				$point->addChild("cluster", $item->cluster);
				$point->addChild("time", $item->timestamp);
				$additionalinfo = $item->getAdditionalInfo();
				$info = $point->addChild("additionalinfo");
				foreach ($additionalinfo as $key => $value){
					$info->addChild($key, $value);
				}
			}
			
		}

		echo $xml->asXML();
	}
}

?>