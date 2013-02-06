
		<?php
		require_once("mvcclasses/Loader.php");
		require_once("mvcclasses/Controller.php");
		require_once("mvcclasses/ViewModel.php");
		require_once("mvcclasses/Debuger.php");

		//require_once("models/Home.php");

		//require_once("controllers/Home.php");

		Debuger::$SendDebuginfoToBrowser = false;
		//Debuger::SetSendInfoToBrowser("MVC", true);

		$loader = new Loader($_GET);
		$controller = $loader->CreateController();
		$controller->ExecuteAction();
		?>
	