<?php
namespace Controllers{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Core.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Exceptions.php";

    use Exception;
    use const LPGP_CONF;
    use Core\UsersData;
    use Core\ClientsData;
    use Core\ProprietariesData;
    use Core\SignaturesData;

    if(!defined("CONTROLLER_RAW_DELIMITER")) define("CONTROLLER_RAW_DELIMITER", ";");
    if(!defined("CONTROLLER_LOGIN")) define("CONTROLLER_LOGIN", $_SERVER['DOCUMENT_ROOT'] . "/core/controll/login.log");
    if(!defined("CONTROLLER_CDOWN")) define("CONTROLLER_CDOW", $_SERVER['DOCUMENT_ROOT'] . "/core/controll/cdownload.log");
    // TODO: More defines.


    /**
     * <Exception> Thrown when a controller try to load a control file, but it
     * already haves another control file loaded. It exists to prevent that a control
     * file data stay as it was the data from another control file.
     *
     */
    class ControlFileLoaded extends Exception{

        public function __construct(){
            parent::__construct("There's a control file loaded already");
        }
    }

    /**
     * <Exception> Thrown when a controller try to achieve a control file that
     * isn't loaded by him.
     */
    class ControlFileNotFound extends Exception{

        public function __construct(){
            parent::__construct("There's no control file loaded yet!");
        }
    }

    /**
     * <Exception> Thrown when the controller's trying to load a control file, but
     * the controller can't access it, maybe a PermissionError or a FileNotFoundError.
     */
    class ControlFileUnreachable extends Exception{

        public function __construct(string $controlPath){
            parent::__construct("Can't access the $controlPath file");
        }
    }

    /**
     * That class have the basic operations of a controller. it's used as the super
     * class of the other specific controllers, it's the superclass of the following
     * hierarchy. If you want to make your own controller, then extend your class
     * from it. To that basic structure you must set the attributes in this class,
     * as I said, they're essential to the basic operations of any controller.
     *
     * @param string|null $control The control file path.
     * @param boolean $gotData If the class got the control file and it data, that attribute
     *                          must be true. But, if the conrol file don't have the control
     *                          it must be false.
     *
     * @param string|null $buffer It haves the control file content that's read and
     *                             thrown in that attribute memory point
     * @param bool|null $toDumpB If the buffer were changed, if it's true, you
     *                           must commit the buffer change before unseting
     *                           the instance.
     */
    class SimpleController{
        protected $control = null;
        protected $gotData = false;
        protected $buffer  = null;
        protected $toDumpB = null;

        /**
         * That's a primordial method used to set the main attributes. It will
         * read the control file and set the $buffer.
         * @param string $controlFile The control file path to load.
         * @throws ControlFileLoaded If there's another file loaded.
         * @throws ControlFileUnreachable If the php can't read the file path.
         */
        protected function readControl(string $controlFile){
            if($this->gotData) throw new ControlFileLoaded();
            $this->control = $controlFile;
            try{
                $this->buffer = file_get_contents($controlfile);
            }
            catch(Exception $e){ throw new ControlFileUnreachable($e->getMessage());}
            $this->gotData = true;
        }

        /**
         * Class constructor, by default it uses the method readControl
         */
        final public function __construct(string $controlFile){
            $this->readControl($controlFile);
        }

        /**
         * That method writes all the changes at the buffer to the control file.
         * @throws ControlFileNotFound If there's no control file loaded.
         */
        protected function commit(){
            if(!$this->gotData) throw new ControlFileNotFound();
            $handler = fopen($this->controlFile, "w+");
            $rp = fwrite($handler, $this->buffer);
            $this->$toDumpB = false;
        }

        /**
         * That method commit the changes at the control file and unsets the
         * attributes of the class.
         * @throws ControlFileNotFound If there's no control file loaded.
         */
        final protected function unloadControl(){
            if(!$this->gotData) throw new ControlFileNotFound();
            if($this->$toDumpB) $this->commit();
            $this->buffer = null;
            $this->$toDumpB = null;
            $this->control = null;
            $this->gotData = false;
        }

        /**
         * Class default destructor, it'll call the unloadControl method by default
         * if the class instance have a control file loaded.
         */
        final public function __destruct(){
            if($this->gotData) $this->unloadControl();
        }

        /**
         * That method reads the control buffer line by line separating the records
         * at each '\n' found on the buffer.
         * Warning it'll separate the rows too, using the default delimiter.
         *
         * @throws ControlFileNotFound If there's no control file loaded yet
         * @return array A array full of other arrays with strings.
         */
        protected function readRecords() : array{
            if(!$this->gotData) throw new ControlFileNotFound();
            $line_exp = explode($this->buffer, "\n");
            if(count($line_exp) <= 0) return [];
            $resultSet = [];
            foreach($line_exp as $record){
                $row = explode($record, CONTROLLER_RAW_DELIMITER);
                $resultSet[] = $row;
            }
            return $resultSet;
        }

        /**
         * That method converts a matrix to a valid string file, in other words
         * It does the inverse of the readRecords method. It'll get your records
         * and'll turn it into a file content, ready to be wrote at the buffer
         * and commited.
         *
         * @param array $records The records that will go to the buffer.
         * @return string
         */
        public static dumpRecords(array $records): string{
             $dumpedRecords = "";
             foreach($records as $record)
                 $dumpedRecords .= implode(";", (string)$record) . "\n";
             return $dumpedRecords;
        }

        /**
         * That method appends a string to the attribute buffer.
         *
         * @param string $content The content to append to the control buffer
         * @throws ControlFileNotFound
         */
        public function appendBuffer(string $appendex){
            if(!$this->gotData) throw new ControlFileNotFound();
            $thid->buffer .= $appendex;
            $this->$toDumpB = true;
        }

        /**
         * That method overrides the buffer attribute of the class.
         * @param string $newBuffer The content to override the buffer attribute
         * @throws ControlFileNotFound
         */
        public function writeBuffer(string $newBuffer){
            if(!$this->gotData) throw new ControlFileNotFound();
            $this->buffer = $newBuffer;
        }

        /**
         * That method returns the control file loaded by the controller class.
         * If there's no control file loadedd, will return null
         * @return string|null
         */
        public function getControlFile(){ return $this->controlFile; }

        /**
         * That method returns the actual buffer attribute value.
         * If there's no control file loaded, it'll return null.
         * @return string|null
         */
        public function readBuffer(){ return $this->buffer; }

        /**
         * That method will return a specific record of the local bufffer.
         * @param int $row The specific row to return
         * @throws ControlFileNotFound If there's no control file loaded.
         * @return array|null Array if the content exists, null if it doesn't
         */
        public function at(int $row): array{
            if(!$this->gotData) throw new ControlFileNotFound();
            $whole = $this->readRecords();
            return isset($whole[$row]) ? $whole[$row] : null;
        }
    }
}
