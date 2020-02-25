<?php
namespace Server{
	use Exception;
	try {
		include $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/dbapi.php";
	}
	catch(Exception $e){
		require_once "devcenter/devcore/dbapi.php";
	}

	use ClientsDatabase\ClientsManager;

	if(!defined("MAX_LISTEN")) define("MAX_LISTEN", 5000);
	if(!defined("PROTOCOLS_F")) define("PROTOCOLS_F", "devcenter/devcore/protocols.json");
	if(!defined("DFT_TYPE_IP")) define("DFT_TYPE_IP", AF_INET);
	if(!defined("DFT_STREAM")) define("DFT_STREAM", SOCK_STREAM);
	if(!defined("DFT_PORT_VL")) define("DFT_PORT_VL", 1987);

	/**
	 * That class have the main functions and procedures for the socket server.
	 * @var resource|null $sock The server socket, initialized using the __construct method
	 * @var bool $started If the server started and created the socket ($sock)
	 * @var array|null $connection_info The main info about the server connection, it have the IP address, the IP type, the port, 
	 * @var array|null $connection_logs All the data received and sent of the server at any connections.
	 * @var ClientsManager|null The client manager database of the connection.
	 */
	class ServerSocket{
		private $sock;
		private $started;
		private $connection_info;
		private $connection_logs;

		const HANDSHAKE   = "Welcome to the LPGP official client authentication server.\nPlease send us your client information in the LPGP documents content format.\nExample: '192/168/0/11/98'";
		const LOGS_FILE   = "logs/server.log";
		const TALKBACK_EN = TRUE;
		const DELIMITER   = "/";

		/**
		 * That method create the socket and set it as the $sock attribute.
		 *
		 * @param string $IP The IP address to bind
		 * @param integer $port The port to bind
		 * @param integer $type The type of the connection, if it's IPV4 or IPV6. By default it's the DFT_TYPE_IP
		 * @param integer $stream The socket stream to use.
		 * @throws ServerCreatedError If the server already started
		 * @throws ServerCreationError If the server can't be created
		 * @throws ServerBindingError If the server can't bind the selected address
		 * @return void
		 */
		public function start(string $IP = "127.0.0.1", int $port = DFT_PORT_VL, int $type = DFT_TYPE_IP, int $stream = DFT_STREAM){
			if($this->started) throw new ServerCreatedError("The server's already created!", 1);
			$this->sock = @socket_create($type, $stream, SOL_TCP);
			if($this->sock === false) throw new ServerCreationError(socket_strerror(socket_last_error()));
			$bin = @socket_bind($this->sock, $IP, $port);
			if($bin === false) throw new ServerBindingError(socket_strerror(socket_last_error()));
			$lst = socket_listen($this->sock, MAX_LISTEN);
			$this->connection_info = array(
				"Host"   => $IP,
				"Port"   => $port,
				"Type"   => $type,
				"Stream" => $stream,
				"Created" => date("Y-M-d H:i:s")
			);
			$this->connection_logs = [];
			$this->started         = true;
		}

		/**
		 * That method destroy the server socket and reset the attributes to the default values.
		 *
		 * @throws ServerCreatedError If the server wasn't created yet
		 * @return void
		 */
		public function stop(){
			if(!$this->started) throw new ServerCreatedError("The server wasn't created yet!", 1);
			$rcv = @socket_close($this->sock);
			if($rcv === false) throw new ServerClosingError(socket_strerror(socket_last_error()));
			// writes the data at the talkback.dat file.
			$dt = date("Y-M-d H:i:s");
			$this->connection_logs[] = "[$dt] Closing socket.";
			$doc = implode("\n", $this->connection_logs);
			$all_dt = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/talkback.dat");
			$document = $all_dt . "\n" . $doc;
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/talkback.dat", $document);
		}

		/**
		 * That method decode the client data received and authenticate the data.
		 * @param string $data The client data received, that data have the same cipher then the .lpgp files, it's a json with the client data 
		 * @throws DataRecvError If the data received have errors.
		 * @return bool
		 */
		private function authData(string $data){
			if(!strpos($data, self::DELIMITER)) throw new DataRecvError("The received data isn't valid!", 1);
			$exp = explode(self::DELIMITER, $data);
			$json_data = "";
			for($chr = 0; $chr < count($exp); $chr++) $json_data .= chr((int) $exp[$chr]);
			$parsed = json_decode($json_data, true);
			$manager = new ClientsManager("giulliano_php", "");
			return $manager->authClient($parsed['ClientName'], $parsed['Token']);
		}

		/**
		 * Add a log to the connection_logs;
		 *
		 * @param string $data The data sended/received
		 * @param bool $ext If the client is who sended the data or if the server sended the data
		 * @throws ServerCreatedError If the server wasn't created yet.
		 * @return void
		 */
		private function addLog(string $data, bool $ext = false){
			if(!$this->started) throw new ServerCreatedError("The server hadn't created yet!", 1);
			$dt_tm = date("Y-m-d H:i:s");
			$sender = $ext ? "Client" : "Server";
			$this->connection_logs[] = "[$dt_tm] {$sender} - $data";
		}

		/**
		 * The loop of the server, that loop makes it receive and send data with any client.
		 * @throws ServerCreatedError If the socket wasn't created yet
		 * @throws ServerRuntimeError If had errors during the server proccess.
		 * @throws RecvError If the server can't receive any data
		 * @throws SendError If the server can't send any data for any client.
		 * @return void
		 */
		public function loop(){
			if(!$this->started) throw new ServerCreatedError("The socket haven't started yet!", 1);
			while($accepted = socket_accept($this->sock)){
				// starts if the server accepted any connection
				$snd = @socket_send($accepted, self::HANDSHAKE, 1024, 0);
				if($snd === false) throw new SendError(socket_strerror(socket_last_error()));
				$this->addLog(self::HANDSHAKE);
				$rtv = @socket_recv($accepted, $client_data, MAX_LISTEN, 0);
				if($rtv === false) throw new RecvError(socket_strerror(socket_last_error()));
				$this->addLog($client_data, true);
				$responce = $this->authData($client_data) ? "1" : "0";
				$snd = @socket_send($accepted, $responce, 1, 0);
				if($snd === false) throw new SendError(socket_strerror(socket_last_error()));
				$this->addLog($responce);
				socket_close($accepted);
			}
		}
	}
}