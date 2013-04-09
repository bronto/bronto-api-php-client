<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 */
class Bronto_Api_Field_TypeGuesser
{
    const DEFAULT_CHOICE       = 'text';
    const CONFIDENCE_THRESHOLD = 70;

    /**
     * @var int
     */
    protected $_total = 0;

    /**
     * @var array
     */
    protected $_guesses = array();

    /**
     * @var array
     */
    protected $_defaultGuesses = array(
        'text'     => 0,
        'integer'  => 0,
        'currency' => 0,
        'float'    => 0,
        'date'     => 0,
    );

    /**
     * @return text
     */
    public function getChoice()
    {
        if ($this->_total > 0) {
            arsort($this->_guesses);

            foreach ($this->_guesses as $type => $votes) {
                $confidence = ($votes / $this->_total) * 100;
                if ($confidence >= self::CONFIDENCE_THRESHOLD) {
                    return $type;
                }
                break;
            }
        }

        return self::DEFAULT_CHOICE;
    }

    /**
     * @param type $name
     * @param array $values
     * @param bool $inversed
     */
    public function processValues(array $values = array(), $inversed = true)
    {
        $this->_guesses = $this->_defaultGuesses;

        foreach ($values as $i => $value) {
            $this->_total++;

            if ($inversed) {
                $this->processValue($i);
            } else {
                $this->processValue($value);
            }

            if ($this->_total === 1000) {
                break;
            }
        }
    }

    /**
     * @param text $name
     * @param mixed $value
     */
    protected function processValue($value)
    {
        if ($value === '') {
            return;
        }

        // Only A-Z
        if (!is_int($value) && ctype_alpha($value)) {
            $this->_guesses['text'] += 1;
            return;
        }

        // Only 0-9
        if (is_int($value) || ctype_digit($value)) {
            if (strlen($value) <= 11) {
                $this->_guesses['integer'] += 1;
            } else {
                $this->_guesses['text']  += 1;
                $this->_guesses['integer']  = 0;
            }
            return;
        }

        // 0-9 with .
        if (is_numeric($value)) {
            if (strlen($value) <= 15) {
                $this->_guesses['currency'] += 1;
            } elseif (strlen($value) <= 53) {
                $this->_guesses['float']    += 1;
                $this->_guesses['currency']  = 0;
            } else {
                $this->_guesses['text']   += 1;
                $this->_guesses['float']     = 0;
                $this->_guesses['currency']  = 0;
            }
            return;
        }

        if (strtotime($value) !== false) {
            // Possible date
            $this->_guesses['date'] += 1;
        } else {
            // String is last choice
            $this->_guesses['text'] += 1;
        }
    }
}
