<?php
namespace ADWLM\CategorySelector\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Torsten Schrade <Torsten.Schrade@adwmainz.de>, Academy of Sciences and Literature | Mainz
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

class CategoryController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \ADWLM\CategorySelector\Domain\Repository\CategoryRepository
	 * @inject
	 */
	protected $categoryRepository;

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeAction() {
			// assign the selected categories, as CSV list for links and as array holding selected category objects for easy property access
		if ($this->request->hasArgument('selectedCategories')) {
			$selectedCategories = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->request->getArgument('selectedCategories'), TRUE);
			foreach ($selectedCategories as $selectedCategory) {
				$selectedCategoriesArray[] = $this->categoryRepository->findByUid($selectedCategory);
			}
			$this->settings['selectedCategories'] = implode(',', $selectedCategories);
			$this->settings['selectedCategoriesArray'] = $selectedCategoriesArray;
		}
	}

	/**
	 * Displays a category tree as nested list (parent/children)
	 *
	 * @return void
	 */
	public function listAction() {

		$levelCategoryUids = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->settings['parentCategories']);

		if ($this->settings['categories2skip']) {
			$categories2skip = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->settings['categories2skip']);
		}

		$categoryTree = array();

			// builds the category tree as two dimensional array - the keys represent the level of recursion, the values are
			// arrays of child categories per level bound together by the uid of their parent
		for ($i = 0; $i <= $this->settings['recursive']; $i++) {
			if ($i === 0) {
				foreach ($levelCategoryUids as $currentCategoryUid) {
					$parent = $this->categoryRepository->findByUid($currentCategoryUid);
					$categoryTree[$i][] = $parent;
				}
			} else {
				$newLevelCategoryUids = array();
				foreach ($levelCategoryUids as $currentCategoryUid) {
					$parent = $this->categoryRepository->findByUid($currentCategoryUid);
					$children = $this->categoryRepository->findByParent($currentCategoryUid, $categories2skip);
					if (is_object($parent) && $children->count() > 0) {
						$categoryTree[$i][$parent->getUid()] = $children;
						foreach ($children as $child) {
							$newLevelCategoryUids[] = $child->getUid();
						}
					}
				}
				$levelCategoryUids = $newLevelCategoryUids;
			}
		}

		$this->view->assign('categoryTree', $categoryTree);

			// set the target pid and reassign the modified settings
		if (!$this->settings['targetPid']) $this->settings['targetPid'] = $GLOBALS['TSFE']->id;
		$this->view->assign('settings', $this->settings);

	}


	/**
	 * Selects/Unselects categories and returns to the list action
	 *
	 * @param \ADWLM\CategorySelector\Domain\Model\Category $category
	 *
	 * @return void
	 */
	public function selectAction(\ADWLM\CategorySelector\Domain\Model\Category $category) {

			// get current arguments
		$arguments = $this->request->getArguments();

			// clean arguments for redirect
		unset($arguments['category']);

		$selectedCategories = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $arguments['selectedCategories'], TRUE);

			// test if current category was already selected
		$key = array_search($category->getUid(), $selectedCategories);

			// select action: no key exists, add clicked category to selectedCategories
		if ($key === FALSE) {
			$selectedCategories[] = $category->getUid();
			// unselect action: key/category already exists in selectedCategories
		} else {
			unset($selectedCategories[$key]);
		}

			// reconstitute the modified argument
		if (count($selectedCategories) > 0) {
			$arguments['selectedCategories'] = implode(',', $selectedCategories);
			// happens when all categories have been unselected
		} else {
			unset($arguments['selectedCategories']);
		}

			// redirect to list action which will take over the display of the full list with the selected categories
		$this->redirect('list', NULL, NULL, $arguments);
	}
}
?>