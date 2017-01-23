<?php
/**
 * Utility & helpers functions
 *
 * @author 8guild
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! function_exists( 'nucleus_get_class_set' ) ) :
	/**
	 * Prepare and sanitize the class set.
	 *
	 * Caution! This function sanitize each class,
	 * but don't escape the returned result.
	 *
	 * E.g. [ 'my', 'cool', 'class' ] or 'my cool class'
	 * will be sanitized and converted to "my cool class".
	 *
	 * @param array|string $classes
	 *
	 * @return string
	 */
	function nucleus_get_class_set( $classes ) {
		if ( empty( $classes ) ) {
			return '';
		}

		if ( is_string( $classes ) ) {
			$classes = (array) $classes;
		}

		// remove empty elements before loop, if exists
		// and explode array into the flat list
		$classes   = array_filter( $classes );
		$class_set = array();
		foreach ( $classes as $class ) {
			$class = trim( $class );
			if ( false === strpos( $class, ' ' ) ) {
				$class_set[] = $class;

				continue;
			}

			// replace possible multiple whitespaces with single one
			$class = preg_replace( '/\\s\\s+/', ' ', $class );
			foreach ( explode( ' ', $class ) as $subclass ) {
				$class_set[] = trim( $subclass );
			}
			unset( $subclass );
		}
		unset( $class );

		// do not duplicate
		$class_set = array_unique( $class_set );
		$class_set = array_map( 'sanitize_html_class', $class_set );
		$class_set = array_filter( $class_set );

		$set = implode( ' ', $class_set );

		return $set;
	}
endif;

if ( ! function_exists( 'nucleus_get_html_attr' ) ) :
	/**
	 * Return HTML attributes list for given attributes pairs
	 *
	 * Caution! This function does not escape attribute value,
	 * only attribute name, for more flexibility. You should do this manually
	 * for each attribute before calling this function.
	 *
	 * Also you can pass a multidimensional array with one level depth,
	 * this array will be encoded to json format.
	 *
	 * @example <pre>
	 * nucleus_get_html_attr(array(
	 *   'class' => 'super-class',
	 *   'title' => 'My cool title',
	 *   'data-settings' => array( 'first' => '', 'second' => '' ),
	 * ));
	 * </pre>
	 *
	 * Sometimes some attributes are required and should be present in attributes
	 * list. For example, when you build attributes for a link "href" is mandatory.
	 * So if user do not fill this field default values will be used. Should
	 * be an array with the same keys as in $attr.
	 *
	 * @example <pre>
	 * nucleus_get_html_attr([href => ''], [href => #]); // returns href="#"
	 * </pre>
	 *
	 * @param array $attr     Key and value pairs of HTML attributes
	 * @param array $defaults Default values, that should be present in attributes list
	 *
	 * @return string
	 */
	function nucleus_get_html_attr( $attr, $defaults = array() ) {
		$attributes = array();

		foreach ( (array) $attr as $attribute => $value ) {
			$template = '%1$s="%2$s"';

			// if user pass empty value, use one from defaults if same field exists
			// allowed only for scalar types
			if ( is_scalar( $value )
			     && '' === (string) $value
			     && array_key_exists( $attribute, $defaults )
			) {
				$value = $defaults[ $attribute ];
			}

			// convert array to json
			if ( is_array( $value ) ) {
				$template = '%1$s=\'%2$s\'';
				$value    = json_encode( $value );
			}

			if ( is_bool( $value ) ) {
				$template = '%1$s';
			}

			// $value should not be empty
			if ( empty( $value ) ) {
				continue;
			}

			$attributes[] = sprintf( $template, $attribute, $value );
		}

		return implode( ' ', $attributes );
	}
endif;

if ( ! function_exists( 'nucleus_get_unique_id' ) ):
	/**
	 * Return the unique ID for general purposes
	 *
	 * @param string $prepend Will be prepended to generated string
	 * @param int    $limit   Limit the number of unique symbols! How many unique symbols should be in a string,
	 *                        maximum is 32 symbols. $prepend not included.
	 *
	 * @return string
	 */
	function nucleus_get_unique_id( $prepend = '', $limit = 8 ) {
		$unique = substr( md5( uniqid() ), 0, $limit );

		return $prepend . $unique;
	}
endif;

if ( ! function_exists( 'nucleus_get_unique_key' ) ) :
	/**
	 * Return the cache field based on some $slug and $salt
	 *
	 * Should be less than 45 symbols!
	 *
	 * @param string $slug Name of the element which required hashing
	 * @param string $salt Some unique information, e.g. post ID
	 *
	 * @return string Example $slug_8
	 */
	function nucleus_get_unique_key( $slug, $salt = '' ) {
		$hash = substr( md5( $salt . $slug ), 0, 8 );

		$slug = preg_replace( '/[^a-z0-9_-]+/i', '-', $slug );
		$slug = str_replace( array( '-', '_' ), '-', $slug );
		$slug = trim( $slug, '-' );
		$slug = str_replace( '-', '_', $slug );

		return "{$slug}_{$hash}";
	}
endif;

