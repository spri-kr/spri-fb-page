<?php

/*
Plugin Name: Spri Fb Page display set
Description: facebook page feed widget
Version: 0.1
Author: ungsik yun
*/

require_once( "vendor/facebook/php-sdk/src/facebook.php" );
require_once( "fb-connector.php" );
require_once( "spri-fb-page-widget.php" );
require_once( "spri-fb-page-option.php" );


class spri_fb_page {

	private $post_date_table;
	private $post_tag_table;
	private $target_page_table;
	private $fb;

	function __construct() {

		// database table name setup
		global $wpdb;
		$this->post_date_table   = $wpdb->prefix . "spri_fb_page_post_date";
		$this->post_tag_table    = $wpdb->prefix . "spri_fb_page_post_tag";
		$this->target_page_table = $wpdb->prefix . "spri_fb_target_page";

		// Generate FB object
		$this->fb = fb_connector::get_fb_obj();

		// widget scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

		// widget register
		add_action( 'widgets_init', array( $this, 'fb_page_register_widget' ) );

		// Shortcode
		add_shortcode( 'spri-fb-page-feed', array( $this, 'fb_page_feed_shortcode' ) );

		// Filter
		add_filter( 'query_vars', array( $this, 'url_query_filter' ) );

		// create menu pages
		new spri_fb_page_option_menu();

		// activation and deactivation hooks
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

		//cron job
		add_action( 'spri_fb_page_crawl_cron', array( $this, 'do_cron_job' ) );

		// time interval for test
		add_filter( 'cron_schedules', array( $this, 'add_custom_cron_interval' ) );

		// register style
		wp_register_style( 'spri-fb-page-auto-complete-style',
			plugins_url( '/lib/jQuery-autoComplete/jquery.auto-complete.css', __FILE__ ) );
		wp_register_style( 'spri-fb-page-shortcode-style', plugins_url( '/css/shortcode.css', __FILE__ ) );
		wp_register_style( 'spri-fb-page-widget-style', plugins_url( '/css/widget.css', __FILE__ ) );
		wp_register_style( 'spri-fb-page-widget-slider-style',
			plugins_url( '/lib/owl-carousel/owl.carousel.css', __FILE__ ) );

		//	register script
		wp_register_script( 'spri-fb-page-shortcode-script',
			plugins_url( '/js/shortcode.js', __FILE__ ),
			array( 'jquery' ) );
		wp_register_script( 'spri-fb-page-auto-complete-script',
			plugins_url( '/lib/jQuery-autoComplete/jquery.auto-complete.min.js', __FILE__ ),
			array( 'jquery' ) );

		//	register ajax
		add_action( 'wp_ajax_spri_fb_page_tag_list', array( $this, 'get_tag_list' ) );
		add_action( 'wp_ajax_nopriv_spri_fb_page_tag_list', array( $this, 'get_tag_list' ) );
	}

	public function get_tag_list() {
		global $wpdb;

		$page_id = $_REQUEST['page_id'];

		//TODO preparing query

		$sql = <<<SQL
SELECT DISTINCT post_tag
FROM {$this->post_tag_table}
WHERE post_id in (SELECT post_id from {$this->post_date_table} WHERE page_id = '{$page_id}')
SQL;

		$raw_tag_list = $wpdb->get_results( $sql, ARRAY_N );

		$tag_list = array_map( function ( $t ) {
			return $t[0];
		},
			$raw_tag_list );

		echo json_encode( $tag_list );

		wp_die();
	}

	function register_widget_scripts() {
		wp_enqueue_script(
			'spri-fb-page-widget-slide-script',
			plugins_url( '/js/slide.js', __FILE__ ),
			array( 'jquery' )
		);

		wp_enqueue_script(
			'spri-fb-page-owl-slider-script',
			plugins_url( '/lib/owl-carousel/owl.carousel.min.js', __FILE__ ),
			array( 'jquery' )
		);

		wp_enqueue_style( 'spri-fb-page-widget-style' );
		wp_enqueue_style( 'spri-fb-page-widget-slider-style' );
	}

	function fb_page_register_widget() {
		register_widget( 'spri_fb_page_widget' );
	}

