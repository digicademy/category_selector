<?php
if (!defined('TYPO3_MODE')) die('Access denied!');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'ADWLM.' . $_EXTKEY,
	'Pi1',
	array(
		'Category' => 'list, select',
	),
	array(
		'Category' => 'select',
	)
);
?>