if ( ! function_exists( 'nucleus_get_image_src' ) ):
	/**
	 * Return the URL of the attachment by given ID.
	 * Perfect for background images or img src attribute.
	 *
	 * @param int    $attachment_id Attachment ID
	 * @param string $size          Image size, can be "full", "large", etc..
	 *
	 * @uses wp_get_attachment_image_src()
	 *
	 * @return string String with url on success, FALSE on fail.
	 */
	function nucleus_get_image_src( $attachment_id, $size = 'full' ) {
		if ( empty( $attachment_id ) ) {
			return '';
		}

		$attachment = wp_get_attachment_image_src( $attachment_id, $size );
		if ( false === $attachment ) {
			return '';
		}

		if ( ! empty( $attachment[0] ) ) {
			return $attachment[0];
		}

		return '';
	}
endif;

if ( ! function_exists( 'nucleus_get_image_size' ) ):
	/**
	 * Return prepared image size
	 *
	 * @param string $size User specified image size. Default is "full"
	 *
	 * @return array|string Built-in size keyword or array of width and height
	 */
	function nucleus_get_image_size( $size = 'full' ) {
		/**
		 * @var array Allowed image sizes and aliases
		 */
		$allowed = array_merge( get_intermediate_image_sizes(), array(
			'thumb',
			'post-thumbnail',
			'full'
		) );

		$out = 'full';
		if ( is_numeric( $size ) ) {
			// user specify single integer
			$size = (int) $size;
			$out  = array( $size, $size );
		} elseif ( false !== strpos( $size, 'x' ) ) {
			// user specify pair of width and height
			$out = array_map( 'absint', explode( 'x', $size ) );
		} elseif ( in_array( $size, $allowed, true ) ) {
			// user specify one of the built-in sizes
			$out = $size;
		}

		return $out;
	}
endif;

if ( ! function_exists( 'nucleus_get_dir_contents' ) ) :
	/**
	 * Returns the contents of directory
	 *
	 * Designed for CPTs and shortcodes directories for auto loading
	 * files.
	 *
	 * @param string      $path Absolute path to directory
	 * @param string|null $ext  Suffix for {@see DirectoryIterator::getBasename}
	 *
	 * @return array A list of [filename => path]
	 */
	function nucleus_get_dir_contents( $path, $ext = '.php' ) {
		$files = array();
		try {
			$dir = new DirectoryIterator( $path );
			foreach( $dir as $file ) {
				if ( $file->isDot() || ! $file->isReadable() ) {
					continue;
				}

				$filename = $file->getBasename( $ext );

				// Do not load files if name starts with underscores
				if ( '_' === substr( $filename, 0, 1 ) ) {
					continue;
				}

				// do not load files if name starts with dots
				if ( '.' === substr( $filename, 0, 1 ) ) {
					continue;
				}

				$files[ $filename ] = $file->getPathname();
				unset( $filename );
			}
			unset( $file );
		} catch ( Exception $e ) {
			trigger_error( 'nucleus_get_dir_contents(): ' . $e->getMessage() );
		}

		return $files;
	}
endif;

if ( ! function_exists( 'nucleus_get_opacity_value' ) ) :
	/**
	 * Return the value for opacity css property
	 *
	 * @param string|int $opacity Opacity in percents from 0 to 100
	 *
	 * @return string
	 */
	function nucleus_get_opacity_value( $opacity ) {
		$opacity = trim( (string) $opacity, '.%' );

		if ( '0' === $opacity ) {
			return '0';
		} elseif( '100' === $opacity ) {
			return '1';
		}

		return sprintf( '.%d', (int) $opacity );
	}
endif;

if ( ! function_exists( 'nucleus_get_text' ) ) :
	/**
	 * Maybe returns some text.
	 *
	 * HTML allowed
	 *
	 * @param string $text   A piece of text
	 * @param string $before Before the text
	 * @param string $after  After the text
	 *
	 * @return string
	 */
	function nucleus_get_text( $text, $before = '', $after = '' ) {
		if ( empty( $text ) ) {
			return '';
		}

		return $before . $text . $after;
	}
endif;

if ( ! function_exists( 'nucleus_get_asset' ) ) :
	/**
	 * Get the unescaped fully qualified uri to the theme asset
	 *
	 * Also you can overwrite file in child-theme
	 *
	 * @uses get_stylesheet_directory_uri
	 *
	 * @param string $path Relative path to asset (img, css, js, etc)
	 *
	 * @return string
	 */
	function nucleus_get_asset( $path ) {
		$theme_uri = get_stylesheet_directory_uri();

		$path = ltrim( $path, '/' );
		$uri  = $theme_uri . '/' . $path;

		return $uri;
	}
endif;

if ( ! function_exists( 'nucleus_get_meta' ) ) :
	/**
	 * Returns the values of meta box.
	 *
	 * If $field is specified will return the field's value.
	 *
	 * @param int         $post_id Post ID
	 * @param string      $slug    Meta box unique name
	 * @param null|string $field   Key of the field
	 * @param mixed       $default Default value
	 *
	 * @return mixed Array with field-value, mixed data if field is specified and the value
	 *               of $default field if nothing found.
	 */
	function nucleus_get_meta( $post_id, $slug, $field = null, $default = array() ) {
		// pass to nucleus if exists
		if ( function_exists( 'equip_get_meta' ) ) {
			return equip_get_meta( $post_id, $slug, $field, $default );
		}

		$cache_key   = nucleus_get_unique_key( $slug, $post_id );
		$cache_group = 'meta_box';

		// Cached value should always be an array
		$values = wp_cache_get( $cache_key, $cache_group );
		if ( false === $values ) {
			$values = get_post_meta( $post_id, $slug, true );
			if ( empty( $values ) ) {
				// possible cases: meta box not saved yet
				// or mistake in $post_id or $slug
				return $default;
			}

			// cache for 1 day
			wp_cache_set( $cache_key, $values, $cache_group, 86400 );
		}

		$result = null;
		if ( ! is_array( $values ) ) {
			// return AS IS for non-array values
			$result = $values;
		} elseif ( null === $field ) {
			// return whole array if $field not specified
			$result = $values;
		} elseif ( array_key_exists( $field, $values ) ) {
			// if specified $field present
			$result = $values[ $field ];
		} else {
			// nothing matched, return default value
			$result = $default;
		}

		return $result;
	}
