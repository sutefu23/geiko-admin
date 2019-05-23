<?php
/*
Plugin Name: 芸工アプリ管理用プラグイン
Plugin URI:
Description: 芸工アプリ管理用のプラグインです。ユーザーのバッジ付与。投稿の削除等
Version: 1.0
Author: SEREAL
Author URI:
License: GPL2
*/
if (!defined('ABSPATH'))
    exit;
define( 'GEIKOADMIN_PLUGINPATH', plugin_dir_path( __FILE__ ) );
define( 'GEIKOADMIN_APPAPI_URL', 'https://');

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once dirname(__FILE__ ) . "/classes/BadgeImageManager.php";

$response = wp_remote_request( $url, [
    'method' => 'PUT',
    'body'  => $put_content,
] );
if(!is_wp_error( $response )){
    $response['body'];
}

wp_remote_get($url);
wp_remote_post($url ,[
    'body'  => $post_arr,
]);