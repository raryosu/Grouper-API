# Grouper-API
## 概要
このリポジトリでは第25回全国高等専門学校プログラミングコンテスト課題部門「Grouper -集まりを つながりに-」で使う予定だった  
WebAPIを公開しています。

## 言語など
* PHP5.5
* MySQL

## 仕様
**/index.php**: 各種設定及びオートロード機構  
**/api.php**: パラメータを受け取り，function.php(各クラス)に渡す  
**/function.php**: パラメータを受け取り各種処理を行う  
**/config.php**: DB設定などです。内容はダミーです。  
**/classes/org.sysken.grouper.\*.class.php**: function.phpのクラスファイル。うまく動かなかったため却下

## コーディング規約
Zend Framework PHP 標準コーディング規約に原則的に則っています。  

## Lisence
This software is released under the MIT License, see LICENSE.  
&copy; 2014 Ryosuke Hagihara
