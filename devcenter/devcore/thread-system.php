<?php
namespace ThrearedServer{
	require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/exceptions.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/dbapi.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/server.php";

	if(!defined("MAX_LISTEN"))       define("MAX_LISTEN", 5000);
	if(!defined("ROOT_CL_ACCESS"))   define("ROOT_CL_ACCESS", "client_root");
	if(!defined("ROOT_CL_PASSWD"))   define("ROOT_CL_PASSWD", "");
	if(!defined("NORMAL_CL_ACCESS")) define("NORMAL_CL_ACCESS", "client_normal");
	if(!defined("NORMAL_CL_PASSWD")) define("NORMAL_CL_PASSWD", "");

	// use Server\ServerSocket;

    use ClientsDatabase\ClientsHistory;
    use Thread;
	use Core\SignaturesData;
	use Core\ClientsData;
	use Core\ClientsAccessData;
	use ClientsDatabase\ClientsManager;
	use Exception;
    use Server\RecvError;
    use Server\SendError;

	/**
	  * That class is used to represents the client connected to the server, the clients connecteds are separateds
	  * by threads, to simply accept more then one client.
	  * 
	  * @var resource|null The client socket connected.
	  * @var boolean $running If the thread is running the client authentication service.
	  */
	class ClientThread extends Thread{
		private $clientSocket = null;
		public $running = false;

		const DEFAULT_ERR_1 = "There's no client connected as a thread!";
		const HANDSHAKE = "Welcome to the LPGP official client authentication server.Please send us your client information in the LPGP documents content format.Example: '192/168/0/11/98'";
		const TALKBACK = "devcenter/devcore/talkback.dat";

		/**
		 * That method starts the class and runs the authentication proccess.
		 *
		 * @param resource $client The client connected to the server;
		 */
		public function __construct($client){
			$this->clientSocket = $client;
			$this->start();
			$this->running = true;
		}

		/**
		 * That method decodes a LPGP signature file content.
		 *
		 * @param string $data The content to decode.
		 * @return array|null Null if the content isn't valid.
		 */
		private static function decodeData(string $data): array{
			$exp = explode(SignaturesData::DELIMITER, $data);
			$noASCII = "";
			foreach($exp as $ascii) $noASCII .= chr((int) $ascii);
			$json_con = json_decode($noASCII, true);
			// validating the content
			if(!isset($json_con['Client'])) return null;
			if(!isset($json_con['Proprietary'])) return null;
			if(!isset($json_con['Token'])) return null;
			return $json_con;
		}

		/**
		 * That method authenticate the client data received
		 *
		 * @param string $data The client data received, to authenticate.
		 * @return boolean
		 */
		private function authDataRecv(string $data): bool{
			$objClients = new ClientsManager("giulliano_php", "");
			$hsObj = new ClientsHistory("giulliano_php", "");
			$bruteData = $this->decodeData($data);
			try{
				$responce = $objClients->authenticateContent($data);
				if($responce){
					$hsObj->addReg($bruteData['Client']);
					return true;
				}
			}catch(Exception $error){
				$hsObj->addReg($data['Client'], null, 0);
				return false;
			}
		}

		/**
		 * That method get the access if the client is root or normal.
		 *
		 * @param array $data The client data.
		 * @return string The database access
		 */
		private function getAccess(array $data): string{
			$objClients = new ClientsManager("giulliano_php", "");
			$clientData = $objClients->getConnectionAttr()->query("SELECT vl_root FROM tb_clients WHERE cd_client = " . (int)$data['Client'] . ";")->fetch_array();
			return $clientData['vl_root'] == 0 ? NORMAL_CL_ACCESS . SignaturesData::DELIMITER . NORMAL_CL_PASSWD : ROOT_CL_ACCESS . SignaturesData::DELIMITER . ROOT_CL_PASSWD;
		}

		/**
		 * That method works authenticating the client connected.
		 * 
		 * @throws NoClientConnected If the class don't have received the resource of the accepted client.
		 * @return void
		 */
		private function authenticate(){
			if(!$this->running || is_null($this->clientSocket))
				throw new NoClientConnected(self::DEFAULT_ERR_1);
			while($this->running){
				$hsSent = @socket_send($this->clientSocket, self::HANDSHAKE, strlen(self::HANDSHAKE), 0);
				if($hsSent === false)
					throw new SendError("Couldn't send the handshake: ERROR: " . socket_strerror(socket_last_error()));
				// after handshake
				$recvProc = @socket_recv($this->clientSocket, $recvData, 2048, 0);
				if($recvProc === false)
					throw new RecvError("Couldn't receive: " . socket_strerror(socket_last_error()), 1);
				$bruteData = $this->decodeData($recvData);
				$responce = $this->authDataRecv($recvData) ? "1" : "0";
				$responce .= $this->getAccess($bruteData);
				$repSent = @socket_send($this->clientSocket, $responce, strlen($responce), 0);
				if($repSent === false)
					throw new SendError("Couldn't send the data: " . socket_strerror(socket_last_error()));
				socket_close($this->clientSocket);
				$this->running = false;
				break;
			}
		}

		/**
		 * Default thread data runing.
		 *
		 * @return void
		 */
		public function run(){
			$this->authenticate();
		}
	}

	/**
	 * That class is a representation of the socket client using the threads.
	 * 
	 * @var array|null $clients A array with the clients threads
	 * @var resource|null $mainSock The main socket, used to listen the clients.
	 * @var boolean $created If the main socket was created.
	 * @var integer THREAD_LIMIT A constant, the limit of threads that can be at the $clients array, to avoid DDOS attacks.
	 */
	class ThrearedServer{
		private $clients = null;
		private $mainSock = null;
		const THREAD_LIMIT = 20;  // smallint for while
		public $created = false;
		
		public function __construct(string $hostName = '127.0.0.1', int $port = 1987, int $domain = AF_INET, int $stream = SOCK_STREAM){
			$this->mainSock = socket_create($domain, $stream, SOL_TCP);
			socket_bind($this->mainSock, $hostName, $port);
			socket_listen($this->mainSock. MAX_LISTEN);
			$this->created = true;
			$this->clients = array();
		}

		public function loop(){
			while(true){
				if(count($this->clients) > self::THREAD_LIMIT) throw new ClientLimitAchieved("The client limit was achieved!");
				if(($clientNew = socket_accept($this->mainSock)) !== false){
					$this->clients[] = new ClientThread($clientNew);
				}
			}
		}
	}
}