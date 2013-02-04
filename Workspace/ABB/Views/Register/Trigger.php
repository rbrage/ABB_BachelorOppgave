<?php

if($viewmodel->noCoding){
	echo $viewmodel->errmsg;
}
elseif($viewmodel->error){
	if($viewmodel->returnCoding == "json"){
		$response = array("Error" => array("Message" => $viewmodel->errmsg));
		echo json_encode($response);
	}
	elseif($viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Error></Error>");
		$msg = $xml->addChild("Message", $viewmodel->errmsg);
		
		echo $xml->asXML();
	}
	else{
		echo $viewmodel->errmsg;
	}
}
elseif ($viewmodel->success){
	if($viewmodel->returnCoding == "json"){
		$response = array("Registered" => array("Success" => "true", "Message" => "Successfull upload of triggerpoint"));
		echo json_encode($response);
	}
	elseif($viewmodel->returnCoding == "xml"){
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><Registered></Registered>");
		$xml->addChild("Success", "true");
		$xml->addChild("Message", "Successfull upload of triggerpoint");
	
		echo $xml->asXML();
	}
}
else{
	echo "Couldn't find Errors nor get a successfull upload.";
}

?>