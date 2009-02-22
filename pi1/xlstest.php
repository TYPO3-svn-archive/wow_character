<?php

$url = "http://armory.worldofwarcraft.com/item-tooltip.xml?i=%d";
$url = sprintf($url,$id);
$opts = array(
  'http' => array(
    'user_agent' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; de-DE; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
    'header' => 'Accept-language: de-DE'
  )
);
ini_set("user_agent",$opts['http']['user_agent']); 
ini_set("header",$opts['http']['header']);
$context = stream_context_create($opts);
libxml_set_streams_context($context);

// Load the XML source
$xml = new DOMDocument;
$xml->load('http://armory.wow-europe.com/item-info.xml?i=32494');

$xsl = new DOMDocument;
$xsl->load('http://armory.wow-europe.com/layout/item-info.xsl');

// Configure the transformer
$proc = new XSLTProcessor;
$proc->importStyleSheet($xsl); // attach the xsl rules

var_dump($xml);

?>
