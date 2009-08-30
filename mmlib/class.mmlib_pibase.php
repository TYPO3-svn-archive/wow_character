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
require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_pibase.php');
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * @author	Jobe <jobe@jobesoft.de>
 * @package	TYPO3
 */
class mmlib_pibase extends tslib_pibase {

  public function main($content,$conf,$nocache=0){
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
    // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
    $this->pi_USER_INT_obj = $nocache;
    $this->pi_initPIflexForm();
    // merge ts-config with ff-config
    foreach($this->cObj->data['pi_flexform']['data'] as $key => $sheet)
      foreach( $sheet['lDEF'] as $key => $value )
        $this->conf[$key] = strval($value['vDEF']);
    // load possible css
    if($this->css)$this->setCSS($this->css);
  }

  /* DATA ACCESS: */
  
  public function __get($name){
    switch($name){
      case 'template': return $this->getTemplate();
      case 'locallang': return $this->getLL();
      case 'piKey': return get_class($this);
      default: return $this->conf[$name];
    }
  }

  /**
   * merge template and data
   */
  public function display($template,$array,$subpart=null){
    if($_GET['mminfo']){print('<pre>');print_r($array);die('</pre>');/*DEBUG*/}
    if($subpart)$template = $this->cObj->getSubpart($template,mmlib_pibase::asMarker($subpart));
    return parent::pi_wrapInBaseClass($this->marker_parse($template,array_merge($array,$this->locallang)));
  }

  /* PRIVATE: */
  
  private function getTemplate(){
		$template = $this->conf['template.'];
    if($this->conf['template']){// if first level is set use as filepath
      $template['template'] = 'FILE';
      $template['template.']['file'] = $this->conf['template'];
    }
    return $this->cObj->TEMPLATE($template);
  }
  
  private function getLL(){
    $result = array();
    $LLkey = $this->LOCAL_LANG[$this->LLkey]?$this->LLkey:'default';
    foreach( $this->LOCAL_LANG[$LLkey] as $key => $value) $result[strtoupper('LLL_'.$key)] = $value;
    return $result;
  }
  
  /* UTILITIES: */
  
  /**
  * Substitute markers and subparts in a template. Markers with a list of sub-marker-sets represent subparts.
  * $marker = array( 'MARKER' => 'VALUE', 'SUBPART' => array( ENTRY_UID => array( 'MARKER' => 'VALUE' ) ) );
  * Subparts inherit markers from parents.
  */
  public function marker_parse($tpl,$marker){
    $subparts = array_filter($marker,'is_array');// extract arrays
    $marker = array_diff_key($marker,$subparts);// filter arrays
    foreach( $subparts as $key => $entrys ){
      $key = mmlib_pibase::asMarker($key);
			while( $subtpl = $this->cObj->getSubpart($tpl,$key) ){
				$tmp = '';
				foreach( $entrys as $uid => $submarker ){
					$tmp .= sprintf('<!-- %s[%d] begin -->',substr($key,3,-3),$uid);
					$tmp .= $this->marker_parse($subtpl,array_merge($marker,$submarker));// parse and collect every entry
					$tmp .= sprintf('<!-- %s[%d] end -->',substr($key,3,-3),$uid);
				}
				$tpl = $this->cObj->substituteSubpart($tpl,$key,$tmp,0);// substitute first subpart
			}
		}
    $tpl = $this->cObj->substituteMarkerArray($tpl,mmlib_pibase::asMarkerArray($marker));// ensure markers and substitute
    return $tpl;
  }

	public function link($params,$urlParameters=array(),$target=''){
		return $this->cObj->getTypoLink_URL($params,$urlParameters,$target);
	}

  /* SETTERS: */
  
  public function setCSS($file){
    $this->setHeaderData('css',sprintf('<link rel="stylesheet" href="%s" type="text/css" />',mmlib_pibase::file($file)));
  }
  
  public function setRSS($type=99){
    $this->setHeaderData('rss',sprintf('<link rel="alternate" type="application/rss+xml" title="RSS" href="http://%s/%s?type=%d" />',$_SERVER['HTTP_HOST'],$_SERVER['SCRIPT_NAME'],$type));
  }
  
  public function setHeaderData($key,$value){
    return $GLOBALS['TSFE']->additionalHeaderData[sprintf('%s_%s',$this->piKey,$key)] = $value;
  }
  
  public function setRegisters($array){
    return $this->cObj->LOAD_REGISTER($array,'');
  }
  
  /* STATICS: */
  
  public static function asMarkerArray($array){
    $result = array();
    foreach($array as $key => $val) $result[mmlib_pibase::asMarker($key)] = $val;
    return $result;
  }

  /**
   * ensure marker format
   */
  public static function asMarker($string){
    return strtoupper('###'.preg_replace('/[^\w-_\.]/i','',$string).'###');
  }

  /**
   * Handle abbreviations like "EXT:", etc.
   */
  public static function file($file){
    return $GLOBALS['TSFE']->tmpl->getFileName($file);
  }
  
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/mmlib/class.mmlib_pibase.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/mmlib/class.mmlib_pibase.php']);
}
?>