endif;

if ( ! function_exists( 'nucleus_get_tag' ) ) :
	/**
	 * Returns the string representation of HTML tag
	 *
	 * Supports paired and self-closing tags.
	 * If $contents is empty tag will be considered as a self closing.
	 *
	 * @param string $tag     The tag
	 * @param array  $atts    HTML attributes
	 * @param string $content Content
	 * @param string $type    Type of the tag: paired or self-closing
	 *
	 * @return string
	 */
	function nucleus_get_tag( $tag, $atts = array(), $content = null, $type = 'self-closing' ) {
		if ( empty( $tag ) ) {
			return '';
		}

		// specify the $content, even the empty string to make tag paired
		if ( 'paired' === $type || null !== $content ) {
			$result = sprintf( '<%1$s %2$s>%3$s</%1$s>',
				$tag,
				nucleus_get_html_attr( $atts ),
				$content
			);
		} else {
			$result = sprintf( '<%1$s %2$s>', $tag, nucleus_get_html_attr( $atts ) );
		}

		return $result;
	}
endif;

if ( ! function_exists( 'nucleus_get_option' ) ) :
	/**
	 * Get theme option by its name
	 *
	 * All theme options are stored as an array
	 *
	 * @param string     $field   Option name or "all" for whole bunch of options.
	 * @param bool|mixed $default Option default value
	 *
	 * @return mixed
	 */
	function nucleus_get_option( $field = 'all', $default = false ) {
		// the global slug
		// @see functions.php
		$slug = NUCLEUS_OPTIONS;
		if ( function_exists( 'equip_get_option' ) ) {
			return equip_get_option( $slug, $field, $default );
		}

		// support for multisite
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$site_id   = get_current_blog_id();
			$cache_key = nucleus_get_unique_key( $slug, $site_id );
		} else {
			$cache_key = nucleus_get_unique_key( $slug );
		}
		$cache_group = 'nucleus_options';

		$options = wp_cache_get( $cache_key, $cache_group );
		if ( empty( $options ) ) {
			$options = get_option( $slug );
			// if equip is missing cache for 1 hour
			// in other case equip will take care of caching
			wp_cache_set( $cache_key, $options, $cache_group, 3600 );
		}

		// if something wrong with options
		if ( empty( $options ) ) {
			return $default;
		}

		if ( ! is_array( $options) || 'all' === $field ) {
			$value = $options;
		} elseif( array_key_exists( $field, $options)) {
			$value = $options[ $field ];
		} else {
			$value = $default;
		}

		return $value;
	}
endif;

if ( ! function_exists( 'nucleus_get_options_slice' ) ) :
	/**
	 * Returns only those options which names started with given prefix.
	 *
	 * Based on naming convention when each option should has a global
	 * prefix based on section where this option is added.
	 *
	 * E.g. for typography options prefix should be "typography_",
	 * for colors options prefix should be "color_", etc.
	 *
	 * @param string $prefix Part of option name
	 *
	 * @return array Options names without prefix and its values
	 */
	function nucleus_get_options_slice( $prefix ) {
		$options = nucleus_get_option();
		if ( empty( $options ) ) {
			return array();
		}

		$prefix = rtrim( $prefix, '_' );
		$prefix .= '_';

		$sliced = array();
		foreach ( (array) $options as $option => $value ) {
			if ( false === strpos( $option, $prefix ) ) {
				continue;
			}

			$option = str_replace( $prefix, '', $option );

			$sliced[ $option ] = $value;
		}

		return $sliced;
	}
endif;

if ( ! function_exists( 'nucleus_get_setting' ) ) :
	/**
	 * Get the setting
	 *
	 * Respect the local settings before checking the global
	 *
	 * This function should return the value for single
	 * setting, if you want the whole bunch of page settings
	 * {@see nucleus_get_page_setting()}
	 *
	 * @param string $setting Setting name
	 * @param mixed  $default Default value
	 *
	 * @return mixed
	 */
	function nucleus_get_setting( $setting, $default = false ) {
		// check the local setting first
		$result = nucleus_get_page_setting( $setting, $default );

		// if option not set of used "default" one check the global
		if ( 'default' === (string) $result || false === $result || is_array( $result ) ) {
			$result = nucleus_get_option( $setting, $default );
		}

		return $result;
	}
endif;

