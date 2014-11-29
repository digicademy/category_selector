<?php
namespace ADWLM\CategorySelector\Domain\Repository;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Torsten Schrade <Torsten.Schrade@adwmainz.de>, Academy of Sciences and Literature | Mainz
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

class CategoryRepository extends \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository {

	protected $defaultOrderings = array('title' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING);

	/**
	 * Finds categories based on a selection (CSV list)
	 *
	 * @param string $selectedCategories
	 *
	 * @return object
	 */
	public function findSelectedCategories($selectedCategories) {

		$query = $this->createQuery();

		$constraints = array();

		$selectedCategories = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $selectedCategories);

		foreach($selectedCategories as $selectedCategory) {
			$constraints[] = $query->like('uid', $selectedCategory);
		}

		$query->matching(
			$query->logicalOr($constraints)
		);

		$result = $query->execute();

		return $result;
	}

	/**
	 * Finds categories based on their parents, possibly taking categories2skip into account
	 *
	 * @param integer $parent
	 * @param array $categories2skip
	 *
	 * @return object
	 */
	public function findByParent($parent, $categories2skip = array()) {

		$query = $this->createQuery();

		$constraints = array();

		$constraints[] = $query->equals('parent', $parent);

		if (count($categories2skip) > 0) {
			$constraints[] = $query->logicalNot($query->in('uid', $categories2skip));
		}

		$query->matching(
			$query->logicalAnd($constraints)
		);

		$result = $query->execute();

		return $result;
	}

	/**
	 * Counts objects belonging to the current category taking the currently selected categories into account. Categories are
	 * ANDed, which means that all selected categories AND the current category must match for an object to be included in the count.
	 *
	 * @param integer $category
	 * @param array $selectedCategories
	 * @param string $table
	 * @param string $pids
	 *
	 * @return integer
	 */
	public function findCategoryCount($category, $selectedCategories = array(), $table, $pids = '') {

			// full quote table name but trim first and last '
		$tablename = substr(substr($GLOBALS['TYPO3_DB']->fullQuoteStr($table, $table), 1), 0, -1);

			// prepare categories for query
		$selectedCategories[] = $category;
		$categories = $GLOBALS['TYPO3_DB']->cleanIntArray(array_unique($selectedCategories));

			// append enable fields for given table
		$cObj = $this->objectManager->get('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
		$enableFields = $cObj->enableFields($table);

			// optional pid
		if ($pids) {
			$wherePid = ' AND ' . $tablename . '.pid IN (' . $GLOBALS['TYPO3_DB']->cleanIntList($pids) . ')';
		}

//		$categoryStatementPart = ' AND sys_category_record_mm.uid_local IN ('. implode(',', $categories) .')';
		$categoryStatementPart = ' AND sys_category_record_mm.uid_local = ' . (int) $category;
//		$categoryStatementCount = count($categories);
		$categoryStatementCount = 1;

		$statement = '
			SELECT COUNT(*) AS count FROM (
				SELECT ' . $tablename . '.uid
				FROM '. $tablename .'
				LEFT OUTER JOIN sys_category_record_mm ON sys_category_record_mm.uid_foreign = ' . $tablename . '.uid
				WHERE ' . $tablename . '.sys_language_uid = ' . (int) $GLOBALS['TSFE']->sys_language_uid . '
				AND sys_category_record_mm.tablenames = \'' . $tablename . '\'' .
				$categoryStatementPart .
				$wherePid . $enableFields . '
				GROUP BY ' . $tablename . '.uid
				HAVING COUNT(DISTINCT sys_category_record_mm.uid_local) = ' . $categoryStatementCount . '
			) AS count;
		';

			// create the query object
		$query = $this->createQuery();

			// ignore storage PID and use TS/FF settings instead
		$query->getQuerySettings()->setRespectStoragePage(FALSE);

			// raw result
		$query->getQuerySettings()->setReturnRawQueryResult(TRUE);

			// set statement
		$query->statement($statement);

			// execute
		$result = $query->execute();

		return $result[0]['count'];
	}

}
?>