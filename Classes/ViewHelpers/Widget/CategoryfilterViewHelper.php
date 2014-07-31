<?php
namespace ADWLM\CategorySelector\ViewHelpers\Widget;
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

class CategoryfilterViewHelper extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper {

	/**
	 * @var \ADWLM\CategorySelector\ViewHelpers\Widget\Controller\CategoryfilterController
	 */
	protected $controller;

	/**
	 * @param \ADWLM\CategorySelector\ViewHelpers\Widget\Controller\CategoryfilterController $controller
	 * @return void
	 */
	public function injectController(\ADWLM\CategorySelector\ViewHelpers\Widget\Controller\CategoryfilterController $controller) {
		$this->controller = $controller;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $objects
	 * @param string $as
	 * @param array $configuration
	 * @return string
	 */
	public function render(\TYPO3\CMS\Extbase\Persistence\QueryResultInterface $objects, $as, array $configuration = array('propertyName' => 'category', 'displaySelectedCategoryNames' => 1)) {
		return $this->initiateSubRequest();
	}
}

?>