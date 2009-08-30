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
require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_db.php');
***************************************************************/

/**
 * @author	Jobe <jobe@jobesoft.de>
 * @package	TYPO3
 */
class mmlib_db{

	/* STATICS: */
	
	public static function query($table,$where='',$group='',$order='',$limit='',$index=''){
		$where = mmlib_db::where($where,$table);
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*',$table,$where,$group,$order,$limit,$index);
	}
	
	public static function update($table,$where,$values){
		$where = mmlib_db::where($where,$table);
		if(!is_array($values))throw new Exception('mmlib_db: incorrect values-format');
		return $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table,$where,$values);
	}

	public static function where($value,$table){
		if(empty($value))return '';
		if(!is_array($value))throw new Exception('mmlib_db: incorrect where-format');
		$value =  $GLOBALS['TYPO3_DB']->fullQuoteArray($value,$table);
		$value = implode(' AND ',array_map(create_function('$k,$v','return $k.\' = \'.$v;'),array_keys($value),$value));
		return $value;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/mmlib/class.mmlib_db.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/mmlib/class.mmlib_db.php']);
}
?>