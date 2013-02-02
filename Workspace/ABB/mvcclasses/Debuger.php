<?php

class Debuger {

	public static $SendDebuginfoToBrowser = false;
	protected static $DebugIDinfo = array();
	const BrowserArrayNum = 0;
	// not yet implemented
	const FileArrayNum = 1;

	protected static $counter = 0;

	// not yet implemented
	public static $StoreDebuginfoToFile = false;

	public static function RegisterPoint($msg, $id = NULL){
		$id = strtolower($id);
		if(!array_key_exists($id, self::$DebugIDinfo)){
			self::SetupNewDebugID($id);
		}

		if((self::$SendDebuginfoToBrowser && $id == NULL) || (self::$DebugIDinfo[$id][self::BrowserArrayNum]))
			echo "Debugerpoint " . self::$counter . ", at " . microtime() . ": "  . $msg . "\n";

		self::$counter++;
	}

	public static function SetSendInfoToBrowser($id, $visible){
		$id = strtolower($id);
		if(array_key_exists($id, self::$DebugIDinfo)){
			if(is_array(self::$DebugIDinfo[$id]))
				self::$DebugIDinfo[$id][self::BrowserArrayNum] = $visible;
			else
				self::$DebugIDinfo = array(self::BrowserArrayNum => $visible);
		}
		else{
			self::SetupNewDebugID($id);
			self::$DebugIDinfo[$id][self::BrowserArrayNum] = $visible;
		}
	}

	public static function SetupNewDebugID($id){
		$id = strtolower($id);
		self::$DebugIDinfo[$id] = array(self::BrowserArrayNum => false, self::FileArrayNum => false);
	}
}

?>