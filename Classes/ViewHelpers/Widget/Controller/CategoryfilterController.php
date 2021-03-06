<?php
namespace ADWLM\CategorySelector\ViewHelpers\Widget\Controller;
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

class CategoryfilterController extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController {

	/**
	 * @var array
	 */
	protected $configuration = array('propertyName' => 'category', 'displaySelectedCategoryNames' => 1, 'pluginNamespace' => 'tx_categoryselector_pi1');

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	protected $objects;

	/**
	 * @var \ADWLM\CategorySelector\Domain\Repository\CategoryRepository
	 * @inject
	 */
	protected $categoryRepository;

	/**
	 * @return void
	 */
	public function initializeAction() {
		$this->objects = $this->widgetConfiguration['objects'];
		$this->configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule($this->configuration, $this->widgetConfiguration['configuration'], TRUE);
	}

	/**
	 * @return void
	 */
	public function indexAction() {

		$pluginNamespaceArguments = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET($this->configuration['pluginNamespace']);

		if ($pluginNamespaceArguments['selectedCategories']) {

			$selectedCategories = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $pluginNamespaceArguments['selectedCategories']);

			foreach ($selectedCategories as $selectedCategory) {
				$selectedCategoriesArray[] = $this->categoryRepository->findByUid($selectedCategory);
			}

			$this->view->assign('selectedCategories', implode(',', $selectedCategories));
			$this->view->assign('selectedCategoriesArray', $selectedCategoriesArray);

			$query = $this->objects->getQuery();

			$newConstraints = array();
			$categoryConstraints = array();
			foreach($selectedCategories as $selectedCategory) {
				$categoryConstraints[] = $query->contains($this->configuration['propertyName'], $selectedCategory);
			}
//			$newConstraints[] = $query->logicalAnd($categoryConstraints);
			$newConstraints[] = $query->logicalOr($categoryConstraints);

			if (is_object($existingConstraints = $query->getConstraint())) {
				$newConstraints[] = $existingConstraints;
			}

			$query->matching(
				$query->logicalAnd($newConstraints)
			);

			$modifiedObjects = $query->execute();

			$this->view->assign('contentArguments', array(
				$this->widgetConfiguration['as'] => $modifiedObjects
			));

			$this->view->assign('objectCount', $modifiedObjects->count());

		} else {
			$this->view->assign('contentArguments', array(
				$this->widgetConfiguration['as'] => $this->objects
			));
		}

		$this->view->assign('configuration', $this->configuration);

	}

}

?>