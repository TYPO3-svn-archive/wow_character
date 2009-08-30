<?php

require_once(t3lib_extMgm::extPath('wow_character').'mmlib/class.mmlib_xml.php');

DEFINE(TYPO3TEMP,PATH_site.'typo3temp/');
DEFINE(CACHETIME,86400);/* = 1 days */

DEFINE(RACEID_OR,2);
DEFINE(RACEID_UD,5);
DEFINE(RACEID_TA,6);
DEFINE(RACEID_BE,10);

DEFINE(CLASSID_WR,1);
DEFINE(CLASSID_PA,2);
DEFINE(CLASSID_HU,3);
DEFINE(CLASSID_RO,4);
DEFINE(CLASSID_PR,5);
DEFINE(CLASSID_DK,6);
DEFINE(CLASSID_SH,7);
DEFINE(CLASSID_MA,8);
DEFINE(CLASSID_WL,9);
DEFINE(CLASSID_DR,11);

DEFINE(SLOT_HEAD,         01);
DEFINE(SLOT_NECK,         02);
DEFINE(SLOT_SHOULDERS,    03);
DEFINE(SLOT_SHIRT,        04);
DEFINE(SLOT_CHEST,        05);
DEFINE(SLOT_WAIST,        06);
DEFINE(SLOT_LEGS,         07);
DEFINE(SLOT_FEET,         08);
DEFINE(SLOT_WRIST,        09);
DEFINE(SLOT_HAND,         10);
DEFINE(SLOT_FINGER1,      11);
DEFINE(SLOT_FINGER2,      12);
DEFINE(SLOT_TRINKET1,     13);
DEFINE(SLOT_TRINKET2,     14);
DEFINE(SLOT_BACK,         15);
DEFINE(SLOT_WEAPON_LEFT,  16);
DEFINE(SLOT_SHIELD,       17);
DEFINE(SLOT_WEAPON_RIGHT, 18);
DEFINE(SLOT_TABARD,       19);

class tx_wowcharacter_character extends mmlib_xml{
  
  public function tx_wowcharacter_character(array $conf){
    if(!$conf['where']['uid'])throw new Exception('tx_wowcharacter_character: where->uid missing');
    parent::mmlib_xml(array_merge(array(
      'table'       => 'tx_wowcharacter_characters',
      'where'       => array(
        'uid'         => $conf['where']['uid'],
        'pid'         => $conf['where']['pid'],
      ),
      'itemOrder'   => '0,1,2,14,4,3,18,8,9,5,6,7,10,11,12,13,15,16,17',
      'url'         => 'http://armory.wow-europe.com/character-sheet.xml?r={$realm}&n={$name}',
      'filename'    => 'wowcharacter-{$realm}-{$name}-{$lang}.xml',
      'lang'        => 'de',
    ),$conf));
  }
  
  public function __get($name){
    switch($name){
      case 'marker':return $this->getMarker();
      case 'character':return $this->getCharacter();
      case 'talents':return $this->getTalentSpec();
      case 'pvp':return $this->getPvp();
      case 'professions':return $this->getProfessions();
      case 'characterbars':return $this->getCharacterBars();
      case 'baseStats':return $this->getBaseStats();
      case 'melee':return $this->getMelee();
      case 'ranged':return $this->getRanged();
      case 'spell':return $this->getSpell();
      case 'defenses':return $this->getDefenses();
      case 'items':return $this->getItems();
      case 'glyphs':return $this->getGlyphs();
      default: return parent::__get($name);
    }
  }
  
  public function __toString(){
    $result = sprintf(
      "&lt;!-- CHARACTER (%s,%s,%s) --&gt;\n%s&lt;!-- CHARACTER --&gt;\n",
      $this->xml->characterInfo->character['realm'],
      $this->xml->characterInfo->character['name'],
      $this->xml['lang'],
      $this->asString($this->marker,'  ')
    );
    return $result;
  }
  
  private function asString($array,$prefix=''){
    $result = '';
    foreach( $array as $key => $val )if(is_array($val)){
      $result .= sprintf("%s&lt;!--%s--&gt;\n%s%s&lt;!--%s--&gt;\n",$prefix,$key,$this->asString($val,$prefix.'  '),$prefix,$key);
    }else{
      $result .= sprintf("%s%s = %s\n",$prefix,$key,$val);
    }
    return $result;
  }
  
