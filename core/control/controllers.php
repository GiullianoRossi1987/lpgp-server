<?php
namespace Control{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Core.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Exceptions.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/core/control/exceptions.php";

    use Core\SignaturesData;
    use Core\UsersData;
    use Core\ProprietariesData;
    use Core\ClientsData;
    use const LPGP_CONF;

    /**
     * That class have the basic operations and structures to all the control files,
     * it works only reading, loading, dumping and writing a control file. The
     * control files are simple JSON records files, they're used to log specific
     * actions at the main server. It's important to the new security feature,
     * in this new feature, the signature or the client authentication file
     * have to inform when they were downloaded, it reduces the posible falsification
     * future cases.
     *
     * @var string|null $controlF The control file loaded by the controller.
     * @var array|null $bufferedR The JSON parsed content of the control file
     *                              at a buffer in the class as a attribute
     * @var boolean $gotControl That's a indication variable, it indicates if the
     *                          class have the control file references to work
     */
    class BaseController{
        protected $controlF     = null;
        protected $bufferedR    = null;
        protected $gotControl   = false;
        protected $bufferChange = false;

        /**
         * That method set the control file to the class attributes.
         * @param string $controlFile The control file path to load.
         * @throws ControlFileLoadedError If there's another control file loaded already
         * @throws ControlFileUnreachable If the class can't access the control file
         * @return void
         */
        protected function setControl(string $controlFile){
            if($this->gotControl) throw new ControlFileLoadedError();
            try{
                $this->controlF = $controlFile;
                $pureBuffer = file_get_contents($controlFile);
                $this->bufferedR = json_decode($pureBuffer, true);
                $this->gotControl = true;
                unset($pureBuffer);
                return;
            }
            catch(Exception $e){
                throw new ControlFileUnreachable();
            }
        }

        /**
         * That method writes all the changes in the buffer to the control file
         * @throws ControlFileNotFound If there's no control file loaded.
         * @return void;
         */
        protected function commitB(){
            if(!$this->gotControl) throw new ControlFileNotFound();
            $dumped = json_encode($this->bufferedR);
            file_put_contents($this->controlF, $dumped);
            unset($dumped);
            return;
        }

        /**
         * That method unloads the control file loaded at the class.
         * @throws ControlFileNotFound If there's no control file loaded.
         * @return void
         */
        protected function unsetControl(){
            if(!$this->gotControl) throw new ControlFileNotFound();
            if($this->bufferChange) $this->commitB();
            $this->controlF = null;
            $this->bufferedR = null;
            $this->bufferChange = false;
            $this->gotControl = false;
        }

        /**
         * Default class contructor. In that class it can be naturally overrided.
         *
         * @param string|null $controlF The control file to load.
         * @throws ControlFileLoadedError If there's a control file loaded already
         */
        final public function __construct(string $controlF = null){
            if($controlF != null) $this->setControl($controlF);
        }

        /**
         * Default class destructor. Also can be overrided by a subclass.
         */
        final public function __destruct(){
            if($this->gotControl) $this->unsetControl();
        }

        /**
         * That method returns the JSON buffer of the control file.
         * @throws ControlFileNotFound If the controller don't have a control file
         * @return array
         */
        public function getBuffer(): array{
            if(!$this->gotControl) throw new ControlFileNotFound();
            else return $this->bufferedR;
        }

        /**
         * Returns the control file loaded by the controller.
         * @throws ControlFileNotFound If there's no control file loaded yet.
         * @return string
         */
        public function getController(): string{
            if(!$this->gotControl) throw new ControlFileNotFound();
            else return $this->controlF;
        }
    }

    /**
     * Specific signature controller, used for record every time a signature
     * is downloaded or uploaded in the system. That class can have only access to a specific
     * part of the control file.
     *
     * @var string DEFAULT_DOWNLOAD_INDEX The index of the signatures downloads control
     * @var string DEFAULT_UPLOAD_INDEX The index of the signatures uploads control
     * @var string DEFAULT_FDOWN_TOKEN_I The index of the download token at the signature file.
     */
    class SignaturesController extends BaseController{
        const DEFAULT_DOWNLOAD_INDEX = "sdownloads";
        const DEFAULT_UPLOAD_INDEX   = "suploads";
        const DEFAULT_FDOWN_TOKEN_I  = "downToken";


        /**
         * It checks if the buffer have the provided download token at the
         * signatures download control local at the control file.
         *
         * @param string $token The download token to search
         * @throws ControlFileNotFound If there's no control file loaded yet.
         * @return bool If the token is in the control records.
         */
        private function checkDownloadTokenExists(string $token): bool{
            if(!$this->gotControl) throw new ControlFileNotFound();
            $bufferR = $this->$bufferedR;  // buffer representation, to avoid changing the content
            foreach($bufferR[SignaturesController::DEFAULT_DOWNLOAD_INDEX] as $record){
                if($record['dtk'] == $token) return true;
            }
            unset($bufferR);
            return false;
        }

