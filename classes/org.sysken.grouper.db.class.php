<?php
/**
 * Grouper DBアクセスクラス
 *
 * DBにアクセスします
 *
 * @copyright &copy; 2014 Ryosuke Hagihara <raryosu@sysken.org>
 * @version 0.3.5
 */
class db
{
  /**
   * MySQLハンドラの保持
   */
  protected $_mysqli;

  /**
   * クエリ文字列
   */
  protected $_query;

  /**
   * MySQL接続先ホスト名・ユーザ名・パスワード・db・ポート
   */
  protected $host;
  protected $username;
  protected $password;
  protected $db;
  protected $port;

  /**
   * コンストラクト
   *
   * @param string [$host = null]     MySQL接続先
   * @param string [$username = null] MySQLユーザ
   * @param string [$password = null] MySQLパスワード
   * @param string [$db = null]       データベースホスト名
   * @param int    [$port = null]     ポート
   */
  function __construct($host = null, $username = null, $password = null, $db = null, $port = null)
  {
    if($host === NULL)
    {
      $this -> host = 'localhost';
                      //ini_get('mysqli.default_host');
    }else{
      $this -> host = $host;
    }

    if($username === NULL)
    {
      $this -> username = 'connection';
                          //ini_get('m.default_user');
    }else{
      $this -> username = $username;
    }

    if($password === NULL)
    {
      $this -> password = 'grouper_server_tsuyama';
                          //ini_get('mysqli.default_pw');
    }else{
      $this -> password = $password;
    }

    if($db === NULL)
    {
      $this -> db = 'Grouper_new';
    }else{
      $this -> db = $db;
    }

    if($port === NULL)
    {
      $this -> port = ini_get('mysqli_default_port');
    }else{
      $this -> port = $port;
    }

    $this -> connect();
  }

  /**
   * データベースに接続します
   *
   * @return bool 実行結果
   */
  function connect()
  {
    $this -> _mysqli = new mysqli ($this -> host, $this -> username, $this -> password,
                                   $this -> db, $this ->port);

    if($this -> _mysqli -> connect_error)
    {
      common::error('db', 'Error connecting to DB');
      return false;
    }
    $this -> _mysqli -> set_charset('utf-8');
    return true;
  }

  /**
   * SQL インジェクション対策
   *
   * @param string $t 信頼に値しない情報
   * @return string 処理した文字列
   */
  function security($t)
  {
    return $this -> _mysqli -> real_escape_string($t);
  }

  /**
   * クエリを生成
   *
   * @param string $type  実行モードの指定[INSERT, UPDATE, DELETE, SELECT]
   * @param string $table 問い合わせたいテーブル
   * @param array $array  アサインしたいデータ配列
   * @return string       生成したクエリ
   */
  function buildQuery($type, $table, $search, $update = null)
  {
    $query = '';
    switch (mb_strtolower($type))
    {
      case 'insert':
        $query .= "INSERT INTO Grouper_new.{$table} ( " . implode(array_keys($search), ', ') . ' ) VALUE ( ';
        foreach ($search as $key => $value)
        {
          $query .= "'" . self::security($value) . "',";
        }
        $query = substr($query, 0, -1);
        $query .= ' )';
        break;

      case 'select':
        $query .= "SELECT * FROM Grouper_new.{$table} WHERE ";
        foreach($search as $key => $value)
        {
          $query .= "{$key} = '" . self::security($value) . "' AND " ;
        }
        $query = substr($query, 0, -5);
        break;

      case 'update':
        $query .= "UPDATE `{$table}` SET ";
        foreach($search as $key => $value)
        {
          $query .= "`{$key}` = '" . self::security($value) . "', ";
        }
        $query = substr($query, 0, -2);

        foreach($update as $key => $value)
        {
          $query .= "WHERE `{$key}` = '" . self::security($value) . "'";
        }
        break;

      case 'delete':
        $query .= "DELETE FROM `{$table}` WHERE";
        foreach($search as $key => $value)
        {
          $query .= "`{$key}` = '" . self::security($value) . "'";
        }
        break;
    }

    return $query;
  }

  /**
   * 完全なクエリの実行
   *
   * @param string $query   SQL問い合せ
   * @param bool $is_secure 安全かどうか
   * @return  bool          問い合わせ結果
   */
  function goQuery($query, $is_secure = false, $mode = null)
  {
    if(!$is_secure)
    {
      common::error('db', 'Emergency STOP');
    }
    switch ($mode)
    {
      case 'select':
        $rest = $this -> _mysqli -> query($query);
        return $rest;
        break;

      default:
        $rest = $this -> _mysqli -> query($query);
        if($rest === false)
        {
          return false;
        }elseif($rest === true){
          return true;
        }
        break;
    }
    return $rest -> fetch_all(MYSQLI_ASSOC);
  }

  /**
   * オートインクリメントで処理された値の取得
   * @return  int
   */
  function getID()
  {
    return $this -> _mysqli -> mysqli_stmt_insert_id;
  }
}
