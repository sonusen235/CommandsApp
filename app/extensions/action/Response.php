<?php

namespace app\extensions\action;

class Response extends \lithium\action\Response
{
    /**
     * Adds config values to the public properties when a new object is created.
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->_statuses[520] = 'Checksum error';
        $this->_statuses[210] = 'Warning';
    }
}