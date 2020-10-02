<?php
namespace Core{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Exceptions.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/core/Core.php";

    use Core\DatabaseConnection;
    use ChangeLogExceptions\SignatureReferenceError;
    use ChangeLogExceptions\ClientReferenceError;
    use ChangeLogExceptions\ChangeLogNotFound;
    use ChangeLogExceptions\JSONChangelogError;
    use DatabaseActionsExceptions\NotConnectedError;
    use DatabaseActionsExceptions\AlreadyConnectedError;

    if(!defined("WAYBACK_CHANGE_CODE"))   define("WAYBACK_CHANGE_CODE", 0);
    if(!defined("NAME_CHANGE_CODE"))      define("NAME_CHANGE_CODE", 1);
    if(!defined("KEY_TOKEN_CHANGE_CODE")) define("KEY_TOKEN_CHANGE_CODE", 2);
    if(!defined("CODE_ROOT_CHANGE_CODE")) define("CODE_ROOT_CHANGE_CODE", 3);

    /**
     * The interface made for all the classes who operates with changelogs
     * and machine time feature
     */
    interface changelogManager {

        /**
         * Checks if there's a changelog with the same primary key reference
         * as the received
         *
         * @param integer $reference The primary key reference
         * @throws NotConnectedError If there's no database connected
         * @return boolean If the reference is valid or not
         */
        private function existsChangelog(int $reference): bool;

        /**
         * Adds a change log to the system.
         *
         * @param integer $reference The reference of the item who changed.
         * @param DateTime|string|null $date The date of the change. If null, it will get the actual datetime
         * @param integer $code The change code that sinalizes what was changed
         * @param integer|null $waybackRef If the change was a wayback, it must
         *                                 reference which changelog was reset (by default it's null).
         * @param string|null $p_name The name reference of the item before the change.
         * @param string|null $p_key_pass The key/password of the item before the change.
         * @param integer|boolean $p_root_code The root value/code of the item before the change.
         *
         * @throws NotConnectedError If there's no database connected
         * @throws SignatureReferenceError If the signature (item) reference isn't valid
         * @throws ClientReferenceError If the client (item) reference isn't valid
         * @return void
         */
        public function addChangelog(int $reference, $date = null, int $code, $waybackRef = null, string $p_name, string $p_key_pass, $p_root_code): void;

        /**
         * This action removes a changelog item from the database forever.
         * @param integer $changelog The item primary key reference
         * @throws NotConnectedError If there's no database connected
         * @throws ChangeLogNotFound If the changelog reference isn't valid
         * @return void
         */
        public function removeChangelog(int $changelog): void;

        /**
         * Lists all the changelogs in the database
         * @return array
         */
        public function lsChangelogs(): array;

        /**
         * Lists all the changelogs of a specific reference (client/signature)
         * @param integer $reference The reference to search in the database
         * @return array
         */
        public function changesFrom(int $reference): array;

        /**
         * Lists all the changelogs of a specific timestamp
         * @param string $when The timestamp to search
         * @return array
         */
        public function changelogsWhen(string $when): array;

        /**
         * Restores the changelog data to the original reference and storages a new
         * changelog with the wayback id, referencing which changelog was
         * restored.
         * @param integer $changelog The changelog to restore the data
         * @return integer The primary key of the wayback changelog
         */
        public function restore(int $changelog): int;
    }

    class ClientsChangeLogs extends DatabaseConnection implements changelogManager{

    }

    class SignaturesChangeLogs extends DatabaseConnection implements changelogManager{
        
    }
}
 ?>
