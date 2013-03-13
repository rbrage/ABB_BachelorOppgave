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
		$response = array(	"Request" => array("Success" => true, "Error" => $viewmodel->error, "Message" => "Size gotten."), 
							"Register" => array("Size" => $this->viewmodel->listsize + 0));
		echo json_encode($response);
	}
	elseif($viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Request></Request>");
		$xml->addChild("Success", "true");
		$xml->addChild("Error", $viewmodel->error);
		$xml->addChild("Message", "Size gotten.");
		$reg = $xml->addChild("Register");
		$reg->addAttribute("Size", $this->viewmodel->listsize);

		echo $xml->asXML();
	}
}
?>