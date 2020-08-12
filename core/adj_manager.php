<?php
namespace Ajd_Core{
    ///////////////////////////////////////////////////////////////////////////
    // Declaration of classes using and constants
    use Exception;
    use SQLite3;

    if(!defined("URE_PROPVL")) define("URE_PROPVL", 1);
    if(!defined("URE_NRMLVL")) define("URE_NRMLVL", 1);

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
     * <Exception> Thrown when the user reference received don't exist at the main database
     */
    class UserReferenceError extends Exception{

        /**
         * Start the class and throw itself.
         * @param integer $mode The type of user reference (1 -> Proprietary, 2 -> Normal User)
         */
        public function __construct($reference, int $mode = URE_NRMLVL){
            $notation = $mode == 1 ? "Proprietary" : "Normal User";
            parent::__construct("Invalid " . $notation . " user reference " . $reference);
        }
    }

    /**
     * <Exception> Thrown when the reports manager try to add a invalid report
     * subject reference
     */
    class SubjectReferenceError extends Exception{

        public function __construct(int $subject){
            parent::__construct("Invalid subject reference: $subject");
        }
    }

    /**
     * <Exception> Thrown when the connection handler received a SQL error after
     * a query
     */
    class InternalConnectorError extends Exception{
        public function __construct(string $error){ parent::__construct("INTERNAL CONNECTOR ERROR::SQLITE3 " . $error); }
    }

    ///////////////////////////////////////////////////////////////////////////
    // Main Classes

    /**
     * Interface that contain the necessary methods to manage the subjects of the
     * reports.
     */
    interface SubjectManager{

        /**
         * Returns the Primary key reference of a specific subject on the database.
         * If the subject don't exist it'll return -1
         * @param string $subject The subject name reference
         * @return integer
         */
        private function getSubjectId(string $subject): int;

        /**
         * Returns the subject ID, Name and Visibility (if only proprietaries can access those)
         * @param integer|string $subject The subject to get the data
         * @return array
         */
        private function getSubjectData($subject): array;

        /**
         * Returns the integer reference (primary key) of the subject with another reference.
         * It's used to handle the use of a subject reference, accepting the name and the
         * primary key. If the type of the reference isn't valid or the reference it self isn't
         * valid, will return -1;
         * @param string|integer $ref The reference used to search
         * @return integer.
         */
        private function hndSubjectReference($ref): int;

        /**
         * Checks if the subject referenced exists or not.
         * @param string|integer $subject The reference to validate
         * @return boolean
         */
        private function checkSubjectExists($subject): bool;

        /**
         * Adds a new subject to the database, if the subject already exists it
         * will return false, else it return true
         *
         * @param string $subject The subject
         * @param integer $scope The visibility of the subject
         */
        private function addSubject(string $subject, int $scope = 0): bool;

        /**
         * Get all the subjects of the database.
         *
         * @return array
         */
        public function getSubjects(): array;
    }


    /**
     * Manages the conection to the adjacent database of the server
     */
    class SQLite3Connector{

    }
}
 ?>
