<?php

namespace Sonority\LibTableparser;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Sonority\LibTableparser\Exception\ParserException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * The main factory class, which acts as the entrypoint for generating an Parser object which
 * is responsible for rendering an parser. Checks for the correct parser provider through the ParserRegistry.
 *
 * USAGE:
 *   use Sonority\LibTableparser\ParserFactory;
 *   $this->parserFactory = GeneralUtility::makeInstance(ParserFactory::class)->getData($absFileName);
 *
 * @author Stephan Kellermayr <stephan.kellermayr@gmail.com>
 */
class ParserFactory
{

    /**
     * @var ParserRegistry
     */
    protected $parserRegistry;

    /**
     * @param ParserRegistry $parserRegistry
     * @return void
     */
    public function __construct(ParserRegistry $parserRegistry = null)
    {
        $this->parserRegistry = $parserRegistry ? $parserRegistry : GeneralUtility::makeInstance(ParserRegistry::class);
    }

    /**
     * @param string $filePath
     * @param string $identifier
     * @param array $options
     * @return Parser
     */
    public function getData($filePath, $identifier = null, $options = [])
    {
        if (!self::isAllowedAbsPath($filePath)) {
            throw new ParserException('The filepath "' . $filePath . '" is not allowed by TYPO3.');
        }

        // Set parser configuration default values
        $parserConfiguration['options'] = [
            'colLimit' => 0,
            'rowLimit' => 0,
            'header' => true
        ];

        // Autodetect file format
        if (!$identifier || empty($identifier)) {
            $identifier = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        }
        // Get parser configuration and merge it
        ArrayUtility::mergeRecursiveWithOverrule($parserConfiguration,
            $this->parserRegistry->getParserConfigurationByIdentifier($identifier));

        // Merge provided options into parserConfiguration
        if (is_array($options) && count($options)) {
            ArrayUtility::mergeRecursiveWithOverrule($parserConfiguration['options'], $options);
        }

        $parser = GeneralUtility::makeInstance(Parser::class);

        /** @var ParserProviderInterface $ParserProvider */
        $parserProvider = GeneralUtility::makeInstance($parserConfiguration['provider']);
        $parserProvider->parseData($parser, $filePath, $parserConfiguration['options']);

        return $parser;
    }

    /**
     * Returns TRUE if the path is absolute, without backpath '..' and within 'upload_tmp_dir' OR within the lockRootPath
     *
     * @param string $path File path to evaluate
     * @return bool
     */
    protected static function isAllowedAbsPath($path)
    {
        $lockRootPath = $GLOBALS['TYPO3_CONF_VARS']['BE']['lockRootPath'];
        return GeneralUtility::isAbsPath($path) && GeneralUtility::validPathStr($path) && (GeneralUtility::isFirstPartOfStr($path,
                ini_get('upload_tmp_dir')) || $lockRootPath && GeneralUtility::isFirstPartOfStr($path, $lockRootPath));
    }

}
