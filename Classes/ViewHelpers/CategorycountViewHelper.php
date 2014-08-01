<?php
namespace ADWLM\CategorySelector\ViewHelpers;

/***************************************************************
*  Copyright notice
*
*  (c) 2013 Torsten Schrade <Torsten.Schrade@adwmainz.de>
*
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


class CategorycountViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @param string $categorizedObject
	 * @param string $categorizedObjectPids
	 * @param integer $currentCategory
	 * @param string $selectedCategories
	 * 
	 * @return integer
	 */
	public function render($categorizedObject, $categorizedObjectPids, $currentCategory, $selectedCategories) {

var_dump($selectedCategories);

		if (substr($categorizedObject, strrpos($categorizedObject, '_')+1) > 0) {
			$table = substr($categorizedObject, 0, strrpos($categorizedObject, '_'));
		} else {
			$table = $categorizedObject;
		}

		$where = 'sys_category_record_mm.tablenames = \'' . $table . '\' AND sys_category_record_mm.uid_local = ' . (int) $currentCategory;

		if ($categorizedObjectPids) {
			$where .= ' AND ' . $table . '.pid IN (' . $GLOBALS['TYPO3_DB']->cleanIntList($categorizedObjectPids) . ')';
		}

		if ($selectedCategories) {
			$categories = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $selectedCategories, 1);
			foreach ($categories as $category) {
				$where .= '';
			}
		}

		$cObj = $this->objectManager->get('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
		$enableFields = $cObj->enableFields($table);

$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = TRUE;

		$count = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows(
			'*',
			$table . ' LEFT JOIN sys_category_record_mm ON sys_category_record_mm.uid_foreign = ' . $table . '.uid',
			$where . $enableFields
		);

var_dump($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
die();

		return $count;
	}
}
?>