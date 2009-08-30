<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Jobe <jobe@jobesoft.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
****************************************************************
require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_xml.php');
***************************************************************/

require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_object.php');

DEFINE(TYPO3TEMP,PATH_site.'typo3temp/');
DEFINE(CACHETIME,86400);/* = 1 days */

/**
 * @author	Jobe <jobe@jobesoft.de>
 * @package	TYPO3
 */
class mmlib_xml extends mmlib_object {
	
	public $xml = null;
	
	public function mmlib_xml(array $conf){
		// check required data
		if(!$conf['url'])throw new Exception('mmlib_xml: missing url');
		if(!$conf['filename'])throw new Exception('mmlib_xml: missing filename');
		// initialize object
		parent::mmlib_object(array_merge(array(
			'url'				=> $conf['url'],
			'filename'	=> $conf['filename'],
			'lang'			=> 'de',
		),$conf));
		// load finale data
		if(!$this->load($this->filename,$this->url,$this->lang))throw new Exception('mmlib_xml: couldn\'t load data');
	}
	
	/* PRIVATES: */
	
	private function query($url,$lang){
		libxml_use_internal_errors(false); libxml_clear_errors();
		libxml_set_streams_context(stream_context_create(array('http' => array(
			'user_agent' => sprintf('Mozilla/5.0 (Windows; U; Windows NT 5.1; %s; rv:1.8) Gecko/20051111 Firefox/1.5',$lang),
			'header' => sprintf('Accept-Language: %s, en',$lang),
		))));
		$this->xml = simplexml_load_file($url);
		return( !empty($this->xml) && empty($this->xml->errorCode['value']) );
	}
	
	private function load($filename,$url,$lang){
		if( !file_exists(TYPO3TEMP.$filename) || ( ( time() - filemtime(TYPO3TEMP.$filename) ) > CACHETIME ) ){// if cache not exists or too old
			if( $this->query($url,$lang) ){// if query successfull
				$this->save($filename);// save data
			}else{
				if( file_exists(TYPO3TEMP.$filename) ){// fallback to saved data if exists
					$this->xml = simplexml_load_file(TYPO3TEMP.$filename);
				}else{
					throw new Exception('no data available');
				}
			}
		}else{
			$this->xml = simplexml_load_file(TYPO3TEMP.$filename);
		}
		return( !empty($this->xml) );
	}
	
	private function save($filename){
		$this->xml->asXML(TYPO3TEMP.$filename);
	}
	
	/* UTILITIES: */
	
  /**
   * convert xml to arry
   * @param SimpleXMLElement $xml Data to convert
   * @param string $prefix Prefix to prepend key with
   * @param string $base Base value to return as prefix only
   */
  public static function xml_array(SimpleXMLElement $xml,$prefix='',$base=''){
    $result = array();
    if($base) $result[strtoupper($prefix)] = strval($xml[strtolower($base)]);
    if($prefix) $prefix .= '.';
    foreach( $xml->attributes() as $key => $val )if(strtoupper($key)!=strtoupper($base)) $result[strtoupper($prefix.$key)] = strval($val);
    return $result;
  }
  
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/mmlib/class.mmlib_object.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/mmlib/class.mmlib_object.php']);
}
?>