if ( ! function_exists( 'nucleus_get_post_terms' ) ) :
	/**
	 * Return terms, assigned for specified Post ID,
	 * depending on {@see $context} param: "slug" or "name".
	 *
	 * TODO: add caching
	 *
	 * @param integer $post_id  Post ID.
	 * @param string  $taxonomy The taxonomy for which to retrieve terms.
	 * @param string  $context  [optional] Term slug or name. Default is "slug".
	 *
	 * @return array [ term, term, ... ]
	 */
	function nucleus_get_post_terms( $post_id, $taxonomy, $context = 'slug' ) {
		$post_terms = wp_get_post_terms( $post_id, $taxonomy );
		// Catch the WP_Error or if any terms was not assigned to post
		if ( is_wp_error( $post_terms ) || 0 === count( $post_terms ) ) {
			return array();
		}

		$terms = array();
		foreach ( $post_terms as $term ) {
			$terms[] = $term->$context;
		}
		unset( $term, $post_terms );

		return $terms;
	}
endif;

if ( ! function_exists( 'nucleus_get_networks' ) ) :
	/**
	 * Get networks list
	 *
	 * @see misc/networks.ini
	 *
	 * @param string $path Path to custom .ini file
	 *
	 * @return array
	 */
	function nucleus_get_networks( $path = '' ) {
		if ( function_exists( 'equip_get_networks' ) ) {
			return equip_get_networks();
		}

		if ( empty( $path ) ) {
			/**
			 * Filter the path to networks.ini file
			 *
			 * @param string $path Path to .ini
			 */
			$path = apply_filters( 'nucleus_path_to_networks_ini', get_stylesheet_directory() . '/misc/networks.ini' );
		}

		$networks = array();
		if ( file_exists( $path ) ) {
			$ini      = wp_normalize_path( $path );
			$networks = parse_ini_file( $ini, true );

			ksort( $networks );
		}		

		/**
		 * Filter the networks array
		 *
		 * Useful in cases when you need to add some new networks,
		 * or change the existing data
		 *
		 * @param array $networks Networks list
		 */
		return apply_filters( 'nucleus_get_networks', $networks );
	}
endif;

if ( ! function_exists( 'nucleus_get_animation_class' ) ) :
	/**
	 * Returns the animation classes
	 *
	 * @param string $is_animation Check if animation is enabled
	 * @param array  $attr         Animation attributes
	 *
	 * @return string
	 */
	function nucleus_get_animation_class( $is_animation, array $attr ) {
		if ( 'disable' === $is_animation ) {
			return '';
		}

		// make sure all fields are present
		$a = wp_parse_args( $attr, array(
			'type'   => 'top',
			'delay'  => 0,
			'easing' => 'none',
		) );

		return implode( ' ', array(
			'scrollReveal',
			'sr-' . esc_attr( $a['type'] ),
			'sr-delay-' . absint( $a['delay'] ),
			'sr-ease-in-out-' . sanitize_key( $a['easing'] ),
		) );
	}
endif;

if ( ! function_exists( 'nucleus_get_filter_cats' ) ) {
	/**
	 * Return categories for filtration by provided taxonomy
	 *
	 * Also allow to exclude some categories
	 * This function is designed for shortcodes with isotope filters,
	 * but you can use it on your own. Just provide the taxonomy, and
	 * get a list of WP_Terms objects.
	 *
	 * @param string $tax      Taxonomy
	 * @param string $excluded A comma or space separated list of slugs
	 *
	 * @return array
	 */
	function nucleus_get_filter_cats( $tax, $excluded = '' ) {
		$exclude = array();
		if ( ! empty( $excluded ) ) {
			$exclude_slugs = nucleus_parse_slugs( $excluded );
			$exclude_terms = get_terms( array(
				'taxonomy'     => $tax,
				'hierarchical' => false,
				'slug'         => $exclude_slugs,
			) );

			if ( ! is_wp_error( $exclude_terms ) ) {
				foreach( (array) $exclude_terms as $term ) {
					if ( $term instanceof WP_Term ) {
						$exclude[] = (int) $term->term_id;
					}
				}
			}
		}

		$categories = get_terms( array(
			'taxonomy'     => $tax,
			'hierarchical' => false,
			'exclude'      => $exclude,
		) );

		if ( is_wp_error( $categories ) ) {
			return array();
		}

		return $categories;
	}
}

if ( ! function_exists( 'nucleus_get_filter_markup' ) ) :
	/**
	 * Get the markup for isotope filtration
	 *
	 * As a first parameter use the result of
	 * @see nucleus_get_filter_cats()
	 *
	 * @param array $categories A list of categories
	 * @param array $args       Arguments
	 *
	 * @return string
	 */
	function nucleus_get_filter_markup( $categories, $args = array() ) {
		if ( empty( $categories ) ) {
			return '';
		}

		$a = wp_parse_args( $args, array(
			'id'       => nucleus_get_unique_id( 'filter-' ),
			'class'    => 'nav-filters',
			'grid_id'  => '',
			'show_all' => '',
			'position' => 'center',
		) );

		$attr = nucleus_get_html_attr( array(
			'id'           => $a['id'],
			'class'        => $a['class'],
			'data-grid-id' => $a['grid_id'],
		) );

		$html = '<div class="padding-top text-' . $a['position'] . '">';
		$html .= '<ul ' . $attr . '>';
		$html .= '<li class="active">';
		$html .= sprintf( '<a href="#" data-filter="*">%s</a>', esc_html( $a['show_all'] ) );
		$html .= '</li>';

		foreach ( $categories as $category ) {
			$html .= sprintf( '<li><a href="#" data-filter=".%1$s">%2$s</a></li>',
				esc_attr( $category->slug ),
				esc_html( $category->name )
			);
		}

		$html .= '</ul>';
		$html .= '</div>';

		return $html;
	}
