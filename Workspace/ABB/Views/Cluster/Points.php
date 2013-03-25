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
							"Cluster" => array("Size" => $this->viewmodel->list->size()));

		$points = array();
		for($i = 0; $i < $this->viewmodel->list->size(); $i++){
			$item = $this->viewmodel->list->get($i);
			$points[] = array("clusterID" => $i, "x" => round($item->x, 3), "y" => round($item->y, 3), "z" => round($item->z, 3), "connections" => $item->getAdditionalInfo(KMeans::CLUSTERCOUNTNAME));
		}
		$response["Cluster"]["Points"] = $points;
		echo json_encode($response);
	}
	elseif($viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Request></Request>");
		$xml->addChild("Success", "true");
		$xml->addChild("Error", $viewmodel->error);
		$xml->addChild("Message", $viewmodel->msg);
		$reg = $xml->addChild("Cluster");
		$reg->addAttribute("Size", $this->viewmodel->list->size());
		for($i = 0; $i < $this->viewmodel->list->size(); $i++){
			$point = $reg->addChild("Point");
			$item = $this->viewmodel->list->get($i);
			if(get_class($item) == "TriggerPoint"){
				$point->addChild("clusterID", $i);
				$point->addChild("x", round($item->x, 3));
				$point->addChild("y", round($item->y, 3));
				$point->addChild("z", round($item->z, 3));
				$point->addChild("connections", $item->getAdditionalInfo(KMeans::CLUSTERCOUNTNAME));
			}
				
		}

		echo $xml->asXML();
	}
}

?>