  /* marker handlers */
  
  private function getMarker(){
    return array_merge(
      $this->character,
      $this->talents,
      $this->pvp,
      $this->professions,
      $this->characterbars,
      $this->baseStats,
      $this->melee,
      $this->ranged,
      $this->spell,
      $this->defenses,
      $this->items,
      $this->glyphs
    );
  }
  
  private function getTalentSpec(){
    $xml = $this->xml->characterInfo->characterTab->talentSpecs->talentSpec;
    if($xml[1]){// has dual spec
      $result['TALENT'] = array(mmlib_xml::xml_array($xml[$xml[0]['active']?0:1]));
      $result['TALENT1'] = array(mmlib_xml::xml_array($xml[0]));
      $result['TALENT1'][0]['ACTIVE'] = $result['TALENT1'][0]['ACTIVE']?'active':'inactive';
      $result['TALENT2'] = array(mmlib_xml::xml_array($xml[1]));
      $result['TALENT2'][0]['ACTIVE'] = $result['TALENT2'][0]['ACTIVE']?'active':'inactive';
    }else{
      $result['TALENT'] = array(mmlib_xml::xml_array($xml[$xml[0]['active']?0:1]));
      $result['TALENT1'] = array(mmlib_xml::xml_array($xml[0]));
      $result['TALENT1'][0]['ACTIVE'] = $result['TALENT1'][0]['ACTIVE']?'active':'inactive';
      $result['TALENT2'] = array();
    }
    unset($result['TALENT'][0]['ACTIVE']);
    return $result;
  }
  
  private function getCharacter(){
    return mmlib_xml::xml_array($this->xml->characterInfo->character);
  }
  
  private function getPvp(){
    $xml = $this->xml->characterInfo->characterTab->pvp;
    return array(
      'LIFETIMEHONORABLEKILLS' => strval($xml->lifetimehonorablekills['value']),
      'ARENACURRENCY' => strval($xml->arenacurrency['value'])
    );
  }
  
  private function getProfessions(){
    $xml = $this->xml->characterInfo->characterTab->professions;
    return array_merge(
      mmlib_xml::xml_array($xml->skill[0],'PROFESSION1','NAME'),
      mmlib_xml::xml_array($xml->skill[1],'PROFESSION2','NAME')
    );
  }
  
  private function getCharacterBars(){
    $xml = $this->xml->characterInfo->characterTab->characterBars;
    $secondBar = array(
      'm' => 'BAR_MANA',
      'r' => 'BAR_RAGE',
      'e' => 'BAR_ENERGY',
      'p' => 'BAR_POWER',
    );
    return array_merge(
      array( 'HEALTH' => strval($xml->health['effective']) ),
      array( 'BAR_MANA' => array() ),
      array( 'BAR_RAGE' => array() ),
      array( 'BAR_ENERGY' => array() ),
      array( 'BAR_POWER' => array() ),
      array( $secondBar[strval($xml->secondBar['type'])] => array( mmlib_xml::xml_array($xml->secondBar) ) )
    );
  }
  
  private function getBaseStats(){
    $xml = $this->xml->characterInfo->characterTab->baseStats;
    return array_merge(
      mmlib_xml::xml_array($xml->strength, 'STRENGTH',   'EFFECTIVE' ),
      mmlib_xml::xml_array($xml->agility,  'AGILITY',    'EFFECTIVE' ),
      mmlib_xml::xml_array($xml->stamina,  'STAMINA',    'EFFECTIVE' ),
      mmlib_xml::xml_array($xml->intellect,'INTELLECT',  'EFFECTIVE' ),
      mmlib_xml::xml_array($xml->spirit,   'SPIRIT',     'EFFECTIVE' ),
      mmlib_xml::xml_array($xml->armor,    'ARMOR',      'EFFECTIVE' )
    );
  }
  
