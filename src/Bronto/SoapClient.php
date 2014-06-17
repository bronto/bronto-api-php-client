<?php

/**
 * @author Jared Hodak <jhodak@kadro.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
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
        if (function_exists('log_file')){
            log_file($request,'Bronto soap request','logs/soap.log');
            log_file($result,'Bronto soap result','logs/soap.log');
        }
        // Only replace unicode characters if PCRE version is less than 8.30
        if (version_compare(strstr(constant('PCRE_VERSION'), ' ', true), '8.30', '<')) {
            $result = preg_replace('/[\x{0}-\x{8}\x{B}-\x{C}\x{E}-\x{1F}\x{D800}-\x{DFFF}]/u', '', $result);
        }
        
        return $result;
    }
}