endif;

if ( ! function_exists( 'nucleus_get_attachment' ) ) :
	/**
	 * Get attachment data
	 *
	 * @param int|WP_Post $attachment Attachment ID or post object.
	 *
	 * @see  wp_prepare_attachment_for_js()
	 * @link https://wordpress.org/ideas/topic/functions-to-get-an-attachments-caption-title-alt-description
	 *
	 * @return array
	 */
	function nucleus_get_attachment( $attachment ) {
		$_attachment = $attachment;
		$attachment  = get_post( $attachment );
		if ( ! $attachment ) {
			return array();
		}

		if ( 'attachment' != $attachment->post_type ) {
			return array();
		}

		$data = array(
			'id'          => $attachment->ID,
			'title'       => $attachment->post_title,
			'alt'         => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
			'author'      => $attachment->post_author,
			'description' => $attachment->post_content,
			'caption'     => $attachment->post_excerpt,
			'name'        => $attachment->post_name,
			'status'      => $attachment->post_status,
			'uploaded_to' => $attachment->post_parent,
			'menu_order'  => $attachment->menu_order,
			'mime'        => $attachment->post_mime_type,
			'date'        => strtotime( $attachment->post_date_gmt ) * 1000,
			'modified'    => strtotime( $attachment->post_modified_gmt ) * 1000,
		);

		/**
		 * Filter the attachment data
		 *
		 * This filters allows you easily add custom attachment data,
		 * like "filename", "url", "link", etc
		 *
		 * @param array       $data        Attachment data
		 * @param WP_Post     $attachment  Attachment post
		 * @param int|WP_Post $_attachment Attachment ID ot post object, passed to the function
		 */
		return apply_filters( 'nucleus_get_attachment', $data, $attachment, $_attachment );
	}
endif;

if ( ! function_exists( 'nucleus_the_tag' ) ) :
	/**
	 * Echoes the HTML tag
	 *
	 * Supports paired and self-closing tags.
	 * If $contents is empty tag will be considered as a self closing.
	 *
	 * @param string $tag     The tag
	 * @param array  $atts    HTML attributes
	 * @param mixed  $content Content
	 * @param string $type    Type of the tag: paired or self-closing
	 */
	function nucleus_the_tag( $tag, $atts = array(), $content = null, $type = 'self-closing' ) {
		echo nucleus_get_tag( $tag, $atts, $content, $type );
	}
endif;

if ( ! function_exists( 'nucleus_the_text' ) ) :
	/**
	 * Maybe echoes some text
	 *
	 * HTML allowed
	 *
	 * @param string $text   A piece of text
	 * @param string $before Before the text
	 * @param string $after  After the text
	 */
	function nucleus_the_text( $text, $before = '', $after = '' ) {
		echo nucleus_get_text( $text, $before, $after );
	}
endif;

if ( ! function_exists( 'nucleus_the_asset' ) ) :
	/**
	 * Echoes the URI to the asset
	 *
	 * @see nucleus_get_asset()
	 *
	 * @param string $path Relative path to asset (img, css, js, etc)
	 */
	function nucleus_the_asset( $path ) {
		echo esc_url( nucleus_get_asset( $path ) );
	}
endif;

if ( ! function_exists( 'nucleus_parse_slugs' ) ) :
	/**
	 * Clean up an array, comma- or space-separated list of slugs.
	 *
	 * @example
	 * <pre>
	 * nucleus_parse_slugs("a,b,c,d"); // returns [a, b, c, d]
	 * </pre>
	 *
	 * @example
	 * <pre>
	 * nucleus_parse_slugs("a b c d"); // returns [a, b, c, d]
	 * </pre>
	 *
	 * @param array|string $list List of slugs
	 *
	 * @return array Sanitized array of slugs
	 */
	function nucleus_parse_slugs( $list ) {
		if ( ! is_array( $list ) ) {
			$list = preg_split( '/[\s,]+/', $list );
		}

		$list = array_map( 'sanitize_key', $list );
		$list = array_unique( $list );

		return $list;
	}
endif;

if ( ! function_exists( 'nucleus_parse_array' ) ):
	/**
	 * Find and extract the same-prefixed items from the given array
	 *
	 * Should the the associative array and test the keys, not values.
	 * Designed for integrated shortcodes and for logically grouped
	 * set of options.
	 *
	 * Very close to {@see nucleus_get_options_slice},
	 * except working with given raw array, not theme options
	 *
	 * @example
	 * <pre>
	 * // integrated shortcode
	 * nucleus_parse_array( array(
	 *   'title' => '',
	 *   'type' => '',
	 *   'etc' => '',
	 *   'icon_library' => '',
	 *   'icon_position' => '',
	 *   'icon_alignment' => '',
	 * ), 'icon_' );
	 *
	 * // returns array('library' => '', 'position' => '', 'alignment' => '');
	 * </pre>
	 *
	 * @example
	 * <pre>
	 * // options
	 * nucleus_parse_array( array(
	 *   'typography_font_style' => '',
	 *   'typography_font_weight' => '',
	 *   'header_height' => '',
	 *   'header_mobile_height' => '',
	 * ), 'typography_' );
	 *
	 * // returns array('font_style' => '', 'font_weight' => '');
	 * </pre>
	 *
	 * @param array  $data   Some data
	 * @param string $prefix Integrated attributes prefix
	 *
	 * @return array Integrated shortcode attributes without prefix
	 */
	function nucleus_parse_array( $data, $prefix ) {
		// prefix should always be appended with underscores,
		// e.g. "prefix_"
		$prefix = rtrim( $prefix, '_' );
		$prefix .= '_';

		$attributes = array();
		foreach ( $data as $k => $v ) {
			if ( false !== strpos( $k, $prefix ) ) {
				$clean                = str_replace( $prefix, '', $k );
				$attributes[ $clean ] = $v;
			}

			continue;
		}

		return $attributes;
	}
