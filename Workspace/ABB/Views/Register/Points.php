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
		$response = array("Register" => array("Success" => true, "Message" => "", "Size" => $this->viewmodel->listsize));
		echo json_encode($response);
	}
	elseif($viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Register></Register>");
		$xml->addChild("Success", "true");
		$xml->addChild("Message", "");
		$xml->addChild("Size", $this->viewmodel->stop - $this->viewmodel->start);
		for($i = $this->viewmodel->start; $i < $this->viewmodel->stop && $i < $this->viewmodel->list->size(); $i++){
			$point = $xml->addChild("Point");
			$point->addChild("x", $this->viewmodel->list->get($i)->x);
			$point->addChild("y", $this->viewmodel->list->get($i)->y);
			$point->addChild("z", $this->viewmodel->list->get($i)->z);
			$point->addChild("time", $this->viewmodel->list->get($i)->timestamp);
			$point->addChild("img", $this->viewmodel->list->get($i)->img);
		}

		echo $xml->asXML();
	}
}

?>