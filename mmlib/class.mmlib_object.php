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
require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_object.php');
***************************************************************/

require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_db.php');

/**
 * @author	Jobe <jobe@jobesoft.de>
 * @package	TYPO3
 */
class mmlib_object {

	private $conf = null;// stores configuration array
	private $data = null;// stores data array

	public function mmlib_object(array $conf){
		if(!$conf['table'])throw new Exception('mmlib_object: table missing');
		if(!$conf['where']['uid'])throw new Exception('mmlib_object: where->uid missing');
		$this->conf = array_merge(array(
			'table' => $conf['table'],
			'where' => array(
				'uid' => $conf['uid'],
			),
		),$conf);
		// each object can store only one set of data
		$this->data = array_shift(mmlib_db::query($conf['table'],$conf['where'],null,null,1,null));
		if(!$this->data)throw new Exception('mmlib_object: no data found');
	}
	
  public function __get($name){
    switch($name){
      default: return $this->value($name);
    }
  }

	public function __set($name,$value){
		if(!$this->data[$name])throw new Exception('mmlib_object: value \''.$name.'\' does not exist');
		$this->data[$name] = $value;
		$this->conf['modified'] = true;
	}
	
	public function __destruct(){
		if($this->modified){
			$tmp = $this->data;
			unset($tmp['uid']);// protect uid
			unset($tmp['crdate']);// protect crdate
			$tmp['tstamp'] = strval(time());// update last modified
			mmlib_db::update($this->table,$this->where,$tmp);
		}
	}
	
	public function __toString(){
		return get_class($this).'='.serialize($this->conf);
	}
	
	/**
	 * Returns a value from data or conf where data preceedes conf.
	 * Within strings all {$keyname} are substituted with their values from $data or $conf
	 */
	private function value($key){
		$tmp = array_merge($this->conf,$this->data);// data preceedes conf
		switch(gettype($tmp[$key])){
			case 'string': return str_replace(array_map(create_function('$v','return \'{$\'.$v.\'}\';'),array_keys($tmp)),$tmp,$tmp[$key]);
			default: return $tmp[$key];
		}
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/mmlib/class.mmlib_object.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/mmlib/class.mmlib_object.php']);
}
?>