	/**
	 * @param $attr
	 *
	 * @return string
	 */
	function fb_page_feed_shortcode( $attr ) {
		global $wpdb;
		global $wp_query;

		// Set default value
		$attr = shortcode_atts( array(
			'page_id'  => 'spribook',
			'number'   => 6,
			'template' => 'basic'
		),
			$attr );

		// load style sheet
		wp_enqueue_style( 'spri-fb-page-shortcode-style' );
		wp_enqueue_style( 'spri-fb-page-auto-complete-style' );

		// load script
		wp_enqueue_script( 'spri-fb-page-shortcode-script' );
		wp_enqueue_script( 'spri-fb-page-auto-complete-script' );

		// get tag from url
		if ( isset( $wp_query->query_vars['t'] ) ) {
			$t = $wp_query->query_vars['t'];
		} else {
			$t = "";
		}

		// get page number from url
		if ( isset( $wp_query->query_vars['pn'] ) ) {
			$pn = (int) $wp_query->query_vars['pn'] - 1;
		} else {
			$pn = 0;
		}


		$page_id = $attr['page_id'];
		$n       = $attr['number'];

		//TODO 새거면 포스트 크롤링 및 태그 추출 바로 실행
		$r = $this->insert_page_id_if_new( $page_id );

		// initialize html snippet
		$html = "";

		$html .= $this->generate_tag_filter_html( $page_id );

		$raw_post_list   = $this->get_posts_by_page_id_and_tag( $page_id, $t );
		$paged_post_list = array_slice( $raw_post_list, $pn * $n, $n );
		$post_list       = $this->combine_tags_into_posts( $paged_post_list );

		// It could be done with map.
		// TODO get post content at front with ajax
		$post_list = $this->get_posts_content( $post_list );


		foreach ( $post_list as $post ) {
			//if(isset($post->message)){

			$html .= $this->generate_html_frag( $post, $attr['template'] );
			//}
		}

		$tp = (int) count( $raw_post_list ) / $n;
		$html .= $this->generate_paging_navi_html_frag( $t, $pn, $tp );

		return $html;
	}


	/**
	 * @param $page_id
	 */
	protected function insert_page_id_if_new( $page_id ) {
		global $wpdb;

		$sql = <<<SQL
INSERT IGNORE INTO {$this->target_page_table} (page_id) VALUE ('{$page_id}')
SQL;
		$r   = $wpdb->query( $sql );

		return $r;
	}


	private function generate_tag_filter_html( $page_id ) {
		$html = <<<HTML
<form id="tag_filter_form" class="pull-right">
    <label class="pull-left" for="tag_filter_input"> # 검색 </label>
    <input class="pull-left" type="hidden" id="page_filter_input" name="page_id" value="{$page_id}">
    <input class="pull-left" type="text" id="tag_filter_input" name="t">
    <button class="pull-left" type="submit">검색</button>
</form>

<div class="clear-both"></div>
<hr/>
HTML;


		return $html;
	}


	private function get_posts_by_page_id_and_tag( $page_id, $t = "" ) {
		global $wpdb;

		// TODO preparing query
		$sql = <<<SQL
SELECT *
FROM {$this->post_date_table} WHERE page_id = '{$page_id}'
SQL;

		if ( $t != "" ) {
			$sql .= <<<SQL
 AND post_id IN (select post_id FROM {$this->post_tag_table} WHERE post_tag = '{$t}')
SQL;
		}

		$sql .= <<<SQL
 ORDER BY post_date DESC
SQL;

		$post_list = $wpdb->get_results( $sql );

		return $post_list;
	}

	private function combine_tags_into_posts( $raw_post_list ) {
		$post_and_tag_list = array();

		//get tags and conbine into post obj
		foreach ( $raw_post_list as $post ) {
			$tag_list            = $this->get_tags_by_post_id( $post->post_id );
			$post->tags          = $tag_list;
			$post_and_tag_list[] = $post;
		}

		return $post_and_tag_list;

	}

