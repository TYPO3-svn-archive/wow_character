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

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'WOW - Character List' for the 'wow_character' extension.
 *
 * @author	Jobe <jobe@jobesoft.de>
 * @package	TYPO3
 * @subpackage	tx_wowcharacter
 */
class tx_wowcharacter_pi2 extends tslib_pibase {
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
	function main($content,$conf)	{
    
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
    $this->pi_initPIflexForm();

    $displayPage = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'display_page', 'sDEF');
    $charsFolder = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'chars_folder', 'sDEF');
    
    $SQLwhere = sprintf('pid = %d',$charsFolder);
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_wowcharacter_characters',$SQLwhere);

        // load extension registers
    $this->cObj->LOAD_REGISTER(array(
      'tx_wowcharacter_pi1_realm'   => '[realm]',
      'tx_wowcharacter_pi1_name'    => '[name]',
    ),'');

    // get html template
    $content_html = $this->cObj->cObjGetSingle($this->conf['template.']['html'],$this->conf['template.']['html.']);
    // get css template
    $content_css = $this->cObj->cObjGetSingle($this->conf['template.']['css'],$this->conf['template.']['css.']);
    
    // get template parts:
    $content_html = $this->cObj->getSubpart( $content_html, '###MAIN###' );
    $content_character = $this->cObj->getSubpart( $content_html, '###CHARACTER###' );

    // build marker array
    foreach( $this->LOCAL_LANG[$this->LLkey] as $key => $value )$marker[sprintf('###LLL_%s###',strtoupper($key))] = $value;

    // build character subpart
    while( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) ){
      $marker['###NAME###'] = $row['name'];
      $marker['###REALM###'] = $row['realm'];
      $marker['###URL_DISPLAY###'] = $this->cObj->getTypoLink_URL($displayPage,array('tx_wowcharacter_pi1[id]' => intval($row['uid'])));
      $tmp .= $this->cObj->substituteMarkerArray( $content_character, $marker );
    }
    $content_character = '<!--CHARACTER-->'.$tmp.'<!--CHARACTER-->';

    // substitute markers & subparts:
    $content_html = $this->cObj->substituteSubpart( $content_html, '###CHARACTER###', $content_character );
    $content_html = $this->cObj->substituteMarkerArray( $content_html, $marker );

    // add css to page
    $GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] = '<style type="text/css">'.$content_css.'</style>';
		return $this->pi_wrapInBaseClass($content_html);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/pi2/class.tx_wowcharacter_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/pi2/class.tx_wowcharacter_pi2.php']);
}

?>