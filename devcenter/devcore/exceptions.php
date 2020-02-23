<?php
namespace ClientsDatabase{
	use Exception;

	/**
	 * <Exception> thrown when the requested client register doesn't exist.
	 * @access public
	 */
	class ClientNotFound extends Exception{
		public function __construct(string $client_rf, int $code = 1){
			parent::__construct("Don't exists a Client with the client reference '$client_rf'", $code);
		}
	}

	/**
	 * <Exception> thrown when the client manager try to add a new client, but there's another one with the same important 
	 * data and references.
	 * @access public
	 */
	class ClientAlreadyExists extends Exception{
		public function __construct(int $code = 1){ parent::__construct("There's another client with those data!", $code); }
	}

	/**
	 * <Exception> thrown when the client token is already in use. That exception is normally used to handle
	 * the tokens generation.
	 * @access public
	 */
	class TokenInUse extends Exception{
		public function __construct(string $tk, int $code = 1){
			parent::__construct("The token '$tk' is already being used by another client!", $code);
		}
	}

	/**
	 * <Exception> Thrown when the client manager try to add a client, or make a query using the proprietary account
	 * reference, but the reference is invalid.
	 * @access public
	 */
	class PropRefNotFound extends Exception{

		/**
		 * @param string|int $prop_ref The proprietary reference, the Primary Key (cd_proprietary/id_proprietary) or the proprietary name.
		 */
		public function __construct($prop_ref, int $code = 1){
			parent::__construct("The Proprietary account reference '$prop_ref' is invalid!", $code);
		}
	}
}

namespace ClientConfGen{
	use Exception;

	/**
	 * <Exception> Thrown when the client configurations file generator try to generate a configurations file with
	 * a client reference of a client that don't exist.
	 * @access public
	 */
	class ClientRefError extends Exception{
		public function __construct(string $ref, int $code = 1){
			parent::__construct("Can't generate the client configurations file of the client '$ref'", 1);
		}
	}

	/**
	 * <Exception> Thrown when the clients configurations manager try to add a configurations file with a filename in 
	 * use.
	 * @access public
	 */
	class ConfExistsError extends Exception{
		public function __construct(string $fl, int $code = 1){
			parent::__construct("There's another configurations file using the reference '$fl'!", $code);
		}
	}

	/**
	 * <Exception> Thrown when the clients configurations manager try to authenticate a client configurations file that
	 * wasn't uploaded.
	 * @access public
	 */
	class ConfNotFound extends Exception{
		public function __construct(string $fl, int $code = 1){
			parent::__construct("Can't load the configurations file '$fl' [FileNotFound]", $code);
		}
	}
}