<?php

if($viewmodel->noCoding){
	echo $viewmodel->msg;
}
else{
	if($viewmodel->returnCoding == "json"){
		$response = array(	"Request" => array("Success" => $this->viewmodel->success, "Error" => $this->viewmodel->error, "Message" => $viewmodel->msg),
				"Register" => array("Size" => $this->viewmodel->list->size(), "Points" => array()));

		for($i = 0; $i < $this->viewmodel->list->size(); $i++){
			$item = $this->viewmodel->list->get($i);
			$response["Register"]["Points"][] = $item;
		}

		echo json_encode($response);
	}
	elseif($viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Request></Request>");
		$xml->addChild("Success", $this->viewmodel->success);
		$xml->addChild("Error", $viewmodel->error);
		$xml->addChild("Message", $viewmodel->msg);
		$reg = $xml->addChild("Register");
		$reg->addAttribute("Size", $this->viewmodel->list->size());
		for($i = 0; $i < $this->viewmodel->list->size(); $i++){
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