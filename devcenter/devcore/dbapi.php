<?php
// !/usr/bin/php
namespace Database;
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/devcenter/devcore/Exceptions.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Exceptions.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/logs-system.php";

use ServersExceptions\ServerNotFound;
use ServersExceptions\InvalidIP;
use ServersExceptions\InvalidPort;
use ServersExceptions\InvalidXML;
use ServersExceptions\ServerAlreadyExists;
use ServersExceptions\TokenAuthError;
use ServersExceptions\TokenExistsError;

use Core\DatabaseConnection;
use ProprietariesExceptions\ProprietaryNotFound;
use LogsSystem\Logger;

if(!defined("GL_USR_ACCESS")) define("GL_USR_ACCESS", "server.oth");
if(!defined("GL_PASS_ACCESS")) define("GL_PASS_ACCESS", "");

/**
 * That class manages all the registred servers at the MySQL database. That class just have new methods to manage all the servers.
 */
class ServersManager extends DatabaseConnection{

	const EMAIL_USING = "giulliano.scatalon.rossi@gmail.com";

	/**
	 * That function checks if a server exists in the database, the query's made using the server name. To check if the server exists
	 * using the IP address, use the ckServerIPEx function.
	 *
	 * @param string $server_nm The name of the server to query
	 * @return bool
	 */
	private function ckServerEx(string $server_nm){
		$this->checkNotConnected();
		$qr = $this->connection->query("SELECT nm_server FROM tb_servers WHERE nm_server = \"$server_nm\";");
		while($row = $qr->fetch_array()){
			if($row['nm_server'] == $server_nm) return true;
		}
		unset($qr);
		return false;
	}

	/**
	 * Function that check if a IP address is valid or not, (but it only works with IPV4 addresses, maybe I'll create another method to check the IPV6 addresses).
	 * To be valid, the IP address need to have 3 numbers, spliteds with "." and those numbers also need to be less then 1000;
	 *
	 * @param string $IP The IP address to check
	 * @return bool
	 */
	public static function ckIP(string $IP){
		$exp = explode(".", $IP);
		if(count($exp) != 4) return false;
		for($sep = 0; $sep < 4; $sep++){
			if((int) $exp[$sep] >= 1000 || (int) $exp[$sep] < 0) return false;
		}
		if((int) $exp[3] <= 0) return false;
		return true;
	}

	/**
	 * Function that check if a server exists in the database using the IP address of the server. For now there's only the IPV4 addresses to use.
	 * 
	 * @param string $server_IP The IP address of the server to check.
	 * @throws InvalidIP If the IP address received is invalid.
	 * @return bool
	 */
	private function ckServerIPEx(string $server_IP){
		$this->checkNotConnected();
		if(!$this->ckIP($server_IP)) throw new InvalidIP("The IP address '$server_IP' is invalid!", 1);
		$qr = $this->connection->query("SELECT vl_ip FROM tb_servers WHERE vl_ip = \"$server_IP\";");
		while($row = $qr->fetch_array()){
			if($row['vl_ip'] == $server_IP) return true;
		}
		unset($qr);
		return false;
	}

	/**
	 * Check if a server token exists, used for the token creation also.
	 *
	 * @param string $tk The token value to check;
	 * @return bool
	 */
	public function ckToken(string $tk){
		$this->checkNotConnected();
		$qr = $this->connection->query("SELECT tk_server FROM tb_servers WHERE tk_server = \"$tk\";");
		while($row = $qr->fetch_array()){
			if($row['tk_server'] == $tk) return true;
		}
		unset($qr);
		return false;
	}

	/**
	 * Generate a new token, that don't exist in the database.
	 *
	 * @return string
	 */
	private function genToken(){
		$this->checkNotConnected();
		$fnTk = "";
		while(true){
			$tk = "";
			$rd = random_int(2, 6);
			for($i = 0; $i <= $rd; $i++) $tk .= (string) random_int(1, 10);
			if(!$this->ckToken($tk)){
				$fnTk = $tk;
			break;
			}
			else continue;
		}
		return $fnTk;
	}

	/**
	 * Get the Primary Key reference of the proprietary account selected, with a query made using the proprietary
	 * name.
	 *
	 * @param string $prop The proprietary name to get the Primary Key Refrence
	 * @return integer
	 */
	private function getPropID(string $prop){
		$this->checkNotConnected();
		$qr = $this->connection->query("SELECT cd_proprietary FROM tb_proprietaries WHERE nm_proprietary = \"$prop\";")->fetch_array(); // only one row
		if(count($qr) <= 0) throw new ProprietaryNotFound("There's no proprietary \"$prop\"", 1);
		else return (int) $qr['cd_proprietary'];
	}

	/**
	 * Add a server to the database.
	 * 
	 * @param string $nm_server The server name to add.
	 * @param integer $port The port that the server will use.
	 * @param string $ip The server IP
	 * @param string $usr The username to login to the server (LPGP official server).
	 * @param string $pass The $user password.
	 * @param string $prop_nm The name of the proprietary account that own the server.
	 * @throws InvalidIP If the IP address received isn't valid
	 * @throws ServerAlreadyExists If the server name or the IP is already in use.
	 * @throws InvalidPort If the port value is less then zero or equal to zero
	 * @return void
	 */
	public function addServer(string $nm_server, int $port, string $ip, string $usr = GL_USR_ACCESS, string $pass = GL_PASS_ACCESS, string $prop_nm){
		$this->checkNotConnected();
		if($this->ckServerEx($nm_server)) throw new ServerAlreadyExists("The server name \"$nm_server\" is already being used.", 1);
		if($this->ckServerIPEx($ip)) throw new ServerAlreadyExists("There's a server with the IP address \"$ip\" already!", 1);
		if($port <= 0) throw new InvalidPort("The value '$port' isn't a valid port value!", 1);
		$tk = $this->genToken();
		$id = $this->getPropID($prop_nm);
		$qr = $this->connection->query("INSERT INTO tb_servers (nm_server, vl_ip, vl_port, usr_access, passwd_access, id_proprietary, tk_server) VALUES (\"$nm_server\", \"$ip\", \"$port\", \"$usr\", \"$pass\", $id, \"$tk\");");
		unset($qr);
		unset($id);
		unset($tk);
		return ;
	}

