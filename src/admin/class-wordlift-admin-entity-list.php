<?php

/**
 * Customization of the entity list in wp-admin/edit.php
 * 
 * @since 3.3.0
 */

/**
 * Wordlift_Entity_List_Service Class
 *
 * Handles the edit entities views.
 */
class Wordlift_Entity_List_Service {
	
	/**
	 * Size of the entity thumbnail in pixels
	 * 
	 * @since  3.3.0
	 */
	const THUMB_SIZE = 50;

	/**
	 * Register custom columns for entity listing in backend
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/manage_posts_custom_column
	 *
	 * @since 3.3.0
	 *
	 * @param array $columns the default columns.
	 *
	 * @return array Enhanced columns array.
	 */
	public function register_custom_columns( $columns ) {
		
		// Take away first column and keep a reference,
		// so we can later insert the thumbnail between the first and the rest of columns.
		$columns_cb = $columns['cb'];
		unset( $columns['cb'] );
		
		// Thumbnails column is inserted in second place, while the related posts on the end.
		$columns = array_merge(
			array( 'cb'                      => $columns_cb ),                      // re-add first column
			array( 'wl_column_thumbnail'     => __( 'Image', 'wordlift' ) ),        // thumb
			$columns,                                                               // default columns (without the first)
			array( 'wl_column_related_posts' => __( 'Related Posts', 'wordlift' ) ) // related posts
		);

		return $columns;		
	}

	/**
	 * Render custom columns
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/manage_$post_type_posts_custom_column
	 *
	 * @since 3.3.0
	 *
	 * @param string $column the current column.
	 * @param int $entity_id An entity post id.
	 *
	 * @return true if the post is an entity otherwise false.
	 */
	public function render_custom_columns( $column, $entity_id ) {
		
		switch ( $column ) {
			
			case 'wl_column_related_posts':
				echo count( wl_core_get_related_post_ids( $entity_id ) );
				break;
			
			case 'wl_column_thumbnail':
				
				$edit_link = get_edit_post_link( $entity_id );
				$thumb     = get_the_post_thumbnail( $entity_id, array( self::THUMB_SIZE, self::THUMB_SIZE ) );
				
				if( ! $thumb ) {
					$thumb = "<img src='" . WL_DEFAULT_THUMBNAIL_PATH . "' width='" . self::THUMB_SIZE . "' />";
				}
				echo "<a href='$edit_link'>$thumb</a>";
				break;	
		}
		
	}
	
	/**
	 * Add 4W select box before the 'Filter' button.
	 *
	 * @since 3.3.0
	 */
	public function add_4W_filter() {
		global $typenow;
		
		// Only show on entity list page
		if( $typenow !== Wordlift_Entity_Service::TYPE_NAME ){
			return;
		}
		
		// Was a W already selected?
		$selected = isset( $_GET['4W_filter'] ) ? $_GET['4W_filter'] : '' ;
		
		// Build select box with the 4W
		$all_w = array(
			'Select W',
			WL_WHAT_RELATION,
			WL_WHO_RELATION,
			WL_WHERE_RELATION,
			WL_WHEN_RELATION
		);
		$output  = '<select name="4W_filter" id="dropdown_4W_type">';
		foreach ( $all_w as $w ) {
			$default  = ( $selected == $w ) ? 'selected' : ''; 
			$output  .= "<option value='$w' $default >" . ucfirst( __( $w, 'wordlift' ) ) . '</option>';
		}
		$output .= '</select>';
		
		// Print on page
		echo $output;
	}

	/**
	 * Server side response operations for the 4W filter set in *add_4W_filter*
	 *
	 * @since 3.3.0
	 * 
	 * @param array $clauses WP main query clauses.
	 * 
	 * @return array Modified clauses.
	 */
	public function add_4W_filter_query( $clauses ) {
				
		global $typenow, $wpdb;
		
		// Only apply on entity list page and if the 4W_filter query param is set
		if( ! ( $typenow == Wordlift_Entity_Service::TYPE_NAME && isset( $_GET['4W_filter'] ) ) ) {
			return $clauses;
		}
			
		// Check a valid W was requested
		$requested_w = $_GET['4W_filter'];
		if ( ! in_array( $requested_w, array( WL_WHAT_RELATION, WL_WHO_RELATION, WL_WHERE_RELATION, WL_WHEN_RELATION )) ) {
			return $clauses;
		}
		
		$w_table = wl_core_get_relation_instances_table_name();
		
		// Change WP main query clauses
		$clauses['join']     .= "INNER JOIN $w_table ON $wpdb->posts.ID = $w_table.object_id";
		$clauses['where']    .= "AND $w_table.predicate = '$requested_w'";
		$clauses['distinct'] .= "DISTINCT";
		
		return $clauses;
	}
}
