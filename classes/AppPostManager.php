<?php
namespace GeikouApp;
if (!defined('ABSPATH'))
    exit;
/*
目的：
アプリの投稿一覧をJsonで取得し、管理画面に一覧で表示します。
*/
if (!defined('ABSPATH'))
    exit;


Class AppPostManager extends WP_List_Table
{
    use ApiBaseTrait;
    protected $posts;
    protected $url;
    public $post_num;
    public function __construct()
    {
        global $status, $page;
        parent::__construct(array(
            'singular'  =>  'app_post',
            'plural'    =>  'app_posts',
            'ajax'      =>  false
        ));
        if(!is_numeric($_GET['paged'])) unset($_GET['paged']);
        if(!empty($_GET['paged'])){
            $page = esc_html($_GET['paged']);
            $this->url = "http://rakupa.info/api/posts?page=" . esc_html($page);
        }else{
            $this->url = "http://rakupa.info/api/posts";
        }
        $json = file_get_contents($this->url);
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $data = json_decode($json,true);
        $this->posts = $data['posts'];
        $this->post_num = $data['count'];
    }
    function column_default($item, $column_name){
                return $item[$column_name];
    }
    public function get_columns(){
        $columns = array(
            'id'            => 'ID',
            'uuid'          => 'UUID',
            'name'          => 'お名前',
            'email'         => 'E-mail',
            'account_count' => 'アカウント数',
            'logged_in_at'  => '最終ログインイン',
            'checkins_count'=> 'チェックイン回数',
        );
        return $columns;
    }
    function get_sortable_columns() {
        $sortable_columns = array(
            'id'            => array('id',false),
            'uuid'          => array('uuid',false),
            'name'          => array('name',false),
            'email'         => array('email',false),
            'account_count' => array('account_count',true),
            'logged_in_at'  => array('logged_in_at',true),
            'checkins_count'=> array('checkins_count',true),
        );
        //return $sortable_columns;
    }
    public function prepare_items(){
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            if(is_numeric($a[$orderby])){
                $result = $a[$orderby] - $b[$orderby];
            }else{
                $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            }
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($this->posts, 'usort_reorder');

        $this->set_pagination_args([
            'total_items' => $this->post_num,
            'per_page' => 20
        ]);

        $this->items = $this->posts;
    }
}

function register_app_post_table()
{
    add_menu_page('アプリユーザー', 'アプリユーザー', 'manage_options', 'app_post_list', 'add_app_post_list_page', 'dashicons-admin-posts', 70);
}
add_action('admin_menu', 'register_app_post_table');

function add_app_post_list_page(){
$appPostManager = new AppPostManager();
$appPostManager->prepare_items();

?>
<div class="wrap">
    <div id="icon-posts" class="icon32"><br/></div>
    <h2>アプリユーザー一覧</h2>
    <p>ユーザー数:<?php echo $appPostManager->post_num; ?></p>
    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="movies-filter" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <!-- Now we can render the completed list table -->
        <?php $appPostManager->display() ?>
    </form>
</div>

<?php
}
