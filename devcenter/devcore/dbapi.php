<?php
namespace ClientsDatabase{
	use Exception;

	try{
		require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/logs-system.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
	}
	catch(Exception $e){
		require_once "core/logs-system.php";
		require_once "core/Core.php";
	}

	use LogsSystem\Logger;
	use Core\DatabaseConnection;
    use TypeError;

	if(!defined("ROOT_USR_ACCESS")) define("ROOT_USR_ACCESS", "client_root");
	if(!defined("ROOT_PAS_ACCESS")) define("ROOT_PAS_ACCESS", "");
	if(!defined("NRML_USR_ACCESS")) define("NRML_USR_ACCESS", "client_normal");
	if(!defined("NRML_PAS_ACCESS")) define("NRML_PAS_ACCESS", "");

	/**
	 * That class manages all the MySQL database clients connections 
	 */
	class ClientsManager extends DatabaseConnection{

		/**
		 * Checks if a client exists in the database
		 *
		 * @param string $client_nm The client name reference to search
		 * @return bool
		 */
		private function ckClientEx(string $client_nm){
			$this->checkNotConnected();
			$qr = $this->connection->query("SELECT nm_client FROM tb_clients WHERE nm_client = \"$client_nm\";");
			while($row = $qr->fetch_array()){
				if($row['nm_client'] == $client_nm) return true;
			}
			return false;
		}

		/**
		 * Checks if a token exists for a client profile.
		 * 
		 * @param string $token The token value to search in the database.
		 * @return bool
		 */
		private function ckTokenEx(string $token){
			$this->checkNotConnected();
			$qr = $this->connection->query("SELECT tk_client FROM tb_clients WHERE tk_client = \"$token\";");
			while($row = $qr->fetch_array()){
				if($row['tk_client'] == $token) return true;
			}
			return false;
		}

		/**
		 * Generates a new token for a client, normally used at the addClient method.
		 * @return string
		 */
		private function genToken(){
			$tk = "";
			$len = random_int(2, 6);
			do{
				for($i = 0; $i < $len; $i++){
					$tk .= random_int(1, 9);
				}
			}while($this->ckTokenEx($tk) || $tk == "");
			return $tk;
		}

		/**
		 * Checks the client proprietary reference. It can be a name, or the proprietary Primary key reference.
		 * If the value received is a proprietary name, it will check if the proprietary name exists and will return 
		 * the proprietary primary key searching by the name reference. If the value received is a Primary key, 
		 * the method will check if the primary key exists, and then will return it self. If the reference doesn't 
		 * exist, then will return false.
		 * 
		 * @param string|int $ref The proprietary reference
		 * @return int|false
		 */
		private function ckPropRef($ref){
			$this->checkNotConnected();
			if(is_string($ref)){
				$qr = $this->connection->query("SELECT cd_proprietary FROM tb_proprietaries WHERE nm_proprietary = \"$ref\";")->fetch_array();
				if(isset($qr['cd_proprietary'])) return (int) $qr['cd_proprietary'];
				else return false;
			}
			else if(is_int($ref)){
				$qr = $this->connection->query("SELECT cd_proprietary FROM tb_proprietaries WHERE cd_proprietary = $ref;")->fetch_array();
				if($qr['cd_proprietary'] == $ref) return $ref;
				else return false;
			}
			else throw new TypeError("Expecting integer or string value, but got " . gettype($ref), 1);
		}

		/**
		 * Add a client to the database.
		 * @param string $nm_client The new client name reference
		 * @param string|int $proprietary The client proprietary name/PK reference.
		 * @param bool $root If the client will be a root client. It's False by default.
		 * @throws ClientAlreadyExists If the client name reference already exists in the database.
		 * @throws PropRefNotFound If the class can't found the proprietary referred
		 * @return void
		 */
		public function addClient(string $nm_client, $proprietary, bool $root = false){
			$this->checkNotConnected();
			if($this->ckClientEx($nm_client)) throw new ClientAlreadyExists(1);
			if(($rf = $this->ckPropRef($proprietary)) === false) throw new PropRefNotFound($proprietary, 1);
			$tk = $this->genToken();
			$rt = $root ? 1: 0;
			$qr = $this->connection->query("INSERT INTO tb_clients (nm_client, id_proprietary, tk_client, vl_root) VALUES (\"$nm_client\", $rf, \"$tk\", $rt);");
			unset($qr);
		}

		/**
		 * Removes a client from the database, using the nm_client reference value
		 * 
		 * @param string $nm_client The client name reference
		 * @throws ClientNotFound If the client name reference don't exist.
		 * @return void
		 */
		public function rmClient(string $nm_client){
			$this->checkNotConnected();
			if(!$this->ckClientEx($nm_client)) throw new ClientNotFound($nm_client, 1);
			$qr = $this->connection->query("DELETE FROM tb_clients WHERE nm_client = \"$nm_client\";");
			unset($qr);
		}

		/**
		 * Changes the client name reference of a client.
		 * 
		 * @param string $client The actual client name reference.
		 * @param string $new_name The new client name reference value.
		 * @throws ClientNotFound If the actual client reference don't exist.
		 * @throws ClientAlreadyExists If the new client name reference is already being used by other client.
		 * @return void
		 */
		public function chClientName(string $client, string $new_name){
			$this->checkNotConnected();
			if(!$this->ckClientEx($client)) throw new ClientNotFound($client);
			if($this->ckClientEx($new_name)) throw new ClientAlreadyExists();
			$qr_ch = $this->connection->query("UPDATE tb_clients SET nm_client = \"$new_name\" WHERE nm_client = \"$client\";");
			unset($qr_ch);
		}

		/**
		 * Generate a new token for the client profile.
		 * 
		 * @param string $client The client name reference to change the token
		 * @throws ClientNotFound If there's no client name such the referred
		 * @return void
		 */
		public function genNewToken(string $client){
			$this->checkNotConnected();
			if(!$this->ckClientEx($client)) throw new ClientNotFound($client);
			$new_tk = $this->genToken();
			$qr = $this->connection->query("UPDATE tb_clients SET tk_client = \"$new_tk\" WHERE nm_client = \"$client\";");
			unset($qr);
		}

		/**
		 * Changes the privileges on the client	profile privileges (root database field)
		 * 
		 * @param string $client The client name reference
		 * @param bool $toRoot If the client will have root privileges now.
		 * @throws ClientNotFound If the client name reference don't exist.
		 * @return void
		 */
		public function chClientPri(string $client, bool $toRoot = false){
			$this->checkNotConnected();
			if(!$this->ckClientEx($client)) throw new ClientNotFound($client);
			$vl = $toRoot ? 1: 0;
			$qr = $this->connection->query("UPDATE tb_clients SET vl_root = $vl WHERE nm_client = \"$client\";");
			unset($qr);
			unset($vl);
		}

		/**
		 * Authenticate the client data, normally received from the socket server.
		 * @param string $client_nm The client name reference.
		 * @param string $token The client token
		 * @return bool
		 */
		public function authClient(string $client_nm, string $token){
			$this->checkNotConnected();
			if(!$this->ckClientEx($client_nm)) return false;  // the client doesn't exist
			$or_tk = $this->connection->query("SELECT tk_client FROM tb_clients WHERE nm_client = \"$client_nm\";")->fetch_array();
			return $or_tk['tk_client'] == $token;
		}
	}
}