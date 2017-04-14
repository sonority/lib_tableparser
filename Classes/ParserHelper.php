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

/**
 * Class ParserHelper
 *
 * @author Stephan Kellermayr <stephan.kellermayr@gmail.com>
 */
class ParserHelper
{

    /**
     * Encoding list
     *
     * @var array
     */
    public static $encodingList = [
        'UTF-8', 'ASCII',
        'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
        'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
        'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
        'Windows-1251', 'Windows-1252', 'Windows-1254',
    ];

    /**
     * Parse XML-data and read the tables/rows/cells into array
     *
     * @param \SimpleXMLElement $xml
     * @param string $sheetName
     * @param string $rowName
     * @param string $cellName
     * @param string $defaultNamespace
     * @return array
     */
    public static function getRowsFromXml(\SimpleXMLElement $xml, $sheetName = '', $rowName = '', $cellName = '', $defaultNamespace = 'ss', $allSheets = false)
    {
        $data = [];
        // Load namespaces from XML and register them
        foreach ($xml->getDocNamespaces() as $strPrefix => $strNamespace) {
            if (strlen($strPrefix) === 0) {
                // Assign an arbitrary namespace prefix.
                $strPrefix = $defaultNamespace;
            }
            // Register namespace
            $xml->registerXPathNamespace($strPrefix, $strNamespace);
        }
        // Get sheets
        $xmlSheets = $xml->xpath($sheetName);
        $i = 0;
        // Iterate through sheets
        foreach ($xmlSheets as $xmlSheet) {
            // Process only first sheet per default
            if ($i === 0 || $allSheets) {
                // Get rows
                $xmlRows = $xmlSheet->xpath($rowName);
                // Iterate through rows
                foreach ($xmlRows as $xmlRow) {
                    $finalRow = [];
                    // Get text-nodes from table-cells
                    $xmlCell = $xmlRow->xpath($cellName);
                    // Iterate through cells
                    foreach ($xmlCell as $content) {
                        $finalRow[] = $content->__toString();
                    }
                    // Add a new row if all cells are non empty
                    if (count($finalRow)) {
                        $data[] = $finalRow;
                    }
                }
            }
            // Increment sheet count
            $i++;
        }
        return $data;
    }

    /**
     * Load a specific file within a ZIP-archive
     *
     * @param string $archiveFile
     * @param string $dataFile
     * @return mixed
     */
    public static function readCompressedFile($archiveFile, $dataFile)
    {
        $data = '';
        // Create ZIP object
        $zip = new \ZipArchive;
        // Open archive file
        if ($zip->open($archiveFile) === true) {
            // Search for the data file in the archive
            if (($index = $zip->locateName($dataFile)) !== false) {
                // Read file into string
                $data = $zip->getFromIndex($index);
            }
            // Close archive file
            $zip->close();
        }
        return $data;
    }

    /**
     * Check if zip-extension is installed
     *
     * @return void
     * @throws ParserException
     */
    public static function checkZip()
    {
        // Check if the PHP-extension for unzipping ODS and XLSX is enabled
        if (!extension_loaded('zip')) {
            if (!dl('zip.so')) {
                throw new ParserException('Libzip is missing!');
            }
        }
    }

}