        /**
         * It generates a download code, a unique token created when the signature
         * file is downloaded, it must be added to the signautre file and to the
         * control file. Basicly it generates a random hex and checks if it exists,
         * if the generated token doesn't exist then it will return it.
         *
         * @throws ControlFileNotFound If there's no control file loaded yet.
         * @return string the download token to be used in the file and in the record
         */
        public function generateDownloadToken(): string{
            if(!$this->gotControl) throw new ControlFileNotFound();
            do{
                $rand = (string)bin2hex(random_byte(8));
            }while($this->checkDownloadTokenExists($rand));
            return $rand;
        }

        /**
         * That method adds a new record at the uploads control space of the control
         * file, using the buffer.
         * @param int $signature The signature primary key identifier
         * @param bool $autoCommit If the method will call the commitBuffer method
         *                         to write the new buffer value at the control file.
         * @throws ControlFileNotFound If there's no control file loaded yet.
         * @throws SignatureReferenceError If the signature identifier doesn't exists.
         */
        public function addUploadRecord(int $signature, bool $autoCommit = false){
            if(!$this->gotControl) throw new ControlFileNotFound();
            $sigObj = new SignaturesData(LPGP_CONF['mysql']['sysuser'], LPGP_CONF['mysql']['passwd']);
            if(!$sigObj->checkSignatureExists((int)$signature)) throw new SignatureReferenceError($signature);
            unset($sigObj);
            $record = array('signature' => $signature, 'timestamp' => date("Y-m-d H:i:s"));
            array_push($this->bufferedR[SignaturesController::DEFAULT_UPLOAD_INDEX], $record);
            if($autoCommit) $this->commitB();
        }

        /**
         * Adds a record of the download of a signature to the signatures download
         * control file.
         *
         * @param int $signature The signature primary key reference
         * @param string $token The download token using
         * @param bool $autoCommit If the method will write the changes at the file after adding the record.
         *
         * @throws ControlFileNotFound If there's no control file loaded.
         * @throws DownloadTokenDuplicate If the selected token already exists in the control file
         * @throws SignatureReferenceError If the signature selected doesn't exist.
         */
        public function addDownloadRecord(int $signature, string $token, bool $autoCommit = false){
            if(!$this->gotControl) throw new ControlFileNotFound();
            if($this->checkDownloadTokenExists($token)) throw new DownloadTokenDuplicate($token, 0);
            $sigObj = new SignaturesData(LPGP_CONF['mysql']['sysuser'], LPGP_CONF['mysql']['passwd']);
            if(!$sigObj->checkSignatureExists($signature)) throw new SignatureReferenceError($signature);
            unset($sigObj);
            $data = array('signature' => $signature, 'dtk' => $token, 'timestamp' => date("Y-m-d H:i:s"));
            array_push($this->bufferedR[SignaturesController::DEFAULT_DOWNLOAD_INDEX], $data);
            if($autoCommit) $this->commitB();
        }

        /**
         * It authenticates the received data of a downloaded signature file.
         *
         * @param int $signature The signature id to Search
         * @param string $dtk The download token using of the signature file
         * @param string $timestamp The date and hour of the file download
         * @throws ControlFileNotFound If there's no control file loaded yet.
         * @throws DownloadTokenNotFound If the token isn't valid.
         * @throws SignatureReferenceError If the signature referenced doesn't exist.
         * @return boolean
         */
        public function authDownloadData(int $signature, string $token, string $timestamp): bool{
            if(!$this->gotControl) throw new ControlFileNotFound();
            if(!$this->checkDownloadTokenExists($token)) throw new DownloadTokenNotFound($token, 0);
            $sigObj = new SignaturesData(LPGP_CONF['mysql']['sysuser'], LPGP_CONF['mysql']['passwd']);
            if(!$sigObj->checkSignatureExists($signature)) throw new SignatureReferenceError($signature);
            unset($sigObj);
            foreach($this->bufferedR[DEFAULT_DOWNLOAD_INDEX] as $record){
                if($record['signature'] == $signature && $record['dtk'] == $token && $record['timestamp'] == $timestamp)
                    return true;
            }
            return false;
        }

        /**
         * It authenticates the data read from a specific file at the u.signatures.d
         * directory. It'll decode the primary mask and will authenticate the download data
         * received by the file.
         *
         * @param string $fileName The file to search at the u.signatures.d
         * @throws ControlFileNotFound If there's no control file loaded yet.
         * @throws DownloadTokenNotFound If the token isn't valid.
         * @throws SignatureReferenceError If the signature referenced doesn't exist.
         * @return bool
         */
        public function authDownloadFile(string $fileName): bool{
            if(!$this->gotControl) throw new ControlFileNotFound();
            // decode the main file encoding
            $content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/u.signatures.d/$fileName");
            $exp = explode(SignaturesData::DELIMITER, $content);
            $asciiNone = "";
            foreach($exp as $char) $asciiNone .= chr((int)$char);
            $jsonParsed = json_decode($asciiNone, true);
            try{
                $signature = (int)$jsonParsed['ID'];
                $token = (string)$jsonParsed['DToken'];
                $timestamp = (string)$jsonParsed['Date-Creation'];
            }
            catch(Exception $e) {return false;}
            // if no errors with the indexes
            return $this->authDownloadData($signature, $token, $timestamp);
        }

