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
	 * @var \ADWLM\CategorySelector\Domain\Repository\CategoryRepository
	 * @inject
	 */
	protected $categoryRepository;

	/**
	 * @param integer $currentCategory
	 * @param string $selectedCategories
	 * @param string $categorizedObject
	 * @param string $categorizedObjectPids
	 * 
	 * @return integer
	 */
	public function render($currentCategory, $selectedCategories, $categorizedObject, $categorizedObjectPids) {

		if (substr($categorizedObject, strrpos($categorizedObject, '_')+1) > 0) {
			$table = substr($categorizedObject, 0, strrpos($categorizedObject, '_'));
		} else {
			$table = $categorizedObject;
		}

		$selectedCategoriesArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $selectedCategories, 1);

		$count = $this->categoryRepository->findCategoryCount($currentCategory, $selectedCategoriesArray, $table, $categorizedObjectPids);

		return $count;
	}

}
?>