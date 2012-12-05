<?php
	
function createArrays(){
global $vars, $errors, $error_tags, $is_input;
	foreach (func_get_args() as $nam){
		$vars[$nam] = "";
		$errors[$nam] = "";
		$error_tags[$nam] = "";
		$is_input[$nam] = false;
	}
}
	
function setVars (){
global $vars, $is_input;
	foreach ($vars as $key => $value){
		if (isset($_POST[$key])){
			$vars[$key] = $_POST[$key];
			$is_input[$key] = true;
		}
		else
			$vars[$key] = "";
	}
}
?>