<?php
namespace ClientsDatabase{

	use Exception;
	
	try{
		require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/logs-system.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
		require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/exceptions.php";
	}
	catch(Exception $e){
		require_once "core/logs-system.php";
		require_once "core/Core.php";
	}

	use Core\DatabaseConnection;
	use TypeError;
	use Core\SignaturesData;
	use ClientHistory\ReferenceError;
    use SignaturesExceptions\InvalidSignatureFile;

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
		private function ckClientEx(int $client_id){
			$this->checkNotConnected();
			$qr = $this->connection->query("SELECT cd_client FROM tb_clients WHERE cd_client = $client_id;");
			while($row = $qr->fetch_array()){
				if($row['cd_client'] == $client_id) return true;
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
		public function authClient(int $client_id, string $token){
			$this->checkNotConnected();
			if(!$this->ckClientEx($client_id)) return false;  // the client doesn't exist
			$or_tk = $this->connection->query("SELECT tk_client FROM tb_clients WHERE cd_client = $client_id;")->fetch_array();
			return $or_tk['tk_client'] == $token;
		}


		/**
		 * Creates a filename for the signature file. 
		 *
		 * @param int $initial_counter The first contage of the filename (signature-file-$initial_counter)
		 * @return string
		 */
		public static function generateFileNm(int $initial_counter = 0, string $path){
			$local_counter = $initial_counter;
			while(true){
				if(!file_exists("$path/signature-file-". $local_counter . ".lpgp"))
					break;
				else $local_counter++;
			}
			return "client-file-".$local_counter . ".lpgp";
		}

		/**
		 * Generate a client signature file, with the same script then the signatures generation script. But with a different structure of
		 * the encoded JSON content.
		 * 
		 * @param string $client The client name reference for generate the auth.lpgp file for it
		 * @param string $path The file path to generate the .lpgp file.
		 * @param bool $HTML_mode If the file will be available for download. If it's true, then will return a <a> tag with the path to the download.
		 * @throws ClientNotFound If the client referred doesn't exist.
		 * @return string The path or the <a> tag for download, or whatever you would want to do with the lpgp file of that client.
		 */
		public function genAuth(string $client, string $path, bool $HTML_mode = false){
			$this->checkNotConnected();
			if(!$this->ckClientEx($client)) throw new ClientNotFound($client);
			// generate the file name;
			$nm = $this->generateFileNm(0, $path);
			// create the content
			$con = "";
			$client_data = $this->connection->query("SELECT tk_client, id_proprietary FROM tb_clients WHERE nm_client = \"$client\";")->fetch_array();
			$tmp_arr = array();
			$json_arr = array(
				"Client" => $client,
				"Proprietary" => $client_data['id_proprietary'],
				"Token" => $client_data['tk_client'],
				"Dt" => date("Y-m-d H:i:s")
			);
			$json_con = json_encode($json_arr);
			for($chr = 0; $chr < strlen($json_con); $chr++) array_push($tmp_arr, (string) ord($json_con[$chr]));
			$con = implode(SignaturesData::DELIMITER, $tmp_arr);
			$path = $_SERVER['REMOTE_HOST'] . "/lpgp-server/devcenter/l.clientf/$nm";
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/l.clientf/$nm", $con);
			return $HTML_mode ? "<a href=\"$path\" download=\"$path\" class=\"btn btn-primary\" role=\"button\">Download</a>" : "$path";
		}

		/**
		 * Authenticate a client .lpgp file. The authentication will check the file Client, Token and proprietary.
		 * @param string $file The .lpgp file name to get and authenticate, all the uploaded .lpgp files are in the u.clientf folder
		 * @throws InvalidSignatureFile If the file isn't valid.
		 * @return true Only if the file is valid.
		 */
		public function authenticateClientF(string $file){
			$this->checkNotConnected();
			$raw_con = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/u.clientf/$file");
			$exp_con = explode(SignaturesData::DELIMITER, $raw_con);
			$json_con = "";
			foreach($exp_con as $ascii) $json_con .= chr((int) $ascii);
			$pure_content = json_decode($json_con, true);
			// Authentication process ^W^
			if(!$this->ckClientEx($pure_content['Client'])) throw new InvalidSignatureFile("There's no client such as in the file", 1);
			if(!$this->ckPropRef((int) $pure_content['Proprietary'])) throw new InvalidSignatureFile("Proprietary reference error!", 1);
			if(!$this->authClient($pure_content['Client'], $pure_content['Token'])) throw new InvalidSignatureFile("Token access error", 1);
			return true;
		}

		/**
		 * Authenticate a client .lpgp file content. That authentication is used **ONLY AT THE SERVER.PHP**.
		 * 
		 * @param string $content The .lpgp file content.
		 * @throws InvalidSignatureFile If the .lpgp file isn't valid.
		 * @return true
		 */
		public function authenticateContent(string $content){
			$this->checkNotConnected();
			$exp_con = explode(SignaturesData::DELIMITER, $content);
			$json_con = "";
			foreach($exp_con as $ascii) $json_con .= chr((int) $ascii);
			$pure = json_decode($json_con, true);
			////////////////////////////////////////////////////////////
			if(!$this->ckClientEx($pure['Client'])) throw new InvalidSignatureFile("There's no client such as in the content!", 1);
			if(!$this->ckPropRef((int) $pure['Proprietary'])) throw new InvalidSignatureFile("Proprietary Reference error!", 1);
			if(!$this->authClient($pure['Client'], $pure['Token'])) throw new InvalidSignatureFile("Token access error", 1);
			return true;
		}
	}

	/**
	 * That class manages the history of the clients authentications. That history is a table at the
	 * MySQL database, everytime a client's authenticated it creates a register to the 
	 */
	class ClientsHistory extends DatabaseConnection{

		/** 
		 * Checks if a client primary key reference exists in the history. It's too much necessary for the hole system, so don't broke it.
		 * @param integer $ref The client primary key reference to search in the database.
		 * @return bool
		*/
		private function ckRefEx(int $ref){
			$this->checkNotConnected();
			$res = $this->connection->query("SELECT COUNT(cd_client) 'Num' FROM tb_clients WHERE cd_client = $ref;")->fetch_array();
			return $res['Num'] != 0;
		}

		/**
		 * Return the client primary key reference using him name. Return null if the client doesn't exist.
		 *
		 * @param string $ref The client name (nm_client) reference
		 * @return integer|null
		 */
		private function gtNmId(string $ref){
			$this->checkNotConnected();
			$qr1 = $this->connection->query("SELECT COUNT(nm_client) 'tot' FROM tb_clients WHERE nm_client = \"$ref\"")->fetch_array();
			if($qr1['tot'] != 1) return null;
			$qr = $this->connection->query("SELECT cd_client FROM tb_clients WHERE nm_client = \"$ref\";")->fetch_array();
			return $qr['cd_client'];
		}

		/**
		 * Add a register in the history, using a client reference (name or primary key), date reference (normally using the default) and if it was a success.
		 * 
		 * @param string|integer $client The client reference.
		 * @param string|null $datetime The datetime value to use;
		 * @param integer|bool $success If the authentication was successfull.
		 * @throws ReferenceError
		 * @return void
		 */
		public function addReg($client, $datetime = null, $success = 1){
			$this->checkNotConnected();
			print($this->connection->error);
			if(is_string($client)){
				$tmp = $this->gtNmId($client);   // int now
				if(is_null($client)) throw new ReferenceError("There's no '$client' as a client reference", 1);
				else $client = $tmp;
			}
			else if(is_int($client) && !$this->ckRefEx($client)) throw new ReferenceError("There's no ID #$client as a client reference", 1);
			if(is_bool($success)) $success = $success ? 1 : 0;

			if(is_null($datetime)) $qr = $this->connection->query("INSERT INTO tb_access (id_client, vl_success) VALUES ($client, $success);");
			else $qr = $this->connection->query("INSERT INTO tb_access (id_client, vl_success, dt_access) VALUES ($client, $success, '$datetime');");
			unset($qr);
			return ; 
		}

		/**
		 * Return true or false if all the array items are integer type.
		 *
		 * @param array $items The array to check the items
		 * @return boolean
		 */
		private static function is_all_int(array $items){
			for($i = 0; $i < count($items); $i++){
				if(!is_int($items[$i])) return false;
			}
			return true;
		}

		/**
		 * Return true or fals if all the array items are string type
		 * 
		 * @param array $items The array to check the items
		 * @return boolean
		 */
		private static function is_all_str(array $items){
			for($i = 0; $i < count($items); $i++){
				if(!is_string($items[$i])) return false;
			}
			return true;
		}

		/**
		 * Returns the total of registers wich have specific clients, used for calculating the percentual of clients accesses.
		 * @param array $clients A clients array with the each client reference
		 * @throws TypeError If the $clients array have a item wich isn't integer or string
		 * @throws ReferenceError If any of those $clients refereds doesn't exist.
		 * @return int The percent of the access of those clients.
		 */
		public function getClientsPercent(array $clients){
			if($this->is_all_str($clients)){
				$int_arr = array();
				foreach($clients as $str_ref){
					$tmp = $this->gtNmId($str_ref);
					if(is_null($tmp)) throw new ReferenceError("There's no '$str_ref' as a client name reference!", 1);
					else $int_arr[] = $tmp;
				}
				$tot_count = 0;
				foreach($int_arr as $client){
					$qr = $this->connection->query("SELECT COUNT(id_client) 'tt' FROM tb_access WHERE id_client = $client;")->fetch_array();
					$tot_count += (int)$qr['tt'];
				}
				return $tot_count;
			}
			else if($this->is_all_int($clients)){
				$tot_count = 0;
				foreach($clients as $client){
					if(!$this->ckRefEx($client)) throw new ReferenceError("There's no ID #$client as a client reference!", 1);
					$qr = $this->connection->query("SELECT COUNT(id_client) 'tt' FROM tb_access WHERE id_client = $client;")->fetch_array();
					$tot_count += (int)$qr['tt'];
				}
				return $tot_count;
			}
			else throw new TypeError("Expecting a array with string/integer items type", 1);
		}

		/**
		 * Return the total access  of a client at all the access.
		 * 
		 * @param string|int $client The client reference
		 * @throws ReferenceError If there's no such client reference as the received
		 * @return integer
		 */
		public function calcPerClient($client){
			$this->checkNotConnected();
			if(is_string($client)) $client = $this->gtNmId($client);
			$qr_client  = $this->connection->query("SELECT COUNT(id_client) 'tclient' FROM tb_access WHERE id_client = $client;")->fetch_array();
			return (int)$qr_client['tclient'];
		}

		/**
		 * Return the access percentage of the selected clients at all the access;
		 * 
		 * @param array $clients The clients to search
		 * @throws ReferenceError If the client referred doesn't exist
		 * @return array
		 */
		public function calcPerClients(array $clients){
			$this->checkNotConnected();
			$tot = [];
			foreach($clients as $client) $tot["$client"] = $this->calcPerClient($client);
			return $tot;
		}


		/**
		 * Return the total access of a client, filtring by the year.
		 * 
		 * @param string|integer $client The client reference to search, it can be the client name or the client PK
		 * @param string|integer|null $year The year to search, it can be the year value (string or integer) or the current year (null)
		 * @throws ReferenceError If there're errors with the client reference
		 * @return integer
		 */
		public function PerClientYear($client, $year = null){
			$this->checkNotConnected();
			if(is_string($client)) {
				$client_tmp = $this->gtNmId($client);
				if(is_null($client_tmp)) throw new ReferenceError("There's no client '$client' such the referred", 1);
				else $client = $client_tmp;
			}
			$dt_sr = is_null($year) ? date("Y") : $year;
			$qr = $this->connection->query("SELECT COUNT(cd_access) 'Data' FROM tb_access WHERE id_client = $client AND YEAR(dt_access) = $dt_sr;")->fetch_array();
			return $qr['Data'];
		}

		/**
		 * Return the access percent of selected clients and a year.
		 * @param array $clients The array with all the clients references;
		 * @param string|integer|null $year The search to filter.
		 * @throws ReferenceError In case of errors with the client reference
		 * @return array
		 */
		public function PerClientsYearMany($clients, $year = null){
			$this->checkNotConnected();
			$arr_tot = [];
			foreach($clients as $client) $arr_tot["$client"] = $this->PerClientYear($client, $year);
			return $arr_tot;
		}


	}
}
