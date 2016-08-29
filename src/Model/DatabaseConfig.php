<?php

namespace Scrubber\Model;

class DatabaseConfig
{
    /** @var string */
    private $name;
    /** @var string */
    private $user;
    /** @var string */
    private $pass;
    /** @var string */
    private $host;

    public function __construct($name, $user, $pass, $host){
        $this->name = $name;
        $this->user = $user;
        $this->pass = $pass;
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->pass;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }


}
