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
***************************************************************/

require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_pibase.php');
require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_db.php');
require_once(t3lib_extMgm::extPath('wow_character').'inc/class.tx_wowcharacter_character.php');/*characters*/

define(ARMORY_IMAGE,"http://eu.wowarmory.com/wow-icons/_images/43x43/%s.png");

/**
 * Plugin 'WOW - Character List' for the 'wow_character' extension.
 *
 * @author	Jobe <jobe@jobesoft.de>
 * @package	TYPO3
 * @subpackage	tx_wowcharacter
 */
class tx_wowcharacter_pi2 extends mmlib_pibase {
	var $prefixId      = 'tx_wowcharacter_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_wowcharacter_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'wow_character';	// The extension key.
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf){try{
		parent::main($content,$conf);
		// merge ts-defined locallang files
		if(file_exists( $llf = $this->file($this->conf['locallang']) ))
			$this->LOCAL_LANG = array_rmerge($this->LOCAL_LANG,t3lib_div::readLLfile($llf,$this->LLkey));
		// merge locallang.xml with ts-locallang:
		$conf['locallang.'] = array_rmerge($this->LLTS(),$conf['locallang.']);
		// copy ts-locallang to marks:
		foreach($conf['locallang.'] as $key => $value)$tmp['LLL_'.$key] = $value;
		$conf['marks.'] = array_rmerge($tmp,$conf['marks.']);
		//print('<pre>');print_ts($conf);die('</pre>');/*DEBUG*/
		// generate output:
    return $this->display($conf);
			
	}catch (Exception $e){
		return $this->display($this->template,array('MESSAGE'=>$e->getMessage(),'TRACE'=>$e->getTraceAsString()),'ERROR');
	}}
	
  public function display($conf){
		if(empty($conf['template']))throw new Exception('template missing');
    return $this->pi_wrapInBaseClass($this->cObj->TEMPLATE($conf));
  }
	
	private function LLTS(){
		foreach($this->LOCAL_LANG as $LLKey => $locallang ){
			foreach($locallang as $key => $value){
				$key = strtoupper($key);
				$ll[$key] = 'TEXT';
				if(!strcasecmp($LLKey,'default')){
					$ll[$key.'.']['value'] = $value;
				}else{
					$ll[$key.'.']['lang.'][$LLKey] = $value;
				}
			}
		}
		return $ll;
	}
	
}

/**
 * recursive array merge
 */
function array_rmerge($a,$b){
	if(!is_array($b))return $a;
	if(!is_array($a))return $b;
	foreach(array_keys(array_merge($a,$b)) as $k)
		if( is_array($a[$k]) && is_array($b[$k]) )$b[$k] = array_rmerge($a[$k],$b[$k]);
	return array_merge($a,$b);
}

/**
 * print as typoscript
 */
function print_ts($ts,$prefix=''){
	foreach($ts as $key => $value)if(is_array($value)){
		print_ts($value,$prefix.$key);
	}else{
		print($prefix.$key." = ".$value."\n");
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/pi2/class.tx_wowcharacter_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/pi2/class.tx_wowcharacter_pi2.php']);
}

?>