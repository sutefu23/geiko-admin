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
define( 'GEIKOADMIN_DEBUG_MODE', true);

if(GEIKOADMIN_DEBUG_MODE){
    define( 'GEIKOADMIN_APPAPI_URL', 'http://54.65.245.167/');
}else{
    define( 'GEIKOADMIN_APPAPI_URL', 'https://');
}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once dirname(__FILE__ ) . "/classes/ApiBaseTrait.php";
require_once dirname(__FILE__ ) . "/classes/BadgeImageManager.php";

