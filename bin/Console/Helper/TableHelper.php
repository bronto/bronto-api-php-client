<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Console\Helper;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Helper;

/**
 *
 *
 * @author Chris Jones <leeked@gmail.com>
 */
class TableHelper extends Helper
{
    /**
     * @var OutputInterface
     */
    private $output;

    private $headings = array();
    private $rows = array();

    public function addHeading($label, $key = null)
    {
        if ($key === null) {
            $this->headings[] = $label;
        } else {
            $this->headings[$key] = $label;
        }
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'table';
    }
}
