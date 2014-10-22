<?php
/**
 * Grouper API実行
 *
 * 実行用ファイルです(2014.08.25更新)
 *
 * @copyright &copy; 2014 Ryosuke Hagihara
 * @create 2014.08.05
 * @auther Ryosuke Hagihara <raryosu@sysken.org>
 * @since PHP5.5+ / MySQL 5.3+
 * @version 0.3.5
 * @link http://grouper.sysken.org/
 */

/**
 * アプリケーションとの通信を行います
 */
// ファンクションファイルを読み込む(失敗為たら取り合えず終了)
// ちゃんと失敗したときの処理考えて無い
if(!include_once '/var/www/html/api/beta/index.php'){
  exit();
}

// httpヘッダ設定
header("Content-Type: application/json; charset=utf-8");

// $_GETと$_POSTは信用できないので 取り合えず一番はじめに処理しておく
foreach($_GET  as $key => $value) { $_GET[$key]  = $common->security($value); }
foreach($_POST as $key => $value) { $_POST[$key] = $common->security($value); }

// モードスイッチによって実行モードを変更する
switch($_GET['mode']) {
  // 1. ユーザ登録
  case 'regist':
    $rest = $api->regist($_GET['user_name'], $_GET['deviceID'], $_GET['tel1'], $_GET['tel2'], $_GET['tel3'],$_GET['is_tel_pub'], $_GET['regID']);
    echo $rest;
  break;

  // 2. ログイン
  case 'login':
    $rest = $api->login($_GET['userID'], $_GET['password']);
    echo $rest;
  break;

  // 3. グループ作成
  case 'create':
    $rest = $api->create($_GET['group_name'], $_GET['group_desc'], $_GET['sessionID'], $_GET['userID'], $_GET['query_mode']);
    echo $rest;
  break;

  // 4. 招待コードの生成及び登録
  case 'inviteID':
    $rest = $api->addInvitation($_GET['groupID'], $_GET['sessionID']);
    echo $rest;
  break;

  // 5. グループにユーザを追加
  case 'addUser':
    $rest = $api->addGroupUser($_GET['inviteID'], $_GET['userID'], $_GET['sessionID']);
    echo $rest;
  break;

  // 6. トークをDBに登録しプッシュ通知を行う準備をします
  case 'talk':
    $rest = $api->talk($_GET['groupID'], $_GET['userID'], $_GET['sessionID'], $_GET['talk'], $_GET['media'], $_GET['geo_x'], $_GET['geo_y']);
    echo $rest;
  break;

  // 7. アラームを設定
  case 'alarm':
    $rest = $api->alarm($_GET['groupID'], $_GET['userID'], $_GET['sessionID'], $_GET['alarm_time'], $_GET['alart_desc'], $_GET['alert_opt1'], $_GET['alart_opt2']);
    echo $rest;
  break;

  // 8. アラームへの応答
  case 'alartchoice':
    $rest = $api->alartchoice($_GET['userID'], $_GET['sessionID'], $_GET['alarmID'], $_GET['alart_choice']);
    echo $rest;
  break;

  // 9. アラームへの応答確認
  case 'alartcheck':
    $rest = $api->alartcheck($_GET['alarmID'], $_GET['groupID'], $_GET['sessionID']);
    echo $rest;
  break;

  // 10. トークの削除
  case 'delTalk':
    $rest = $api->delTalk($_GET['userID'], $_GET['sessionID'], $_GET['talkID']);
    echo $rest;
  break;

  // 11. グループの設定
  case 'settingGroup':
    $rest = $api->settingGroup($_GET['userID'], $_GET['sessionID'], $_GET['groupID'], $_GET['group_name'], $_GET['group_desc'], $_GET['is_group_del']);
    echo $rest;
  break;

  // 12. ユーザの設定
  case 'settingUser':
    $rest = $api->settingUser($_GET['userID'], $_GET['sessionID'], $_GET['groupID'], $_GET['user_name'], $_GET['is_user_del']);
    echo $rest;
  break;

  // 13. ユーザ情報の取得
  case 'getUser':
    $rest = $api->getUser($_GET['userID'], $_GET['sessionID']);
    echo $rest;
  break;

  // 14. グループ情報の取得
  case 'getGroup':
    $rest = $api->getGroup($_GET['groupID'], $_GET['query_mode']);
    echo $rest;
  break;

  /**
   * 以後災害時用機能(Group mode = 'disaster'(1)の場合のみ利用可能=>func.phpで定義)
   *
   * 15-17. 掲示板関連
   * 18-20. スケジュール機能
   */

  // 15. 掲示板機能
  case 'board':
    $rest = $api->board($_GET['sessionID'],$_GET['groupID'], $_GET['userID'], $_GET['contents']);
    echo $rest;
  break;

  // 16. 掲示板同期
  case 'fetch_board':
    $rest = $api->fetchBoard($_GET['sessionID'],$_GET['groupID']);
    echo $rest;
  break;

  // 17. 掲示板削除
  case 'del_board':
    $rest = $api->delBoard($_GET['sessionID'],$_GET['boardID']);
    echo $rest;
  break;

  // 18. スケジュール機能
  case 'schedule':
    $rest = $api->schedule($_GET['groupID'], $_GET['userID'], $_GET['sessionID'], $_GET['alarm_time'], $_GET['alart_desc']);
    echo $rest;
  break;

  // 19. スケジュール同期
  case 'fetchSchedule':
    $rest = $api->fetchSchedule($_GET['sessionID'], $_GET['alarmID']);
    echo $rest;
  break;

  // 20. スケジュール変更・削除
  case 'editSchedule':
    $rest = $api->editSchedule($_GET['sessionID'], $_GET['alarmID'], $_GET['is_schedule_del'], $_GET['alarm_time'], $_GET['alarm_desc']);
    echo $rest;
  break;

  default:
    echo $common->error('query','モードが間違っています');
  break;

}
