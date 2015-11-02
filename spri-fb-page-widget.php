<?php

class spri_fb_page_widget extends WP_Widget {

	function __construct() {
		global $wpdb;

		parent::__construct(
				'spri-fb-page-article-widget', //id
				'SPRI Facebook Page Feed Widget', //name
				array(
						'description' => 'Facebook post slide widget'
				)
		);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$page_id = trim( $instance['page_id'] );
		$n       = (int) $instance['n'];

        $fb = fb_connector::get_fb_obj();
		$post_list = $fb->api( "/{$page_id}?fields=feed", "GET" );


		$html = "<div class='owl-carousel spri-fb-page-slide widget_dock'>";
		foreach ( array_slice( $post_list['feed']['data'], 0, $n ) as $item ) {
			$html .= '<div>';

			$html .= <<<ARTICLE
    <p>
    <a href="http://facebook.com/{$item['id']}" target="_blank">

    {$item['message']}
    </a>

    </p>
ARTICLE;

			$html .= '</div>';

		}
		$html .= "</div>";

		echo $before_widget;
		echo $html;
		echo $after_widget;
	}

	function form( $instance ) {

		$n       = isset( $instance['n'] ) ? absint( $instance['n'] ) : 6;
		$page_id = isset( $instance['page_id'] ) ? $instance['page_id'] : "spribook";

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'page_id' ) ?>">Page ID</label>
			<input type="text" id="<?php echo $this->get_field_id( 'page_id' ); ?>"
				   name="<?php echo $this->get_field_name( 'page_id' ) ?>" value="<?php echo $page_id ?>"
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'n' ) ?>">Number of Posts</label>
			<input type="text" id="<?php echo $this->get_field_id( 'n' ); ?>"
				   name="<?php echo $this->get_field_name( 'n' ) ?>" value="<?php echo $n ?>" size="3">
		</p>
		<?php

	}


	function update( $new_instance, $old_instance ) {
		$instance            = $old_instance;
		$instance['n']       = $new_instance['n'];
		$instance['page_id'] = $new_instance['page_id'];

		return $instance;
	}
}