	/**
	 * Removes a server registre from the database, searching by the server name, or by the server IP address.
	 *
	 * @param string $server_nm The server name to remove
	 * @throws ServerNotFound If the server referenced doesn't exist
	 * @return void
	 */
	public function rmServer(string $server_nm){
		$this->checkNotConnected();
		if(!$this->ckServerEx($server_nm)) throw new ServerNotFound("There's no server \"$server_nm\"!", 1);
		$qr_del = $this->connection->query("DELETE FROM tb_servers WHERE nm_server = \"$server_nm\";");
		unset($qr_del);
	}

	/**
	 * Removes a server registre from the database searching by the server IP address.
	 * 
	 * @param string $server_ip The server IP address to delete
	 * @throws ServerNotFound If there's no server with that IP address
	 * @return void;
	 */
	public function rmServerIP(string $server_ip){
		$this->checkNotConnected();
		if(!$this->ckServerIPEx($server_ip)) throw new ServerNotFound($server_ip, 1);
		$qr_del = $this->connection->query("DELETE FROM tb_servers WHERE vl_ip = \"$server_ip\";");
		unset($qr_del);
	}
	
	/**
	 * Changes a server name. If the new name is already being used by another server will throw a error.
	 * @param string $server_nm The server to change the name
	 * @param string $new_name The new server name
	 * @throws ServerNotFound If the selected server don't exist.
	 * @throws ServerAlreadyExists If the new server's already in use.
	 * @return void
	 */
	public function chServerName(string $server_nm, string $new_name){
		$this->checkNotConnected();
		if(!$this->ckServerEx($server_nm)) throw new ServerNotFound($server_nm, 1);
		if($this->ckServerEx($new_name)) throw new ServerAlreadyExists($new_name, 1);
		$qr_ch = $this->connection->query("UPDATE tb_servers SET nm_server = \"$new_name\" WHERE nm_server = \"$server_nm\";");
		unset($qr_ch);
	}

	/**
	 * Changes the server IP address. If the IP address is already in use, then will throw a exception.
	 * 
	 * @param string $server_nm The server name to change the IP address
	 * @param string $ip The new IP address to the server
	 * @throws ServerNotFound If the selected server don't exist
	 * @throws ServerAlreadyExists If the new IP address is already in use.
	 * @return void
	 */
	public function chServerIP(string $server_nm, string $ip){
		$this->checkNotConnected();
		if(!$this->ckServerEx($server_nm)) throw new ServerNotFound($server_nm, 1);
		if($this->ckServerIPEx($ip)) throw new ServerAlreadyExists($ip, 1);
		$ch_qr = $this->connection->query("UPDATE tb_servers SET vl_ip = \"$ip\" WHERE nm_server = \"$server_nm\";");
		unset($ch_qr);
	}

	/**
	 * Generates a new token for a server, and aply it to the server.
	 * 
	 * @param string $nm_server The server to generate the new token
	 * @throws ServerNotFound If the selected server don't exist.
	 * @return void
	 */
	public function regenServerTK(string $nm_server){
		$this->checkNotConnected();
		if(!$this->ckServerEx($nm_server)) throw new ServerNotFound($nm_server, 1);
		$new_tk = $this->genToken();
		$qr_ch = $this->connection->query("UPDATE tb_servers SET tk_server = \"$new_tk\" WHERE nm_server = \"$nm_server\";");
		unset($qr_ch);
		unset($new_tk);
	}

	/**
	 * Sends the server token to the server Proprietary email. It will use a default template for sending the email.
	 * @param string $nm_server The server name to send the token to the proprietary.
	 * @throws ServerNotFound If the selected server don't exist.
	 * @return void;
	 */
	public function sendSMPTTK(string $nm_server){
		$this->checkNotConnected();
		if(!$this->ckServerEx($nm_server)) throw new ServerNotFound($nm_server, 1);
		$data = $this->connection->query("SELECT prp.nm_proprietary \"proprietary\", prp.vl_email \"email\", srv.tk_server \"token\" FROM tb_servers as srv INNER JOIN tb_proprietaries as prp ON srv.id_proprietary = prp.cd_proprietary WHERE nm_server = \"$nm_server\";")->fetch_array();
		$headers = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=ISO-8859\n";
		$headers .= "From: " . self::EMAIL_USING . "\n";
		$headers .= "Cc: " . $data['email'] . "\n";
		$content = "<body>\n";
		$content .= "    <h1>Your server ($nm_server)</h1>\n";
		$content .= "    <h3>That's the access token of your server, " . $data['proprietary'] . "</h3>\n";
		$content .= "    <p>Your access token is: <b>" . $data['token'] . "</b></p>";
		$content .= "</body>";
		mail($data['email'], "Your server token [LPGP OFFICIAL]", $content, $headers);
	}
}

?>