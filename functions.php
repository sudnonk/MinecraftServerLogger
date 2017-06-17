<?php
//コンフィグファイルの場所
define('INI_PATH','./config.ini');

$util = new Util();

/**
 * Class Util
 */
class Util{
    private $db_ini;
    private $mc_ini;

    /**
     * Util constructor.
     * エラーが起きたらexit
     */
    function __construct(){
        $ini = parse_ini_file(INI_PATH,true);
        if($ini === false) exit("Failed to open config file. Check your config file exists.");
        $this->db_ini = $ini["MySQL"];
        $this->mc_ini = $ini["Minecraft"];
    }

    /**
     * @param $key string iniのkey
     * @param $required bool そのキーが必須か
     * @return $value string iniのvalue
     */
    function get_db_ini_value($key,$required = true){
        $value = $this->db_ini[$key];
        if($value !== false || $required !== true) return $value;
        exit("$key not found. Check your config.");
    }
    function get_mc_ini_value($key,$required = true){
        $value = $this->mc_ini[$key];
        if($value !== false || $required !== true) return $value;
        exit("$key not found. Check your config.");
    }

    /**
     * @return string
     * エラーが起きたらexit
     */
    function get_log_file(){
        $log = file_get_contents($this->get_mc_ini_value("LOG_PATH"));
        if($log !== false) return $log;
        exit("Failed to open log file. Check your log file exists as you configured.");
    }

    /**
     * @param $log string ログファイル
     * @return array
     * エラーが起きたらexit
     */
    function parse_log_file($log){
        $lines = preg_split("\n",$log);
        if($lines !== false) return $lines;
        exit("Failed to parse log file. Check error_log.");
    }

    function connect(){
        try {
            $dsn = "mysql:host=" . $this->get_db_ini_value("DB_HOST") . ";dbname=" . $this->get_db_ini_value("DB_NAME");
            $pdo = new PDO($dsn, $this->get_db_ini_value("DB_USER"), $this->get_db_ini_value("DB_PASS"));
            return $pdo;
        }catch (PDOException $e){
            exit("Failed to connect database. The PDO message is ".$e->getMessage());
        }
    }
}