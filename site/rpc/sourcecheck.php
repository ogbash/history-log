<?php

require_once(dirname(__FILE__) . "/xml.php");
require_once("../lib/source.php");

function parse_request($xmldoc) {
  $a = array();
  $a['type'] = $xmldoc->documentElement->getAttribute('type');
  $a['title'] = $xmldoc->getElementsByTagName('title')->item(0)->nodeValue;
  return $a;
}

$xml = xml_from_request();
$req = parse_request($xml);

// connect to DB and search source

require(dirname(__FILE__) . "/../database_connect.php");
$result = searchSourcesByTitle($req['title']);
mysql_close($db) or die("Closing db failed");

// create and send response

$xmlresp = xml_create_response($result);
echo $xmlresp->saveXML();
?>