<?php

if($this->viewmodel->noCoding){
	echo $this->viewmodel->msg;
}
else{
	if($this->viewmodel->returnCoding == "json"){
		$response = array(	"Request" => array("Success" => $this->viewmodel->success, "Error" => $this->viewmodel->error, "Message" => $this->viewmodel->msg));


		echo json_encode($response);
	}
	elseif($this->viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Request></Request>");
		$xml->addChild("Success", $this->viewmodel->success);
		$xml->addChild("Error", $this->viewmodel->error);
		$xml->addChild("Message", $this->viewmodel->msg);

		echo $xml->asXML();
	}
}

?>