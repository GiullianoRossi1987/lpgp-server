<?php
namespace Ajd_Core{
    ///////////////////////////////////////////////////////////////////////////
    // Declaration of classes using
    use Exception;
    use SQLite3;

    //////////////////////////////////////////////////////////////////////////
    // Exceptions

    /**
     * <Exception> Thrown when the Adjacent Database Api class try to use a loaded
     * database but there's no database loaded
     */
    class DatabaseNotLoaded extends Exception{
        public function __construct(){ parent::__construct("No Database loaded"); }
    }

    /**
     * <Exception> Thrown when the Adjacent Database Api class try to load a database
     * but there's another database loaded already
     */
    class DatabaseAlreadyLoaded extends Exception{
        public function __construct(){ parent::__construct("There's a database loaded already"); }
    }

    /**
     * <Exception> Thrown when the
     */
    class UserReferenceError extends Exception{
        
    }
}
 ?>
