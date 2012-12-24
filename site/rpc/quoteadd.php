<?php

require_once(dirname(__FILE__) . "/xml.php");
require_once("../lib/quotation.php");

function parse_request($xmldoc) {
  $a = array();
  $a['type'] = $xmldoc->documentElement->getAttribute('type');
  $a['source_id'] = $xmldoc->getElementsByTagName('source_id')->item(0)->nodeValue;
  $a['description'] = $xmldoc->getElementsByTagName('description')->item(0)->nodeValue;
  $a['content'] = $xmldoc->getElementsByTagName('content')->item(0)->nodeValue;
  $a['startdate'] = $xmldoc->getElementsByTagName('startdate')->item(0)->nodeValue;
  $a['enddate'] = $xmldoc->getElementsByTagName('enddate')->item(0)->nodeValue;
  $a['tags'] = $xmldoc->getElementsByTagName('tags')->item(0)->nodeValue;
  return $a;
}

$xml = xml_from_request();
$req = parse_request($xml);
$a = getErrors($req);
$quotation = $a[0];
$errors = $a[1];

$quotation['source_id'] = (int)$req['source_id'];

if (sizeof($errors) == 0) {
  // connect to DB and add quote
  require(dirname(__FILE__) . "/../database_connect.php");
  $result = addQuotation($quotation);
  mysql_close($db) or die("Closing db failed");

  // create and send response
  $xmlresp = xml_create_response($result);
  echo $xmlresp->saveXML();
} else {
  // send errors
  echo "errors";
}
?>