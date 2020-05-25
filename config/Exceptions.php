<?php

namespace ConfigExceptions{
    use Exception;

    class ConfigNotLoaded extends Exception{

        public __construct(int $code = 1){
            parent::__construct("There's no configurations file loaded!", $code);
        }
    }

    class ConfigAlreadyLoaded extends Exception{

        public __construct(int $code = 1){
            parent::__construct("There's a configurations file loaded already", $code);
        }
    }

    class ParsingInternalError extends Exception{

        public __construct(string $message, int $code = 1){
            parent::__construct($message, $code);
        }
    }

    class UnreachableFile extends Exception{
        public __construct(string $file, int $code = 1){
            parent::__construct("Can't access file '$file'", $code);
        }
    }
}

 ?>
