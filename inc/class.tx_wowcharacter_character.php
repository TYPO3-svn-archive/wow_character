<?php

DEFINE(TYPO3TEMP,PATH_site.'typo3temp/');
DEFINE(CACHETIME,2592000);/* = 30 days */

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

class tx_wowcharacter_character{
    
    public $xml = null;
    public $items = null;
    
    public function tx_wowcharacter_character($realm,$char,$lang='de-de'){
      $this->load($realm,$char,$lang);
      if($this->xml->characterInfo->characterTab->items)
        foreach( $this->xml->characterInfo->characterTab->items->item as $item )$this->items[intval($item['slot'])+1] = $item;// parse items
    }

    private function query($realm,$char,$lang='de-de'){
      if(empty($realm))throw new Exception('noName');// name is mandatory
      if(empty($char))throw new Exception('noRealm');// realm is mandatory
      if(empty($lang))$lang = array_shift(explode(',',$_SERVER["HTTP_ACCEPT_LANGUAGE"]));// default to browser language
      $lang = explode('-',$lang);
      libxml_use_internal_errors(false); libxml_clear_errors();
      libxml_set_streams_context(stream_context_create(array('http' => array(
        'user_agent' => sprintf('Mozilla/5.0 (Windows; U; Windows NT 5.1; %s; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6',$lang[0]),
        'header' => sprintf('Accept-language: %s-%s',$lang[0],$lang[1]),
      ))));
      $realm = urlencode(utf8_encode($realm));
      $char = urlencode(utf8_encode($char));
      $url = sprintf("http://armory.wow-europe.com/character-sheet.xml?r=%s&n=%s",$realm,$char);
      $this->xml = simplexml_load_file($url);
      if(empty($this->xml))throw new Exception(sprintf('%s [%s,%s]','armoryNoReply',$realm,$char));
      if($this->xml->errorCode['value'])throw new Exception(sprintf('%s [%s,%s]',$this->xml->errorCode['value'],$realm,$char));
      if($this->xml->characterInfo['errCode'])throw new Exception(sprintf('%s [%s,%s]',$this->xml->characterInfo['errCode'],$realm,$char));
      return( !empty($this->xml) && empty($this->xml->errorCode['value']) && empty($this->xml->characterInfo['errCode']) );
    }
    
    private function load($realm,$char,$lang='de-de'){
      $lang = implode('',explode('-',$lang));
      $cache = sprintf(TYPO3TEMP.'wowcharacter-%s-%s-%s.xml',strtolower($realm),strtolower($char),strtolower($lang));
      if( !file_exists($cache) || ( ( time() - filemtime($cache) ) > CACHETIME ) ){// if cache not exists or too old
        if( $this->query($realm,$char,$lang) ){// if query successfull
          $this->save($cache);// save data
        }else{
          if( file_exists($cache) ){// fallback to saved data if exists
            $this->xml = simplexml_load_file($cache);
          }else{
            throw new Exception('noDataAvailable');
          }
        }
      }else{
        $this->xml = simplexml_load_file($cache);
      }
      return( !empty($this->xml) );
    }
    
    private function save($filename){
      $this->xml->asXML($filename);
    }
    
    public function getItem($pos,$tag){
      if(empty($this->items[$pos]))return null;
      return strval($this->items[$pos][$tag]);
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/pi1/class.tx_wowcharacter_character.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wow_character/pi1/class.tx_wowcharacter_character.php']);
}

//print('<pre style="text-align:left;position:absolute;">');var_dump($GLOBALS['TSFE']->fe_user->user);print('</pre>');
?>