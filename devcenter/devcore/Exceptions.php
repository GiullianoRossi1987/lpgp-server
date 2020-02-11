<?php
//!/usr/bin/php
namespace ServersExceptions{
	use Exception;

	/**
	 * <Exception> Thrown when the server adder class try to add a server at the tb_servers, but there's another server with the same name.
	 */
	class ServerAlreadyExists extends Exception{
		/**
		 * Initialize the class with a faster way to describe the error message.
		 *
		 * @param string $server The server that already exists in the database
		 * @param integer $code The error code, like the Exception superclass.
		 */
		public function __construct(string $server, int $code = 0){
			parent::__construct("The server \"$server\" already exists!", $code);
		}
	}

	/**
	 * <Exception> Thrown when the selected server don't exists in the database.
	 */
	class ServerNotFound extends Exception{
		/**
		 * Initialize the class with a faster way to describe the error message.
		 * 
		 * @param string $server The server reference that doesn't exists
		 * @param integer $code The error code default of the Exception class
		 */
		public function __construct(string $server, int $code = 0){
			parent::__construct("There's no server \"$server\" at the servers list!", $code);
		}
	}

	/**
	 * <Exception> Thrown when the ip address received from a server is invalid.
	 */
	class InvalidIP extends Exception{
		/**
		 * Initialize the class with a faster way to describe the error message
		 * 
		 * @param string $ip The invalid IP address
		 * @param integer $code The error code selected.
		 */
		public function __construct(string $ip, int $code = 0){
			parent::__construct("The ip address \"$ip\" isn't valid!", $code);
		}
	}

	/**
	 * <Exception> Thrown when the server token received doesn't exists in the database.
	 */
	class TokenAuthError extends Exception{}

	/**
	 * <Exception> Thrown when the server manager class try to add a server with a new token, but the token's already being used by another server.
	 */
	class TokenExistsError extends Exception{}

	/**
	 * <Exception> Thrown when the server manager try to add a server with a invalid port value, or change the port value from a server, but the new 
	 * value is invalid
	 */
	class InvalidPort extends Exception{}

	/**
	 * <Exception> Thrown when the XML configurations manager try to load a invalid server configurations file
	 */
	class ConfigXMLError extends Exception{}

	/**
	 * <Exception> Thrown when the XML configurations class try to export a server configurations file, but had errors with the server info
	 */
	class GenerationError extends Exception{}

	/**
	 * <Exception> Thrown when the XML configurations class try to work with a invalid server configurations file
	 */
	class InvalidXML extends Exception{}
}

namespace AppExceptions{
	use FFI\Exception;

	/**
	 * <Exception> Thrown when the APP's manager class try to work with a APP at the database, however that APP doens't exists.
	 */
	class AppNotFound extends Exception{}

	/**
	 * <Exception> Thrown when the APP's manager try to authenticate the APP token, but the token's invalid.
	 */
	class TokenAuthError extends Exception{}

	/**
	 * <Exception> Thrown when the APP's manager try to add a APP in the database with a token, but the app token already exists.
	 */
	class TokenExistsError extends Exception{}

	/**
	 * <Exception> Thrown when the APP's manager try to work with a APP configurations file, but the file's invalid.
	 */
	class ConfigXMLError extends Exception{}

	/**
	 * <Exception> Thrown when the APP's manager try to generate a APP configurations file, but there were errors at the generations.
	 */
	class GenerationError extends Exception{}
}