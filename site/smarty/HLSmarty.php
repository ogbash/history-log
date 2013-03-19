<?php
define ('SMARTY_DIR', 'C:/Program Files (x86)/Apache Software Foundation/Apache2.2/smarty/');
require_once(SMARTY_DIR.'Smarty.class.php');

class MySmarty extends Smarty {
	function MySmarty() {
		parent::__construct();
		$this->template_dir = 'C:/Projects/history-log/site/smarty/templates/';
		$this->compile_dir = 'C:/Projects/history-log/site/smarty/templates_c/';
		$this->config_dir = 'C:/Projects/history-log/site/smarty/configs/';
		$this->cache_dir = 'C:/Projects/history-log/site/smarty/cache/';
	}
}
?>