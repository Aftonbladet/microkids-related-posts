<?php
class WP_Widget_Related_Posts extends WP_Widget {

    /**
     * WP_Widget_Related_Posts - Constructor function 
     */
	function WP_Widget_Related_Posts() {
		$widget_ops = array('classname' => 'widget_related_posts', 'description' => __( 'Display related posts as a widget', 'microkids-related-posts' ) );
		parent::__construct('related_posts', __('Related Posts', 'microkids-related-posts'), $widget_ops);
	}
	
	/**
	 * widget - Standard function called to display widget contents
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
 	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget( $args, $instance ) {
		extract( $args );
		if( is_single() || is_page() ) {
		    global $post;
			$post_type = ( $instance['post_type'] == 'all' ) ? null : $instance['post_type'];
			$related_posts = MRP_get_related_posts( $post->ID, 0, 0, $post_type );
            if( $related_posts ) {
	            echo $before_widget;
				echo $before_title;
				echo $instance['title'];
				echo $after_title;
				echo "<ul>\n";
                foreach( $related_posts as $related_post_id => $related_post_title  ) {
					echo "<li>";
					if( $instance['show_thumbnail'] && has_post_thumbnail($related_post_id)) {
    					echo "<a href=\"".get_permalink( $related_post_id )."\">";
    					echo get_the_post_thumbnail($related_post_id, $instance['thumbnail_size']);
    					echo "</a>";
    				}
					echo "<a href=\"".get_permalink( $related_post_id )."\">".$related_post_title."</a>";
					echo "</li>\n";
				}
                echo "</ul>";
                echo $after_widget;
			}
			else {
				if(!$instance['hide_if_empty']) {
					echo $before_widget;
					echo $before_title;	
					echo $instance['title'];
					echo $after_title;
					echo "<p>".$instance['text_if_empty']."</p>\n";
					echo $after_widget;
				}
			}
		}
	}
	
	/**
	 * update - Save the settings for the widgets
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
 	 * @param array $old_instance Old settings for this instance
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = esc_attr($new_instance['title']);
		$instance['post_type'] = esc_attr($new_instance['post_type']);
		$instance['hide_if_empty'] = (int) $new_instance['hide_if_empty'];
		$instance['text_if_empty'] = esc_attr($new_instance['text_if_empty']);
		$instance['show_thumbnail'] = (int) $new_instance['show_thumbnail'];
		$instance['thumbnail_size'] = esc_attr($new_instance['thumbnail_size']);
		return $instance;
	}
	
	/**
	 * form - Create the form for the widget
	 *
	 * @param array $instance Current settings
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('Related posts', 'microkids-related-posts'), 'hide_if_empty' => '0', 'text_if_empty' => __('None', 'microkids-related-posts'), 'post_type' => 'all' ) );
		$title = esc_attr( $instance['title'] );
		$hide_if_empty = esc_attr( $instance['hide_if_empty'] );
		$text_if_empty = esc_attr( $instance['text_if_empty'] );
		$display_post_type = esc_attr( $instance['post_type'] );
		$custom_post_types = get_post_types( array( '_builtin' => false ) , 'object' );
		$show_thumbnail = (int) $instance['show_thumbnail'];
		$thumbnail_size = esc_attr($instance['thumbnail_size']);
        ?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('post_type') ?>"><?php _e( 'Post type to display:', 'microkids-related-posts' ); ?></label>
		<p>
		    <select name="<?php echo $this->get_field_name('post_type') ?>" id="<?php echo $this->get_field_id('post_type') ?>">
		        <option value="all"<?php if( $display_post_type == 'all' ) : ?> selected="selected"<?php endif; ?>><?php _e('All', 'microkids-related-posts') ?></option>
		        <option value="post"<?php if( $display_post_type == 'post' ) : ?> selected="selected"<?php endif; ?>><?php _e('Posts', 'microkids-related-posts') ?></option>
		        <option value="page"<?php if( $display_post_type == 'page' ) : ?> selected="selected"<?php endif; ?>><?php _e('Pages', 'microkids-related-posts') ?></option>
		        <?php if( $custom_post_types ) : foreach( $custom_post_types as $post_type ) : ?>
		        <option value="<?php echo $post_type->name; ?>"<?php if( $display_post_type == $post_type->name ) : ?> selected="selected"<?php endif; ?>><?php echo $post_type->label ?></option>
		        <?php endforeach; endif; ?>
		    </select>
		</p>
		<p><label for="<?php echo $this->get_field_id('hide_if_empty') ?>-1"><?php _e('If there are no related posts:', 'microkids-related-posts' ); ?></label></p>
		<p><input type="radio" <?php checked( $hide_if_empty, '1' ); ?> name="<?php echo $this->get_field_name('hide_if_empty') ?>" value="1" id="<?php echo $this->get_field_id('hide_if_empty') ?>-1" /> <label for="<?php echo $this->get_field_id('hide_if_empty') ?>-1"><?php _e( 'Hide the entire widget', 'microkids-related-posts' ); ?></label></p>
		<p><input type="radio" <?php checked( $hide_if_empty, '0' ); ?> name="<?php echo $this->get_field_name('hide_if_empty') ?>" value="0" id="<?php echo $this->get_field_id('hide_if_empty') ?>-2" /> <label for="<?php echo $this->get_field_id('hide_if_empty') ?>-2"><?php _e( 'Show this text:', 'microkids-related-posts' ); ?></label></p>
		<input class="widefat" id="<?php echo $this->get_field_id('text_if_empty') ?>" name="<?php echo $this->get_field_name('text_if_empty') ?>" type="text" value="<?php echo $text_if_empty; ?>" /></p>
		<p><?php _e('Display thumbnail with related posts:') ?></p>
		<p><input type="checkbox" <?php checked( $show_thumbnail, '1' ); ?> name="<?php echo $this->get_field_name('show_thumbnail') ?>" value="1" id="<?php echo $this->get_field_id('show_thumbnail') ?>" class="MRP_widget_thumbnail_checkbox" /> <label for="<?php echo $this->get_field_id('show_thumbnail') ?>"><?php _e( 'Show thumbnail', 'microkids-related-posts' ); ?></label></p>
		<p><?php _e('Thumbnail size:') ?></p>
	    <p>
		    <select name="<?php echo $this->get_field_name('thumbnail_size') ?>" id="<?php echo $this->get_field_id('thumbnail_size') ?>">
		        <?php global $_wp_additional_image_sizes; foreach ($_wp_additional_image_sizes as $size_name => $size_attrs): ?>
		            <option value="<?php echo $size_name ?>"<?php if($thumbnail_size == $size_name) : ?>selected="selected"<?php endif ?>><?php echo $size_name ?></option>
		        <?php endforeach; ?>
		    </select>
		</p>
<?php			
	}
}
?>
