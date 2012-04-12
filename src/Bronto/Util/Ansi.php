<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 */
class Bronto_Util_Ansi
{
    /**
     * @var array
     */
    protected $_attributes = array(
        'black'       => '0;30',
        'darkgray'    => '1;30',
        'blue'        => '0;34',
        'lightblue'   => '1;34',
        'green'       => '0;32',
        'lightgreen'  => '1;32',
        'cyan'        => '0;36',
        'lightcyan'   => '1;36',
        'red'         => '0;31',
        'lightred'    => '1;31',
        'purple'      => '0;35',
        'lightpurple' => '1;35',
        'brown'       => '0;33',
        'yellow'      => '1;33',
        'lightgray'   => '0;37',
        'white'       => '1;37',
        'onblack'     => '40',
        'onred'       => '41',
        'ongreen'     => '42',
        'onyellow'    => '43',
        'onblue'      => '44',
        'onmagenta'   => '45',
        'oncyan'      => '46',
        'onlightgray' => '47',
    );

    /**
     * @param string|array $codes
     * @return string
     */
    public function color($codes = array())
    {
        $attribute = '';
        if (is_string($codes)) {
            $codes = explode(' ', $codes);
        }
        foreach ($codes as $code) {
            $code = strtolower($code);
            if (isset($this->_attributes[$code])) {
                $attribute .= "{$this->_attributes[$code]};";
            }
        }
        $attribute = substr($attribute, 0, -1);
        return empty($attribute) ? false : chr(27) . "[$attribute" . "m";
    }

    /**
     * @param string $text
     * @param string|array $codes
     * @return string
     */
    public function colored($text = '', $codes = array())
    {
        $attr = $this->color($codes);
        return $attr . $text . chr(27) . "[0m";
    }

    /**
     * @param string $text
     * @return string
     */
    public function parse($text = '')
    {
        preg_match_all('/(<([\w\s]+)[^>]*>)(.*?)(<\/\\2>)/', $text, $matches, PREG_SET_ORDER);
        foreach ($matches as $value) {
            $codes     = explode(' ', trim($value[1], '<>'));
            $substring = $this->colored($value[3], $codes);
            $text      = str_replace($value[0], $substring, $text);
        }
        return $text;
    }
}