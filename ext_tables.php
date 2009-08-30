<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$TCA["tx_wowcharacter_characters"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:wow_character/locallang.xml:tx_wowcharacter_characters',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY name",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_wowcharacter_characters.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden, fe_group, fe_user, realm, name, avatar",
	)
);

/*PI1*/
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';// hide ?,?,startingpoint
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';// you add pi_flexform to be renderd when your plugin is shown
t3lib_extMgm::addPlugin(array('LLL:EXT:'.$_EXTKEY.'/locallang.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','WOW - Character Display');// add static typoscript
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/pi1/flexform.xml');// add flexform description file 
if (TYPO3_MODE=='BE')	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_wowcharacter_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_wowcharacter_pi1_wizicon.php';

/*PI2*/
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key,pages';// hide ?,?,startingpoint
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi2']='pi_flexform';// you add pi_flexform to be renderd when your plugin is shown
t3lib_extMgm::addPlugin(array('LLL:EXT:'.$_EXTKEY.'/locallang.xml:tt_content.list_type_pi2', $_EXTKEY.'_pi2'),'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY,'pi2/static/','WOW - Character List');// add static typoscript
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:'.$_EXTKEY.'/pi2/flexform.xml');// add flexform description file 
if (TYPO3_MODE=='BE')	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_wowcharacter_pi2_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi2/class.tx_wowcharacter_pi2_wizicon.php';

?>