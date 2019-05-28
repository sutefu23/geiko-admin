<?php
namespace GeikouApp;
if (!defined('ABSPATH'))
    exit;

trait ApiBaseTrait{
    protected $url = GEIKOADMIN_APPAPI_URL;

    public function remote_get($get_arr){
        $http_parameter = http_build_query($get_arr);
        return $this->_remote_request('GET',$get_arr , $this->url . '?' . $http_parameter);
    }
    public function remote_post($post_arr){
        return $this->_remote_request('POST',$put_arr);
    }
    public function remote_put($put_arr){
        return $this->_remote_request('PUT',$put_arr);
    }
    private function _remote_request($httpmethod, $para_arr ,$url = null) {
        if(empty($url)){ $url = $this->url; }

        $response = wp_remote_request( $url, [
            'method' => $httpmethod,
            'body'  => $para_arr,
        ]);

        if(!is_wp_error( $response )){
            return $response['body'];
        }else{
            return $response->get_error_messages();
        }
    }
    public function json_to_data($json){
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $data = json_decode($json,true);
    }
    public function get_badge_image_srcs() : array{
        $image_arr = [];
        if(get_option('badge_images')){
            $image_ids = get_option('badge_images');
            foreach ($image_ids as $image_id) {
                $image_arr = $wp_get_attachment_image_src($image_id,'full',false)[0] ;
            }
        }
        return $image_arr;
    }
}