endif;

if ( ! function_exists( 'nucleus_css_declarations' ) ) :
	/**
	 * Generate CSS declarations like "width: auto;", "background-color: red;", etc.
	 *
	 * May be used either standalone function or in pair with {@see nucleus_css_rules}
	 *
	 * @param array $props Array of properties where field is a property name
	 *                     and value is a property value
	 *
	 * @return string
	 */
	function nucleus_css_declarations( $props ) {
		$declarations = array();

		foreach ( $props as $name => $value ) {
			if ( is_scalar( $value ) ) {
				$declarations[] = "{$name}: {$value};";
				continue;
			}

			/*
			 * $value may be an array, not only scalar,
			 * in case of multiple declarations, like background gradients, etc.
			 *
			 * background: white;
			 * background: -moz-linear-gradient....
			 *
			 * $sub (sub value) should be a string!
			 */
			foreach ( (array) $value as $sub ) {
				$declarations[] = "{$name}: {$sub};";
			}
			unset( $sub );
		}
		unset( $name, $value );

		return implode( ' ', $declarations );
	}
endif;

if ( ! function_exists( 'nucleus_css_rules' ) ) :
	/**
	 * Generate CSS rules
	 *
	 * @uses nucleus_css_declarations
	 *
	 * @param string|array $selectors Classes or tags where properties will be applied to.
	 * @param string|array $props     Array of css rules where field is property name itself
	 *                                and value is a property value. Example: [font-size => 14px].
	 *                                Or string with CSS rules declarations in format: "font-size: 14px;"
	 *
	 * @return string CSS rules in format .selector {property: value;}
	 */
	function nucleus_css_rules( $selectors, $props ) {
		// Convert to string
		if ( is_array( $selectors ) ) {
			$selectors = implode( ', ', $selectors );
		}

		// convert to string, too
		if ( is_array( $props ) ) {
			$props = nucleus_css_declarations( $props );
		}

		return sprintf( '%1$s {%2$s}', $selectors, $props );
	}
endif;

if ( ! function_exists( 'nucleus_css_background_image' ) ) :
	/**
	 * Returns ready-to-use and escaped "background-image: %" string
	 * for style attribute.
	 *
	 * Useful for situations when you need only a background image.
	 * Specify the fallback if you do not want see element without
	 * the background.
	 *
	 * @example
	 * <pre>
	 * $attr = nucleus_get_html_attr(array(
	 *   'class' => 'some-class',
	 *   'style' => nucleus_css_background_image( 123, 'medium', 'placeholder.jpg' )
	 * ));
	 * </pre>
	 *
	 * @param int    $attachment Attachment ID or URI to the image
	 * @param string $size       Image size, like "full", "medium", etc..
	 * @param string $fallback   Full URI to fallback image, good for placeholders.
	 *
	 * @return string
	 */
	function nucleus_css_background_image( $attachment, $size = 'full', $fallback = '' ) {
		if ( is_numeric( $attachment ) ) {
			$src = nucleus_get_image_src( $attachment, $size );
		} else {
			$src = $attachment;
		}

		if ( empty( $src ) && empty( $fallback ) ) {
			return '';
		}

		if ( empty( $src ) && ! empty( $fallback ) ) {
			$src = $fallback;
		}

		return nucleus_css_declarations( array(
			'background-image' => sprintf( 'url(%s)', esc_url( $src ) ),
		) );
	}
endif;

if ( ! function_exists( 'nucleus_content_encode' ) ) :
	/**
	 * Encode the content before caching.
	 *
	 * @param string $content Some content, usually HTML string.
	 *
	 * @return string
	 */
	function nucleus_content_encode( $content ) {
		return str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $content );
	}
endif;

if ( ! function_exists( 'nucleus_content_decode' ) ) :
	/**
	 * Decode the previously encoded content
	 *
	 * @param string $content Encoded and cached value
	 *
	 * @return string
	 */
	function nucleus_content_decode( $content ) {
		return $content;
	}
endif;

if ( ! function_exists( 'nucleus_query_encode' ) ) :
	/**
	 * Encoding the query args for passing into the html
	 *
	 * @see bnb_query_decode
	 *
	 * @param array $query Query args for WP_Query
	 *
	 * @return string
	 */
	function nucleus_query_encode( $query ) {
		return (array) $query;
	}
endif;

if ( ! function_exists( 'nucleus_query_decode' ) ):
	/**
	 * Decoding the encoded string with query args for WP_Query
	 *
	 * @see bnb_query_encode
	 *
	 * @param string $query Encoded string with query args
	 *
	 * @return array|null
	 */
	function nucleus_query_decode( $query ) {
		return is_array( $query ) ? $query : json_decode( $query, true );
	}
endif;

