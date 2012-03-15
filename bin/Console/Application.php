<?php

namespace Console;

class Application extends \Symfony\Component\Console\Application
{
    /**
     * @var \Bronto_Api
     */
    protected $api;

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