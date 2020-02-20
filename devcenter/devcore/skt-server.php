<?php
namespace SocketServer;
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/logs-system.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/dbapi.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/Exceptions.php";

if(!defined("DFT_LOGS_SERVER")) define("DFT_LOGS_SERVER", $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/logs/server.log");
if(!defined("LOCAL_IP_TCP")) define("LOCAL_IP_TCP", "tcp://127.0.0.1");
if(!defined("LOCAL_IP_UDP")) define("LOCAL_IP_UDP", "udp://127.0.0.1");
if(!defined("LOCAL_TCP_PORT")) define("LOCAL_TCP_PORT", 1983);
if(!defined("LOCAL_UDP_PORT")) define("LOCAL_UDP_PORT", 1987);

use ScktServerExceptions\AcceptionError;
use ScktServerExceptions\CreationError;
use ScktServerExceptions\InvalidArgs;
use ScktServerExceptions\SocketReadingError;
use ScktServerExceptions\SocketWritingError;
use ScktServerExceptions\ServerAlreadyStarted;
use ScktServerExceptions\ServerNotStarted;

use Database\ServersManager;

use Core\DatabaseConnection;
use LogsSystem\Logger;


/**
 * That class receives all the access servers requests of all the servers requesteds.
 * 
 * @var string|null $localIp The IP address of the receiver server.
 * @var int|null $localPort The port of the receiver server.
 * @var array|null $connections All the access servers connecteds to the receiver server.
 * @var bool $started If the receiver server already started.
 * @var resource|null $sckt The socket server main object.
 * @var string|null $srv The socket type [TCP/UDP]
 * 
 * @var HANDSHAKE The default message that the Receiver server will send when a access server connects
 * @var ARGS A array that contains all the possible options to the server. They are:
 * 			* gProp => That option gets some data of a proprietary on the database (Primary Key, nm_proprietary, vl_email)
 * 			* gUsr => That option gets some data of a normal user on the database (Primary Key, nm_user, vl_email)
 * 			* gSrv => That option gets some data of a access server (Primary Key, nm_server, nm_proprietary, vl_ip, vl_port)
 * 			* gSig => That option gets some data of a signature (Primary key, nm_proprietary, dt_creation)
 * 			* aSig => Creates a new signatures for the logged proprietary
 * 			* rAcc => Removes the self logged account
 * 			* rSig => Removes a signature from a logged proprietary
 * 			* uAcc => Changes the logged account data
 * 			* uSig => Chagens a signature data of the logged proprietary
 * 			* cSig => Checks a signature file content.
 * 			* lSrv => Authenticate the access server token
 * 			* dRcv => Request the Receiver server main data
 * 			* lProp => Makes login in the Receiver server using a proprietary account
 * 			* lUsr => Makes login the Receiver server using a normal user account
 * @var DELIMITER The character that delimits the data received from the access server.
 * @var ERR_MODEL The error string model used at logs and 
 */
class ReceiverServer{
	private $localIp = null;
	private $localPort = null;
	private $connections = null;
	private $started = false;
	private $sckt = null;
	private $srv = null;

	const HANDSHAKE = "Welcome to the LPGP offical Receiver Server!\nThat server accept only special args, so please make sure that you understood the documentation.";
	const DELIMITER = " ";  // whitespace
	const ERR_MODEL = "ERROR [%code]: %msg";

	/**
	 * That method starts the socket server, but that method don't make the server work, to make the server work use the loop method.
	 * 
	 * @param string $ipaddr The IP address of the server.
	 * @param int $portaddr The port that the server will use.
	 * @param string $srv The server connection type [TCP / UDP], TCP is choosen by default
	 * @throws ServerAlreadyStarted If there's a server stream started already.
	 * @throws CreationError If there was a error when creating and starting the server stream
	 * @return void
	 */
	final public function start(string $ipaddr = LOCAL_IP_TCP, int $portaddr = LOCAL_TCP_PORT, string $srv = "TCP"){
		if($this->started) throw new ServerAlreadyStarted("There's a socket stream already created at the server.", 1);
		$this->srv = $srv;
		$this->sckt = stream_socket_server($ipaddr . ":" . (string) $portaddr, $errno, $errstr);
		$this->started = true;
		$this->localIp = $ipaddr;
		$this->localPort = $portaddr;
		$this->connections =  array();  // empty array
	}

	
}