  private function getMelee(){
    $xml = $this->xml->characterInfo->characterTab->melee;
    return array( 'MELEE' => array( array_merge(
      mmlib_xml::xml_array($xml->mainHandDamage, 'mainHandDamage' ),
      mmlib_xml::xml_array($xml->offHandDamage, 'offHandDamage' ),
      mmlib_xml::xml_array($xml->mainHandSpeed, 'mainHandSpeed', 'value' ),
      mmlib_xml::xml_array($xml->offHandSpeed, 'offHandSpeed', 'value' ),
      mmlib_xml::xml_array($xml->power, 'power', 'effective' ),
      mmlib_xml::xml_array($xml->hitRating, 'hitRating', 'value' ),
      mmlib_xml::xml_array($xml->critChance, 'critChance', 'percent' ),
      mmlib_xml::xml_array($xml->expertise, 'expertise', 'value' )
    )));
  }
  
  private function getRanged(){
    $xml = $this->xml->characterInfo->characterTab->ranged;
    return array( 'RANGED' => array( array_merge(
      mmlib_xml::xml_array($xml->weaponSkill, 'weaponSkill', 'value' ),
      mmlib_xml::xml_array($xml->damage, 'damage' ),
      mmlib_xml::xml_array($xml->speed, 'speed', 'value' ),
      mmlib_xml::xml_array($xml->power, 'power', 'effective' ),
      mmlib_xml::xml_array($xml->hitRating, 'hitRating', 'value' ),
      mmlib_xml::xml_array($xml->critChance, 'critChance', 'percent' )
    )));
  }
  
  private function getSpell(){
    $xml = $this->xml->characterInfo->characterTab->spell;
    return array( 'SPELL' => array( array_merge(
      //mmlib_xml::xml_array($xml->bonusDamage, 'bonusDamage', 'value' ),
      mmlib_xml::xml_array($xml->bonusHealing, 'damage', 'value' ),
      mmlib_xml::xml_array($xml->hitRating, 'hitRating', 'value' ),
      mmlib_xml::xml_array($xml->critChance, 'critChance', 'rating' ),
      mmlib_xml::xml_array($xml->penetration, 'penetration', 'value' ),
      mmlib_xml::xml_array($xml->manaRegen, 'manaRegen' ),
      mmlib_xml::xml_array($xml->hasteRating, 'hasteRating' )
    )));
  }
  
  private function getDefenses(){
    $xml = $this->xml->characterInfo->characterTab->defenses;
    return array( 'DEFENSES' => array( array_merge(
      mmlib_xml::xml_array($xml->armor, 'armor', 'effective' ),
      mmlib_xml::xml_array($xml->defense, 'defense', 'value' ),
      mmlib_xml::xml_array($xml->dodge, 'dodge', 'percent' ),
      mmlib_xml::xml_array($xml->parry, 'parry', 'percent' ),
      mmlib_xml::xml_array($xml->block, 'block', 'percent' ),
      mmlib_xml::xml_array($xml->resilience, 'resilience', 'value' )
    )));
  }
  
  private function getGlyphs(){
    $result = array();
    $xml = $this->xml->characterInfo->characterTab->glyphs;
    foreach( $xml->glyph as $key => $glyph ) $result[] = mmlib_xml::xml_array($glyph);
    return array( 'GLYPHS' => $result );
  }
  
  private function getItems(){
    $result = array();
    $items = array();
    foreach( $this->xml->characterInfo->characterTab->items->item as $key => $item )
      $items[intval($item['slot'])] = $item;
    if(is_array($this->itemOrder))
      foreach($this->itemOrder as $group => $order)
        $result[strtoupper('ITEMS_'.$group.'')] = $this->getOrderedItems($items,$order);
    else $result['ITEMS'] = $this->getOrderedItems($items,$this->itemOrder);
    return $result;
  }
  
  /* utilities */
  
  /**
   * returns markers of items in specific order
   * @param array $items Pool of items
   * @param string $order Komma separated list of items to return
   */
  private function getOrderedItems($items,$order){
    $result = array();
    $order = explode(',',$order);
    foreach($order as $num => $key) if($items[intval($key)])
      $result[] = array( 'ITEM' => array(mmlib_xml::xml_array($items[intval($key)])) );
    else
      $result[] = array( 'EMPTY' => array(array( 'SLOT' => intval($key) )) );
    return $result;
  }
  
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/inc/class.tx_wowcharacter_character.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/inc/class.tx_wowcharacter_character.php']);
}

//print('<pre style="text-align:left;position:absolute;">');var_dump($GLOBALS['TSFE']->fe_user->user);print('</pre>');
?>