if ( ! function_exists( 'nucleus_query_per_page' ) ) :
	/**
	 * Handle the "posts_per_page" option for WP_Query.
	 *
	 * Return -1 for "all" posts, absolute number or if valid value not given
	 * returns the value from Settings > Reading option.
	 *
	 * @param mixed $per_page
	 *
	 * @return int
	 */
	function nucleus_query_per_page( $per_page ) {
		if ( 'all' === strtolower( $per_page ) ) {
			$pp = -1;
		} elseif ( is_numeric( $per_page ) ) {
			$pp = (int) $per_page;
		} else {
			$pp = (int) get_option( 'posts_per_page' );
		}

		return $pp;
	}
endif;

if ( ! function_exists( 'nucleus_query_single_tax' ) ) :
	/**
	 * Build a tax_query with a single taxonomy for WP_Query
	 *
	 * Use the taxonomy slug because of export/import issues.
	 * During the import process WordPress creates new taxonomy
	 * (with new ID) based on import information.
	 *
	 * @param string $terms    A comma-separated list of slugs, directly from a shortcode attr
	 * @param string $taxonomy Taxonomy name
	 *
	 * @return array A read-to-use tax_query
	 */
	function nucleus_query_single_tax( $terms, $taxonomy ) {
		$tax_queries = array();

		$tax_queries[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => nucleus_parse_slugs( $terms ),
		);

		return $tax_queries;
	}
endif;

if ( ! function_exists( 'nucleus_query_multiple_tax' ) ) :
	/**
	 * Build a tax_query for WP_Query with multiple number of taxonomies
	 *
	 * @param string $terms      A comma-separated list of terms slugs, directly from a shortcode attr.
	 * @param array  $taxonomies A list of taxonomies, like "category", "post_tag", custom tax.
	 *
	 * @return array
	 */
	function nucleus_query_multiple_tax( $terms, $taxonomies ) {
		$_terms = get_terms( array(
			'taxonomy'     => $taxonomies,
			'hierarchical' => false,
			'slug'         => nucleus_parse_slugs( $terms ),
		) );

		if ( ! is_array( $_terms ) || empty( $_terms ) ) {
			return array();
		}

		/*
		 * Build the taxonomies array for use in tax_query
		 *
		 * If taxonomy already exists in list, just add value to terms array.
		 * Otherwise add a new taxonomy to $tax_queries array.
		 */
		$tax_queries = array();
		foreach ( $_terms as $t ) {
			if ( array_key_exists( $t->taxonomy, $tax_queries ) ) {
				$tax_queries[ $t->taxonomy ]['terms'][] = $t->term_id;
			} else {
				$tax_queries[ $t->taxonomy ] = array(
					'taxonomy' => $t->taxonomy,
					'field'    => 'term_id',
					'terms'    => array( (int) $t->term_id ),
				);
			}
		}
		unset( $t );

		return array_values( $tax_queries );
	}
endif;

if ( ! function_exists( 'nucleus_query_build' ) ) :
	/**
	 * Building an query for using in WP_Query
	 *
	 * If callback is specified the parsed query should be passed into it,
	 * so you can use some extra logic to process the query args before it
	 * will be returned. A good place for passing an anonymous function.
	 *
	 * @param array         $args     Key-value pairs for using inside WP_Query
	 * @param null|callable $callback Process query args with extra logic before returning
	 *
	 * @return array|mixed
	 */
	function nucleus_query_build( $args, $callback = null ) {
		$query = array();

		$args = array_filter( $args );
		foreach ( $args as $param => $value ) {
			switch ( $param ) {
				case 'post__in':
				case 'post__not_in':
					$query[ $param ] = wp_parse_id_list( $value );
					break;

				case 'orderby':
				case 'order':
					$query[ $param ] = sanitize_text_field( $value );
					break;

				case 'posts_per_page':
					$query[ $param ] = nucleus_query_per_page( $value );
					break;

				case 'categories':
				case 'taxonomies':
					// these are service keys, that are used for
					// building the tax_query, they should be processing in a callback,
					// so pass as is.
					$query[ $param ] = $value;
					break;

				default:
					$query[ $param ] = $value;
					break;
			}
		}
		unset( $param, $value );

		if ( is_callable( $callback ) ) {
			$query = call_user_func( $callback, $query );
		}

		// remove empty values
		$query = array_filter( $query );

		return $query;
	}
endif;

if ( ! function_exists( 'nucleus_shortcode_cache_key' ) ) :
	/**
	 * This function is designed for creating a cache keys
	 * for shortcodes and based on shortcodes params.
	 *
	 * @param string $shortcode Shortcode name
	 * @param array  $atts      Shortcode attributes
	 * @param mixed  $content   Shortcode content
	 *
	 * @return string
	 */
	function nucleus_shortcode_cache_key( $shortcode, $atts, $content ) {
		$params  = serialize( $atts );
		$content = (string) $content;
		$key     = substr( md5( $shortcode . $params . $content ), 0, 8 );

		return "{$shortcode}_{$key}";
	}
endif;

if ( ! function_exists( 'nucleus_shortcode_build' ) ) :
	/**
	 * Build a shortcode
	 *
	 * @param       $tag
	 * @param array $atts
	 * @param null  $content
	 *
	 * @return string
	 */
	function nucleus_shortcode_build( $tag, $atts = array(), $content = null ) {
		$attributes = array();
		$enclosed   = '';
		if ( count( $atts ) > 0 ) {
			foreach ( $atts as $param => $value ) {
				$attributes[] = sprintf( '%1$s="%2$s"', $param, $value );
			}

			$attributes = implode( ' ', $attributes );
		}

		if ( null !== $content ) {
			$enclosed .= $content;
			$enclosed .= "[/{$tag}]";
		}

		return sprintf( '[%1$s %2$s]%3$s', $tag, $attributes, $enclosed );
	}
