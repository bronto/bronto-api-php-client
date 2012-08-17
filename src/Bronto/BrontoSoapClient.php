<?php

class BrontoSoapClient extends SoapClient {

	//overriding to replace known invalid xml characters - http://www.w3.org/TR/xml/#charsets
        function __doRequest($request, $location, $action, $version, $one_way = 0) {
                $result = parent::__doRequest($request, $location, $action, $version);
                $result = preg_replace('/[\x{0}-\x{8}\x{B}-\x{C}\x{E}-\x{1F}\x{D800}-\x{DFFF}]/u', '', $result);
                return $result;
        }
}
?>
