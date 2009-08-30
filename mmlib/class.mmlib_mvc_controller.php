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
require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_mvc_controller.php');
***************************************************************/

require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_pibase.php');

/**
 * @author	Jobe <jobe@jobesoft.de>
 * @package	TYPO3
 */
class mmlib_mvc_controller extends mmlib_pibase {
  
  private $models = array();
  private $views = array();

	public function mmlib_mvc_controller(array $conf){
		if(!$conf['models'])throw new Exception('mmlib_mvc_controller: needs at least one model');
		if(!$conf['views'])throw new Exception('mmlib_mvc_controller: needs at least one view');
		$this->conf = array_merge(array(
			'models' => $conf['models'],
			'views' => $conf['views'],
		),$conf);
	}
  
	public function main($content,$conf){
		/*DUMMY*/
		return $content;
	}
	
  /* DATA ACCESS: */
  
  public function __get($name){
    switch($name){
      case 'model': return $this->models;
      case 'view': return $this->views;
      default: parent::__get($name);
    }
  }
	
	/* ACTIONS */
  
	
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/mmlib/class.mmlib_mvc_controller.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/mmlib/class.mmlib_mvc_controller.php']);
}
?>