<?php

require(dirname(__FILE__) . "/xml.php");

function esc($reqstr) { return mysql_real_escape_string($reqstr); }
function parse_request($xmldoc) {
  $a = array();
  $a['type'] = $xmldoc->documentElement->getAttribute('type');
  $a['title'] = $xmldoc->getElementsByTagName('title')->item(0)->nodeValue;
  return $a;
}

$xml = xml_from_request();
$req = parse_request($xml);

// connect to DB and search source

$db = mysql_connect('localhost','root','ares') or die("Connection failed!!!");
mysql_select_db('historylog') or die ("Databse selection failed!!!");
mysql_set_charset('utf8',$db); 

$q = mysql_query('SELECT * FROM sources WHERE title LIKE "'.esc($req['title']).'%"') or die("mysql query failed");

$result = array();
while($source = mysql_fetch_assoc($q)) {
  array_push($result, $source);
}
mysql_close($db) or die("Closing db failed");

// create and send response

$xmlresp = xml_create_response($result);
echo $xmlresp->saveXML();

?>
