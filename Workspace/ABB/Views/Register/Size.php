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
		$response = array("Register" => array("Success" => true, "Message" => "Size gotten.", "Size" => $this->viewmodel->listsize));
		echo json_encode($response);
	}
	elseif($viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Register></Register>");
		$xml->addChild("Success", "true");
		$xml->addChild("Message", "Size gotten.");
		$xml->addChild("Size", $this->viewmodel->listsize);

		echo $xml->asXML();
	}
}
?>