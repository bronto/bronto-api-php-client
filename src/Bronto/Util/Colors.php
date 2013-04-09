<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 */
class Bronto_Util_Colors
{
    /**
     * @see http://media.if-not-true-then-false.com/2010/01/PHP_CLI_Coloring_Class_Example.png
     * @var array
     */
    protected $_attributes = array(
        'black'       => array(
            'ansi' => '0;30',
            'hex'  => '#2e3336',
        ),
        'darkgray'    => array(
            'ansi' => '1;30',
            'hex'  => '#1e1e1e',
        ),
        'blue'        => array(
            'ansi' => '0;34',
            'hex'  => '#3466a5',
        ),
        'lightblue'   => array(
            'ansi' => '1;34',
            'hex'  => '#3edde0',
        ),
        'green'       => array(
            'ansi' => '0;32',
            'hex'  => '#4e9a06',
        ),
        'lightgreen'  => array(
            'ansi' => '1;32',
            'hex'  => '#87d939',
        ),
        'cyan'        => array(
            'ansi' => '0;36',
            'hex'  => '#07989b',
        ),
        'lightcyan'   => array(
            'ansi' => '1;36',
            'hex'  => '#39d7da',
        ),
        'red'         => array(
            'ansi' => '0;31',
            'hex'  => '#cc0000',
        ),
        'lightred'    => array(
            'ansi' => '1;31',
            'hex'  => '#c83a39',
        ),
        'purple'      => array(
            'ansi' => '0;35',
            'hex'  => '#75507b',
        ),
        'lightpurple' => array(
            'ansi' => '1;35',
            'hex'  => '#aa7faa',
        ),
        'brown'       => array(
            'ansi' => '0;33',
            'hex'  => '#8d6736',
        ),
        'yellow'      => array(
            'ansi' => '1;33',
            'hex'  => '#c4a001',
        ),
        'lightgray'   => array(
            'ansi' => '0;37',
            'hex'  => '#d2d7d0',
        ),
        'white'       => array(
            'ansi' => '1;37',
            'hex'  => '#ffffff',
        ),
        'onblack'     => array(
            'ansi' => '40',
            'hex'  => '#2e3336',
        ),
        'onred'       => array(
            'ansi' => '41',
            'hex'  => '#cc0001',
        ),
        'ongreen'     => array(
            'ansi' => '42',
            'hex'  => '#4e9a06',
        ),
        'onyellow'    => array(
            'ansi' => '43',
            'hex'  => '#c4a100',
        ),
        'onblue'      => array(
            'ansi' => '44',
            'hex'  => '#3466a5',
        ),
        'onmagenta'   => array(
            'ansi' => '45',
            'hex'  => '#75507b',
        ),
        'oncyan'      => array(
            'ansi' => '46',
            'hex'  => '#07989b',
        ),
        'onlightgray' => array(
            'ansi' => '47',
            'hex'  => '#d4d7d0',
        ),
    );

    /**
     * @param string|array $codes
     * @param bool $html
     * @return string
     */
    public function color($codes = array(), $html = false)
    {
        $attribute = '';
        if (is_string($codes)) {
            $codes = explode(' ', $codes);
        }
        foreach ($codes as $code) {
            $code = strtolower($code);
            if ($html) {
                if (isset($this->_attributes[$code]['hex'])) {
                    if (stripos($code, 'on') === 0) {
                        $attribute .= "background-color: {$this->_attributes[$code]['hex']};";
                    } else {
                        $attribute .= "color: {$this->_attributes[$code]['hex']};";
                    }
                } else {
                    switch ($code) {
                        case 'bold':
                            $attribute .= 'font-weight: bold;';
                            break;
                        default:
                            break;
                    }
                }
            } else {
                if (isset($this->_attributes[$code]['ansi'])) {
                    $attribute .= "{$this->_attributes[$code]['ansi']};";
                }
            }
        }

        if ($html) {
            return $attribute;
        } else {
            $attribute = substr($attribute, 0, -1);
            return empty($attribute) ? false : chr(27) . "[$attribute" . "m";
        }
    }

    /**
     * @param string $text
     * @param string|array $codes
     * @param bool $html
     * @return string
     */
    public function colored($text = '', $codes = array(), $html = false)
    {
        $attr = $this->color($codes, $html);

        if ($html) {
            if (empty($attr)) {
                return false;
            } else {
                return "<span style=\"{$attr}\">{$text}</span>";
            }
        } else {
            return $attr . $text . chr(27) . "[0m";
        }
    }

    /**
     * @param string $text
     * @param bool $html
     * @return string
     */
    public function parse($text = '', $html = false)
    {
        $matches = array();
        preg_match_all('/(<([\w\s]+)[^>]*>)(.*?)(<\/\\2>)/', $text, $matches, PREG_SET_ORDER);
        foreach ($matches as $value) {
            $codes = explode(' ', trim($value[1], '<>'));
            if ($substring = $this->colored($value[3], $codes, $html)) {
                $text = str_replace($value[0], $substring, $text);
            }
        }
        return $text;
    }
}
