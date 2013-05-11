<?php
class login extends base {
    protected $__requirements = ["session", "db"];
    private $table = "user";
    function getTable() {
        return $this->table;
    }
    function setTable($table = "users") {
        return $this->table = $table;
    }
    function setEncryption($level) {
        return dechex($level);
    }
    function encrypt($password, $level = 3) {
        if($level < 0)
            $level = -$level;
        $tmp = strrev(base64_encode(crc32($password)));
        do {
            $tmp = base64_encode(hash("tiger128,3", strrev($tmp)));
            $password = base64_encode(hash("haval256,4", substr($password, 0, 2).$tmp.substr($password, 2)));
            $level--;
        } while($level > 0);
        $password = $this->main->db->hash(base64_encode(hash("whirlpool", strrev($password.$tmp))));
        return $password;
    }
    function «($username, $password, $level = 3) {
        if(isset($this->main->session->id))
            return $this->main->keyword("already_logged_in");
        return $this->main->db->{"count".ucfirst(strtolower($table))}([
            "username" => $username,
            "password" => $this->encrypt($password, $level)
        ]) > 0 ? $this->main->keyword("login_succefull") : $this->main->keyword("login_unsuccefull");
    }
    function »($username, $password, $level = 3) {
        if(isset($this->main->session->id))
            return $this->main->keyword("already_logged_in"); /* presume that a logged in user cannot register again… i can push a fix if someone needs that a logged in user can register */
        $this->main->db->{"insert".ucfirst(strtolower($table))}([
            "username" => $username,
            "password" => $this->encrypt($password, $level)
        ]);
        return $this->main->keyword("register_succefull");
    }
    function do($username, $password, $level = 3) {
        return $this->«($username, $password, $level);
    }
    function register($username, $password, $level) {
        return $this->»($username, $password, $level);
    }
}