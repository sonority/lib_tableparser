<?php

namespace Sonority\LibTableparser\ParserProvider;

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

use Sonority\LibTableparser\Parser;
use Sonority\LibTableparser\ParserProviderInterface;
use Sonority\LibTableparser\ParserHelper;

/**
 * Class CsvParserProvider
 *
 * @author Stephan Kellermayr <stephan.kellermayr@gmail.com>
 */
class CsvParserProvider implements ParserProviderInterface
{

    /**
     * Parse CSV
     *
     * @param Parser $parser
     * @param string $filePath
     * @param array $options
     * @return void
     */
    public function parseData(Parser $parser, $filePath = '', array $options = [])
    {
        $data = [];
        $rows = [];
        if (empty($options['delimiter'])) {
            $options['delimiter'] = ',';
        }
        if (empty($options['enclosure'])) {
            $options['enclosure'] = '"';
        }
        // Read content of source file
        $content = file_get_contents($filePath);
        // Set target encoding
        $encodeTo = 'UTF-8';
        // Determine source Encoding
        $encodeFrom = mb_detect_encoding($content, ParserHelper::$encodingList, true);
        // Explode by linebreaks into array
        $source = explode(chr(10), mb_convert_encoding($content, $encodeTo, $encodeFrom));
        foreach ($source as $line) {
            if (!empty(trim($line))) {
                $data[] = $line;
            }
        }
        // Parse csv-lines into array
        if (count($data)) {
            foreach ($data as $value) {
                $rows[] = str_getcsv($value, $options['delimiter'], $options['enclosure']);
            }
        }
        $parser->setRows($rows);
    }

}