	private function get_tags_by_post_id( $post_id ) {
		global $wpdb;

		$tag_list = $wpdb->get_results( "
			SELECT post_tag FROM {$this->post_tag_table} WHERE post_id = '{$post_id}'
		" );

		$r_tag_list = array();

		foreach ( $tag_list as $tag ) {
			$r_tag_list[] = $tag->post_tag;
		}

		return $r_tag_list;
	}

	/**
	 * get post contents
	 *
	 * @param $post_list
	 *
	 * @return mixed
	 */
	private function get_posts_content( $post_list ) {

		foreach ( $post_list as $post ) {
			$post_content  = $this->fb->api( $post->post_id . "?fields=picture,message,story", "GET" );
			$post->message = $post_content['message'];
			$post->story   = $post_content['story'];
			if ( isset( $post_content['picture'] ) ) {
				$post->picture = $post_content['picture'];
			}
		}


		return $post_list;
	}

	private function generate_html_frag( $post, $template ) {

		$html = "<div class='post'>";

		require( "template/" . $template . ".php" );

		$html .= "</div>";

		return $html;

	}

	private function generate_paging_navi_html_frag( $t, $pn, $tp ) {

		$html = "<div class='page_nav'>";

		foreach ( range( 1, $tp + 1 ) as $i ) {
			$html .= "<a class='nav_link' href='?t=$t&pn=$i'>{$i}</a>";
		}

		$html .= "</div>";

		return $html;
	}

	/**
	 * Crawl the page's post into DB
	 * After that, analysis post's tag
	 * */
	function do_cron_job() {
		global $wpdb;

		$page_list = $wpdb->get_results( "SELECT page_id FROM {$this->target_page_table}" );

		$this->update_posts_of_page( $page_list );

		$all_post_list = $wpdb->get_results( "SELECT * FROM {$this->post_date_table} ORDER BY post_date DESC " );

		$this->extract_tags_of_post( $all_post_list );

	}

	/**
	 * @param $page_list
	 * @param $fb
	 * @param $wpdb
	 */
	protected function update_posts_of_page( $page_list ) {
		global $wpdb;

		// crawling page's posts into db
		foreach ( $page_list as $page ) {

			$post_list = $this->get_next_posts_from_paging( $page );

			//insert posts into db
			foreach ( $post_list as $post ) {
				$wpdb->insert( $this->post_date_table,
					array(
						"page_id"   => $page->page_id,
						"post_id"   => (string) $post['id'],
						"post_date" => $post['created_time']
					),
					array(
						'%s',
						'%s',
						'%s'
					) );
			}
		}
	}

	/**
	 * @param $page
	 *
	 * @return array
	 */
	protected function get_next_posts_from_paging( $page ) {


		//initialize post list
		$post_list = array();

		$tmp_list = $this->fb->api( "/{$page->page_id}?fields=posts{picture,message,created_time}", "GET" );
		$tmp_list = $tmp_list['posts'];

		//get all of post from page
		while ( count( $tmp_list['data'] ) ) {
			foreach ( $tmp_list['data'] as $post ) {
				$post_list[] = $post;
			}
			$tmp_list = json_decode( file_get_contents( $tmp_list['paging']['next'] . "&access_token=" . $this->fb->getAccessToken() ),
				true );

		}

		return $post_list;
	}

	function extract_tags_of_post( $all_post_list ) {
		global $wpdb;

		foreach ( $all_post_list as $post ) {
			$post_content = $this->fb->api( $post->post_id, "GET" );
			$pat          = "/#([가-힣a-zA-Z]+)/";
			preg_match_all( $pat, $post_content['message'], $tags );

			foreach ( $tags[1] as $tag ) {
				$wpdb->query( "
				INSERT IGNORE INTO {$this->post_tag_table} (post_id, post_tag) VALUE ('{$post->post_id}', '{$tag}' )
				" );
			}

		}
	}

	function activation() {
		$this->setup_database();
		$this->cron_register();
	}

	function setup_database() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql1 = <<<SQL1
CREATE TABLE {$this->post_date_table} (
post_id char(40) not null,
page_id char(40) not null,
post_date timestamp not null default current_timestamp,
primary key (post_id),
index (post_id),
index (page_id),
index (post_date),
index (page_id, post_id),
index (post_id, post_date)

) $charset_collate;

SQL1;

		$sql2 = <<<SQL2
CREATE TABLE {$this->post_tag_table} (
post_id char(40) not null,
post_tag varchar(100) not null,
index (post_id),
index (post_tag),
unique (post_id, post_tag),
index (post_id, post_tag)
) $charset_collate;

SQL2;

		$sql3 = <<<SQL3
CREATE TABLE {$this->target_page_table} (
id int(11) not null auto_increment,
page_id char(40) not null,
INDEX (id),
primary key (page_id),
index (page_id)
) $charset_collate;
SQL3;


		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql1 );
		dbDelta( $sql2 );
		dbDelta( $sql3 );

	}

	function cron_register() {
		wp_schedule_event( time(), 'hourly', 'spri_fb_page_crawl_cron' );
		//wp_schedule_event( time(), 'daily', 'spri_fb_page_crawl_cron' );
	}

	function deactivation() {
		$this->cron_clear();
	}

	function cron_clear() {
		wp_clear_scheduled_hook( 'spri_fb_page_crawl_cron' );
	}

	function add_custom_cron_interval( $schedules ) {
		// $schedules stores all recurrence schedules within WordPress
		$schedules['ten_seconds'] = array(
			'interval' => 10,  // Number of seconds, 600 in 10 minutes
			'display'  => 'Once Every 10 seconds'
		);

		// Return our newly added schedule to be merged into the others
		return (array) $schedules;
	}

	function url_query_filter( $q ) {
		$q[] = 't';
		$q[] = 'pn';

		return $q;
	}

	private function html_pre( $obj ) {
		$html = "<pre>";
		$html .= print_r( $obj, true );
		$html .= "</pre>";

		return $html;
	}

}

// We are away.
new spri_fb_page();