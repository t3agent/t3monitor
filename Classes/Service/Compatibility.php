<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Oliver Hader <oliver.hader@typo3.org>
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * This class is a copy of the tt_news class class.tx_ttnews_compatibility.php
 */
class Tx_T3monitor_Service_Compatibility {
	/**
	 * @var boolean
	 */
	protected $isVersion6 = FALSE;

	/**
	 * @var t3lib_l10n_parser_Llxml
	 */
	protected $llxmlParser;

	/**
	 * @return Tx_T3monitor_Service_Compatibility
	 */
	public static function getInstance() {
		return self::makeInstance('Tx_T3monitor_Service_Compatibility');
	}

	/**
	 * Creates this object.
	 */
	public function __construct() {
		if (class_exists('t3lib_utility_VersionNumber')) {
			if (Tx_T3monitor_Service_Compatibility::int_from_ver(TYPO3_version) >= 6000000) {
				$this->isVersion6 = TRUE;
			}
		}
	}

	/**
	 * Forces the integer $theInt into the boundaries of $min and $max. If the $theInt is 'FALSE' then the $zeroValue is applied.
	 *
	 * @param integer $theInt Input value
	 * @param integer $min Lower limit
	 * @param integer $max Higher limit
	 * @param integer $zeroValue Default value if input is FALSE.
	 * @return integer The input value forced into the boundaries of $min and $max
	 */
	public function intInRange($theInt, $min, $max = 2000000000, $zeroValue = 0) {
			// Returns $theInt as an integer in the integerspace from $min to $max
		$theInt = intval($theInt);
		if ($zeroValue && !$theInt) {
			$theInt = $zeroValue;
		} // If the input value is zero after being converted to integer, zeroValue may set another default value for it.
		if ($theInt < $min) {
			$theInt = $min;
		}
		if ($theInt > $max) {
			$theInt = $max;
		}
		return $theInt;
	}

	/**
	 * Returns an integer from a three part version number, eg '4.12.3' -> 4012003
	 *
	 * @param string $versionNumber Version number on format x.x.x
	 * @return integer Integer version of version number (where each part can count to 999)
	 */
	public function int_from_ver($versionNumber) {
		$versionParts = explode('.', $versionNumber);
		return intval(((int) $versionParts[0] . str_pad((int) $versionParts[1], 3, '0', STR_PAD_LEFT)) . str_pad((int) $versionParts[2], 3, '0', STR_PAD_LEFT));
    }

	public function testInt($var) {
		if ($var === '') {
			return FALSE;
		}
		return (string) intval($var) === (string) $var;
	}
    public function array2xml(array $array, $NSprefix = '', $level = 0, $docTag = 'phparray') {
        if (class_exists('t3lib_div')) {
            return t3lib_div::array2xml($array, '', 0, 'xml');
        } else {
            return  \TYPO3\CMS\Core\Utility\GeneralUtility::array2xml($array, $NSprefix, $level, $docTag);
        }
    }
    public function compat_version($verNumberStr) {
        if (class_exists('t3lib_div')) {
            return t3lib_div::compat_version($verNumberStr);
        } else {
            return  \TYPO3\CMS\Core\Utility\GeneralUtility::compat_version($verNumberStr);
        }
    }

    public function trimExplode($delim, $string, $removeEmptyValues = FALSE, $limit = 0) {
        if (class_exists('t3lib_div')) {
            return t3lib_div::trimExplode($delim, $string, $removeEmptyValues, $limit);
        } else {
            return  \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode($delim, $string, $removeEmptyValues, $limit);
        }
    }

    public function verifyFilenameAgainstDenyPattern($filename) {
        if (class_exists('t3lib_div')) {
            return t3lib_div::verifyFilenameAgainstDenyPattern($filename);
        } else {
            return  \TYPO3\CMS\Core\Utility\GeneralUtility::verifyFilenameAgainstDenyPattern($filename);
        }
    }

	/**
	 * Includes a locallang-xml file and returns the $LOCAL_LANG array
	 * Works only when the frontend or backend has been initialized with a charset conversion object. See first code lines.
	 *
	 * @param string $fileRef Absolute reference to locallang-XML file
	 * @param string $langKey TYPO3 language key, eg. "dk" or "de" or "default"
	 * @param string $charset Character set (optional)
	 * @return array LOCAL_LANG array in return.
	 */
	public function readLLXMLfile($fileRef, $langKey, $charset = '') {
		if ($this->isVersion6) {
			return $this->getLlxmlParser()->getParsedData($fileRef, $langKey, $charset);
		} else {
			return t3lib_div::readLLXMLfile($fileRef, $langKey, $charset);
		}
	}

	/**
	 * @return t3lib_l10n_parser_Llxml
	 */
	protected function getLlxmlParser() {
		if (!isset($this->llxmlParser)) {
			$this->llxmlParser = self::makeInstance('TYPO3\\CMS\\Core\\Localization\\Parser\\LocallangXmlParser');
		}
		return $this->llxmlParser;
	}

    /**
     * Initializes language object
     */
    public function initLang()
    {
        $t3ver = $this->int_from_ver(TYPO3_version);
        if ($t3ver >= 6000000) {
            require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('lang') . 'Classes/LanguageService.php';
            $GLOBALS['LANG'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Lang\LanguageService');
            $GLOBALS['LANG']->init('en');
        } else {
            require_once(PATH_typo3.'sysext/lang/lang.php');
            $GLOBALS['LANG'] = t3lib_div::makeInstance('language');
            $GLOBALS['LANG']->init('en');
        }
    }

    /**
     * Creates an instance of a class taking into account the class-extensions
     *
     * @throws \InvalidArgumentException if class name is an empty string
     * @param string $className name of the class to instantiate, must not be empty
     * @return object the created instance
     */
    public static function makeInstance($className)
    {
        if (class_exists('t3lib_div')) {
            return t3lib_div::makeInstance($className);
        } else {
            return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($className);
        }
    }

    public function initTsfe()
    {
        global $TYPO3_CONF_VARS;
        // $GLOBALS['TSFE']->sys_page only needed for TYPO3 >= 6.x
        $t3ver = $this->int_from_ver(TYPO3_version);
        if ($t3ver >= 6000000) {
            if (!($GLOBALS['TSFE'] instanceof \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController)) {
                $pageId = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');
                if (empty($pageId)) {
                    $db = Tx_T3monitor_Helper_Database::getInstance();
                    $startRow = $db->getStartPage();
                    if (!empty($startRow)) {
                        $pageId = $startRow['uid'];
                    }
                }
				/** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController */
                $GLOBALS['TSFE'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', $TYPO3_CONF_VARS, $pageId, 0, true);
				if (!isset($GLOBALS['TSFE']->config['config'])) {
					$GLOBALS['TSFE']->config['config'] = array();
				};
            }
            if (!($GLOBALS['TSFE']->sys_page instanceof \TYPO3\CMS\Frontend\Page\PageRepository)) {
                $GLOBALS['TSFE']->sys_page = self::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
            }
        }
    }
}
?>