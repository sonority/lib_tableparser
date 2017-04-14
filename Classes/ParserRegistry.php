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
use Sonority\LibTableparser\ParserProvider\CsvParserProvider;
use Sonority\LibTableparser\ParserProvider\OdsParserProvider;
use Sonority\LibTableparser\ParserProvider\XmlParserProvider;
use Sonority\LibTableparser\ParserProvider\XlsxParserProvider;

/**
 * Class ParserRegistry, which makes it possible to register custom parsers from within an extension.
 *
 * Usage:
 *   $parser = GeneralUtility::makeInstance(ParserFactory::class);
 *   $rows = $parser->getData($absFilePath, $fileType, $parserOptions)->getRows();
 *
 * @author Stephan Kellermayr <stephan.kellermayr@gmail.com>
 */
class ParserRegistry implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * Registered parsers
     *
     * @var array
     */
    protected $parsers = [
        // Mimetypes
        'application/vnd.oasis.opendocument.spreadsheet' => [
            'provider' => OdsParserProvider::class,
            'options' => [
                'icon' => 'mimetypes-open-document-spreadsheet',
                'nodes' => [
                    'ns' => 'office',
                    'table' => '//office:body/office:spreadsheet/table:table',
                    'row' => 'table:table-row',
                    'cell' => 'table:table-cell/text:p'
                ],
                'allSheets' => false
            ]
        ],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => [
            'provider' => XlsxParserProvider::class,
            'options' => [
                'icon' => 'mimetypes-excel'
            ]
        ],
        'text/csv' => [
            'provider' => CsvParserProvider::class,
            'options' => [
                'icon' => 'mimetypes-text-csv',
                'delimiter' => ',',
                'enclosure' => '\"'
            ]
        ],
        'text/xml' => [
            'provider' => XmlParserProvider::class,
            'options' => [
                'icon' => 'mimetypes-text-html',
                'nodes' => [
                    'ns' => 'ss',
                    'table' => '//ss:Worksheet/ss:Table',
                    'row' => 'ss:Row',
                    'cell' => 'ss:Cell/ss:Data'
                ],
                'allSheets' => false
            ]
        ]
    ];

    /**
     * Mapping of file extensions to mimetypes
     *
     * @var string[]
     */
    protected $fileExtensionMapping = [
        'csv' => 'text/csv',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xml' => 'text/xml'
    ];

    /**
     * Registers a parser to be available inside the Parser Factory
     *
     * @param string $identifier
     * @param string $parserProviderClassName
     * @param array $options
     * @return void
     * @throws \InvalidArgumentException
     */
    public function registerParser($identifier, $parserProviderClassName, array $options = [])
    {
        if (!in_array(ParserProviderInterface::class, class_implements($parserProviderClassName), true)) {
            throw new \InvalidArgumentException('An ParserProvider must implement ' . ParserProviderInterface::class . '.');
        }
        $this->parsers[$identifier] = [
            'provider' => $parserProviderClassName,
            'options' => $options
        ];
    }

    /**
     * Register a parser for a specific file extension
     *
     * @param string $fileExtension
     * @param string $parserIdentifier
     * @return void
     */
    public function registerFileExtension($fileExtension, $parserIdentifier)
    {
        $this->fileExtensionMapping[$fileExtension] = $parserIdentifier;
    }

    /**
     * Get the keys of all registered parsers (mime types)
     *
     * @return array
     */
    public function getAllRegisteredParsers()
    {
        return array_keys($this->parsers);
    }

    /**
     * Get the keys of all registered file formats
     *
     * @return array
     */
    public function getAllRegisteredParserFormats()
    {
        return array_keys($this->fileExtensionMapping);
    }

    /**
     * Fetches the configuration provided by registerParser()
     *
     * @param string $identifier The parser identifier
     * @return mixed
     * @throws ParserException
     */
    public function getParserConfigurationByIdentifier($identifier)
    {
        // If the file extension or the parser identifier is not valid return NULL
        if (isset($this->fileExtensionMapping[$identifier])) {
            return $this->parsers[$this->fileExtensionMapping[$identifier]];
        } else {
            if (isset($this->parsers[$identifier])) {
                return $this->parsers[$identifier];
            } else {
                throw new ParserException('Parser or filename mapping with identifier "' . $identifier . '" is not registered.');
            }
        }
    }

}
