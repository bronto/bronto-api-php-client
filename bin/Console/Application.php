<?php
/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
namespace Console;

class Application extends \Symfony\Component\Console\Application
{
    /**
     * @var \Bronto_Api
     */
    protected $api;

    /**
     * Constructor.
     *
     * @param string  $name    The name of the application
     * @param string  $version The version of the application
     */
    public function __construct($name = 'Bronto Console Tools', $version = '1.0')
    {
        parent::__construct($name, $version);
    }

    /**
     * @return \Bronto_Api
     */
    public function getApi()
    {
        if ($this->api === null) {
            $this->api =  new \Bronto_Api();
        }
        return $this->api;
    }
}
