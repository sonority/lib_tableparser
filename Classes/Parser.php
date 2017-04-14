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
 * Parser Utility class
 */
class Parser
{

    protected $rows = [];

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param string $rows
     * @return void
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
    }

}
