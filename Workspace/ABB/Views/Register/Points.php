<?php

if($viewmodel->noCoding){
	echo $viewmodel->errmsg;
}
elseif($viewmodel->error){
	if($viewmodel->returnCoding == "json"){
		$response = array("Register" => array("Error" => true, "Message" => $viewmodel->errmsg));
		echo json_encode($response);
	}
	elseif($viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Register></Register>");
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
		$response = array("Register" => array("Success" => true, "Message" => $viewmodel->msg, "Size" => ($this->viewmodel->stop - $this->viewmodel->start), "Start" => $this->viewmodel->start));
		$points = array();
		for($i = $this->viewmodel->start; $i < $this->viewmodel->stop && $i < $this->viewmodel->list->size(); $i++){
			$item = $this->viewmodel->list->get($i);
			$points[] = array("x" => $item->x, "y" => $item->y, "z" => $item->z, "timestamp" => $item->timestamp, "img" => $item->img);
		}
		$response["Register"]["Points"] = $points;
		echo json_encode($response);
	}
	elseif($viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Register></Register>");
		$xml->addChild("Success", "true");
		$xml->addChild("Message", $viewmodel->msg);
		$xml->addChild("Size", $this->viewmodel->stop - $this->viewmodel->start);
		for($i = $this->viewmodel->start; $i < $this->viewmodel->stop && $i < $this->viewmodel->list->size(); $i++){
			$point = $xml->addChild("Point");
			$item = $this->viewmodel->list->get($i);
			$point->addChild("x", $item->x);
			$point->addChild("y", $item->y);
			$point->addChild("z", $item->z);
			$point->addChild("time", $item->timestamp);
			$point->addChild("img", $item->img);
		}

		echo $xml->asXML();
	}
}

?>