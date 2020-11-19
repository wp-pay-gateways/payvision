<?php
/**
 * Integration
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\Payvision
 */

namespace Pronamic\WordPress\Pay\Gateways\Payvision;

use Pronamic\WordPress\Pay\AbstractGatewayIntegration;

/**
 * Integration
 *
 * @author  Remco Tolsma
 * @version 1.1.2
 * @since   1.0.0
 */
class Integration extends AbstractGatewayIntegration {
	/**
	 * REST route namespace.
	 *
	 * @var string
	 */
	const REST_ROUTE_NAMESPACE = 'pronamic-pay/payvision/v1';

	/**
	 * Construct Payvision integration.
	 *
	 * @param array<string, array<string>> $args Arguments.
	 */
	public function __construct( $args = array() ) {
		$args = \wp_parse_args(
			$args,
			array(
				'id'            => 'payvision',
				'name'          => 'Payvision',
				'provider'      => 'payvision',
				'url'           => \__( 'https://www.payvision.com/', 'pronamic_ideal' ),
				'product_url'   => \__( 'https://www.payvision.com/', 'pronamic_ideal' ),
				'dashboard_url' => 'https://tools.payvisionservices.com/acecontrol/dashboard',
				'manual_url'    => \__(
					'https://www.pronamic.eu/manuals/using-payvision-pronamic-pay/',
					'pronamic_ideal'
				),
				'supports'      => array(),
			)
		);

		parent::__construct( $args );
	}

	/**
	 * Setup.
	 */
	public function setup() {
		\add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Admin initialize.
	 *
	 * @return void
	 */
	public function admin_init() {
		\add_action( 'manage_pronamic_gateway_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
	}

	/**
	 * Custom columns.
	 *
	 * @param string $column  Column.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function custom_columns( $column, $post_id ) {
		$id = \get_post_meta( $post_id, '_pronamic_gateway_id', true );

		if ( $this->get_id() !== $id ) {
			return;
		}

		$config = $this->get_config( $post_id );

		switch ( $column ) {
			case 'pronamic_gateway_id':
				echo \esc_html( $config->get_business_id() );

				break;
		}
	}

	/**
	 * Get settings fields.
	 *
	 * @return array<int, array<string, callable|int|string|bool|array<int|string,int|string>>>
	 */
	public function get_settings_fields() {
		$fields = array();

		// Business Id.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => \FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_payvision_business_id',
			'title'    => \_x( 'Business Id', 'payvision', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
			'tooltip'  => \__(
				'A Merchant connecting to the platform is identified by its Business ID (“businessId”).',
				'pronamic_ideal'
			),
		);

		// User.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => \FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_payvision_username',
			'title'    => \_x( 'User', 'payvision', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
		);

		// Password.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => \FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_payvision_password',
			'title'    => \_x( 'Password', 'payvision', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
		);

		// Store Id.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => \FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_payvision_store_id',
			'title'    => \_x( 'Store ID', 'payvision', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'regular-text', 'code' ),
		);

		// Return fields.
		return $fields;
	}

	/**
	 * Get configuration by post ID.
	 *
	 * @param int $post_id Post ID.
	 * @return Config
	 */
	public function get_config( $post_id ) {
		$mode        = $this->get_meta( $post_id, 'mode' );
		$business_id = $this->get_meta( $post_id, 'payvision_business_id' );
		$username    = $this->get_meta( $post_id, 'payvision_username' );
		$password    = $this->get_meta( $post_id, 'payvision_password' );
		$store_id    = $this->get_meta( $post_id, 'payvision_store_id' );

		return new Config( $mode, $business_id, $username, $password, $store_id );
	}

	/**
	 * Get gateway.
	 *
	 * @param int $post_id Post ID.
	 * @return Gateway
	 */
	public function get_gateway( $post_id ) {
		$config = $this->get_config( $post_id );

		return new Gateway( $config );
	}
}
