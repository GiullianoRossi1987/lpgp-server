<?php
namespace Configurations{
    require_once $_SERVER['DOCUMENT_ROOT'] . "config/Exceptions.php";

    use ConfigExceptions\ConfigNotLoaded;
    use ConfigExceptions\ConfigAlreadyLoaded;
    use ConfigExceptions\ParsingInternalError;
    use ConfigExceptions\UnreachableFile;

    /**
     * That class works loading the configurations from the configurations file, and
     * export it to the class attributes.
     *
     * @var array|null $arrayConfig A array with the configurations loaded.
     * @var string|null $config The configurations file loaded by the class
     */
    class Configurations{

        private $arrayConfig = null;
        private $config = null;

        /**
         * That method checks the strucutre of the configurations file content
         * To be a valid configurations file must have the following structure
         *
         * mysqldb:
        *   usr, passwd,
        *   constant_users ->
        *       default_root, default_rootp, default_normal, default_normalp
        * apache:
        *   virtual_host, protocol, port
        *
        * logs:
        *   default_error, default_talkback
        *
        * file_control:
        *   delimiter
        *   signatures/clients:
        *       downloads, uploads
        * socket_config:
        *   max_listen, port
        */
        private function checkConfig(string $file): bool{
            $content = file_get_contents();
            $jsonContent = json_decode($content, true);
            $checkList = [
                "apache" => false,
                "mysqldb" => false,
                "logs" => false,
                "file_control" => false,
                "socket_config" => false
            ];
            foreach($jsonContent as $configPo => $conf){
                if($configPo == "apache"){
                    try{
                        if(strlen($conf['virtual_host']) == 0) return false;
                        else if($conf['protocol'] != "http" || $conf['protocol'] != "https") return false;
                        else $checkList['apache'] = true;
                    }
                    catch(Exception $notFound) return false;
                }
                else if($configPo == "mysqldb"){
                    try{
                        if(strlen($conf['usr']) == 0 || strlen($conf['passwd']) == 0) return false;
                        foreach($conf['constant_users'] as $key => $content){
                            if(strlen($content) == 0) return false;
                        }
                    }
                }
            }
        }
    }
}

 ?>
