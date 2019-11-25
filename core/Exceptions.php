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

namespace SessionSystemExceptions{
    use Exception;

    class SessionAlreadyRunning extends Exception{
        public function showMessage(){ return "There's a session started already! {line: ".$this->getLine()."}";}
    }

    class NoSessionStarted extends Exception{
        public function showMessage(){ return "There's no session running! {line: ".$this->getLine()."}";}
    }

    class AlreadyLoggedUserError extends Exception{
        public function showMessage(){ return "There's a user logged already. {line: ".$this->getLine()."}";}
    }

    class NoUserLogged extends Exception{
        public function showMessage(){ return "There's no user logged already. {line: ".$this->getLine()."}";}
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
}

?>