<?php
/**
 * Plugin Name:     Cm
 * Plugin URI:      webmaker.today
 * Description:     Набор коман для WP CLI
 * Author:          zimovid
 * Author URI:      webmaker.today
 * Text Domain:     cm
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Cm
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {

	class Commands extends WP_CLI_Command {
		/**
		 *
		 * Prints a greeting.
		 *
		 * ## OPTIONS
		 *
		 * <first_category>
		 * : Первая категория для слияния.
		 *
		 * <second_category>
		 * : Вторая категория для слияния.
		 *
		 * [--delete]
		 * : Удалить категорию от котрой сливали.
		 * ---
		 * default: success
		 * options:
		 *   - success
		 *   - error
		 * ---
		 *
		 * ## EXAMPLES
		 *
		 *     wp cm merge-category categoryName1 categoryName2
		 *
		 * @when after_wp_load
		 * @subcommand merge-category
		 *
		 */
		function merge_category( $args, $assoc_args ) {
			list( $cat1, $cat2 ) = $args;

			$category1      = get_category_by_slug( $cat1 );
			$category2      = get_category_by_slug( $cat2 );
			$delete         = $assoc_args['delete'];
			$postsCategory1 = get_posts( [ 'posts_per_page' => - 1, 'category_name' => $cat1 ] );
			foreach ( $postsCategory1 as $postCat ) {
				$allPostCategories = wp_get_post_categories( $postCat->ID );
				array_push( $allPostCategories, $category2->cat_ID );
				if ( $delete ) {
					if ( ( $key = array_search( $category1->cat_ID, $allPostCategories ) ) !== false ) {
						unset( $allPostCategories[ $key ] );
					}
				}
				if ( wp_set_post_categories( $postCat->ID, $allPostCategories ) ) {
					WP_CLI::success( "Post {$postCat->ID} updated!" );
				} else {
					WP_CLI::error( "Post {$postCat->ID} not updated!" );
				}
			}
		}
	}

	WP_CLI::add_command( 'cm', 'Commands' );
}

