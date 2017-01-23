<?php
namespace Equip\Module;

use Equip\Equip;
use Equip\Factory;
use Equip\Service\Sanitizer;

/**
 * Working with custom fields in user / profile pages
 *
 * @author  8guild
 * @package Equip\Module
 */
class UserModule {

	/**
	 * @var null|\Equip\Service\Storage
	 */
	private $storage = null;

	/**
	 * @var array
	 */
	private $places = array(
		'personal' => 'profile_personal_options',
		'user'     => 'edit_user_profile',
		'profile'  => 'show_user_profile',
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'edit_user_profile', array( $this, 'render' ) );
		add_action( 'show_user_profile', array( $this, 'render' ) );
		//add_action( 'personal_options', array( $this, 'render' ) );
		add_action( 'profile_personal_options', array( $this, 'render' ) );

		add_action( 'personal_options_update', array( $this, 'save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save' ) );

		$this->storage = Factory::service( Equip::STORAGE );
	}

	/**
	 * TODO: make a refactoring. May be storing by hooks not so good idea?
	 *
	 * @param       $slug
	 * @param       $contents
	 * @param array $args
	 */
	public function store( $slug, $contents, $args = array() ) {
		$options = $this->storage->get( Equip::USER );
		if ( empty( $options ) ) {
			$options = array();
		}

		// merge with defaults
		$args    = wp_parse_args( $args, $this->get_defaults() );
		$context = $args['context'];
		$places  = $this->get_places();
		$hook    = $places[ $context ];

		// store by hook's, @see $places and get_defaults()
		$options[ $hook ][ $slug ] = array( 'contents' => $contents, 'args' => $args );

		$this->storage->update( Equip::USER, $options );
	}

	/**
	 * TODO: make a refactoring. Don't like this code.
	 *
	 *
	 *
	 * @param          $filter
	 * @param \WP_User $user
	 *
	 * @return array
	 */
	public function reveal( $filter, $user = null ) {
		if ( 'list' === $filter ) {
			$options = $this->storage->get( Equip::USER );
			$list    = array();
			foreach ( $options as $hook => $fields ) {
				foreach ( $fields as $slug => $data ) {
					$list[ $slug ] = $data['contents'];
				}
			}

			return $list;
		} else {
			$options = $this->storage->get( Equip::USER, $filter );
		}

		if ( empty( $options ) ) {
			return array();
		}

		$user_id    = (int) $user->data->ID;
		$user_login = $user->data->user_login;

		$roles     = array_values( $user->roles );
		$user_role = reset( $roles );

		$result = array();
		foreach ( $options as $slug => $data ) {
			$contents = $data['contents'];
			$args     = $data['args'];

			if ( ! empty( $args['login'] ) && $user_login !== $args['login'] ) {
				continue;
			}

			if ( ! empty( $args['id'] ) && $user_id !== (int) $args['id'] ) {
				continue;
			}

			if ( ! empty( $args['role'] ) && $user_role !== $args['role'] ) {
				continue;
			}

			$result[ $slug ] = $contents;
		}

		return $result;
	}

	/**
	 * Engine custom fields in user / profile pages
	 *
	 * @param \WP_User $user User object
	 */
	public function render( $user ) {
		$action  = current_action();
		$options = $this->reveal( $action, $user );
		if ( empty( $options ) ) {
			return;
		}

		foreach ( $options as $slug => $contents ) {
			$values = get_user_meta( $user->ID, $slug, true );
			$engine = Factory::engine( $contents );
			$engine->render( $slug, $contents, $values );
		}
	}

	/**
	 * Save custom fields from user / profile pages
	 *
	 * @param int $user_id Current user ID
	 *
	 * @since 1.0.0
	 */
	public function save( $user_id ) {
		$options = $this->reveal( 'list' );
		$keys    = array_keys( $options );
		$current = array_intersect_key( $_POST, array_flip( $keys ) );
		if ( empty( $current ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_users', $user_id ) ) {
			return;
		}

		// TODO: add save actions

		foreach ( $current as $slug => $values ) {
			$contents = $options[ $slug ];
			$sanitizer = Factory::service( Equip::SANITIZER );
			$values    = $sanitizer->bulk_sanitize( $values, $contents, $slug );

			update_user_meta( $user_id, $slug, $values );
		}
		unset( $slug, $values, $contents );
	}

	/**
	 * Return the map with context and associated actions
	 *
	 * @return array
	 */
	public function get_places() {
		return $this->places;
	}

	/**
	 * TODO: add filters
	 *
	 * @return array
	 */
	public function get_defaults() {
		$defaults = array(
			'login'   => '',
			'id'      => '',
			'role'    => '',
			'context' => 'user',
		);

		return $defaults;
	}
}