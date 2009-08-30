<?php
require_once(PATH_t3lib.'class.t3lib_befunc.php');

/* append this to your tca.php *****************************************************************************************

if( (TYPO3_MODE=="BE") && (t3lib_div::int_from_ver(TYPO3_version) >= 4001000) ){
  require_once(t3lib_extMgm::extPath('wow_character').'inc/class.tx_wowcharacter_itemsProcFunc.php');
  $TCA["tx_wowcharacter_characters"]["columns"]["fe_group"]["config"]["itemsProcFunc"] = "tx_wowcharacter_itemsProcFunc->getGuildList";// edit view
}

***********************************************************************************************************************/

class tx_wowcharacter_itemsProcFunc{

  function getGuildList($config){
    $config['items'] = array();
    if($config['row']['fe_user']){
      $fe_user = t3lib_BEfunc::getRecord('fe_users',$config['row']['fe_user']); //spieler aus Tabelle holen
      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','fe_groups',sprintf('uid IN (%s)',$fe_user['usergroup']));
      while( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res) )$config['items'][] = array($row['title'],intval($row['uid']));
    }else{
      $config['items'] = array(array('[save player first]',0));
    }
    return $config;
  }
  
}

?>