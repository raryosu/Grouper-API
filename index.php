<?php
/**
 * index.php
 *
 * index.phpです()
 *
 * @copyright &copy; 2014 Ryosuke Hagihara
 * @create    2014.08.05
 * @auther    Ryosuke Hagihara<raryosu@sysken.org>
 * @since     PHP5.5+ / MySQL 5.3+
 * @version   0.3.4
 * @link      http://grouper.sysken.org/
 */
// function.phpを読み込めなかったら・・・
if(!include_once '/var/www/html/api/beta/config.php')
{
  exit();
}

// デバッグフラグ
if($debug)
{
  error_reporting(-1);
  ini_set('display_errors', true);
}else{
  error_reporting(0);
  ini_set('display_errors', false);
}

// インスタンスの生成じゃぁ＾～
$common = new common();
$api = new api($_CONF['db_host'], $_CONF['db_user'], $_CONF['db_pass'], $_CONF['db_table']);

/**
 * オートロード機構
 *
 * 読み込まれていないクラスファイルがあれば必要に応じ動的にロードします
 *
 * @param string $class クラスファイルのタイプ
 * @return bool
 */
spl_autoload_register(function ($class)
{
  include '/var/www/html/api/beta/classes/' . $class . '.class.php';
});
