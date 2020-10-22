<?php
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.js
 * @license          MIT LICENSE
 */

$title = 'カスタムフィールドプラグイン';
$description = 'ブログ記事に入力欄を設定できます。';
$author = 'Catchup, inc.';
$url = 'https://catchup.co.jp';
$adminLink = ['plugin' => 'cu_custom_field', 'controller' => 'cu_custom_field_configs', 'action' => 'index'];
$installMessage = 'ブログ記事の投稿画面に入力欄が追加されます。';
$installMessage .= '<br>インストール直後は、各ブログコンテンツのカスタムフィールドは無効状態です。カスタムフィールド設定画面より、利用するブログを有効に変更したのち利用してください。';
$installMessage .= '<br>また、１つのフィールドに大量の文字列を追加する可能性がある場合は、<br />データベース内の以下のテーブル内のカラム「value」の型を「LONGTEXT」に変更してください。';
$installMessage .= '<ul>';
$installMessage .= '<li>cu_custom_field_values</li>';
$installMessage .= '</ul>';
