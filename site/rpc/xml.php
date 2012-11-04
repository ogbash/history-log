<?php
// XML related routines

function xml_from_request() {
  // http://www.codediesel.com/php/reading-raw-post-data-in-php/
  $content = file_get_contents('php://input');
  $doc = new DOMDocument();
  $doc->loadXML($content);
  return $doc;
}

/* Given array of arrays return response XML. */

function xml_create_response($result) {
  // http://www.codediesel.com/php/reading-raw-post-data-in-php/
  $doc = new DOMDocument();
  
  $root = $doc->createElement("result");
  $doc->appendChild($root);

  foreach ($result as $item) {
    $inode = $doc->createElement("item");
    foreach($item as $k=>$v) {
      $vnode = $doc->createElement($k,$v);
      $inode->appendChild($vnode);
    }
    $root->appendChild($inode);
  }
  return $doc;
}

?>
