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
 * Class XmlParserProvider
 *
 * @author Stephan Kellermayr <stephan.kellermayr@gmail.com>
 */
class XmlParserProvider implements ParserProviderInterface
{

    /**
     * Parse MS-XML
     *
     * @param Parser $parser
     * @param string $filePath
     * @param array $options
     * @return void
     */
    public function parseData(Parser $parser, $filePath = '', array $options = [])
    {
        // Load file into XML-object
        $xml = new \SimpleXMLElement($filePath, null, true);
        $parser->setRows(
            ParserHelper::getRowsFromXml(
                $xml, $options['nodes']['table'], $options['nodes']['row'], $options['nodes']['cell'], $options['nodes']['ns']
            )
        );
    }

}
