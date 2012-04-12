<?php

class Bronto_Util_Ansi
{
    /**
     * @var array
     */
    protected $_attributes = array(
        'clear'           => 0,
        'reset'           => 0,
        'bold'            => 1,
        'dark'            => 2,
        'faint'           => 2,
        'underline'       => 4,
        'underscore'      => 4,
        'blink'           => 5,
        'reverse'         => 7,
        'concealed'       => 8,
        'black'           => 30, 'onblack'         => 40,
        'red'             => 31, 'onred'           => 41,
        'green'           => 32, 'ongreen'         => 42,
        'yellow'          => 33, 'onyellow'        => 43,
        'blue'            => 34, 'onblue'          => 44,
        'magenta'         => 35, 'onmagenta'       => 45,
        'cyan'            => 36, 'oncyan'          => 46,
        'white'           => 37, 'onwhite'         => 47,
        'brightblack'     => 90, 'onbrightblack'   => 100,
        'brightred'       => 91, 'onbrightred'     => 101,
        'brightgreen'     => 92, 'onbrightgreen'   => 102,
        'brightyellow'    => 93, 'onbrightyellow'  => 103,
        'brightblue'      => 94, 'onbrightblue'    => 104,
        'brightmagenta'   => 95, 'onbrightmagenta' => 105,
        'brightcyan'      => 96, 'onbrightcyan'    => 106,
        'brightwhite'     => 97, 'onbrightwhite'   => 107
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
}