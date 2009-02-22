<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_wowcharacter_characters"] = array (
	"ctrl" => $TCA["tx_wowcharacter_characters"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,fe_group,realm,name,avatar"
	),
	"feInterface" => $TCA["tx_wowcharacter_characters"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		"realm" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wow_character/locallang_db.xml:tx_wowcharacter_characters.realm",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"name" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wow_character/locallang_db.xml:tx_wowcharacter_characters.name",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required,trim",
			)
		),
		"avatar" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wow_character/locallang_db.xml:tx_wowcharacter_characters.avatar",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "gif,png,jpeg,jpg",	
				"max_size" => 100,	
				"uploadfolder" => "uploads/tx_wowcharacter",
				"show_thumbs" => 1,	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, realm, name, avatar")
	),
	"palettes" => array (
		"1" => array("showitem" => "fe_group")
	)
);
?>