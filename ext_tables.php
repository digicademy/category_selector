<?php
if (!defined ('TYPO3_MODE')){
	die ('Access denied.');
}

// static TS for the extension
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Category Selector');

// CATEGORIES PLUGIN
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Pi1',
	'Category Selector'
);

// PLUGIN FLEXFORMS

$TCA['tt_content']['types']['list']['subtypes_addlist']['categoryselector_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('categoryselector_pi1', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/CategorySelector.xml');

?>