endif;

if ( ! function_exists( 'hidden' ) ) :
	/**
	 * Outputs the html hidden attribute.
	 *
	 * Compares the first two arguments and if identical marks as hidden.
	 *
	 * @param mixed $hidden  One of the values to compare
	 * @param mixed $current The other value to compare if not just true
	 * @param bool  $echo    Whether to echo or just return the string
	 *
	 * @return string
	 */
	function hidden( $hidden, $current = true, $echo = true ) {
		if ( (string) $hidden === (string) $current ) {
			$result = 'hidden';
		} else {
			$result = '';
		}

		if ( $echo ) {
			echo esc_attr( $result );
		}

		return $result;
	}
endif;

if ( ! function_exists( 'nucleus_build_link' ) ) :
	/**
	 * Parse string like "title:Hello world|weekday:Monday" to [title => 'Hello World', weekday => 'Monday']
	 *
	 * This function is a fallback to Visual Composer's {@see vc_build_link()} function.
	 * Necessary for situations, when user disable VC but do not remove the shortcode.
	 *
	 * @param string $link Encoded link from TinyMCE link builder
	 *
	 * @return array
	 */
	function nucleus_build_link( $link ) {
		if ( function_exists( 'vc_build_link' ) ) {
			return vc_build_link( $link );
		}

		$pairs = explode( '|', $link );
		if ( 0 === count( $pairs ) ) {
			return array( 'url' => '', 'title' => '', 'target' => '' );
		}

		$result = array();
		foreach ( $pairs as $pair ) {
			$param = preg_split( '/\:/', $pair );
			if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
				$result[ $param[0] ] = rawurldecode( $param[1] );
			}
		}

		return $result;
	}
endif;

if ( ! function_exists( 'nucleus_convert_link' ) ) :
	/**
	 * Convert a link in VC compatible format
	 *
	 * [url, title, target] into url|title|target with
	 * URL encoding
	 *
	 * @param array $attr Attributes
	 *
	 * @return string
	 */
	function nucleus_convert_link( $attr ) {
		$link = array();

		foreach( $attr as $a => $v ) {
			switch( $a ) {
				case 'url':
					$link[] = 'url:' . rawurlencode( $v );
					break;

				case 'title':
					$link[] = 'title:' . rawurlencode( $v );
					break;

				case 'target':
					$link[] = 'target:' . rawurlencode( $v );
					break;

				default:
					$link[] = '';
					break;
			}
		}
		unset( $a, $v );

		return implode( '|', array_filter( $link ) );
	}
endif;

if ( ! function_exists( 'nucleus_do_shortcode' ) ) :
	/**
	 * Parse TinyMCE content. Maybe do_shortcode().
	 *
	 * This function is a fallback for Visual Composer's
	 * wpb_js_remove_wpautop() function.
	 *
	 * @param string     $content Shortcode content
	 * @param bool|false $autop   Use {@see wpautop()} or not
	 *
	 * @return string
	 */
	function nucleus_do_shortcode( $content, $autop = false ) {
		if ( function_exists( 'wpb_js_remove_wpautop' ) ) {
			return wpb_js_remove_wpautop( $content, $autop );
		}

		if ( $autop ) {
			$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
		}

		return do_shortcode( shortcode_unautop( $content ) );
	}
endif;

if ( ! function_exists( 'nucleus_google_font_url' ) ) :
	/**
	 * Prepare the link for Google Fonts the right way
	 *
	 * @param string $url A url to a Google Font
	 *
	 * @return string
	 */
	function nucleus_google_font_url( $url ) {
		$query = parse_url( $url, PHP_URL_QUERY );
		if ( null === $query ) {
			return '';
		}

		parse_str( $query, $out );
		if ( ! array_key_exists( 'family', $out ) || empty( $out['family'] ) ) {
			return '';
		}

		$url = add_query_arg( 'family', urlencode( $out['family'] ), '//fonts.googleapis.com/css' );

		return esc_url( $url );
	}
endif;

if ( ! function_exists( 'nucleus_is_parallax' ) ) :
	/**
	 * Check if parallax is enabled on a particular page
	 *
	 * @return bool
	 */
	function nucleus_is_parallax() {
		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		// position can not be 0, so
		return (bool) strpos( $post->post_content, 'is_parallax="enable"' );
	}
endif;

if ( ! function_exists( 'nucleus_is_animation' ) ) :
	/**
	 * Check if animation is enabled on a particular page
	 *
	 * @return bool
	 */
	function nucleus_is_animation() {
		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		// position can not be 0, so
		return (bool) strpos( $post->post_content, 'is_animation="enable"' );
	}
endif;

if ( ! function_exists( 'nucleus_is_map' ) ) :
	/**
	 * Check if Google Map shortcode used
	 *
	 * @return bool
	 */
	function nucleus_is_map() {
		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		$result = false;
		if ( false !== strpos( $post->post_content, '[nucleus_map' ) ) {
			$result = true;
		}

		if ( false !== strpos( $post->post_content, '[nucleus_split_contacts' ) ) {
			$result = true;
		}

		return $result;
	}
endif;