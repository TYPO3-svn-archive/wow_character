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
require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_mvc_view.php');
***************************************************************/

require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_pibase.php');

/**
 * @author	Jobe <jobe@jobesoft.de>
 * @package	TYPO3
 */
class mmlib_mvc_view extends mmlib_pibase {
  
  /* for mvc schemantics */
  
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/mmlib/class.mmlib_mvc_view.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/mmlib/class.mmlib_mvc_view.php']);
}
?>