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
 * Interface ParserProviderInterface
 *
 * @author Stephan Kellermayr <stephan.kellermayr@gmail.com>
 */
interface ParserProviderInterface
{

    /**
     * Prepare the parser
     *
     * @param Parser $parser
     * @param string $filePath
     * @param array $options
     * @return void
     */
    public function parseData(Parser $parser, $filePath = '', array $options = []);
}
