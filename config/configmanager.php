<?php

namespace Configurations{
    use Exception;
    if(!defined("CONFIG_FILE")) define("CONFIG_FILE", $_SERVER['DOCUMENT_ROOT'] . "/config/mainvars.json");

    class ConfigurationsLoaded extends Exception{

        public __construct(int $code = 1){
            parent::__construct("There're configurations loaded already", $code);
        }
    }

    class ConfigurationsNotLoaded extends Exception{

        public __construct(int $code = 1){
            parent::__construct("There's no configurations file loaded", $code);
        }
    }

    class InvalidConfigurations extends Exception{

        public __construct(string $error, int $code = 1){
            parent::__construct("The configurations aren't valid! $error", $code);
        }
    }

    /**
     * That class loads the configurations file setted by default 
     */
    class Configurations{
        private $configurationsFile = null;
        private $config = null;
    }
}
