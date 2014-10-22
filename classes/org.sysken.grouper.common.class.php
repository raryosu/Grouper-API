<?php
/**
 * 共通クラス
 *
 * すべての処理で共通して利用する関数をまとめたクラスです。
 *
 * @copyright &copy;2014 Ryosuke Hagihara <raryosu@sysken.org>
 * @version 0.3.5
 */
class common
{
  /**
   * バイナリセーフでない値をバリデーションします(日本語あやうい)
   *
   * @param string $t 信頼に値しない情報
   * @return string   エスケープ結果
   */
  function security($t)
  {
    return str_replace('\0', '',
                       str_replace(array('\\', '\0', '\n', '\r', '\xla', "'", '"'),
                                   array('\\\\', '\\0', '\\n', '\\r', '\\xla', "\\'", '\\"'),
                                   htmlspecialchars(mb_convert_encoding($t, 'UTF-8', 'UTF-8,SJIS,EUC-JP,Unicode'))
                                   )
                       );
  }

  /**
   * レスポンスヘッダを設定します
   *
   * @param string $header    ヘッダ文字列
   * @return bool             実行結果
   */
  function setHeader($header)
  {
    header($header);
    return true;
  }

  /**
   * データ送信準備
   *
   * @param array $content 連想配列
   * @param array $header  ヘッダ内容
   * @return string        出力できる文字列
   */
  function outgoing($content, $header = '')
  {
    // ヘッダの設定
    if(is_array($header))
    {
      foreach ($header as $value)
      {
        self::setHeader($value);
      }
    }
    self::setHeader("Content-Type: application/json; charset=utf-8");

    // メインコンテンツ
    $content = api::createJson($content);
    return $content;
  }

  /**
   * エラー時に実行する関数
   * エラー用コード生成を行います
   *
   * @param string $type エラーの発生箇所[db, api, session, internal, query, login, other]
   * @param strinr $msg  エラーの詳細
   * @return  null
   */
  function error($type, $msg)
  {
    $json = api::createJson(array('status'=>'500', 'contents'=>array('code'=>'-１', 'msg'=>'未知のエラーが発生しました')));
    switch($type)
    {
      case 'db':
        self::setHeader("x-status-code: 500-1");
        $json = api::createJson(array('status'=>'ERR', 'contents'=>(array('code'=>'500', 'msg'=>$msg))));
        break;

      case 'api':
        self::setHeader("x-status-code: 500-2");
        $json = api::createJson(array('status'=>'ERR', 'contents'=>(array('code'=>'500', 'msg'=>$msg))));
        break;

      case 'session':
        self::setHeader("x-status-code: 500-3");
        $json = api::createJson(array('status'=>'ERR', 'contents'=>(array('code'=>'500', 'msg'=>$msg))));
        break;

      case 'internal':
        self::setHeader("x-status-code: 500-4");
        $json = api::createJson(array('status'=>'ERR', 'contents'=>(array('code'=>'500', 'msg'=>$msg))));
        break;

      case 'login':
        self::setHeader("x-status-code: 401");
        $json = api::createJson(array('status'=>'ERR', 'contents'=>(array('code'=>'401', 'msg'=>$msg))));
        break;

      case 'query':
        self::setHeader("x-status-code:400-1");
        $json = api::createJson(array('status'=>'ERR', 'contents'=>(array('code'=>'400', 'msg'=>$msg))));
        break;

      case 'push':
        self::setHeader("x-status-code:500-5");
        $json = api::createJson(array('status'=>'ERR', 'contents'=>(array('code'=>'400', 'msg'=>$msg))));
        break;
    }
    self::setHeader("x-sid: " . time());
    echo $json;
    exit();
  }

  /**
   * push通知を送信します
   *
   * @param array|string $regID レジストレーションキー
   * @param strinr $msg         送信したいメッセージ
   * @param string $senduser    送信者のID
   * @return  bool              成功したかどうか
   */
  function sender($regID, $msg, $senduser, $mode='talk')
  {
    $url = 'https://android.googleapis.com/gcm/send';
    $header = array(
                    'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
                    'Authorization: key=AIzaSyDwMmyU5RHNn6NL8m_fGHvzaQaWB87HFFY'
                   );
    $msg = array('mode' => $mode, 'message' => $msg);
    // クソ
    for($i=0; !empty($regID); $i++)
    {
      $postParam = array(
                        'registration_id' => $regID[$i],
                        'collapse_key' => 'update',
                        'data.message' => $msg
                        );
      $post = http_build_query($postParam, '&');

      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_FAILONERROR, 1);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($curl, CURLOPT_POST, TRUE);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
      curl_setopt($curl, CURLOPT_TIMEOUT, 5);
      $ret = curl_exec($curl);
      if(!$ret)
      {
        self::error('push', 'push通知を送信できませんでした。');
      }
      unset($regID[$i]);
    }
    return true;
  }
}
