<?php

/**
 * @author Jared Hodak <jhodak@kadro.com>
 */
class Bronto_SoapClient extends SoapClient
{
    /**
     * Overriding to replace known invalid xml characters.
     * @see http://www.w3.org/TR/xml/#charsets
     * @return string
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $result = parent::__doRequest($request, $location, $action, $version);
        
        // If PCRE version is 8.30 or above, no need to replace
        if (version_compare(strstr(constant('PCRE_VERSION'), ' ', true), '8.30', '<')) {
            $result = preg_replace('/[\x{0}-\x{8}\x{B}-\x{C}\x{E}-\x{1F}\x{D800}-\x{DFFF}]/u', '', $result);
        }
        
        return $result;
    }
}
