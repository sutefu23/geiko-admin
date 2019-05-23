<?php
if (!defined('ABSPATH'))
    exit;
class BadgeImageManager{
    function __construct(){
        add_action( 'admin_enqueue_scripts', array($this,'media_script_enqueue'));
        add_action( 'admin_menu', array($this , 'add_badge_admin_menu'));
        add_action( 'wp_ajax_badge_image_save', array($this , 'badge_image_save'));
    }
    public function media_script_enqueue($hook) {
        if ( $hook != 'toplevel_page_badge-image' ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_script(
            'my-media-uploader',
            plugins_url("/../js/media-uploader.js", __FILE__),
            array('jquery'),
            1.0,
            false
        );
        if ( ! wp_script_is( 'jquery-ui', 'done' ) ) {
            wp_enqueue_script( 'jquery-ui' );
        }
    }

    // 設定画面追加
    public function add_badge_admin_menu() {
        add_menu_page(
              'バッジ設定',
              'バッジ設定',
              'administrator', // capability
              'badge-image',
              array($this, 'set_badge_image'), // function
              'dashicons-format-image', // icon_url
              50 // position
         );
    }
    // 設定画面用のHTML
    public function set_badge_image() {
    ?>
    <style type="text/css">
    .notice{
        display: none;
        height: 30px;
        padding-top: 10px;

    }
    .image-item{
        float: left;
    }
    #upload-images img
    {
        cursor:move;
        max-width: 200px;
        max-height: 200px;
        margin: 10px;
        border: 1px solid #cccccc;
    }
    .submit-field{
        text-align: right;
        margin-top: 20px;
        margin-right: 50px;
    }
    .image-remove{
        cursor: pointer;
        color: red;
        margin-top: -0.5em;
        display: block;
    }
    </style>
    <?php 
    if(wp_script_is('jquery','done')):
    ?>
    <script>
    jQuery(document).ready(function($){
        $('.image-remove').on('click',function(e){
            var image_id = $(this).attr('image-id');
            $('[image-id='+ image_id +']').remove();
        });

        $('#save-media').on('click',function(e) {
            var image_ids = new Array();
            $('#upload-images img').each(function(i){
                image_ids[i] = $(this).attr('image-id');
            })
            var data = {
                action: 'badge_image_save',
                image_ids: encodeURIComponent(JSON.stringify(image_ids)),
            };
            $.post('admin-ajax.php', data, function(response) {
                if (response.indexOf('true') > -1 || response == true) {
                    $(".notice")
                    .addClass("notice-success")
                    .addClass("is-dismissible")
                    .text("保存しました。")
                    .fadeIn("slow")
                    .delay(1000)
                    .fadeOut("slow");
                } else {
                    alert("保存出来ませんでした。\n" + response);
                    $('#save-media').data("valid", false);
                }
            });
            return false;
         });
    });
    </script>
    <script>
    jQuery(function($) {
        $(function () {
            $( "#upload-images" ).sortable();
            $( "#upload-images" ).disableSelection();
        })
    });

    </script>
    <?php
    endif;
     ?>
        <div class="wrap">
        <h2>バッジ用アイコン投稿</h2>
        <div class="notice">
        </div>
        <p>ここで選択されたものが順番設定されます。連続で選択が可能です。<br>
        画像をドラッグすると並べ替えを行います。</p>
        <button id="select-media">画像を追加</button>
        <div id="upload-images">
            <?php 
            if(get_option('badge_images')){
                $image_ids = get_option('badge_images');
                foreach ($image_ids as $image_id) {
                    echo '<div class="image-item">';
                    echo '<img image-id="'. $image_id .'" src="'.wp_get_attachment_image_src($image_id,'full',false)[0] .'" />';
                    echo '<span class="image-remove" image-id="'. $image_id .'">削除</span>';
                    echo '</div>';

                }
            } ?>
        </div>
        <div class="submit-field">
        <button id="save-media" class="button button-primary button-large">保存</button>
        </div>
        </div><!-- .wrap -->
    <?php
    }
    public function badge_image_save() {
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $domain = home_url();
        if( strpos($referer, $domain) === false ){
            die();
        }
        $image_ids = json_decode( rawurldecode($_POST['image_ids']),true);
        dump_text(serialize($image_ids));
        update_option('badge_images',$image_ids);
        echo 'true';
    }
}
$BadgeImageManager = new BadgeImageManager();
function dump_text($parameter,$key = null){
    ob_start();
    if($key){
      echo $key ."\n";
    }
    var_dump($parameter);
    $out = ob_get_contents();
    $out = $out . "\n---------------" . date('Y-m-d H:n:s') . "------------\n";

    ob_end_clean();
    file_put_contents(dirname(__FILE__). "/debug.txt", $out, FILE_APPEND);
}