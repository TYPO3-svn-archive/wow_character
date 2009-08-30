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
require_once(t3lib_extMgm::extPath('wow_character').'inc/class.tx_wowcharacter_character.php');/*characters*/

define(ARMORY_IMAGE,"http://eu.wowarmory.com/wow-icons/_images/43x43/%s.png");

/**
 * Plugin 'WOW - Character Display' for the 'wow_character' extension.
 *
 * @author	Jobe <jobe@jobesoft.de>
 * @package	TYPO3
 * @subpackage	tx_wowcharacter
 */
class tx_wowcharacter_pi1 extends mmlib_pibase {

	var $prefixId      = 'tx_wowcharacter_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_wowcharacter_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'wow_character';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf){try{
    
		$character = null;
		
		parent::main($content,$conf,1);
		
    if($this->display_char)// if specified use static selection
      $where = array('uid' => $this->display_char);
    elseif($this->piVars['id'])// if not try loading character with submited id from dynamic folder
      $where = array('uid' => $this->piVars['id'], 'pid' => $this->chars_folder);
		
		if($where){
			$this->character = new tx_wowcharacter_character(array('where' => $where,'itemOrder' => $this->itemOrder,));
			return $this->display($this->template,$this->character->marker,'CHARACTER');
		}else{
			return $this->display($this->template,array(),'NOCHAR');
		}
			
	}catch (Exception $e){
		return $this->display($this->template,array('MESSAGE'=>$e->getMessage(),'TRACE'=>$e->getTraceAsString()),'ERROR');
	}}
	
  public function __get($name){
    switch($name){
      case 'itemOrder': return $this->conf['items.']['order']?$this->conf['items.']['order']:$this->conf['items.']['order.'];// single preceeds group config
      default: return $this->character->$name?$this->character->$name:parent::__get($name);// char preceeds config
    }
  }
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/pi1/class.tx_wowcharacter_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/pi1/class.tx_wowcharacter_pi1.php']);
}

?>