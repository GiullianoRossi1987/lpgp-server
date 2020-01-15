<?php
namespace DatabaseActionsExceptions{
    use Exception;

    class NotConnectedError extends Exception{
        public function showMessage() { return "The system needs to be connected to do that action! {line: ". $this->getLine()."}"; }
    }

    class AlreadyConnectedError extends Exception{
        public function showMessage(){ return "The system's already connected! {line: ".$this->getLine()."}";}
    }
}


namespace UsersSystemExceptions{
    use Exception;

    class UserNotFound extends Exception{
        public function showMessage(string $user){return "There's no user '$user' !{line: ".$this->getLine()."}";}
    }

    class UserAlreadyExists extends Exception{
        public function showMessage(string $user){ return "The user '$user' already exists in the database {line: ". $this->getLine() . "}";}
    }

    class InvalidUserName extends Exception{
        public function showMessage(string $username){ return "'$username' is not a valid username! {line: " . $this->getLine() . "}";}
    }

    class PasswordAuthError extends Exception{
        public function showMessage(){ return "Password authentication error! ERROR: '$this->message' {line: " . $this->getLine() . "}";}
    }

    class UserKeyNotFound extends Exception{
        public function showMessage(string $key){ return "There's no uses from the key '$key'! {line: " . $this->getLine() . "}";}
    }

    class UserAlreadyLogged extends Exception{
        public function showMessage(){ return "There's a user logged already! {line: " . $this->getLine() . "}";}
    }

    class NoUserLogged extends Exception{
        public function showMessage(){ return "There's no user logged already! {line: " . $this->getLine() . "}"; }
    }
}

namespace ProprietariesExceptions{
    use Exception;

    class ProprietaryNotFound extends Exception{
        public function showMessage(string $proprietary){ return "There's no proprietary '$proprietary'! {line: " . $this->getLine() . "}";}
    }

    class ProprietaryAlreadyExists extends Exception{
        public function showMessage(string $proprietary){ return "The proprietary '$proprietary' already exists in the database! {line: ". $this->getLine() . "}";}
    }

    class InvalidProprietaryName extends Exception{
        public function showMessage(string $prop_name){ return "'$prop_name' is not a valid proprietary name! {line: " . $this->getLine() . "}";}
    }

    class AuthenticationError extends Exception{
        public function showMessage(){ return "Authentication error. \nERROR> '$this->message' {line: " . $this->getLine() . "}";}
    }

    class ProprietaryKeyNotFound extends Exception{
        public function showMessage(string $key){return "There's no key '$key' that pertences for any proprietary! {line: " . $this->getLine() . "}";}
    }

    class ProprietaryAlreadyLogged extends Exception{
        public function showMessage(){ return "There's a proprietary user logged already! {line: " . $this->getLine() . "}";}
    }

    class NoProprietaryLogged extends Exception{
        public function showMessage(){ return "There's no proprietary user logged already! {line: " . $this->getLine() . "}"; }
    }
}

namespace SignaturesExceptions{
    use Exception;

    class SignatureNotFound extends Exception{
        public function showMessage(int $sign_id){ return "There's no such signature #$sign_id! {line: " . $this->getLine() . "}";}
    }

    class InvalidSignatureFile extends Exception{
        public function showMessage(string $file_path){ return "'$file_path' is not a valid signature file. {line: " . $this->getLine() . "}";}
    }

    class SignatureAuthError extends Exception{
        public function showMessage(){ return "Signature authentication failed. ERROR> '$this->message' {line: " . $this->getLine() . "}";}
    }

    class SignatureFileNotFound extends Exception{
        public function showMessage(string $file_name) { 
            $dft_path = $_SERVER['DOCUMENT_ROOT'] . "/usignatures.d";
            return "The signature file '$file_name' don't exists at \"$dft_path\"";
        }
    }

    class VersionError extends Exception{
        public function showMessage(string $version, string $a_version){
            return "The version used by the file is not allowed. The file version is $version, the recent allowed version is $a_version. {line: " . $this->getLine() . "}";
        }
    }
}

namespace LogsErrors{
    use Exception;

    class LogsFileNotLoaded extends Exception{
        public function showMessage(){ return "There's no logs file loaded in the class! {line: " . $this->getLine() . "}";}
    }

    class LogsFileAlreadyLoaded extends Exception{
        public function showMessage(){ return "The class already have a logs file loaded! {line: " . $this->getLine() . "}";}
    }

    class InvalidFile extends Exception{
        public function showMessage(string $file){ return "The file '$file' is not a valid logs file! {line: " . $this->getLine() . "}";}
    }

}

namespace ExctemplateSystem{
    use Exception;

    class InvalidFileType extends Exception{
        public function showMessage(string $file){
            return "The file '$file' is not a valid HTML document for fetching";
        }
    }

    class AlreadyLoadedFile extends Exception{
        public function showMessage(){ return "The class already haves a HTML document parsed!";}
    }

    class NotLoadedFile extends Exception{
        public function showMessage(){ return "The class need a HTML document parsed!";}
    }
}

/**
 * Exceptions used for the UsersCheckHistory on the Core.php
 */
namespace CheckHistory{
    use Exception;

    /**
     * Exception thrown when the error code of a register is not in range(0, 3)
     */
    class InvalidErrorCode extends Exception{
        public function __construct(int $code_vl, int $code = 1){
            parent::__construct("The error code '$code_vl' is invalid, expecting a number in 0, 1, 2 or 3", $code);
        }
    }
    /**
     * Exception thrown when the class try to get some register using a primary key reference, but that reference don't exist at the database table.
     */
    class RegisterNotFound extends Exception{

        /**
         * Personalized class constructor, standardize the error message.
         * @param integer $vl_ref The value of the primary key reference
         * @param integer $code The standard parameter of the parent::
         * @return void
         */
        public function _construct(int $vl_ref, int $code = 1){ parent::__construct("Can't find the register using the PK reference #$vl_ref!", $code);}
    }
}
/**
 * Exceptions for the ProprietariesCheckHistory class on the Core.php
 */
namespace PropCheckHistory{
    use Exception;

    /**
     * Exception thrown when the error code of a register is not in range(0, 3)
     */
    class InvalidErrorCode extends Exception{
        public function __construct(int $code_vl = null, int $code = 1){
            $vl = is_null($code_vl) ? "null_value" : $code_vl;
            parent::__construct("The error code '$vl' is invalid, expecting a number in 0, 1, 2 or 3", $code);
        }
    }

    /**
     * Exception thrown when the class try to get some register using a primary key reference, but that reference don't exist at the database table.
     */
    class RegisterNotFound extends Exception{

        /**
         * Personalized class constructor, standardize the error message.
         * @param integer $vl_ref The value of the primary key reference
         * @param integer $code The standard parameter of the parent::
         * @return void
         */
        public function _construct(int $vl_ref, int $code = 1){ parent::__construct("Can't find the register using the PK reference #$vl_ref!", $code);}
    }
}
?>