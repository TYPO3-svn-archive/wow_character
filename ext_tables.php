<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$TCA["tx_wowcharacter_characters"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:wow_character/locallang_db.xml:tx_wowcharacter_characters',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_wowcharacter_characters.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden, fe_group, realm, name, avatar",
	)
);

$tempColumns = Array (
	"tx_wowcharacter_wowchars" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:wow_character/locallang_db.xml:fe_users.tx_wowcharacter_wowchars",		
		"config" => Array (
			"type" => "group",	
			"internal_type" => "db",	
			"allowed" => "tx_wowcharacter_characters",	
			"size" => 5,	
			"minitems" => 0,
			"maxitems" => 100,
		)
	),
);


t3lib_div::loadTCA("fe_users");
t3lib_extMgm::addTCAcolumns("fe_users",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("fe_users","tx_wowcharacter_wowchars;;;;1-1-1");


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array('LLL:EXT:wow_character/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","WOW - Character Display");


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_wowcharacter_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_wowcharacter_pi1_wizicon.php';


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key';


t3lib_extMgm::addPlugin(array('LLL:EXT:wow_character/locallang_db.xml:tt_content.list_type_pi2', $_EXTKEY.'_pi2'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi2/static/","WOW - Character List");

if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_wowcharacter_pi2_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi2/class.tx_wowcharacter_pi2_wizicon.php';

include(t3lib_extMgm::extPath('wow_character').'ext_tables.inc');//use flexforms

?>