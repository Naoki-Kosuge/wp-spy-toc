<?php
/*
Plugin Name:	Spy Table of Contents
Plugin URI: 	https://github.com/Naoki-Kosuge/wp-spy-toc
Description: 	This is a plugin that automatically creates a table of contents. It spies content scrolls and highlights the headings displayed.
Author: 		Naoki Kosuge
Author URI: 	https://libra-nk.net/
Text Domain:	spy-table-of-contents
Domain Path:	/languages
Version: 		0.1
License:		GPLv2 or later
*/

/*  Copyright 2019  Naoki Kosuge <naokikosuge.ict@gmail.com>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2 or later, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Easy Table of Contents; if not, see <http://www.gnu.org/licenses/>.
*/

/**
 * FIXME スムーズスクロールの時におそらくHTMLの<body>にスムーズ属性を追加する処理が必要かも
 * Introduction of Spy-style TOC into WordPress
 *
 * @link      https://github.com/Naoki-Kosuge/wp-spy-toc
 * @version   0.1
 * @author    Naoki Kosuge <naokikosuge.ict@gmail.com>
 * @copyright ©︎2019 Naoki Kosuge
 * @license   http://opensource.org/licenses/gpl-2.0.php GPLv2, or later
 * @package   Spy_Table_of_Contents
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const
SPY_TOC_TEXT_DOMAIN                   = 'spy-table-of-contents',
SPY_TOC_VERSION                       = '0.1';
const
SPY_TOC_POSITION_BEFORE_FIRST_HEADING = 1,
SPY_TOC_POSITION_TOP                  = 2,
SPY_TOC_POSITION_BOTTOM               = 3,
SPY_TOC_POSITION_AFTER_FIRST_HEADING  = 4;
const
SPY_TOC_MIN_START                     = 2,
SPY_TOC_MAX_START                     = 10;
const
SPY_TOC_SMOOTH_SCROLL_OFFSET          = 30;
const
SPY_TOC_WRAPPING_NONE                 = 0,
SPY_TOC_WRAPPING_LEFT                 = 1,
SPY_TOC_WRAPPING_RIGHT                = 2;
const
SPY_TOC_THEME_GREY                    = 1,
SPY_TOC_THEME_LIGHT_BLUE              = 2,
SPY_TOC_THEME_WHITE                   = 3,
SPY_TOC_THEME_BLACK                   = 4,
SPY_TOC_THEME_TRANSPARENT             = 99,
SPY_TOC_THEME_CUSTOM                  = 100;
const
SPY_TOC_DEFAULT_BACKGROUND_COLOUR     = '#f9f9f9',
SPY_TOC_DEFAULT_BORDER_COLOUR         = '#aaaaaa',
SPY_TOC_DEFAULT_TITLE_COLOUR          = '#',
SPY_TOC_DEFAULT_LINKS_COLOUR          = '#',
SPY_TOC_DEFAULT_LINKS_HOVER_COLOUR    = '#',
SPY_TOC_DEFAULT_LINKS_VISITED_COLOUR  = '#';


if ( ! class_exists( 'Spy_TOC' ) ) :
	/**
	 * TODO class Spy_TOC
	 * Class Spy_TOC
	 */
	class Spy_TOC {

		/** @var string eg 'http://www.example.com/wp-content/plugins/spy-table-of-contents' */
		private $path;
		/**
		 * Properties:
		 * ~~~
		 *     int    $position                           Default SPY_TOC_POSITION_BEFORE_FIRST_HEADING.
		 *     int    $start                              Default 4.
		 *     array  $auto_insert_post_types             List of post types that automatically insert　a TOC.
		 *                                                Default only 'post'.
		 *     bool   $show_heading_text                  Default true.
		 *     array  $heading_levels                     List of heading levels to include in the TOC.
		 *                                                Accepts 1 to 6. Default all.
		 *     string $heading_text                       Default 'Contents'.
		 *     bool   $visibility                         Default true.
		 *     string $visibility_show                    Default 'show'.
		 *     string $visibility_hide                    Default 'hide'.
		 *     bool   $visibility_hide_by_default         Default false.
		 *     bool   $show_heirarchy                     Whether to show hierarchically. Default true.
		 *     bool   $ordered_list                       Whether to number. Default true.
		 *     bool   $smooth_scroll                      Whether to scroll smoothly when jumping to the heading.
		 *                                                Default false.
		 *     string $width                              'Auto' (default) or 'User defined'
		 *     string $width_custom                       Default '275'.
		 *     string $width_custom_units                 'px' (default) or '%', or 'em'
		 *     int    $wrapping                           Default SPY_TOC_WRAPPING_NONE.
		 *     string $font_size                          Default '95'.
		 *     string $font_size_units                    Default '%'.
		 *     int    $theme                              Default SPY_TOC_THEME_GREY.
		 *     string $custom_background_colour           Default SPY_TOC_DEFAULT_BACKGROUND_COLOUR.
		 *     string $custom_border_colour               Default SPY_TOC_DEFAULT_BORDER_COLOUR.
		 *     string $custom_title_colour                Default SPY_TOC_DEFAULT_TITLE_COLOUR.
		 *     string $custom_links_colour                Default SPY_TOC_DEFAULT_LINKS_COLOUR.
		 *     string $custom_links_hover_colour          Default SPY_TOC_DEFAULT_LINKS_HOVER_COLOUR.
		 *     string $custom_links_visited_colour        Default SPY_TOC_DEFAULT_LINKS_VISITED_COLOUR.
		 *     bool   $lowercase                          Default false.
		 *     bool   $hyphenate                          Default true.
		 *     bool   $include_homepage                   Whether to include the TOC on the home page
		 *                                                If the conditions are met. Default false.
		 *     bool   $exclude_css                        Default false.
		 *     bool   $bullet_spacing                     Whether to set your own background style for items in the TOC.
		 *                                                Default false.
		 *     int    $smooth_scroll_offset               Default SPY_TOC_SMOOTH_SCROLL_OFFSET.
		 *     string $exclude                            Default ''.
		 *     string $restrict_path                      Path to restrict TOC generation.
		 *                                                This path is from the root of your site and always begins '/'.
		 *                                              　Default ''.
		 *     bool   $show_toc_in_widget_only            Whether the TOC is displayed only in the widget. Default false,
		 *     array  $show_toc_in_widget_only_post_types List of post types for showing table of contents in widget only.
		 *                                                Default only 'post'.
		 *     string $css_container_class                Default ''.
		 * ~~~
		 */
		private $options;
		/** @var bool Allows to override the display (eg through [no_toc] shortcode) */
		private $show_toc;
		/** @var string[] */
		private $exclude_post_types;
		/** @var array Keeps a track of used anchors for collision detecting */
		private $collision_collector;

		function __construct() {
			$this->path                = plugins_url( '', __FILE__ );
			$this->show_toc            = true;
			$this->exclude_post_types  = [
				'attachment',
				'revision',
				'nav_menu_item',
				'safe_css',
			];
			$this->collision_collector = [];  // format: `array('<url>' => int)`

			/** @var array $defaults Default options */
			$defaults = [
				'position'                           => SPY_TOC_POSITION_AFTER_FIRST_HEADING,
				'start'                              => 4,
				'auto_insert_post_types'             => [ 'post' ],
				'show_heading_text'                  => true,
				'heading_levels'                     => [ '1', '2', '3', '4', '5', '6' ],
				'heading_text'                       => 'Contents',
				'visibility'                         => true,
				'visibility_show'                    => 'show',
				'visibility_hide'                    => 'hide',
				'visibility_hide_by_default'         => false,
				'show_heirarchy'                     => true,
				'ordered_list'                       => true,
				'smooth_scroll'                      => false,
				// Appearance
				'width'                              => 'Auto',
				'width_custom'                       => '275',
				'width_custom_units'                 => 'px',
				'wrapping'                           => SPY_TOC_WRAPPING_NONE,
				'font_size'                          => '95',
				'font_size_units'                    => '%',
				'theme'                              => SPY_TOC_THEME_GREY,
				'custom_background_colour'           => SPY_TOC_DEFAULT_BACKGROUND_COLOUR,
				'custom_border_colour'               => SPY_TOC_DEFAULT_BORDER_COLOUR,
				'custom_title_colour'                => SPY_TOC_DEFAULT_TITLE_COLOUR,
				'custom_links_colour'                => SPY_TOC_DEFAULT_LINKS_COLOUR,
				'custom_links_hover_colour'          => SPY_TOC_DEFAULT_LINKS_HOVER_COLOUR,
				'custom_links_visited_colour'        => SPY_TOC_DEFAULT_LINKS_VISITED_COLOUR,
				// Advanced
				'lowercase'                          => false,
				'hyphenate'                          => true,
				'include_homepage'                   => false,
				'exclude_css'                        => false,
				'bullet_spacing'                     => false,
				'smooth_scroll_offset'               => SPY_TOC_SMOOTH_SCROLL_OFFSET,
				'exclude'                            => '',
				'restrict_path'                      => '',
				// Widget
				'show_toc_in_widget_only'            => false,
				'show_toc_in_widget_only_post_types' => [ 'post' ],
				// Short code
				'css_container_class'                => '',
			];
			/** @var array $options SpyTOC options */
			$options       = get_option( 'spy-toc-options', $defaults );
			$this->options = wp_parse_args( $options, $defaults );

			add_action( 'plugins_loaded', [ &$this, 'plugins_loaded' ] );
			add_action( 'wp_enqueue_scripts', [ &$this, 'wp_enqueue_scripts' ] );
			add_action( 'wp_head', [ &$this, 'wp_head' ] );
			add_action( 'admin_init', [ &$this, 'admin_init' ] );
			add_action( 'admin_menu', [ &$this, 'admin_menu' ] );
			add_action( 'widgets_init', [ &$this, 'widgets_init' ] );
			add_action( 'sidebar_admin_setup', [ &$this, 'sidebar_admin_setup' ] );

			add_filter( 'the_content', [ &$this, 'the_content' ], 200 );  // run after shortcodes are interpretted
			// (level 10)
			add_filter( 'plugin_action_links', [ &$this, 'plugin_action_links' ], 10, 2 );
			add_filter( 'widget_text', 'do_short_code' );

			add_shortcode( 'toc', [ &$this, 'shortcode_toc' ] );
			add_shortcode( 'no_toc', [ &$this, 'shortcode_no_toc' ] );
		}


		function __destruct() {
		}


		/*
		+---------------+
        | PUBLIC METHOD |
        +---------------+
        */

		/**
		 * Getter for `SpyTOC::$options`
		 *
		 * This method does not query the database.
		 *
		 * @return array Spy TOC options
		 */
		public function get_options() {
			return $this->options;
		}

		/**
		 * Setter for `SpyTOC::$options`
		 *
		 * This method does not update the database.
		 *
		 * @param array $options Spy TOC options
		 */
		public function set_options( $options ) {
			$this->options = array_merge( $this->options, $options );
		}

		/**
		 * Getter for `SpyTOC::$exclude_post_types`
		 *
		 * @return string[] Exclude post types
		 */
		public function get_exclude_post_types() {
			return $this->exclude_post_types;
		}

		/**
		 * Update `SpyTOC::$options['show_toc_in_widget_only']` with boolean `$value`.
		 *
		 * This will update the DB options table.
		 *
		 * @param mixed $value Optional. Default false.
		 */
		public function update_show_toc_in_widget_only( $value = false ) {
			if ( $value ) {
				$this->options['show_toc_in_widget_only'] = true;
			} else {
				$this->options['show_toc_in_widget_only'] = false;
			}

			update_option( 'spy-toc-options', $this->options );

		}

		/**
		 * Update `SpyTOC::$options['show_toc_in_widget_only_post_types']`.
		 *
		 * This will update the DB options table.
		 *
		 * @param array $value List of post types. Default empty array.
		 */
		public function update_show_toc_in_widget_only_post_types( $value = [] ) {
			if ( $value ) {
				$this->options['show_toc_in_widget_only_post_types'] = $value;
			} else {
				$this->options['show_toc_in_widget_only_post_types'] = [];
			}

			update_option( 'spy-toc-options', $this->options );
		}

		/**
		 * Returns true if the table of contents is eligible to be printed, false otherwise.
		 *
		 * @global WP_Post $post           The post object for the current post.
		 *
		 * @param bool     $shortcode_used True if short code is used in the content. Default false.
		 *
		 * @return bool Whether it is suitable for printing the table of contents
		 * @used-by the_content()
		 */
		public function is_eligible( $shortcode_used = false ) {
			global $post;

			// do not trigger the TOC when displaying an XML/RSS feed
			if ( is_feed() ) {
				return false;
			}

			// if the shortcode was used, this bypasses many of the global options
			if ( $shortcode_used !== false ) {
				// shortcode is used, make sure it adheres to the exclude from
				// homepage option if we're on the homepage
				if ( ! $this->options['include_homepage'] && is_front_page() ) {
					return false;
				} else {
					return true;
				}
			} elseif (
				( in_array( get_post_type( $post ),
						$this->options['auto_insert_post_types'] ) && ! is_search() && is_archive() && ! is_front_page() )
				|| ( $this->options['include_homepage'] && is_front_page() )
			) {
				if ( $this->options['restrict_path'] ) {
					if ( strpos( $_SERVER['REQUEST_URI'],
							$this->options['request_path'] ) === 0 ) {
						return true;
					} else {
						return false;
					}
				} else {
					return true;
				}
			} else {
				return false;
			}
		}

		/**
		 * Extracts headings from the html formatted $content.
		 *
		 * @param array  &$find    Raw headings extracted from $content are added in order.
		 * @param array  &$replace Headings extracted from $content are added in order with the appropriate ID attribute.
		 * @param string  $content HTML content to create a table of contents
		 *
		 * @return string <p>
		 * Heading &lt;li&gt; element with proper anchor.</p><p>
		 * It may be flat or nested, depending on the value of <code>$options['show_heirarchy']</code>.</p><p>
		 * In any case this should be enclosed in &lt;ul&gt; later on.</p>
		 * @used-by the_content(), Spy_TOC_Widget::widget(),
		 * @uses    url_anchor_target()
		 */
		public function extract_headings(
			&$find,
			&$replace,
			$content = ''
		) {
			/**
			 * @var array <pre>
			 * After pattern matching, $matches is:
			 *   $matches[$i][0] - H tag whole (eg '&lt;h3&gt;Lorem Ipsum&lt;/h3&gt;')
			 *   $matches[$i][1] - 1st groupe (eg '&lt;h3&gt;')
			 *   $matches[$i][2] - 2nd groupe (eg '3')
			 * </pre>
			 */
			$matches = [];
			/** @var string List of anchored headings */
			$items = false;

			// reset the internal collision collection as the_content may have been triggered elsewhere
			// eg by themes or other plugins that need to read in content such as metadata fields in
			// the head html tag, or to provide descriptions to twitter/facebook
			$this->collision_collector = [];

			if ( is_array( $find ) && is_array( $replace ) && $content ) {
				// get all headings
				// the html spec allows for a maximum of 6 heading depths
				if ( preg_match_all(
					'/(<h([1-6]{1})[^>]*>).*<\/h\2>/msuU',
					$content, $matches, PREG_SET_ORDER )
				) {

					// remove undesired headings (if any) as defined by heading_levels
					if ( count( $this->options['heading_levels'] ) != 6 ) {
						$new_matches = [];
						for ( $i = 0; $i < count( $matches ); $i ++ ) {
							if ( in_array( /*heading level*/ $matches[ $i ][2],
								$this->options['heading_levels'] )
							) {
								$new_matches[] = $matches[ $i ];
							}
						}
						$matches = $new_matches;
					}

					// remove empty headings
					$new_matches = [];
					for ( $i = 0; $i < count( $matches ); $i ++ ) {
						if ( boolval( trim( strip_tags( $matches[ $i ][0] ) ) ) ) {
							$new_matches[] = $matches[ $i ];
						}
					}
					if ( count( $matches ) != count( $new_matches ) ) {
						$matches = $new_matches;
					}

					for ( $i = 0; $i < count( $matches ); $i ++ ) {
						// get anchor and add to find and replace arrays
						/** @var string id attribute value of the heading */
						$anchor    = $this->url_anchor_target( $matches[ $i ][0] );
						$find[]    = $matches[ $i ][0];
						$replace[] = str_replace(
							[
								$matches[ $i ][1],
								// start of heading (eg '<h3>')
								'</h' . $matches[ $i ][2] . '>'
								// end of heading (eg '</h3>')
							],
							[
								$matches[ $i ][1] . '<span id="' . $anchor . '>"',
								'</span></h' . $matches[ $i ][2] . '>',
							],
							$matches[ $i ][0]
						);

						// assemble flat list
						if ( ! $this->options['show_heirarchy'] ) {
							$items .= '<li class="nav-item"><a class="nav-link" href="#' . $anchor . '">';
							if ( $this->options['ordered_list'] ) {
								$items .= '<span class="toc-number">' . count( $replace ) . '</span>';
							}
							$items .= '<span class="toc-text">' . strip_tags( $matches[ $i ][0] ) . '</span></a></li>';
						}
					}

					// build a hierarchical toc?
					// we could have tested for $items but that var can be quite large in some cases
					if ( $this->options['show_heirarchy'] ) {
						$items = $this->build_hierarchy( $matches );
					}
				}
			}

			return $items;
		}

		/**
		 * This is an action added to {@see 'plugins_loaded'}.
		 *
		 * @link https://codex.wordpress.org/Function_Reference/load_plugin_textdomain Documentation of load_plugin_textdomain().
		 */
		function plugins_loaded() {
			// load mo file
			load_plugin_textdomain( SPY_TOC_TEXT_DOMAIN, false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Load CSS and javascript files for frontend.
		 *
		 * THis is an action added to {@see 'wp_enqueue_scripts'}.
		 */
		function wp_enqueue_scripts() {

			// Bootstrap CSS
			wp_enqueue_style(
				'bootstrap-style',
				$this->path . '/css/bootstrap.min.css',
				[],
				'4.3.1'
			);

			// JQuery
			wp_enqueue_script(
				'jquery-3.4.0',
				$this->path . '/js/jquery-3.4.0.min.js',
				[],
				'3.4.0',
				false
			);

			// Popper.js
			wp_enqueue_script(
				'popper',
				$this->path . '/js/popper.min.js',
				false,
				'2018',
				false
			);

			// Bootstrap JS
			wp_enqueue_script(
				'bootstrap-script',
				'/js/bootstrap.min.js',
				[ 'jquery-3.4.0', 'popper' ],
				'4.3.1',
				false
			);

			// Our CSS
			wp_enqueue_style(
				'spy-toc-screen',
				$this->path . '/css/screen.css',
				[],
				SPY_TOC_VERSION
			);

			// Our JavaScript
			wp_enqueue_script(
				'spy-toc-front',
				'/js/front.js',
				[ 'jquery' ],
				SPY_TOC_VERSION,
				true
			);
		}

		/**
		 * TODO wp_head()
		 * This is an action added to {@see 'wp_head'}.
		 */
		function wp_head() {
			$css = '';

			if ( ! $this->options['exclude_css'] ) {
				if ( $this->options['theme'] == SPY_TOC_THEME_CUSTOM || $this->options['width'] != 'Auto' ) {
					$css .= 'div#toc_container {';
					if ( $this->options['theme'] == SPY_TOC_THEME_CUSTOM ) {
						$css .= 'background: ' . $this->options['custom_background_colour'] . ';border: 1px solid ' . $this->options['custom_border_colour'] . ';';
					}
					if ( $this->options['width'] != 'Auto' ) {
						$css .= 'width: ';
						if ( $this->options['width'] != 'User defined' ) {
							$css .= $this->options['width'];
						} else {
							$css .= $this->options['width_custom'] . $this->options['width_custom_units'];
						}
						$css .= ';';
					}
					$css .= '}';
				}

				if ( '95%' != $this->options['font_size'] . $this->options['font_size_units'] ) {
					$css .= 'div#toc_container ul li {font-size: ' . $this->options['font_size'] . $this->options['font_size_units'] . ';}';
				}

				if ( $this->options['theme'] == TOC_THEME_CUSTOM ) {
					if ( $this->options['custom_title_colour'] != TOC_DEFAULT_TITLE_COLOUR ) {
						$css .= 'div#toc_container p.toc_title {color: ' . $this->options['custom_title_colour'] . ';}';
					}
					if ( $this->options['custom_links_colour'] != TOC_DEFAULT_LINKS_COLOUR ) {
						$css .= 'div#toc_container p.toc_title a,div#toc_container ul.toc_list a {color: ' . $this->options['custom_links_colour'] . ';}';
					}
					if ( $this->options['custom_links_hover_colour'] != TOC_DEFAULT_LINKS_HOVER_COLOUR ) {
						$css .= 'div#toc_container p.toc_title a:hover,div#toc_container ul.toc_list a:hover {color: ' . $this->options['custom_links_hover_colour'] . ';}';
					}
					if ( $this->options['custom_links_hover_colour'] != TOC_DEFAULT_LINKS_HOVER_COLOUR ) {
						$css .= 'div#toc_container p.toc_title a:hover,div#toc_container ul.toc_list a:hover {color: ' . $this->options['custom_links_hover_colour'] . ';}';
					}
					if ( $this->options['custom_links_visited_colour'] != TOC_DEFAULT_LINKS_VISITED_COLOUR ) {
						$css .= 'div#toc_container p.toc_title a:visited,div#toc_container ul.toc_list a:visited {color: ' . $this->options['custom_links_visited_colour'] . ';}';
					}
				}
			}

			if ( $css ) {
				echo '<style type="text/css">' . $css . '</style>';
			}
		}

		/**
		 * This is an action added to {@see 'admin_init'}.
		 */
		function admin_init() {
			wp_register_script( 'spy_toc_admin_script', $this->path . '/js/admin.js' );
			wp_register_style( 'spy_toc_admin_style', $this->path . '/js/admin.css' );
		}

		/**
		 * FIXME admin_print_styles-(hookname)フック と admin_enqueue_scripts フック　のどちらが適切か調べる。
		 * Add sub menu page to the Settings menu.
		 *
		 * This is an action added to {@see 'admin_menu'}.
		 *
		 * @uses admin_options()
		 * @uses admin_options_head()
		 */
		function admin_menu() {
			$page = add_options_page(
				__( 'Spy TOC', SPY_TOC_TEXT_DOMAIN ),
				__( 'Spy TOC', SPY_TOC_TEXT_DOMAIN ),
				'manage_options',
				'spy-toc',
				[ &$this, 'admin_options' ]
			);

			add_action( "admin_print_styles-$page", [ &$this, 'admin_options_head' ] );
		}

		/**
		 * TODO widgets_init()
		 * This is an action added to {@see 'widgetspage_init'}.
		 */
		function widgets_init() {
			register_widget( 'Spy_TOC_Widget' );
		}

		/**
		 * TODO sidebar_admin_setup()
		 * Remove widget options on widget deletion.
		 *
		 * This is an action added to {@see 'sidebar_admin_setup'}.
		 *
		 * @uses update_show_toc_in_widget_only()
		 * @uses update_show_toc_in_widget_only_post_types()
		 */
		function sidebar_admin_setup() {
			// this action is loaded at the start of the widget screen
			// so only do the following only when a form action has been initiated
			if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] )
			     && @$_POST['id_base'] == 'spy-toc-widget'
			     && isset( $_POST['delete_widget'] )
			     && 1 === (int) $_POST['delete_widget']
			) {
				$this->update_show_toc_in_widget_only( false );
				$this->update_show_toc_in_widget_only_post_types( [ 'page' ] );
			}
		}

		/**
		 * TODO the_content()
		 * This is a filter added to {@see 'the_content'}.
		 *
		 * @global WP_Post $post
		 *
		 * @param string   $content
		 *
		 * @return mixed|string
		 * @uses extract_headings()
		 * @uses mb_find_replace()
		 * @uses is_eligible()
		 */
		function the_content( $content ) {
			global $post;
			$items               = $css_classes = $anchor = '';
			$custom_toc_position = strpos( $content, '<!--TOC-->' );
			$find                = $replace = [];

			if ( $this->is_eligible( $custom_toc_position ) ) {
				$items = $this->extract_headings( $find, $replace, $content );

				if ( $items ) {
					// do we display the toc within the content or has the user opted
					// to only show it in the widget?  if so, then we still need to
					// make the find/replace call to insert the anchors
					if ( $this->options['show_toc_in_widget_only']
					     && ( in_array( get_post_type(), $this->options['show_toc_in_widget_only_post_types'] ) )
					) {
						$content = $this->mb_find_replace( $find, $replace, $content );
					} else {

						// wrapping css classes
						switch ( $this->options['wrapping'] ) {
							case TOC_WRAPPING_LEFT:
								$css_classes .= ' toc_wrap_left';
								break;

							case TOC_WRAPPING_RIGHT:
								$css_classes .= ' toc_wrap_right';
								break;

							case TOC_WRAPPING_NONE:
							default:
								// do nothing
						}

						// colour themes
						switch ( $this->options['theme'] ) {
							case TOC_THEME_LIGHT_BLUE:
								$css_classes .= ' toc_light_blue';
								break;

							case TOC_THEME_WHITE:
								$css_classes .= ' toc_white';
								break;

							case TOC_THEME_BLACK:
								$css_classes .= ' toc_black';
								break;

							case TOC_THEME_TRANSPARENT:
								$css_classes .= ' toc_transparent';
								break;

							case TOC_THEME_GREY:
							default:
								// do nothing
						}

						// bullets?
						if ( $this->options['bullet_spacing'] ) {
							$css_classes .= ' have_bullets';
						} else {
							$css_classes .= ' no_bullets';
						}

						if ( $this->options['css_container_class'] ) {
							$css_classes .= ' ' . $this->options['css_container_class'];
						}

						$css_classes = trim( $css_classes );

						// an empty class="" is invalid markup!
						if ( ! $css_classes ) {
							$css_classes = ' ';
						}

						// add container, toc title and list items
						$html = '<div id="toc_container" class="' . $css_classes . '">';
						if ( $this->options['show_heading_text'] ) {
							$toc_title = $this->options['heading_text'];
							if ( strpos( $toc_title, '%PAGE_TITLE%' ) !== false ) {
								$toc_title = str_replace( '%PAGE_TITLE%', get_the_title(), $toc_title );
							}
							if ( strpos( $toc_title, '%PAGE_NAME%' ) !== false ) {
								$toc_title = str_replace( '%PAGE_NAME%', get_the_title(), $toc_title );
							}
							$html .= '<p class="toc_title">' . htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' ) . '</p>';
						}
						$html .= '<ul class="toc_list">' . $items . '</ul></div>' . "\n";

						if ( $custom_toc_position !== false ) {
							$find[]    = '<!--TOC-->';
							$replace[] = $html;
							$content   = $this->mb_find_replace( $find, $replace, $content );
						} else {
							if ( count( $find ) > 0 ) {
								switch ( $this->options['position'] ) {
									case TOC_POSITION_TOP:
										$content = $html . $this->mb_find_replace( $find, $replace, $content );
										break;

									case TOC_POSITION_BOTTOM:
										$content = $this->mb_find_replace( $find, $replace, $content ) . $html;
										break;

									case TOC_POSITION_AFTER_FIRST_HEADING:
										$replace[0] = $replace[0] . $html;
										$content    = $this->mb_find_replace( $find, $replace, $content );
										break;

									case TOC_POSITION_BEFORE_FIRST_HEADING:
									default:
										$replace[0] = $html . $replace[0];
										$content    = $this->mb_find_replace( $find, $replace, $content );
								}
							}
						}
					}
				}
			} else {
				// remove <!--TOC--> (inserted from shortcode) from content
				$content = str_replace( '<!--TOC-->', '', $content );
			}

			return $content;
		}

		/**
		 * TODO plugin_action_links()
		 *
		 * @param array  $links
		 * @param string $file
		 *
		 * @return array list of links to display on the plugins page (beside the activate/deactivate links)
		 */
		function plugin_action_links( $links, $file ) {
			if ( $file == 'spy-table-of-contents/' . basename( __FILE__ ) ) {
				$settings_link = '<a href="options-general.php?page=spy-toc">'
				                 . __( 'Settings', SPY_TOC_TEXT_DOMAIN )
				                 . '</a>';
				$links         = array_merge( [ $settings_link ], $links );
			}

			return $links;
		}

		/**
		 * TODO shortcode_toc()
		 * Corresponds to the short code [toc].
		 *
		 * @param array $atts
		 *
		 * @return string|void
		 */
		function shortcode_toc( $atts ) {
			/**
			 * Overwrite default with attributes.
			 *
			 * @var string $label
			 * @var string $label_show
			 * @var string $label_hide
			 * @var bool   $no_label
			 * @var bool   $class
			 * @var int    $wrapping
			 * @var array  $heading_levels
			 * @var string $exclude
			 * @var bool   $collapse
			 */
			extract( shortcode_atts( [
				'label'          => $this->options['heading_text'],
				'label_show'     => $this->options['visibility_show'],
				'label_hide'     => $this->options['visibility_hide'],
				'no_label'       => false,
				'class'          => false,
				'wrapping'       => $this->options['wrapping'],
				'heading_levels' => $this->options['heading_levels'],
				'collapse'       => false,
			], $atts ) );

			$re_enqueue_scripts = false;

			if ( $no_label ) {
				$this->options['show_heading_text'] = false;
			}
			if ( $label ) {
				$this->options['heading_text'] = html_entity_decode( $label );
			}
			if ( $label_show ) {
				$this->options['visibility_show'] = html_entity_decode( $label_show );
				$re_enqueue_scripts               = true;
			}
			if ( $label_hide ) {
				$this->options['visibility_hide'] = html_entity_decode( $label_hide );
				$re_enqueue_scripts               = true;
			}
			if ( $class ) {
				$this->options['css_container_class'] = $class;
			}
			if ( $wrapping ) {
				switch ( strtolower( trim( $wrapping ) ) ) {
					case 'left':
						$this->options['wrapping'] = SPY_TOC_WRAPPING_LEFT;
						break;

					case 'right':
						$this->options['wrapping'] = SPY_TOC_WRAPPING_RIGHT;
						break;

					default:
						// do nothing
				}
			}

			if ( $exclude ) {
				$this->options['exclude'] = $exclude;
			}
			if ( $collapse ) {
				$this->options['visibility_hide_default'] = true;
				$re_enqueue_scripts                       = true;
			}

			if ( $re_enqueue_scripts ) {
				do_action( 'wp_enqueue_scripts' );
			}

			/* if $heading_levels is an array, then it came from the global options and wasn't provided by per instance */
			if ( $heading_levels && ! is_array( $heading_levels ) ) {
				/** @var string $heading_levels */
				// make sure they are numbers between 1 and 6 and put into
				// the $clean_heading_levels array if not already
				$clean_heading_levels = [];
				foreach ( explode( ',', $heading_levels ) as $heading_level ) {
					if (
						is_numeric( $heading_level )
						&& ( 1 <= $heading_level && $heading_level <= 6 )
						&& ! in_array( $heading_level, $clean_heading_levels )
					) {
						$clean_heading_levels[] = $heading_level;
					}
				}

				if ( count( $clean_heading_levels ) > 0 ) {
					$this->options['heading_levels'] = $clean_heading_levels;
				}
			}

			if ( ! is_search() && ! is_archive() && ! is_feed() ) {
				return '<!--TOC-->';
			} else {
				return;
			}
		}

		/**
		 * TODO shortcode_no_toc()
		 * Corresponds to the short code [no_toc].
		 *
		 * @return void
		 */
		function shortcode_no_toc() {
			$this->show_toc = false;

			return;
		}


		/*
		+----------------+
		| PRIVATE METHOD |
		+----------------+
		*/

		/**
		 * TODO admin_options()
		 *
		 * @used-by admin_menu()
		 * @uses    save_admin_options()
		 */
		function admin_options() {
			$msg = '';

			if ( isset( $_GET['update'] ) ) {
				if ( $this->save_admin_options() ) {
					$msg = '<div id="message" class="update fade"><p>'
					       . __( 'Save failed.', SPY_TOC_TEXT_DOMAIN )
					       . '</p></div>';
				} else {
					$msg = '<div id="message" class="error fade"><p>'
					       . __( 'Save failed', SPY_TOC_TEXT_DOMAIN )
					       . '</p></div>';
				}
			}
			?>
            <div id="toc" class="wrap">
                <div id="icon-options-general" class="icon32"><br></div>
                <h2>Spy Table of Contents</h2>
				<?php echo $msg; ?>
                <form method="post"
                      action="<?php echo htmlentities( '?page=' . $_GET['page'] . '&update' ); ?>">
					<?php wp_nonce_field( plugin_basename( __FILE__ ),
						'spy-toc-admin-options' ); ?>

                    <ul id="tabbed-nav">
                        <li><a href="#tab1"><?php _e( 'Main Options',
									SPY_TOC_TEXT_DOMAIN ); ?></a></li>
                    </ul><!-- /#tabbed-nav -->

                    <div class="tab-container">

                        <div id="tab1" class="tab_content">
                            <table class="form-table">
                                <tbody>
								<?php
								// Position
								?>
                                <tr>
                                    <th>
                                        <label for="position"><?php _e( 'Position',
												SPY_TOC_TEXT_DOMAIN ); ?></label>
                                    </th>
                                    <td>
                                        <select name="position" id="position">
                                            <option
                                                    value="<?php echo SPY_TOC_POSITION_BEFORE_FIRST_HEADING; ?>"
												<?php if ( SPY_TOC_POSITION_BEFORE_FIRST_HEADING == $this->options['position'] ) {
													echo ' selected="selected"';
												} ?>>
												<?php _e( 'Before first heading (default)',
													SPY_TOC_TEXT_DOMAIN ); ?>
                                            </option>
                                            <option
                                                    value="<?php echo SPY_TOC_POSITION_AFTER_FIRST_HEADING; ?>"
												<?php if ( SPY_TOC_POSITION_AFTER_FIRST_HEADING == $this->options['position'] ) {
													echo ' selected="selected"';
												} ?>>
												<?php _e( 'After first heading',
													SPY_TOC_TEXT_DOMAIN ); ?>
                                            </option>
                                            <option
                                                    value="<?php echo SPY_TOC_POSITION_TOP; ?>"
												<?php if ( SPY_TOC_POSITION_TOP == $this->options['position'] ) {
													echo ' selected="selected"';
												} ?>>
												<?php _e( 'Top',
													SPY_TOC_TEXT_DOMAIN ); ?>
                                            </option>
                                            <option
                                                    value="<?php echo SPY_TOC_POSITION_BOTTOM; ?>"
												<?php if ( SPY_TOC_POSITION_BOTTOM == $this->options['position'] ) {
													echo ' selected="selected"';
												} ?>>
												<?php _e( 'Bottom',
													SPY_TOC_TEXT_DOMAIN ); ?>
                                            </option>
                                        </select><!-- /#position -->
                                    </td>
                                </tr>
								<?php
								//Show when
								?>
                                <tr>
                                    <th>
                                        <label for="start"><<?php _e( 'Show when',
												SPY_TOC_TEXT_DOMAIN ); ?></label>
                                    </th>
                                    <td>
                                        <select name="start" id="start">
											<?php
											for ( $i = SPY_TOC_MIN_START; $i <= SPY_TOC_MAX_START; $i ++ ) {
												echo '<option value="' . $i . '"';
												if ( $i == $this->options['start'] ) {
													echo ' selected="selected"';
												}
												echo '>' . $i . '</option>' . "\n";
											}
											?>
                                        </select><!-- /#start -->
										<?php _e( 'Auto insert for the following content types' ); ?>
                                    </td>
                                </tr>
								<?php
								// Auto insert for the following content types
								?>
                                <tr>
                                    <th><?php _e( 'Auto insert for the following content types',
											SPY_TOC_TEXT_DOMAIN ); ?></th>
                                    <td><?php
										foreach ( get_post_types() as $post_type ) {
											// make sure the post type isn't on the exclusion list
											if ( ! in_array( $post_type,
												$this->exclude_post_types ) ) {
												echo '<input type="checkbopx" value="' . $post_type . '" id="auto-insert-post-types-' . $post_type . '" name="auto-insert-post-types';
												if ( in_array( $post_type,
													$this->options['auto_insert_post_types'] ) ) {
													echo ' checked';
												}
												echo '>';
												echo '<label for="auto-insert-post-types-' . $post_type . '">' . $post_type . '</label><br>';
											}
										}
										?></td>
                                </tr>
								<?php
								// Heading text
								?>
                                <tr>
                                    <th><label for="show-heading-text"><?php
											/* translators: this is the title of the table of contents */
											_e( 'Heading text',
												SPY_TOC_TEXT_DOMAIN );
											?></label></th>
                                    <td>
										<?php
										// Show title on top of the table of contents
										?>
                                        <input
                                                type="checkbox" value="1"
                                                id="show-heading-text"
                                                name="show-heading-text"
											<?php if ( $this->options['show_heading_text'] ) {
												echo ' checked';
											} ?>>
                                        <label for="show-heading-text">
											<?php _e( 'Show title on top of the table of contents',
												SPY_TOC_TEXT_DOMAIN ); ?>
                                        </label><br>
                                        <div class="more-toc-options<?php if ( ! $this->options['show_heading_text'] ) {
											echo ' disabled';
										} ?>">
											<?php
											// Eg: Contents, Table of Contents, Page Contents
											?>
                                            <input type="text"
                                                   class="regular-text"
                                                   value="<?php echo htmlentities( $this->options['heading_text'],
												       ENT_COMPAT,
												       'UTF-8' ); ?>"
                                                   id="heading-text"
                                                   name="heading-text">
                                            <span class="description">
														<label for="heading-text">
															<?php _e( 'Eg: Contents, Table of Contents, Page Contents',
																SPY_TOC_TEXT_DOMAIN ); ?>
														</label>
													</span>
                                            <!-- /.description -->
                                            <br> <br>
											<?php
											// Allow the user to toggle the visibility of the table of contents
											?>
                                            <input type="checkbox" value="1"
                                                   id="1"
                                                   name="visibility"<?php if ( $this->options['visibility'] ) {
												echo ' checked';
											} ?>>
                                            <label for="visibility">
												<?php _e( 'Allow the user to toggle the visibility of the table of contents',
													SPY_TOC_TEXT_DOMAIN ); ?>
                                            </label><br>

                                            <div class="more-toc-options<?php if ( ! $this->options['visibility'] ) {
												echo ' disabled';
											} ?>">
                                                <table class="more-toc-options-table">
                                                    <tbody>
													<?php
													// Show text
													?>
                                                    <tr>
                                                        <th>
                                                            <label for="visibility-show"><?php _e( 'Show text',
																	SPY_TOC_TEXT_DOMAIN ); ?></label>
                                                        </th>
                                                        <td>
                                                            <input type="text"
                                                                   class=""
                                                                   value="<?php echo htmlentities( $this->options['visibility-show'],
																       ENT_COMPAT,
																       'UTF-8' ); ?>"
                                                                   id="visibility-show"
                                                                   name="visibility-show">
                                                            <span class="description">
																			<label for="visibility-show"><?php /* translators: example text to display when you want to expand the  table of contents */
																				_e( 'Eg: show',
																					SPY_TOC_TEXT_DOMAIN ); ?></label>
																		</span>
                                                            <!-- /.description -->
                                                        </td>
                                                    </tr>
													<?php
													// Hide text
													?>
                                                    <tr>
                                                        <th>
                                                            <label for="visibility-hide"><?php _e( 'Hide text',
																	SPY_TOC_TEXT_DOMAIN ); ?></label>
                                                        </th>
                                                        <td><input type="text"
                                                                   class=""
                                                                   value="<?php echo htmlentities( $this->options['visibility-hide'],
															           ENT_COMPAT,
															           'UTF-8' ); ?>"
                                                                   id="visibility-hide"
                                                                   name="visibility-hide">
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <!-- .more-toc-options-table -->
												<?php
												// Hide the table
												?>
                                                <input type="checkbox" value="1"
                                                       id="visibility-hide-by-default"
                                                       name="visibility-hide-by-default"<?php if ( $this->options['visibility_hide_by_default'] ) {
													echo ' checked';
												} ?>>
                                                <label for="visibility-hide-by-default"><?php _e( 'Hide the table of contents ionitially',
														SPY_TOC_TEXT_DOMAIN ); ?></label>
                                            </div><!-- /.more-toc-options -->
                                        </div><!-- /.more-toc-options -->
                                    </td>
                                </tr>
								<?php
								// Show hierarchy
								?>
                                <tr>
                                    <th>
                                        <label for="show-hierarchy"><?php _e( 'Show hierarchy',
												SPY_TOC_TEXT_DOMAIN ); ?></label>
                                    </th>
                                    <td><input type="checkbox" value="1"
                                               id="show-hierarchy"
                                               name="show-hierarchy"<?php if ( $this->options['show-hierarchy'] ) {
											echo ' checked';
										} ?>></td>
                                </tr>
								<?php
								// Number list items
								?>
                                <tr>
                                    <th>
                                        <label for="ordered-list"><?php _e( 'Number list items',
												SPY_TOC_TEXT_DOMAIN ); ?></label>
                                    </th>
                                    <td><input type="checkbox" value="1"
                                               id="ordered-list"
                                               name="ordered-list"<?php if ( $this->options['ordered_list'] ) {
											echo ' checked';
										} ?>></td>
                                </tr>
								<?php
								// Scroll rather than jump to the anchor link
								?>
                                <tr>
                                    <th>
                                        <label for="smooth-scroll"><?php _e( 'Enable smooth scroll effect',
												SPY_TOC_TEXT_DOMAIN ); ?></label>
                                    </th>
                                    <td>
                                        <input type="checkbox" value="1"
                                               id="smooth-scroll"
                                               name="smooth-scroll" <?php if ( $this->options['smooth_scroll'] ) {
											echo 'checked';
										} ?>>
                                        <label for="smooth-scroll"><?php _e( 'Scroll rather than jump to the anchor link',
												SPY_TOC_TEXT_DOMAIN ); ?></label>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

							<?php
							// Appearance
							?>
                            <h3><?php _e( 'Appearance',
									SPY_TOC_TEXT_DOMAIN ); ?></h3>
                            <table class="form-table">
                                <tbody>
								<?php
								// width
								?>
                                <tr>
                                    <th><label for="with"><?php _e( 'width',
												SPY_TOC_TEXT_DOMAIN ); ?></label>
                                    </th>
                                    <td>
                                        <select name="width" id="width">
                                            <optgroup
                                                    label="<?php _e( 'Fixed width',
														SPY_TOC_TEXT_DOMAIN ); ?>">
                                                <option value="200px"<?php if ( '200px' == $this->options['width'] ) {
													echo ' selected="selected"';
												} ?>>200px
                                                </option>
                                                <option value="225px"<?php if ( '225px' == $this->options['width'] ) {
													echo ' selected="selected"';
												} ?>>225px
                                                </option>
                                                <option value="250px"<?php if ( '250px' == $this->options['width'] ) {
													echo ' selected="selected"';
												} ?>>250px
                                                </option>
                                                <option value="275px"<?php if ( '275px' == $this->options['width'] ) {
													echo ' selected="selected"';
												} ?>>275px
                                                </option>
                                                <option value="300px"<?php if ( '300px' == $this->options['width'] ) {
													echo ' selected="selected"';
												} ?>>300px
                                                </option>
                                                <option value="325px"<?php if ( '325px' == $this->options['width'] ) {
													echo ' selected="selected"';
												} ?>>325px
                                                </option>
                                                <option value="350px"<?php if ( '350px' == $this->options['width'] ) {
													echo ' selected="selected"';
												} ?>>350px
                                                </option>
                                                <option value="375px"<?php if ( '375px' == $this->options['width'] ) {
													echo ' selected="selected"';
												} ?>>375px
                                                </option>
                                                <option value="400px"<?php if ( '400px' == $this->options['width'] ) {
													echo ' selected="selected"';
												} ?>>400px
                                                </option>
                                            </optgroup>
                                            <optgroup
                                                    label="<?php _e( 'Relative',
														SPY_TOC_TEXT_DOMAIN ); ?>">
                                                <option value="Auto"<?php if ( 'Auto' == $this->options['width'] )
													echo ' selected="selected"' ?>><?php _e( 'Auto (default)',
														SPY_TOC_TEXT_DOMAIN ); ?></option>
                                                <option value="25%"<?php if ( '25%' == $this->options['width'] )
													echo ' selected="selected"' ?>>
                                                    25%
                                                </option>
                                                <option value="33%"<?php if ( '33%' == $this->options['width'] )
													echo ' selected="selected"' ?>>
                                                    33%
                                                </option>
                                                <option value="50%"<?php if ( '50%' == $this->options['width'] )
													echo ' selected="selected"' ?>>
                                                    50%
                                                </option>
                                                <option value="66%"<?php if ( '66%' == $this->options['width'] )
													echo ' selected="selected"' ?>>
                                                    66%
                                                </option>
                                                <option value="75%"<?php if ( '75%' == $this->options['width'] )
													echo ' selected="selected"' ?>>
                                                    75%
                                                </option>
                                                <option value="100%"<?php if ( '100%' == $this->options['width'] )
													echo ' selected="selected"' ?>>
                                                    100%
                                                </option>
                                            </optgroup>
                                            <optgroup label="<?php
											/* translators: other width */
											_e( 'Other',
												SPY_TOC_TEXT_DOMAIN ); ?>">
                                                <option value="User defined"<?php if ( 'User defined' == $this->options['width'] ) {
													echo ' selected="selected"';
												} ?>>
													<?php _e( 'User defined', SPY_TOC_TEXT_DOMAIN ); ?>
                                                </option>
                                            </optgroup>
                                        </select><!-- /#widh -->
                                        <div class="more-toc-options<?php if ( 'User defined' != $this->options['width'] ) {
											echo ' disabled';
										} ?>">
                                            <label for="width-custom"><?php
												/* translators: ignore %s as it's some HTML label tags */
												printf( __( 'Please enter a number and %s select its units, eg: 100px, 10em',
													SPY_TOC_TEXT_DOMAIN ),
													'</label><label for="width-custom-units">' );
												?></label><br>
                                            <input type="text"
                                                   class="regular-text"
                                                   value="<?php echo floatval( $this->options['width-custom'] ); ?>"
                                                   id="width-custom"
                                                   name="width-custom">
                                            <select name="width-custom-units"
                                                    id="width-custom-units">
                                                <option value="px"<?php if ( 'px' == $this->options['width-custom-units'] ) {
													echo ' selected="selected"';
												} ?>>px
                                                </option>
                                                <option value="%"<?php if ( '%' == $this->options['width-custom-units'] ) {
													echo ' selected="selected"';
												} ?>>%
                                                </option>
                                                <option value="em"<?php if ( 'em' == $this->options['width-custom-units'] ) {
													echo ' selected="selected"';
												} ?>>em
                                                </option>
                                            </select>
                                            <!-- /#width-custom-units -->
                                        </div><!-- /.more-toc-options -->
                                    </td>
                                </tr>
								<?php
								// Wrapping
								?>
                                <tr>
                                    <th>
                                        <label for="wrapping"><?php _e( 'Wrapping', SPY_TOC_TEXT_DOMAIN ); ?></label>
                                    </th>
                                    <td>
                                        <select name="wrapping" id="wrapping">
                                            <option value="<?php echo SPY_TOC_WRAPPING_NONE; ?>"<?php if ( SPY_TOC_WRAPPING_NONE == $this->options['wrapping'] ) {
												echo ' selected="selected"';
											} ?>>
												<?php _e( 'None (default', SPY_TOC_TEXT_DOMAIN ); ?>
                                            </option>
                                            <option value="<?php echo SPY_TOC_WRAPPING_LEFT; ?>"<?php if ( SPY_TOC_WRAPPING_LEFT == $this->options['wrapping'] ) {
												echo 'selected="selected"';
											} ?>>
												<?php _e( 'Left', SPY_TOC_TEXT_DOMAIN ); ?>
                                            </option>
                                            <option value="<?php echo SPY_TOC_WRAPPING_RIGHT; ?>"<?php if ( SPY_TOC_WRAPPING_RIGHT == $this->options['wrapping'] ) {
												echo ' selected="selected"';
											} ?>>
												<?php _e( 'Right', SPY_TOC_TEXT_DOMAIN ); ?>
                                            </option>
                                        </select><!-- /#wrapping -->
                                    </td>
                                </tr>
								<?php
								// Font size
								?>
                                <tr>
                                    <th>
                                        <label for="font-size"><?php _e( 'Font-size', SPY_TOC_TEXT_DOMAIN ); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" class="regular-text"
                                               value="<?php echo floatval( $this->options['font_size'] ); ?>"
                                               id="font-size" name="font-size">
                                        <select name="font-size-units"
                                                id="font-size-units">
                                            <option value="pt"<?php if ( 'pt' == $this->options['font-size-units'] ) {
												echo ' selected="selected"';
											} ?>>pt
                                            </option>
                                            <option value="%"<?php if ( '%' == $this->options['font_size_units'] ) {
												echo ' selected="selected"';
											} ?>>%
                                            </option>
                                            <option value="em"<?php if ( 'em' == $this->options['font_size_units'] ) {
												echo ' selected="selected"';
											} ?>>em
                                            </option>
                                        </select>
                                    </td>
                                </tr>
								<?php
								// Presentation
								?>
                                <tr>
                                    <th><?php
										/* translators: appearance / colour / look and feel options */
										_e( 'Presentation',
											SPY_TOC_TEXT_DOMAIN );
										?></th>
                                    <td>
                                        <div class="toc-theme-option">
                                            <input type="radio" name="theme"
                                                   id="theme-<?php echo SPY_TOC_THEME_GREY; ?>"
                                                   value="<?php echo SPY_TOC_THEME_GREY; ?>"<?php if ( SPY_TOC_THEME_GREY == $this->options['theme'] ) {
												echo ' checked';
											} ?>>
                                            <label for="theme-<?php echo SPY_TOC_THEME_GREY; ?>">
												<?php _e( 'Grey (default)', SPY_TOC_TEXT_DOMAIN ); ?>
                                                <br>
                                                <img src="<?php echo $this->path; ?>/images/grey.png"
                                                     alt="">
                                            </label>
                                        </div><!-- /.toc-theme-option -->
                                        <div class="toc-theme-option">
                                            <input type="radio" name="theme"
                                                   id="theme-<?php echo SPY_TOC_THEME_LIGHT_BLUE; ?>"
                                                   value="<?php echo SPY_TOC_THEME_LIGHT_BLUE; ?>"<?php if ( SPY_TOC_THEME_LIGHT_BLUE == $this->options['theme'] ) {
												echo ' checked';
											} ?>>
                                            <label for="theme-<?php echo SPY_TOC_THEME_LIGHT_BLUE; ?>">
												<?php _e( 'Light blue',
													SPY_TOC_TEXT_DOMAIN ); ?>
                                                <br>
                                                <img src="<?php echo $this->path; ?>/images/blue.png"
                                                     alt="">
                                            </label>
                                        </div><!-- /.toc-theme-option -->
                                        <div class="toc-theme-option">
                                            <input type="radio" name="theme"
                                                   id="theme-<?php echo SPY_TOC_THEME_WHITE; ?>"
                                                   value="<?php echo SPY_TOC_THEME_WHITE; ?>"<?php if ( SPY_TOC_THEME_WHITE == $this->options['theme'] ) {
												echo ' checked';
											} ?>>
                                            <label for="theme-<?php echo SPY_TOC_THEME_WHITE; ?>">
												<?php _e( 'White',
													SPY_TOC_TEXT_DOMAIN ); ?>
                                                <br>
                                                <img src="<?php echo $this->path; ?>/images/white.png"
                                                     alt="">
                                            </label>
                                        </div><!-- /.toc-theme-option -->
                                        <div class="toc-theme-option">
                                            <input type="radio" name="theme"
                                                   id="theme-<?php echo SPY_TOC_THEME_BLACK; ?>"
                                                   value="<?php echo SPY_TOC_THEME_BLACK; ?>"<?php if ( SPY_TOC_THEME_BLACK == $this->options['theme'] ) {
												echo ' checked';
											} ?>>
                                            <label for="theme-<?php echo SPY_TOC_THEME_BLACK; ?>">
												<?php _e( 'Black',
													SPY_TOC_TEXT_DOMAIN ); ?>
                                                <br>
                                                <img src="<?php echo $this->path; ?>/images/black.png"
                                                     alt="">
                                            </label>
                                        </div><!-- /.toc-theme-option -->
                                        <div class="toc-theme-option">
                                            <input type="radio" name="theme"
                                                   id="theme-<?php echo SPY_TOC_THEME_TRANSPARENT; ?>"
                                                   value="<?php echo SPY_TOC_THEME_TRANSPARENT; ?>"<?php if ( SPY_TOC_THEME_TRANSPARENT == $this->options['theme'] ) {
												echo ' checked';
											} ?>>
                                            <label for="theme-<?php echo SPY_TOC_THEME_TRANSPARENT; ?>">
												<?php _e( 'Transparent',
													SPY_TOC_TEXT_DOMAIN ); ?>
                                                <br>
                                                <img src="<?php echo $this->path; ?>/images/transparent.png"
                                                     alt="">
                                            </label>
                                        </div><!-- /.toc-theme-option -->
                                        <div class="toc-theme-option">
                                            <input type="radio" name="theme"
                                                   id="theme-<?php echo SPY_TOC_THEME_CUSTOM; ?>"
                                                   value="<?php echo SPY_TOC_THEME_CUSTOM; ?>"<?php if ( SPY_TOC_THEME_CUSTOM == $this->options['theme'] ) {
												echo ' checked';
											} ?>>
                                            <label for="theme-<?php echo SPY_TOC_THEME_CUSTOM; ?>">
												<?php _e( 'Custom',
													SPY_TOC_TEXT_DOMAIN ); ?>
                                                <br>
                                                <img src="<?php echo $this->path; ?>/images/custom.png"
                                                     alt="">
                                            </label>
                                        </div><!-- /.toc-theme-option -->
                                        <div class="clear"></div>

                                        <div class="more-toc-options<?php if ( SPY_TOC_THEME_CUSTOM != $this->options['theme'] ) {
											echo ' disabled';
										} ?>">
                                            <table id="theme-custom"
                                                   class="more-toc-options-table">
                                                <tbody>
                                                <tr>
                                                    <th>
                                                        <label for="custom-background-color"><?php _e( 'Background',
																SPY_TOC_TEXT_DOMAIN ); ?></label>
                                                    </th>
                                                    <td><input type="text"
                                                               class="custom-color-option"
                                                               value="<?php echo htmlentities( $this->options['custom-background-color'] ); ?>"
                                                               id="custom-background-color"
                                                               name="custom-background-color"><img
                                                                src="<?php echo $this->path; ?>/images/color-wheel.png"
                                                                alt=""></td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        <label for="custom-border-color"><?php _e( 'Border',
																SPY_TOC_TEXT_DOMAIN ); ?></label>
                                                    </th>
                                                    <td><input type="text"
                                                               class="custom-color-option"
                                                               value="<?php echo htmlentities( $this->options['custom-border-color'] ); ?>"
                                                               id="custom-border-color"
                                                               name="custom-border-color"><img
                                                                src="<?php echo $this->path; ?>/images/color-wheel.png"
                                                                alt=""></td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        <label for="custom-title-color"><?php _e( 'Title',
																SPY_TOC_TEXT_DOMAIN ); ?></label>
                                                    </th>
                                                    <td><input type="text"
                                                               class="custom-color-option"
                                                               value="<?php echo htmlentities( $this->options['custom-title-color'] ); ?>"
                                                               id="custom-title-color"
                                                               name="custom-title-color"><img
                                                                src="<?php echo $this->path; ?>/images/color-wheel.png"
                                                                alt=""></td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        <label for="custom-links-color"><?php _e( 'Links',
																SPY_TOC_TEXT_DOMAIN ); ?></label>
                                                    </th>
                                                    <td><input type="texr"
                                                               class="custom-color-option"
                                                               value="<?php echo htmlentities( $this->options['custom_links_color'] ); ?>"
                                                               id="custom-links-color"
                                                               name="custom-links-color"><img
                                                                src="<?php echo $this->path; ?>/iamges/color-wheel.png"
                                                                alt=""></td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        <label for="custom-links-hover-color"><?php _e( 'Links (hover)',
																SPY_TOC_TEXT_DOMAIN ); ?></label>
                                                    </th>
                                                    <td><input type="text"
                                                               class="custom-color-option"
                                                               value="<?php echo htmlentities( $this->options['custom_links_hover_color'] ) ?>"
                                                               id="custom-links-hover-color"
                                                               name="custom-links-hover-color"><img
                                                                src="<?php echo $this->path; ?>/images/color-wheel.png"
                                                                alt=""></td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        <label for="custom-links-visited-color"><?php _e( 'Links (visited)',
																SPY_TOC_TEXT_DOMAIN ); ?></label>
                                                    </th>
                                                    <td><input type="text"
                                                               class="custom-color-option"
                                                               value="<?php echo htmlentities( $this->options['custom_links_visited_color'] ) ?>"
                                                               id="custom-links-visited-color"
                                                               name="custom-links-visited-color"><img
                                                                src="<?php echo $this->path; ?>/images/color-wheel.png"
                                                                alt=""></td>
                                                </tr>
                                                </tbody>
                                            </table><!-- /#theme-custom -->
                                            <div id="farbtastic-color-wheel"></div>
                                            <div class="clear"></div>
                                            <p><?php printf( __( "Leaving the value as %s will inherit your theme's styles",
													SPY_TOC_TEXT_DOMAIN ),
													'<code>#</code>' ); ?></p>
                                        </div><!-- /.more-toc-options -->
                                    </td>
                                </tr>
                                </tbody>
                            </table><!-- /.form-table -->

							<?php
							// Advanced
							?>
                            <h3><?php _e( 'Advanced',
									SPY_TOC_TEXT_DOMAIN ) ?><span
                                        class="show-hide">(<a
                                            href="#toc-advanced-usage"><?php _e( 'show',
											SPY_TOC_TEXT_DOMAIN ); ?></a>)</span>
                            </h3>
                            <div id="toc-advanced-usage">
                                <h4><?php _e( 'Power options',
										SPY_TOC_TEXT_DOMAIN ); ?></h4>
                                <table class="form-table">
                                    <tbody>
									<?php
									// Lowercase
									?>
                                    <tr>
                                        <th>
                                            <label for="lowercase"><?php _e( 'Lowercase',
													'spy-table-of-contents' ); ?></label>
                                        </th>
                                        <td><input type="checkbox" value="1"
                                                   id="lowercase"
                                                   name="lowercase"<?php if ( $this->options['lowercase'] ) {
												echo ' checked';
											} ?>><label
                                                    for="lowercase"><?php _e( 'Ensure anchors are in lowercase',
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </td>
                                    </tr>
									<?php
									// Hyphenate
									?>
                                    <tr>
                                        <th>
                                            <label for="hyphenate"><?php _e( 'Hyphenate',
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </th>
                                        <td><input type="checkbox" value="1"
                                                   id="hyphenate"
                                                   name="hyphenate"<?php if ( $this->options['hyphenate'] ) {
												echo ' checked';
											} ?>><label
                                                    for="hyphenate"><?php _e( 'Use - rather then _ in anchors',
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </td>
                                    </tr>
									<?php
									// Include homepage
									?>
                                    <tr>
                                        <th>
                                            <label for="include-homepage"><?php _e( 'Include homepage',
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </th>
                                        <td><input type="checkbox" value="1"
                                                   id="include-homepage"
                                                   name="include-homepage"<?php if ( $this->options['include_homepage'] ) {
												echo ' checked';
											} ?>><label
                                                    for="include-homepage"><?php _e( 'Show the table of contents for qualifying items on the homepage',
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </td>
                                    </tr>
									<?php /* Exclude CSS file */ ?>
                                    <tr>
                                        <th>
                                            <label for="exclude-css"><?php _e( 'Exclude CSS file',
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </th>
                                        <td><input type="checkbox" value="1"
                                                   id="exclude-css"
                                                   name="exclude-css"<?php if ( $this->options['exclude_css'] ) {
												echo ' checked';
											} ?>><label
                                                    for="exclude-css"><?php _e( "Prevent the loading of this plugin's CSS styles. When selected, the appearance options from above will also be ignored.",
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </td>
                                    </tr>
									<?php
									// Preserve theme bullets
									?>
                                    <tr>
                                        <th>
                                            <label for="bullet-spacing"><?php _e( 'Preserve theme bullets',
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </th>
                                        <td><input type="checkbox" value="1"
                                                   id="bullet-spacing"
                                                   name="bullet-spacing"<?php if ( $this->options['bullet_spacing'] ) {
												echo ' checked';
											} ?>><label
                                                    for="bullet-spacing"><?php _e( 'If your theme includes background images for unordered list elements, enable this to support them',
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </td>
                                    </tr>

									<?php
									// Heading levels
									?>
                                    <tr>
                                        <th><?php _e( 'Heading levels',
												SPY_TOC_TEXT_DOMAIN ); ?></th>
                                        <td>
                                            <p><?php _e( 'Include the following heading levels. Deselecting a heading will exclude it.',
													SPY_TOC_TEXT_DOMAIN ); ?></p>
											<?php
											// show heading 1 to 6 options
											for ( $i = 1; $i <= 6; $i ++ ) {
												echo '<input type="checkbox" value="' . $i . '" id="heading-levels"' . $i . '" name="heading-levels"';
												if ( in_array( $i,
													$this->options['heading_levels'] ) ) {
													echo ' checked';
												}
												echo ' >';
												echo '<label for="heading-levels' . $i . '">' . __( 'heading',
														SPY_TOC_TEXT_DOMAIN ) . $i . ' - h' . $i . '</label><br>';
											}
											?>
                                        </td>
                                    </tr>

									<?php
									// Exclude heading
									?>
                                    <tr>
                                        <th>
                                            <label for="exclude"><?php _e( 'Exclude heading',
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </th>
                                        <td>
                                            <input type="text"
                                                   class="regular-text"
                                                   value="<?php echo htmlentities( $this->options['exclude'],
												       ENT_COMPAT,
												       'UTF-8' ); ?>"
                                                   id="exclude" name="exclude"
                                                   style="width: 100%;"><br>
                                            <label for="exclude"><?php _e( 'Specify headings to be excluded from appearing in the table of contents.  Separate multiple headings with a pipe <code>|</code>.  Use an asterisk <code>*</code> as a wildcard to match other text.  Note that this is not case sensitive. Some examples:',
													SPY_TOC_TEXT_DOMAIN ); ?></label><br>
                                            <ul>
                                                <li><?php _e( '<code>Fruit*</code> ignore headings starting with "Fruit"',
														SPY_TOC_TEXT_DOMAIN ); ?></li>
                                                <li><?php _e( '<code>*Fruit Diet*</code> ignore headings with "Fruit Diet" somewhere in the heading',
														SPY_TOC_TEXT_DOMAIN ); ?></li>
                                                <li><?php _e( '<code>Apple Tree|Oranges|Yellow Bananas</code> ignore headings that are exactly "Apple Tree", "Oranges" or "Yellow Bananas"',
														SPY_TOC_TEXT_DOMAIN ); ?></li>
                                            </ul>
                                        </td>
                                    </tr>

									<?php
									// Smooth scroll top offset
									?>
                                    <tr id="smooth-scroll-offset-tr"
                                        class="<?php if ( ! $this->options['smooth_scroll'] ) {
										    echo ' disabled';
									    } ?>">
                                        <th>
                                            <label for="smooth-scroll-offset"><?php _e( 'Smooth scroll top offset',
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </th>
                                        <td>
                                            <input type="text"
                                                   class="regular-text"
                                                   value="<?php echo intval( $this->options['smooth_scroll_offset'] ); ?>"
                                                   id="smooth-scroll-offset"
                                                   name="smooth-scroll-offset">px<br/>
                                            <label for="smooth-scroll-offset"><?php _e( 'If you have a consistent menu across the top of your site, you can adjust the top offset to stop the headings from appearing underneath the top menu. A setting of 30 accommodates the WordPress admin bar. This setting appears after you have enabled smooth scrolling from above.',
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </td>
                                    </tr><!-- /#smooth-scroll-offset-tr -->

									<?php
									// Restrict path
									?>
                                    <tr>
                                        <th>
                                            <label for="restrict-path"><?php _e( 'Restrict path',
													SPY_TOC_TEXT_DOMAIN ); ?></label>
                                        </th>
                                        <td>
                                            <input type="text"
                                                   class="regular-text"
                                                   value="<?php echo htmlentities( $this->options['restrict_path'],
												       ENT_COMPAT,
												       'UTF-8' ); ?>"
                                                   id="restrict-path"
                                                   name="restrict-path"><br>
                                            <label for="restrict-path"><?php _e( 'Restrict generation of the table of contents to pages that match the required path. This path is from the root of your site and always begins with a forward slash.',
													SPY_TOC_TEXT_DOMAIN ); ?>
                                                <br>
                                                <span class="description">
														<?php
														/* translators: example URL path restriction */
														_e( 'Eg: /wiki/, /corporate/annual-reports/',
															SPY_TOC_TEXT_DOMAIN ); ?>
														</span>
                                            </label>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table><!-- /.form-table -->

                            </div><!-- /#toc-advanced-usage -->
                        </div><!-- /#tab1 -->
                    </div><!-- /.tab-container -->

                    <p class="submit"><input type="submit" name="submit"
                                             class="button-primary"
                                             value="<?php _e( 'Update Options',
						                         SPY_TOC_TEXT_DOMAIN ); ?>"/>
                    </p>
                </form>
            </div>
			<?php
		}

		/**
		 * TODO save_admin_options()
		 *
		 * @used-by admin_options()
		 * @global $post_id
		 * @return bool
		 * @uses    hex_value()
		 * @uses    $options
		 */
		private function save_admin_options() {
			global $post_id;

			// security check
			if ( ! wp_verify_nonce( @$_POST['spy-toc-admin-options'], plugin_basename( __FILE__ ) ) ) {
				return false;
			}

			// require an administrator level to save
			if ( ! current_user_can( 'manage_options', $post_id ) ) {
				return false;
			}

			// use stripslashes on free text fields that can have ' " \
			// WordPress automatically slashes these characters as part of
			// wp-includes/load.php::wp_magic_quotes()

			$custom_background_colour    = $this->hex_value( trim( $_POST['custom_background_colour'] ),
				SPY_TOC_DEFAULT_BACKGROUND_COLOUR );
			$custom_border_colour        = $this->hex_value( trim( $_POST['custom_border_colour'] ),
				SPY_TOC_DEFAULT_BORDER_COLOUR );
			$custom_title_colour         = $this->hex_value( trim( $_POST['custom_title_colour'] ),
				SPY_TOC_DEFAULT_TITLE_COLOUR );
			$custom_links_colour         = $this->hex_value( trim( $_POST['custom_links_colour'] ),
				SPY_TOC_DEFAULT_LINKS_COLOUR );
			$custom_links_hover_colour   = $this->hex_value( trim( $_POST['custom_links_hover_colour'] ),
				SPY_TOC_DEFAULT_LINKS_HOVER_COLOUR );
			$custom_links_visited_colour = $this->hex_value( trim( $_POST['custom_links_visited_colour'] ),
				SPY_TOC_DEFAULT_LINKS_VISITED_COLOUR );

			if ( $restrict_path = trim( $_POST['restrict_path'] ) ) {
				if ( strpos( $restrict_path, '/' ) !== 0 ) {
					// restrict path did not start with a / so unset it
					$restrict_path = '';
				}
			}

			$this->options = array_merge(
				$this->options,
				[
					'position'                    => intval( $_POST['position'] ),
					'start'                       => intval( $_POST['start'] ),
					'auto_insert_post_types'      => @(array) $_POST['auto_insert_post_types'],
					'show_heading_text'           => ( isset( $_POST['show_heading_text'] ) && $_POST['show_heading_text'] ) ? true : false,
					'heading_levels'              => @(array) $_POST['heading_levels'],
					'heading_text'                => stripslashes( trim( $_POST['heading_text'] ) ),
					'visibility'                  => ( isset( $_POST['visibility'] ) && $_POST['visibility'] ) ? true : false,
					'visibility_show'             => stripslashes( trim( $_POST['visibility_show'] ) ),
					'visibility_hide'             => stripslashes( trim( $_POST['visibility_hide'] ) ),
					'visibility_hide_by_default'  => ( isset( $_POST['visibility_hide_by_default'] ) && $_POST['visibility_hide_by_default'] ) ? true : false,
					'show_heirarchy'              => ( isset( $_POST['show_heirarchy'] ) && $_POST['show_heirarchy'] ) ? true : false,
					'ordered_list'                => ( isset( $_POST['ordered_list'] ) && $_POST['ordered_list'] ) ? true : false,
					'smooth_scroll'               => ( isset( $_POST['smooth_scroll'] ) && $_POST['smooth_scroll'] ) ? true : false,
					// Appearance
					'width'                       => trim( $_POST['width'] ),
					'width_custom'                => floatval( $_POST['width_custom'] ),
					'width_custom_units'          => trim( $_POST['width_custom_units'] ),
					'wrapping'                    => intval( $_POST['wrapping'] ),
					'font_size'                   => floatval( $_POST['font_size'] ),
					'font_size_units'             => trim( $_POST['font_size_units'] ),
					'theme'                       => intval( $_POST['theme'] ),
					'custom_background_colour'    => $custom_background_colour,
					'custom_border_colour'        => $custom_border_colour,
					'custom_title_colour'         => $custom_title_colour,
					'custom_links_colour'         => $custom_links_colour,
					'custom_links_hover_colour'   => $custom_links_hover_colour,
					'custom_links_visited_colour' => $custom_links_visited_colour,
					// Advanced
					'lowercase'                   => ( isset( $_POST['lowercase'] ) && $_POST['lowercase'] ) ? true : false,
					'hyphenate'                   => ( isset( $_POST['hyphenate'] ) && $_POST['hyphenate'] ) ? true : false,
					'include_homepage'            => ( isset( $_POST['include_homepage'] ) && $_POST['include_homepage'] ) ? true : false,
					'smooth_scroll_offset'        => intval( $_POST['smooth_scroll_offset'] ),
					'exclude_css'                 => ( isset( $_POST['exclude_css'] ) && $_POST['exclude_css'] ) ? true : false,
					'bullet_spacing'              => ( isset( $_POST['bullet_spacing'] ) && $_POST['bullet_spacing'] ) ? true : false,
					'exclude'                     => stripslashes( trim( $_POST['exclude'] ) ),
					'restrict_path'               => $restrict_path,
					// Widget
				]
			);

			// update_option will return false if no changes were made
			update_option( 'toc-options', $this->options );

			return true;
		}

		/**
		 * TODO admin_options_head()
		 *
		 * @link    https://developer.wordpress.org/reference/functions/wp_register_script/#core-registered-scripts Core-Registered Scripts
		 *
		 * @used-by admin_menu()
		 */
		private function admin_options_head() {
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'spy_toc_admin_script' );
			wp_enqueue_style( 'spy_toc_admin_style' );
		}

		/**
		 * TODO hex_value()
		 * Tries to convert $string into a valid hex colour.
		 *
		 * Returns $default if $string is not a hex value, otherwise returns verified hex.
		 *
		 * @param string $string
		 * @param string $default
		 *
		 * @return string
		 */
		private function hex_value( $string = '', $default = '#' ) {
			$return = $default;

			if ( $string ) {
				// strip out non hex chars
				$return = preg_replace( '/[^a-fA-F0-9]*/', '', $string );

				switch ( strlen( $return ) ) {
					case 3: // do next
					case 6:
						$return = '#' . $return;
						break;

					default:
						if ( strlen( $return ) > 6 ) {
							$return = '#' . substr( $return, 0, 6 );
						}  // if > 6 chars, then take the first 6
                        elseif ( strlen( $return ) > 3 && strlen( $return ) < 6 ) {
							$return = '#' . substr( $return, 0, 3 );
						}  // if between 3 and 6, then take first 3
						else {
							return $default;
						}                        // not valid, return $default
				}
			}

			return $return;
		}

		/**
		 * TODO build_hierarchy()
		 *
		 * @param array &$matches
		 *
		 * @return string Hierarchical unordered list HTML.
		 * @used-by extract_heading()
		 * @uses    $options['heading_levels']
		 * @uses    url_anchor_target()
		 */
		private function build_hierarchy( &$matches ) {
			$current_depth     = 100;  // headings can't be larger than h6 but 100 as a default to be sure
			$html              = '';
			$numberd_items     = [];
			$numberd_items_min = null;

			// reset the internal collision collection
			$this->collision_collector = [];

			// find the minimum heading to establish our baseline
			for ( $i = 0; $i < count( $matches ); $i ++ ) {
				if ( $current_depth > $matches[ $i ][2] ) {
					$current_depth = (int) $matches[ $i ][2];
				}
			}

			$numbered_items[ $current_depth ] = 0;
			$numbered_items_min               = $current_depth;

			for ( $i = 0; $i < count( $matches ); $i ++ ) {

				if ( $current_depth == (int) $matches[ $i ][2] ) {
					$html .= '<li class="nav-item">';

					// start lists
				} else {
					for ( ; $current_depth < (int) $matches[ $i ][2]; $current_depth ++ ) {
						$numbered_items[ $current_depth + 1 ] = 0;
						$html                                 .= '<ul class="nav ml-3"><li class="nav-item">';
					}
				}

				// list item
				if ( in_array( $matches[ $i ][2],
					$this->options['heading_levels'] ) ) {
					$html .= '<a class="nav-link" href="#'
					         . $this->url_anchor_target( $matches[ $i ][0] ) . '">'
					         . strip_tags( $matches[ $i ][0] ) . '</a>';
				}

				// end list
				if ( $i != count( $matches ) - 1 ) {
					if ( $current_depth > (int) $matches[ $i + 1 ][2] ) {
						for ( ; $current_depth > (int) $matches[ $i + 1 ][2]; $current_depth -- ) {
							$html .= '</li></ul>';
						}
					} elseif ( $current_depth == (int) $matches[ $i + 1 ][2] ) {
						$html .= '</li>';
					}
				} else {
					// this is the last item, make sure we close off all tags
					for ( ; $current_depth >= $numbered_items_min; $current_depth -- ) {
						$html .= '</li>';
						if ( $current_depth != $numberd_items_min ) {
							$html .= '</li>';
						}
					}
				}
			}

			return $html;
		}

		/**
		 * TODO url_anchor_target()
		 * FIXME マルチバイトの扱い、【パーセントエンコードする】OR【そのまま】
		 * Returns a clean url to be used as the destination anchor target
		 *
		 * Passing a heading to `$title` returns a url based on the text.
		 * This should be able to be treated as an HTML ID attribute value as it is.
		 *
		 * @param string $title
		 *
		 * @return  string Url to the heading that passed the 'spy_toc_url_anchor_target' filter
		 * @used-by extract_heading(), build_hierarchy()
		 */
		private function url_anchor_target( $title ) {
			$return = '';

			if ( $title ) {
				$return = trim( strip_tags( $title ) );

				// convert accented characters to ASCII
				$return = remove_accents( $return );

				// replace newlines with spaces (eg when headings are split over multiple lines)
				$return = str_replace( [ "\r\n", "\n\r", "\r", "\n" ], ' ',
					$return );

				// encode
				$return = esc_attr( $return );

				// convert spaces to _
				$return = str_replace( [ '　', ' ' ], '_', $return );

				// remove trailing - and _
				$return = rtrim( $return, '-_' );
			}

			if ( array_key_exists( $return, $this->collision_collector ) ) {
				$this->collision_collector[ $return ] ++;
				$return .= '-' . $this->collision_collector[ $return ];
			} else {
				$this->collision_collector[ $return ] = 1;
			}

			return apply_filters( 'spy_toc_url_anchor_target', $return );
		}

		/**
		 * TODO mb_find_replace()
		 * Returns a string with all items from the $find array replaced with their matching
		 * items in the $replace array.
		 *
		 * This does a one to one replacement (rather than
		 * globally).
		 * This function is multibyte safe.
		 * $find and $replace are arrays, $string is the haystack.  All variables are
		 * passed by reference.
		 *
		 * @param array|false &$find
		 * @param array|false &$replace
		 * @param string      &$string
		 *
		 * @return string
		 *
		 * @used-by the_content()
		 */
		private function mb_find_replace( &$find = false, &$replace = false, &$string = '' ) {
			if ( is_array( $find ) && is_array( $replace ) && $string ) {
				// check if multibyte strings are supported
				if ( function_exists( 'mb_strpos' ) ) {
					for ( $i = 0; $i < count( $find ); $i ++ ) {
						$string =
							mb_substr( $string, 0, mb_strpos( $string, $find[ $i ] ) ) .    // everything befor $find
							$replace[ $i ] .                                                // its replacement
							mb_substr( $string, mb_strpos( $string,
									$find[ $i ] ) + mb_strlen( $find[ $i ] ) )    // everything after $find
						;
					}
				} else {
					for ( $i = 0; $i < count( $find ); $i ++ ) {
						$string = substr_replace(
							$string,
							$replace[ $i ],
							strpos( $string, $find[ $i ] ),
							strlen( $find[ $i ] )
						);
					}
				}
			}

			return $string;
		}

	} // end class
endif;


if ( ! class_exists( 'Spy_TOC_Widget' ) ) :
	/**
	 * TODO class Spy_TOC_Widget
	 * Class Spy_TOC_Widget
	 *
	 * @see WP_Widget
	 */
	class Spy_TOC_Widget extends WP_Widget {

		/**
		 * TODO Spy_TOC_Widget::__construct()
		 * Register widget with WordPress.
		 *
		 * @see WP_Widget::__construct()
		 */
		public function __construct() {
			$id_base         = 'Spy_TOC';
			$name            = __( 'spy-toc-widget', SPY_TOC_TEXT_DOMAIN );
			$widget_options  = [
				'classname'   => 'spy-toc-widget',
				'description' => __( 'Display the spy table of contents in the sidebar with this widget',
					SPY_TOC_TEXT_DOMAIN ),
			];
			$control_options = [];

			parent::__construct( $id_base, $name, $widget_options,
				$control_options );
		}

		/**
		 * TODO Spy_TOC_Widget::widget()
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			global $spy_toc, $wp_query;
			$items = $custom_toc_position = '';
			$find  = $replace = [];

			$toc_options         = $spy_toc->get_options();
			$post                = get_post( $wp_query->post->ID );
			$custom_toc_position = strpos( $post->post_content,
				'[toc]' );  // at this point, shortcodes haven't run yet so we can't search for <!--TOC-->

			if ( $spy_toc->is_eligible( $custom_toc_position ) ) {

				/**
				 * @var string $before_widget
				 * @var string $before_title
				 * @var string $after_title
				 * @var string $after_widget
				 */
				extract( $args );

				$items = $spy_toc->extract_headings( $find, $replace,
					wptexturize( $post->post_content ) );
				$title = ( array_key_exists( 'title',
					$instance ) ) ? apply_filters( 'widget_title',
					$instance['title'] ) : '';
				if ( strpos( $title, '%PAGE_TITLE%' ) !== false ) {
					$title = str_replace( '%PAGE_TITLE%', get_the_title(),
						$title );
				}
				if ( strpos( $title, '%PAGE_NAME%' ) !== false ) {
					$title = str_replace( '%PAGE_NAME%', get_the_title(),
						$title );
				}
				$hide_inline = $toc_options['show_toc_in_widget_only'];

				$css_classes = '';
				// smooth scroll?
				if ( $toc_options['smooth_scroll'] ) {
					$css_classes .= ' smooth-scroll';
				}
				// bullets?
				if ( $toc_options['bullet_spacing'] ) {
					$css_classes .= ' have-bullets';
				} else {
					$css_classes .= ' no-bullets';
				}

				if ( $items ) {
					// before widget (defined by themes)
					echo $before_widget;

					// display the widget title if one was input (before and after titles defined by themes)
					if ( $title ) {
						echo $before_title . $title . $after_title;
					}

					// display the list
					echo '<ul class="nav nav-pills default-pills"' . $css_classes . '">' . $items . '</ul>';

					// after widget (defined by themes)
					echo $after_widget;
				}
			}
		}

		/**
		 * TODO Spy_TOC_Widget::form()
		 * Outputs the settings update form.
		 *
		 * @param array $instance
		 */
		public function form( $instance ) {
			global $spy_toc;
			$toc_options = $spy_toc->get_options();

			$defaults = [ 'title' => $toc_options['heading_text'] ];
			$instance = wp_parse_args( (array) $instance, $defaults );

			?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php _e( 'Title', SPY_TOC_TEXT_DOMAIN ); ?>:
                </label>
                <input type="text"
                       id="<?php echo $this->get_field_id( 'title' ); ?>"
                       name="<?php $this->get_field_name( 'title' ); ?>"
                       value="<?php echo $instance['title']; ?>"
                       style="width:100%;">
            </p>

            <p>
                <input class="checkbox" type="checkbox"
					<?php checked( $toc_options['show_toc_in_widget_only'],
						1 ); ?>
                       id="<?php echo $this->get_field_id( 'hide-inline' ); ?>"
                       value="1">
                <label for="<?php echo $this->get_field_id( 'hide-inline' ); ?>">
					<?php _e( 'Show the table of contents only in sidebar',
						SPY_TOC_TEXT_DOMAIN ); ?>
                </label>
            </p>

            <div class="show-toc-in-widget-only-post-types"
                 style="margin: 0 0 25px 25px; display:<?php echo ( $toc_options['show_toc_in_widget_only'] === 1 ) ? 'block' : 'none'; ?>;">
                <p><?php _e( 'For the following content tytpes:',
						SPY_TOC_TEXT_DOMAIN ); ?></p>

				<?php
				foreach ( get_post_types() as $post_type ) {
					// make sure the post type isn't on the exclusion list
					if ( ! in_array( $post_type,
						$spy_toc->get_exclude_post_types() ) ) {
						echo '<input type="checkbox" value="' . $post_type
						     . '" id="' . $this->get_field_id( 'show-toc-in-widget-only-post-types-' . $post_type )
						     . '" name="' . $this->get_field_name( "show-toc-in-widget-only-post-types" );
						if ( in_array( $post_type,
							$toc_options['show_toc_in_widget_only_post_types'] ) ) {
							echo ' checked';
						}
						echo '><label for="' . $this->get_field_id( 'show-toc-in-widget-only-post-types-' . $post_type ) . '">' . $post_type . '</label><br>';
					}
				}
				?>
            </div>

            <script type="text/javascript">
				jQuery(document).ready(function($) {
					$('#<?php echo $this->get_field_id( 'hide-inline' ); ?>').click(function() {
						$(this.parent().siblings('div.show-toc-in-widget-only-post-types').toggle('fast');
					});
				});
            </script>

			<?php
		}

		/**
		 * TODO Spy_TOC_Widget::update()
		 * Update the widget settings
		 *
		 * @param array $new_instance
		 * @param array $old_instance
		 *
		 * @return array
		 */
		public function update( $new_instance, $old_instance ) {
			global $spy_toc;
			$instance = $old_instance;

			// strip tags for title to remove HTML (important for text inputs)
			$instance['title'] = strip_tags( trim( $new_instance['title'] ) );

			// no need to strip tags for the following
			//$instance['hide_inline'] = $new_instance['hide_inline'];
			$spy_toc->update_show_toc_in_widget_only( $new_instance['hide-inline'] );
			$spy_toc->update_show_toc_in_widget_only_post_types( (array) $new_instance['show-toc-in-widget-only-post-types'] );

			return $instance;
		}

	}
endif;


$spy_toc = new Spy_TOC();
