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
require_once ( t3lib_extMgm::extPath('wow_character').'pi1/class.tx_wowcharacter_pi1_character.php' );

define(ARMORY_IMAGE,"http://eu.wowarmory.com/wow-icons/_images/43x43/%s.png");

/**
 * Plugin 'WOW - Character Display' for the 'wow_character' extension.
 *
 * @author	Jobe <jobe@jobesoft.de>
 * @package	TYPO3
 * @subpackage	tx_wowcharacter
 */
class tx_wowcharacter_pi1 extends tslib_pibase {// -> http://typo3.org/fileadmin/typo3api-4.0.0/df/d32/classtslib__pibase.html

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
	function main($content,$conf)	{
    
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
    $this->pi_USER_INT_obj=1;  // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
    $this->pi_initPIflexForm();
    $this->cRealm=null;
    $this->cName=null;
    $this->cAvatar=null;
    
    $cID = $this->piVars['id'];

    $cStaticID = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'display_char', 'sDEF');
    $cDynamicID = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'chars_folder', 'sDEF');

    if($cStaticID){// if specified use static selection
      // $GLOBALS['TYPO3_DB']->exec_SELECTquery( SELECT, FROM, WHERE, GROUP BY, ORDER BY, LIMIT );
      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_wowcharacter_characters','uid='.$cStaticID,'','','1');
      $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
    }elseif($cID){// if not try loading character with submited id from dynamic folder
      $SQLwhere = sprintf('pid = %d AND uid = %d',$cDynamicID,$cID);
      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_wowcharacter_characters',$SQLwhere,'','','1');
      $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
    }
    $this->cRealm = $row['realm'];
    $this->cName = $row['name'];
    $this->cAvatar = 'uploads/tx_wowcharacter/'.$row['avatar'];
    
    // load extension registers
    $this->cObj->LOAD_REGISTER(array(
      'tx_wowcharacter_pi1_realm'   => $this->cRealm,
      'tx_wowcharacter_pi1_name'    => $this->cName,
      'tx_wowcharacter_pi1_avatar'  => $this->cAvatar,
    ),'');

    // get html template
    $content_html = $this->cObj->cObjGetSingle($this->conf['template.']['html'],$this->conf['template.']['html.']);
    // get css template
    $content_css = $this->cObj->cObjGetSingle($this->conf['template.']['css'],$this->conf['template.']['css.']);
    
    try{// load selected character
      
      $this->character = new tx_wowcharacter_pi1_character($this->cRealm,$this->cName);
      $characterTab = $this->character->xml->characterInfo->characterTab;
      $characterInfo = $this->character->xml->characterInfo->character;
      
      $secondBar = array( 'm' => 'mana', 'r' => 'rage', 'e' => 'energy' );
      
		  $marker = array(
        '###NAME###' => utf8_decode($characterInfo['name']),
        '###REALM###' => utf8_decode($characterInfo['realm']),
        '###ARMORY_URL###' => sprintf("http://armory.wow-europe.com/character-sheet.xml?r=%s&n=%s",$this->cRealm,$this->cName),
        '###AVATAR###'  => 'uploads/tx_wowcharacter/'.$this->cAvatar,
        '###LEVEL###'   => intval($characterInfo['level']),
        '###GENDER###'  => $this->pi_getLL(intval($characterInfo['genderId'])?'female':'male'),
        '###RACE###'    => $this->pi_getLL(sprintf('race-%02d',$characterInfo['raceId'])),
        '###CLASS###'   => $this->pi_getLL(sprintf('class-%02d',$characterInfo['classId'])),
        '###GUILD###'   => $characterInfo['guildName'],
        '###MODIFIED###'   => strval($characterInfo['lastModified']),
        /* LABELS */
        '###LLL_SECOND###'        => $this->pi_getLL($secondBar[strval($characterTab->characterBars->secondBar['type'])]),
        '###LLL_TALENT1###'       => $this->pi_getLL(sprintf('talent-%02d-1',$characterInfo['classId'])),
        '###LLL_TALENT2###'       => $this->pi_getLL(sprintf('talent-%02d-2',$characterInfo['classId'])),
        '###LLL_TALENT3###'       => $this->pi_getLL(sprintf('talent-%02d-3',$characterInfo['classId'])),
        '###LLL_PROFESSION1###'   => $this->pi_getLL(strval($characterTab->professions->skill[0]['key'])),
        '###LLL_PROFESSION2###'   => $this->pi_getLL(strval($characterTab->professions->skill[1]['key'])),
        /* STATS */
        '###STAT_HEALTH###'       => intval($characterTab->characterBars->health['effective']),
        '###STAT_SECOND###'       => intval($characterTab->characterBars->secondBar['effective']),
        /* baseStats */
        '###STAT_STRENGTH###'     => intval($characterTab->baseStats->strength['effective']),
        '###STAT_AGILITY###'      => intval($characterTab->baseStats->agility['effective']),
        '###STAT_STAMINA###'      => intval($characterTab->baseStats->stamina['effective']),
        '###STAT_INTELLECT###'    => intval($characterTab->baseStats->intellect['effective']),
        '###STAT_SPIRIT###'       => intval($characterTab->baseStats->spirit['effective']),
        '###STAT_ARMOR###'        => intval($characterTab->baseStats->armor['effective']),
        /* melee */
        '###STAT_MELEE_HITRATING###'    => intval($characterTab->melee->hitRating['value']),
        '###STAT_MELEE_DAMAGE###'       => sprintf('%d-%d',$characterTab->melee->mainHandDamage['min'],$characterTab->melee->mainHandDamage['max']),
        '###STAT_MELEE_SPEED###'        => round(floatval($characterTab->melee->mainHandSpeed['value']),2),
        '###STAT_MELEE_POWER###'        => intval($characterTab->melee->power['effective']),
        '###STAT_MELEE_CRITCHANCE###'   => round(floatval($characterTab->melee->critChance['percent']),2).'%',
        '###STAT_MELEE_EXPERTISE###'    => intval($characterTab->melee->expertise['value']),
        /* ranged */
        '###STAT_RANGED_HITRATING###'    => intval($characterTab->ranged->hitRating['value']),
        '###STAT_RANGED_DAMAGE###'       => sprintf('%d-%d',$characterTab->ranged->damage['min'],$characterTab->ranged->damage['max']),
        '###STAT_RANGED_SPEED###'        => round(floatval($characterTab->ranged->speed['value']),2),
        '###STAT_RANGED_POWER###'        => intval($characterTab->ranged->power['effective']),
        '###STAT_RANGED_CRITCHANCE###'   => round(floatval($characterTab->ranged->critChance['percent']),2).'%',
        '###STAT_RANGED_EXPERTISE###'    => intval($characterTab->ranged->expertise['value']),
        /* spell */
        '###STAT_SPELL_BONUSDAMAGE###'  => getAvg($characterTab->spell->bonusDamage,'value',0),
        '###STAT_SPELL_BONUSHEALING###' => intval($characterTab->spell->bonusHealing['value']),
        '###STAT_SPELL_HITRATING###'    => intval($characterTab->spell->hitRating['value']),
        '###STAT_SPELL_CRITCHANCE###'   => getAvg($characterTab->spell->critChance,'percent',2).'%',
        '###STAT_SPELL_PENETRATION###'  => intval($characterTab->spell->penetration['value']),
        '###STAT_SPELL_MANAREGEN###'    => intval($characterTab->spell->manaRegen['notCasting']),
        /* defense */
        '###STAT_DODGE###'        => round(floatval($characterTab->defenses->dodge['percent']),2).'%',
        '###STAT_PARRY###'        => round(floatval($characterTab->defenses->parry['percent']),2).'%',
        '###STAT_BLOCK###'        => round(floatval($characterTab->defenses->block['percent']),2).'%',
        '###STAT_DEFENSE###'      => intval($characterTab->defenses->defense['value']+$characterTab->defenses->defense['plusDefense']),
        '###STAT_RESILIENCE###'   => intval($characterTab->defenses->resilience['value']),
        /* resistances */
        '###STAT_ARCANE###'       => intval($characterTab->resistances->arcane['value']),
        '###STAT_FIRE###'         => intval($characterTab->resistances->fire['value']),
        '###STAT_NATURE###'       => intval($characterTab->resistances->nature['value']),
        '###STAT_FROST###'        => intval($characterTab->resistances->frost['value']),
        '###STAT_SHADOW###'       => intval($characterTab->resistances->shadow['value']),
        /* talentSpec */
        '###STAT_TALENT1###'      => intval($characterTab->talentSpec['treeOne']),
        '###STAT_TALENT2###'      => intval($characterTab->talentSpec['treeTwo']),
        '###STAT_TALENT3###'      => intval($characterTab->talentSpec['treeThree']),
        /* professions */
        '###STAT_PROFESSION1###'  => intval($characterTab->professions->skill[0]['value']),
        '###STAT_PROFESSION2###'  => intval($characterTab->professions->skill[1]['value']),
		  );
      
      // add language labes to marker array
      foreach( $this->LOCAL_LANG[$this->LLkey] as $key => $value )$marker[sprintf('###LLL_%s###',strtoupper($key))] = $value;

      $slots = array('','head','neck','shoulders','shirt','chest','waist','legs','feet','wrist','hand','finger1','finger2','trinket1','trinket2','back','weapon-left','shield','weapon-right','tabard');
      
      /*print('<pre style="text-align:left;">');
      var_dump($this->conf);
      print('</pre>');/**/

      for( $i = 1 ; $i <= 19 ; $i++ ){
        $icon = $this->character->getItem($i,'icon');
        $iconid = $this->character->getItem($i,'id');
        //index.php?id=7&tx_wowitem_pi1[id]=28585
        if(empty($icon)){
          $marker[sprintf('###ITEM%02d###',$i)] = "typo3conf/ext/wow_character/pi1/clear.gif";
        }else{
          $marker[sprintf('###ITEM%02d###',$i)] = sprintf(ARMORY_IMAGE,$icon);
        }
        $marker[sprintf('###ITEM%02dIMG###',$i)] = sprintf(
          '<img src="%s" alt="" border="0" class="item" id="slot-%s">',
          $marker[sprintf('###ITEM%02d###',$i)],
          $slots[$i]
        );
        // link to item display if configured
        if(intval($this->conf['itemDisplayPID']))$marker[sprintf('###ITEM%02dIMG###',$i)] = $this->pi_linkToPage(
          $marker[sprintf('###ITEM%02dIMG###',$i)],
          intval($this->conf['itemDisplayPID']),
          '',
          array('tx_wowitem_pi1[id]'=>$iconid)
        );
      }
      
      
		  $content_html = $this->cObj->getSubpart( $content_html, '###MAIN###' );
		  $content_html = $this->cObj->substituteMarkerArray( $content_html, $marker );
    }catch (Exception $e){
      $content_html = $this->cObj->getSubpart( $content_html, '###ERROR###' );
      $content_html = $this->cObj->substituteMarkerArray( $content_html, array('###ERROR_MSG###' => $e->getMessage() ) );
    }
	
    // add css to page
    $GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] = '<style type="text/css">'.$content_css.'</style>';
    // return page
		return $this->pi_wrapInBaseClass($content_html);
	}
  
}

function getAvg($set,$value,$precision){
  $sum  = 0;
  $sum += floatval($set->arcane[$value]);
  $sum += floatval($set->fire[$value]);
  $sum += floatval($set->frost[$value]);
  $sum += floatval($set->holy[$value]);
  $sum += floatval($set->nature[$value]);
  $sum += floatval($set->shadow[$value]);
  return round( $sum / 6 , $precision );
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/pi1/class.tx_wowcharacter_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/pi1/class.tx_wowcharacter_pi1.php']);
}

?>