        /**
         * That method searches in the uploads control recorder for signatures
         * uploaded at the received timestamp.
         *
         * @param string $timestamp The timestamp to search
         * @throws ControlFileNotFound If there's no control file.
         * @return array
         */
        public function searchUploadsByTime(string $timestamp): array{
            if(!$this->gotControl) throw new ControlFileNotFound();
            $results = array();
            foreach($this->bufferedR[DEFAULT_UPLOAD_INDEX] as $record){
                if($record['timestamp'] == $timestamp) array_push($results, $record);
            }
            return $results;
        }

        /**
         * That method searchs in the uploads control recorder for the records
         * of a specific signature.
         *
         * @param int $signature The signature to search.
         * @param bool $uath If the method will authenticate if the signature exists, if it don't exists will return null.
         * @throws ControlFileNotFound If the control file isn't loaded yet.
         * @return array|null
         */
        public function searchUploadsBySignature(int $signature, bool $auth = false){
            if(!$this->gotControl) throw new ControlFileNotFound();
            if($auth){
                $sigObj = new SignaturesData(LPGP_CONF['mysql']['sysuser'], LPGP_CONF['mysql']['passwd']);
                if(!$sigObj->checkSignatureExists($signature)) return null;
                unset($sigObj);
            }
            $results = array();
            foreach($this->bufferedR[DEFAULT_UPLOAD_INDEX] as $urecord){
                if($urecord['signature'] == $signature) array_push($results, $urecord);
            }
            return $results;
        }

        /**
         * That method searches in the downloads control recorder for signatures
         * downloaded at the received timestamp.
         *
         * @param string $timestamp The timestamp to search
         * @throws ControlFileNotFound If there's no control file.
         * @return array
         */
        public function searchDownloadsByTime(string $timestamp): array{
            if(!$this->gotControl) throw new ControlFileNotFound();
            $results = array();
            foreach($this->bufferedR[DEFAULT_DOWNLOAD_INDEX] as $record){
                if($record['timestamp'] == $timestamp) array_push($results, $record);
            }
            return $results;
        }

        /**
         * That method searchs in the downloads control recorder for the records
         * of a specific signature.
         *
         * @param int $signature The signature to search.
         * @param bool $uath If the method will authenticate if the signature exists, if it don't exists will return null.
         * @throws ControlFileNotFound If the control file isn't loaded yet.
         * @return array|null
         */
        public function searchDownloadsBySignature(int $signature, bool $auth = false){
            if(!$this->gotControl) throw new ControlFileNotFound();
            if($auth){
                $sigObj = new SignaturesData(LPGP_CONF['mysql']['sysuser'], LPGP_CONF['mysql']['passwd']);
                if(!$sigObj->checkSignatureExists($signature)) return null;
                unset($sigObj);
            }
            $results = array();
            foreach($this->bufferedR[DEFAULT_DOWNLOAD_INDEX] as $drecord){
                if($urecord['signature'] == $signature) array_push($results, $drecord);
            }
            return $results;
        }

        /**
         * That method cleans the download control recorders, all the signatures downloaded
         * which doesn't exists any more will be deleted from the records.
         *
         * @throws ControlFileNotFound If there's no control file loaded yet.
         *
         */
        public function cleanDownloadRecords(){
            if(!$this->gotControl) throw new ControlFileNotFound();
            $sigObj = new SignaturesData(LPGP_CONF['mysql']['sysuser'], LPGP_CONF['mysql']['passwd']);
            $counter = 0;
            foreach($this->bufferedR[DEFAULT_DOWNLOAD_INDEX] as $drecord){
                if(!$sigObj->checkSignatureExists((int)$drecord['signature']))
                    array_splice($this->bufferedR[DEFAULT_DOWNLOAD_INDEX], $counter);
                else $counter++;
            }
            return;
        }

        /**
         * That method cleans the upload control recorders, all the signatures uploaded
         * which doesn't exists any more will be deleted from the records.
         *
         * @throws ControlFileNotFound If there's no control file loaded yet.
         */
        public function cleanUploadRecords(){
            if(!$this->gotControl) throw new ControlFileNotFound();
            $sigObj = new SignaturesData(LPGP_CONF['mysql']['sysuser'], LPGP_CONF['mysql']['passwd']);
            $counter = 0;
            foreach($this->bufferedR[DEFAULT_UPLOAD_INDEX] as $drecord){
                if(!$sigObj->checkSignatureExists((int)$drecord['signature']))
                    array_splice($this->bufferedR[DEFAULT_UPLOAD_INDEX], $counter);
                else $counter++;
            }
            return;
        }
    }
}
