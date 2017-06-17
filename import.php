<?php
/**
 * ログファイルをパースして、新しく追加された行のみデータベースに入れます。
 */
require_once "functions.php";

/* @var $log_file string ログファイル */
$log_file = $util->get_log_file();
/* @var $lines array ログファイルの各行 */
$lines = $util->parse_log_file($log_file);

/* @var $pdo PDO  */
$pdo = $util->connect();

/* @var $date string 今日の日付 */
$date = date("Y-m-d");
/* @var $exits array すでに存在しているid */
try {
    $exits = $pdo->query("select id from logs where id like '$date%'")->fetchAll(PDO::FETCH_COLUMN);
}catch (PDOException $e){
    exit("Failed to get ids. PDO message is ".$e->getMessage());
}
$exits = array_flip($exits);

/**
 * @var  $key int 行番号-1
 * @var  $line string
 */
foreach ($lines as $key=>$line) {
    //$lineがから文字列ならスキップ
    if(strlen($line) < 1) continue;

    /* @var $match array */
    preg_match("/^\[([0-9]{2}:[0-9]{2}:[0-9]{2})\] \[(.+)\]: (.+)$/", $line, $match);

    /**
     * 例：[03:38:00] [Server thread/WARN]: Fetching addPacket for removed entity
     * @var $time string "03:38:00"
     * @var $status string "Server thread/WARN"
     * @var $message string "Fetching addPacket for removed entity"
     */
    $time = $match[1];
    $status = $match[2];
    $message = $match[3];
    if(!preg_match("/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/",$time)) continue;
    if(strlen($status) < 1) continue;

    /* @var $id string 日付+ログされた時間+行番号は恐らく一意 */
    $id = $date.$time.$key;

    if(isset($exits[$id])){
        //すでにそのidが存在していればスキップ
        continue;
    }else{
        //存在していなければ$existsに追加
        $exits[$id] = true;
    }

    try {
        $stmt = $pdo->prepare("insert into logs(`id`,`time`,`status`,`message`) VALUES (:id,:time,:status,:message)");
        $stmt->bindValue(":id", $id, PDO::PARAM_STR);
        $stmt->bindValue(":time", $time, PDO::PARAM_STR);
        $stmt->bindValue(":status", $status, PDO::PARAM_STR);
        $stmt->bindValue(":message", $message, PDO::PARAM_STR);
        $stmt->execute();
    }catch (PDOException $e){
        exit("Failed to insert log. PDO message is ".$e->getMessage());
    }
}
