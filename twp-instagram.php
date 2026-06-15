<?php
/**
 * Plugin Name: TWP - InstaFlow Connect
 * Plugin URI: https://github.com/tommasov/twp-instagram
 * Description: An elegant and fast way to integrate your Instagram feed into WordPress. Supports Reels and Carousels.
 * Version: 1.9
 * Author: Tommaso Vietina
 * Text Domain: instaflow-connect
 * Domain Path: /languages
 */

define( "IG_PLUGIN_PATH", plugin_dir_path( __FILE__ ) );
define( "IG_PLUGIN_URL", plugin_dir_url( __FILE__ ) );

require_once "ig.php";

/**
 * Carica le traduzioni del plugin.
 */
function twp_ig_load_textdomain() {
	load_plugin_textdomain( 'instaflow-connect', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'twp_ig_load_textdomain' );

function twp_enqueue_ig_css() {
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'css-twp-ig', plugins_url( 'style.css', __FILE__ ), array(), '1.9' );
}

function twp_enqueue_ig_scripts() {
	// Isotope rimosso perché inutilizzato
}

add_action( 'wp_enqueue_scripts', 'twp_enqueue_ig_css' );
add_action( 'wp_enqueue_scripts', 'twp_enqueue_ig_scripts', 100 );
/**
 * Registra l'endpoint per i webhook
 */
function twp_ig_register_webhook() {
	register_rest_route( 'twp-ig/v1', '/webhook', [
		'methods'  => 'GET,POST',
		'callback' => 'twp_ig_webhook_handler',
		'permission_callback' => '__return_true',
	] );
}
add_action( 'rest_api_init', 'twp_ig_register_webhook' );

/**
 * Gestisce le richieste al webhook
 */
function twp_ig_webhook_handler( $request ) {
	if ( $request->get_method() === 'GET' ) {
		$mode         = $request->get_param( 'hub_mode' );
		$verify_token = $request->get_param( 'hub_verify_token' );
		$challenge    = $request->get_param( 'hub_challenge' );

		$saved_token = get_option( 'ig-verify-token' );

		if ( $mode === 'subscribe' && $verify_token === $saved_token ) {
			return new WP_REST_Response( (int) $challenge, 200 );
		}

		return new WP_REST_Response( 'Forbidden', 403 );
	}

	// Logica per POST (notifiche)
	$params = $request->get_params();
	error_log( 'IG Webhook received: ' . print_r( $params, true ) );

	return new WP_REST_Response( 'OK', 200 );
}


/**
 * Aggiunge la pagina di configurazione nel menu principale di WordPress
 */
function twp_ig_add_admin_menu() {
	add_menu_page(
		'InstaFlow Connect',
		'InstaFlow Connect',
		'manage_options',
		'ig-integration',
		'twp_ig_settings_page',
		'dashicons-instagram',
		80
	);

	add_submenu_page(
		'ig-integration',
		__( 'Settings', 'instaflow-connect' ),
		__( 'Settings', 'instaflow-connect' ),
		'manage_options',
		'ig-integration',
		'twp_ig_settings_page'
	);

	add_submenu_page(
		'ig-integration',
		__( 'Shortcode Guide', 'instaflow-connect' ),
		__( 'Shortcode Guide', 'instaflow-connect' ),
		'manage_options',
		'ig-shortcode-guide',
		'twp_ig_shortcode_guide_page'
	);
}
add_action( 'admin_menu', 'twp_ig_add_admin_menu' );

/**
 * Visualizzazione della pagina Guida Shortcode
 */
function twp_ig_shortcode_guide_page() {
	?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Shortcode Usage Guide', 'instaflow-connect' ); ?></h1>
        <p><?php esc_html_e( 'Use the following shortcode to display your Instagram grid on any page or post.', 'instaflow-connect' ); ?></p>

        <div class="twp-ig-guide-layout">
            <div class="twp-ig-guide-main">
                <div class="card">
                    <h2><?php esc_html_e( 'Basic Shortcode', 'instaflow-connect' ); ?></h2>
                    <code>[twp_instagram_grid]</code>
                    <p><?php esc_html_e( 'Displays the latest 9 posts (videos/reels excluded by default).', 'instaflow-connect' ); ?></p>
                </div>

                <div class="card">
                    <h2><?php esc_html_e( 'Available Parameters', 'instaflow-connect' ); ?></h2>
                    <table class="widefat fixed" cellspacing="0">
                        <thead>
                        <tr>
                            <th class="manage-column"><?php esc_html_e( 'Parameter', 'instaflow-connect' ); ?></th>
                            <th class="manage-column"><?php esc_html_e( 'Default Value', 'instaflow-connect' ); ?></th>
                            <th class="manage-column"><?php esc_html_e( 'Description', 'instaflow-connect' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><code>count</code></td>
                            <td><code>9</code></td>
                            <td><?php esc_html_e( 'The number of posts to display in the grid.', 'instaflow-connect' ); ?></td>
                        </tr>
                        <tr>
                            <td><code>video</code></td>
                            <td><code>no</code></td>
                            <td><?php echo wp_kses_post( __( 'Set <code>yes</code> to include Videos and Reels in the grid.', 'instaflow-connect' ) ); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card">
                    <h2><?php esc_html_e( 'Advanced Examples', 'instaflow-connect' ); ?></h2>
                    <p><strong><?php esc_html_e( 'Show 12 posts including videos:', 'instaflow-connect' ); ?></strong></p>
                    <code>[twp_instagram_grid count="12" video="yes"]</code>

                    <p><strong><?php esc_html_e( 'Show only 3 posts:', 'instaflow-connect' ); ?></strong></p>
                    <code>[twp_instagram_grid count="3"]</code>
                </div>
            </div>

            <aside class="twp-ig-guide-aside">
                <div class="card twp-ig-poweruser">
                    <h2><span class="dashicons dashicons-superhero"></span> <?php esc_html_e( 'Power-user mode', 'instaflow-connect' ); ?></h2>
                    <p><?php esc_html_e( 'Need the raw data? Fetch your media as raw JSON, from PHP or via the REST API.', 'instaflow-connect' ); ?></p>

                    <h3><?php esc_html_e( 'PHP function', 'instaflow-connect' ); ?></h3>
                    <pre><code>// count, video
$json = twp_ig_get_posts_json( 12, true );</code></pre>
                    <p class="twp-ig-note"><?php esc_html_e( 'Returns a JSON string of the media.', 'instaflow-connect' ); ?></p>

                    <h3><?php esc_html_e( 'REST endpoint', 'instaflow-connect' ); ?></h3>
                    <pre><code><?php echo esc_html( get_rest_url( null, 'twp-ig/v1/posts' ) ); ?>?count=12&amp;video=yes</code></pre>
                    <p class="twp-ig-note"><?php esc_html_e( 'Public read-only endpoint that returns the same media as JSON.', 'instaflow-connect' ); ?></p>

                    <h3><?php esc_html_e( 'Parameters', 'instaflow-connect' ); ?></h3>
                    <ul>
                        <li><code>count</code> &mdash; <?php esc_html_e( 'Number of posts to return (default: 9).', 'instaflow-connect' ); ?></li>
                        <li><code>video</code> &mdash; <?php esc_html_e( 'Set to true/yes to include videos and reels (default: false).', 'instaflow-connect' ); ?></li>
                    </ul>
                </div>
            </aside>
        </div>

        <style>
            .twp-ig-guide-layout {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                align-items: flex-start;
                margin-top: 20px;
            }
            .twp-ig-guide-main {
                flex: 1 1 520px;
                min-width: 0;
            }
            .twp-ig-guide-aside {
                flex: 0 1 360px;
            }
            .card {
                margin-top: 20px;
                padding: 20px;
                background: #fff;
                border: 1px solid #ccd0d4;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .twp-ig-guide-main .card {
                max-width: 800px;
            }
            code {
                display: inline-block;
                padding: 5px 10px;
                background: #f0f0f1;
                border-radius: 4px;
                font-size: 1.1em;
                margin: 10px 0;
            }
            .twp-ig-poweruser {
                background: #1d2327;
                border-color: #1d2327;
                color: #dcdcde;
            }
            .twp-ig-poweruser h2,
            .twp-ig-poweruser h3 {
                color: #fff;
            }
            .twp-ig-poweruser h3 {
                margin-bottom: 4px;
            }
            .twp-ig-poweruser .dashicons {
                color: #f0c33c;
                vertical-align: middle;
            }
            .twp-ig-poweruser pre {
                background: #2c3338;
                border-radius: 4px;
                padding: 12px;
                margin: 0;
                overflow: auto;
            }
            .twp-ig-poweruser pre code {
                display: block;
                padding: 0;
                margin: 0;
                background: none;
                color: #7ec699;
                font-size: .95em;
                white-space: pre-wrap;
                word-break: break-all;
            }
            .twp-ig-poweruser code {
                background: #2c3338;
                color: #7ec699;
                margin: 0;
            }
            .twp-ig-poweruser .twp-ig-note {
                font-style: italic;
                color: #a7aaad;
                margin-top: 6px;
            }
            .twp-ig-poweruser ul {
                list-style: disc;
                margin-left: 20px;
            }
        </style>
    </div>
	<?php
}

/**
 * Registra le impostazioni
 */
function twp_ig_settings_init() {
	register_setting( 'twp_ig_settings_group', 'ig-access-token' );
	register_setting( 'twp_ig_settings_group', 'ig-app-id' );
	register_setting( 'twp_ig_settings_group', 'ig-app-secret' );
	register_setting( 'twp_ig_settings_group', 'ig-verify-token' );
	register_setting( 'twp_ig_settings_group', 'ig-username' );
	register_setting( 'twp_ig_settings_group', 'ig-account-type' );

	add_settings_section(
		'twp_ig_settings_section',
		__( 'InstaFlow Configuration', 'instaflow-connect' ),
		null,
		'ig-integration'
	);

	add_settings_field(
		'ig-account-type',
		__( 'Account type', 'instaflow-connect' ),
		'twp_ig_account_type_render',
		'ig-integration',
		'twp_ig_settings_section'
	);

	add_settings_field(
		'ig-app-id',
		__( 'App ID', 'instaflow-connect' ),
		'twp_ig_app_id_render',
		'ig-integration',
		'twp_ig_settings_section'
	);


	add_settings_field(
		'ig-app-secret',
		__( 'App Secret', 'instaflow-connect' ),
		'twp_ig_app_secret_render',
		'ig-integration',
		'twp_ig_settings_section'
	);


	add_settings_field(
		'ig-verify-token',
		__( 'Webhook Verify Token', 'instaflow-connect' ),
		'twp_ig_verify_token_render',
		'ig-integration',
		'twp_ig_settings_section',
		array( 'class' => 'twp-ig-business-only' )
	);
	add_settings_field(
		'ig-access-token',
		__( 'Access Token', 'instaflow-connect' ),
		'twp_ig_token_render',
		'ig-integration',
		'twp_ig_settings_section',
		array( 'class' => 'twp-ig-business-only' )
	);
	add_settings_field(
		'ig-username',
		__( 'Instagram Username', 'instaflow-connect' ),
		'twp_ig_username_render',
		'ig-integration',
		'twp_ig_settings_section'
	);
	
}
add_action( 'admin_init', 'twp_ig_settings_init' );

/**
 * Render del campo App ID
 */
function twp_ig_app_id_render() {
	$app_id = get_option( 'ig-app-id' );
	$account_type = get_option( 'ig-account-type', 'business' );
	?>
    <input type='text' name='ig-app-id' value='<?php echo esc_attr( $app_id ); ?>' style="width: 100%; max-width: 600px;">
    <p class="description" id="ig-app-id-desc">
		<?php if ( $account_type === 'business' ): ?>
			<?php echo wp_kses_post( __( 'Enter the <strong>App ID</strong> found in your Facebook App basic settings.', 'instaflow-connect' ) ); ?>
		<?php else: ?>
			<?php echo wp_kses_post( __( 'Enter the <strong>Instagram App ID</strong> found under <strong>Instagram > API setup with Instagram login</strong> (NOT the generic Facebook App ID).', 'instaflow-connect' ) ); ?>
		<?php endif; ?>
    </p>
	<?php
}

/**
 * Render del campo App Secret
 */
function twp_ig_app_secret_render() {
	$app_secret = get_option( 'ig-app-secret' );
	$account_type = get_option( 'ig-account-type', 'business' );
	?>
    <input type='password' name='ig-app-secret' value='<?php echo esc_attr( $app_secret ); ?>' style="width: 100%; max-width: 600px;">
    <p class="description" id="ig-app-secret-desc">
		<?php if ( $account_type === 'business' ): ?>
			<?php echo wp_kses_post( __( 'Enter the <strong>App Secret</strong> found in your Facebook App basic settings.', 'instaflow-connect' ) ); ?>
		<?php else: ?>
			<?php echo wp_kses_post( __( 'Enter the <strong>Instagram App Secret</strong> found under <strong>Instagram > API setup with Instagram login</strong>.', 'instaflow-connect' ) ); ?>
		<?php endif; ?>
    </p>
	<?php
}

/**
 * Render del campo Tipo di account
 */
function twp_ig_account_type_render() {
	$account_type = get_option( 'ig-account-type', 'business' );
	?>
    <label>
        <input type="radio" name="ig-account-type" value="business" <?php checked( $account_type, 'business' ); ?>>
		<?php esc_html_e( 'Business / Facebook Login (Instagram Graph API)', 'instaflow-connect' ); ?>
    </label>
    <br>
    <label>
        <input type="radio" name="ig-account-type" value="basic" <?php checked( $account_type, 'basic' ); ?>>
		<?php esc_html_e( 'Regular account (Instagram API with Instagram Login)', 'instaflow-connect' ); ?>
    </label>
    <p class="description"><?php esc_html_e( 'Choose the integration type: Business uses Facebook Login and supports Webhooks; "Regular account" uses the Instagram API with Instagram Login (which replaced the deprecated Basic Display API).', 'instaflow-connect' ); ?></p>
	<?php
}

/**
 * Render del campo Access Token
 */

/**
 * Render del campo Verify Token
 */
function twp_ig_verify_token_render() {
	$token = get_option( 'ig-verify-token' );
	?>
    <input type='text' name='ig-verify-token' value='<?php echo esc_attr( $token ); ?>' style="width: 100%; max-width: 600px;">
    <p class="description"><?php esc_html_e( 'Enter a string of your choice to use as the Verify Token in the Facebook Webhook settings.', 'instaflow-connect' ); ?></p>
	<?php
}

function twp_ig_token_render() {
	$token = get_option( 'ig-access-token' );
	?>
    <input type='text' name='ig-access-token' value='<?php echo esc_attr( $token ); ?>' style="width: 100%; max-width: 600px;">
    <p class="description"><?php esc_html_e( 'Enter here the token generated through Facebook for Developers (User Token).', 'instaflow-connect' ); ?></p>
	<?php
}

/**
 * Render del campo Username
 */
function twp_ig_username_render() {
	$username = get_option( 'ig-username' );
	?>
    <input type='text' name='ig-username' value='<?php echo esc_attr( $username ); ?>' style="width: 100%; max-width: 600px;">
    <p class="description"><?php esc_html_e( 'Enter the Instagram username to display.', 'instaflow-connect' ); ?></p>
	<?php
}

/**
 * Visualizzazione della pagina di impostazioni
 */
function twp_ig_settings_page() {
	$app_id       = get_option( 'ig-app-id' );
	$app_secret   = get_option( 'ig-app-secret' );
	$account_type = get_option( 'ig-account-type', 'business' );
	$redirect_uri = twp_ig_get_redirect_uri();

	// Esito dell'autenticazione (impostato in twp_ig_maybe_handle_oauth_callback).
	$auth_notice = get_transient( 'twp_ig_oauth_notice' );
	if ( $auth_notice ) {
		delete_transient( 'twp_ig_oauth_notice' );
	}

	?>
    <style>
        .twp-ig-business-only {
            display: <?php echo ($account_type === 'business') ? 'table-row' : 'none'; ?>;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.querySelectorAll('input[name="ig-account-type"]');
            const businessRows = document.querySelectorAll('.twp-ig-business-only');
            const webhookSection = document.getElementById('twp-ig-webhook-config');
            const appIdDesc = document.getElementById('ig-app-id-desc');
            const appSecretDesc = document.getElementById('ig-app-secret-desc');

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const isBusiness = (this.value === 'business');
                    businessRows.forEach(row => {
                        row.style.display = isBusiness ? 'table-row' : 'none';
                    });
                    if (webhookSection) {
                        webhookSection.style.display = isBusiness ? 'block' : 'none';
                    }
                    if (appIdDesc) {
                        appIdDesc.innerHTML = isBusiness
                            ? <?php echo wp_json_encode( __( 'Enter the <strong>App ID</strong> found in your Facebook App basic settings.', 'instaflow-connect' ) ); ?>
                            : <?php echo wp_json_encode( __( 'Enter the <strong>Instagram App ID</strong> found under <strong>Instagram > API setup with Instagram login</strong> (NOT the generic Facebook App ID).', 'instaflow-connect' ) ); ?>;
                    }
                    if (appSecretDesc) {
                        appSecretDesc.innerHTML = isBusiness
                            ? <?php echo wp_json_encode( __( 'Enter the <strong>App Secret</strong> found in your Facebook App basic settings.', 'instaflow-connect' ) ); ?>
                            : <?php echo wp_json_encode( __( 'Enter the <strong>Instagram App Secret</strong> found under <strong>Instagram > API setup with Instagram login</strong>.', 'instaflow-connect' ) ); ?>;
                    }
                });
            });
        });
    </script>
    <div class="wrap">
        <h1>InstaFlow Connect</h1>
		<?php if ( $auth_notice ) : ?>
            <div class="notice notice-<?php echo ( isset( $auth_notice['type'] ) && $auth_notice['type'] === 'success' ) ? 'success' : 'error'; ?> is-dismissible">
                <p><?php echo esc_html( isset( $auth_notice['message'] ) ? $auth_notice['message'] : '' ); ?></p>
            </div>
		<?php endif; ?>
        <form action='options.php' method='post'>
			<?php
			settings_fields( 'twp_ig_settings_group' );
			do_settings_sections( 'ig-integration' );
			submit_button();
			?>
        </form>

		<?php if ( $app_id && $app_secret ): ?>
            <hr>
            <h2><?php esc_html_e( 'Connection status', 'instaflow-connect' ); ?></h2>
			<?php
			$has_token     = (bool) get_option( 'ig-access-token' );
			$conn_username = get_option( 'ig-account-username' );
			$token_time    = (int) get_option( 'ig-token-time', 0 );

			// Popola lo username se manca (es. token ottenuto prima di questa funzionalità).
			if ( $has_token && ! $conn_username ) {
				twp_ig_store_account_username( get_option( 'ig-access-token' ) );
				$conn_username = get_option( 'ig-account-username' );
			}
			?>
			<?php if ( $has_token ) : ?>
                <p>
                    <span class="dashicons dashicons-yes-alt" style="color:#46b450;vertical-align:middle;"></span>
                    <strong style="color:#46b450;"><?php esc_html_e( 'Connected', 'instaflow-connect' ); ?></strong>
					<?php if ( $conn_username ) : ?>
						<?php
						printf(
							/* translators: %s: Instagram account link, e.g. @username */
							esc_html__( 'as %s', 'instaflow-connect' ),
							'<a href="https://www.instagram.com/' . esc_attr( $conn_username ) . '/" target="_blank" rel="noopener">@' . esc_html( $conn_username ) . '</a>'
						);
						?>
					<?php endif; ?>
                </p>
				<?php if ( $token_time ) : ?>
                    <p class="description">
						<?php
						printf(
							/* translators: %s: date/time the token was updated */
							esc_html__( 'Token updated on %s.', 'instaflow-connect' ),
							esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $token_time ) )
						);
						?>
                    </p>
				<?php endif; ?>
			<?php else : ?>
                <p>
                    <span class="dashicons dashicons-warning" style="color:#dc3232;vertical-align:middle;"></span>
                    <strong style="color:#dc3232;"><?php esc_html_e( 'Not connected', 'instaflow-connect' ); ?></strong>
                    &mdash; <?php esc_html_e( 'use the button below to authorize the account.', 'instaflow-connect' ); ?>
                </p>
			<?php endif; ?>

            <hr>
            <h2><?php esc_html_e( 'Authentication', 'instaflow-connect' ); ?></h2>
			<?php
			$state = wp_create_nonce( 'twp_ig_oauth' );
			if ( $account_type === 'business' ) {
				$auth_url = "https://www.facebook.com/v18.0/dialog/oauth?client_id={$app_id}&redirect_uri=" . urlencode( $redirect_uri ) . "&scope=instagram_basic,pages_show_list,public_profile&response_type=code&state={$state}";
				$login_label = __( 'Login with Facebook/Instagram (Business)', 'instaflow-connect' );
				$login_help  = sprintf(
					/* translators: %s: OAuth redirect URI */
					wp_kses_post( __( 'Use Facebook Login to authorize the app to access Business data. Make sure the URL <code>%s</code> is listed among the Valid OAuth Redirect URIs in Facebook Login.', 'instaflow-connect' ) ),
					esc_url( $redirect_uri )
				);
			} else {
				$auth_url = "https://www.instagram.com/oauth/authorize?client_id={$app_id}&redirect_uri=" . urlencode( $redirect_uri ) . "&scope=instagram_business_basic&response_type=code&state={$state}";
				$login_label = __( 'Login with Instagram', 'instaflow-connect' );
				$login_help  = sprintf(
					/* translators: %s: OAuth redirect URI */
					wp_kses_post( __( 'Authorize via Instagram Login. Enter exactly this URL as the <strong>OAuth Redirect URI</strong> under <strong>Instagram > API setup with Instagram login</strong>: <code>%s</code> (without query parameters, because Instagram strips them).', 'instaflow-connect' ) ),
					esc_url( $redirect_uri )
				);
			}
			?>
            <a href="<?php echo esc_url( $auth_url ); ?>" class="button button-primary"><?php echo esc_html( $login_label ); ?></a>
            <p class="description"><?php echo $login_help; ?></p>
            <div id="twp-ig-webhook-config" style="display: <?php echo ($account_type === 'business') ? 'block' : 'none'; ?>;">
                <hr>
                <h2><?php esc_html_e( 'Webhook Configuration', 'instaflow-connect' ); ?></h2>
                <p><strong><?php esc_html_e( 'Callback URL:', 'instaflow-connect' ); ?></strong> <code><?php echo esc_url( get_rest_url( null, 'twp-ig/v1/webhook' ) ); ?></code></p>
                <p><strong><?php esc_html_e( 'Verify Token:', 'instaflow-connect' ); ?></strong> <code><?php echo esc_html( get_option( 'ig-verify-token', 'ChooseASecureToken' ) ); ?></code></p>
                <p class="description"><?php echo wp_kses_post( __( 'Copy this data into the Webhook settings of your App on Facebook for Developers for the <strong>Instagram</strong> product. When Meta asks you to verify the URL, make sure you have saved the plugin settings with the same Verify Token.', 'instaflow-connect' ) ); ?></p>
            </div>
		<?php endif; ?>
    </div>
	<?php
}

/**
 * Recupera lo username dell'account collegato e lo salva.
 *
 * Popola "ig-account-username" (usato per lo stato connessione) e, se vuoto,
 * anche "ig-username" (usato per il link al profilo nella griglia).
 *
 * @param string $token
 */
function twp_ig_store_account_username( $token ) {
	$account_type = get_option( 'ig-account-type', 'business' );
	$host         = ( $account_type === 'business' ) ? 'https://graph.facebook.com/v18.0/me' : 'https://graph.instagram.com/me';

	$response = wp_remote_get( $host . '?fields=username&access_token=' . urlencode( $token ), [ 'timeout' => 15 ] );
	if ( is_wp_error( $response ) ) {
		return;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ) );
	if ( ! isset( $body->username ) ) {
		return;
	}

	update_option( 'ig-account-username', $body->username );
	if ( ! get_option( 'ig-username' ) ) {
		update_option( 'ig-username', $body->username );
	}
}

/**
 * Restituisce la redirect URI da usare nel flusso OAuth.
 *
 * Per "Account normale" (Instagram Login) si usa un URL senza query string,
 * perché Instagram rimuove i parametri di query dalla redirect URI: il codice
 * viene poi intercettato globalmente in admin_init.
 *
 * @return string
 */
function twp_ig_get_redirect_uri() {
	$account_type = get_option( 'ig-account-type', 'business' );
	if ( $account_type === 'business' ) {
		return admin_url( 'admin.php?page=ig-integration' );
	}

	return admin_url( 'admin.php' );
}

/**
 * Intercetta il ritorno da Instagram/Facebook su qualsiasi pagina admin.
 *
 * Necessario perché Instagram rimanda all'admin.php senza il parametro "page",
 * quindi la pagina delle impostazioni non verrebbe caricata.
 */
function twp_ig_maybe_handle_oauth_callback() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( empty( $_GET['code'] ) || empty( $_GET['state'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['state'] ) ), 'twp_ig_oauth' ) ) {
		return;
	}

	$code         = sanitize_text_field( wp_unslash( $_GET['code'] ) );
	$redirect_uri = twp_ig_get_redirect_uri();

	$result = twp_ig_handle_auth_callback( $code, $redirect_uri );
	set_transient( 'twp_ig_oauth_notice', $result, 60 );

	wp_safe_redirect( admin_url( 'admin.php?page=ig-integration' ) );
	exit;
}
add_action( 'admin_init', 'twp_ig_maybe_handle_oauth_callback' );

/**
 * Gestisce la callback di autenticazione.
 *
 * @param string $code
 * @param string $redirect_uri
 *
 * @return array{type:string,message:string}
 */
function twp_ig_handle_auth_callback( $code, $redirect_uri ) {
	$app_id       = get_option( 'ig-app-id' );
	$app_secret   = get_option( 'ig-app-secret' );
	$account_type = get_option( 'ig-account-type', 'business' );

	if ( $account_type === 'business' ) {
		$response = wp_remote_post( 'https://graph.facebook.com/v18.0/oauth/access_token', [
			'body' => [
				'client_id'     => $app_id,
				'client_secret' => $app_secret,
				'grant_type'    => 'authorization_code',
				'redirect_uri'  => $redirect_uri,
				'code'          => $code,
			],
			'timeout' => 45,
		] );
	} else {
		$response = wp_remote_post( 'https://api.instagram.com/oauth/access_token', [
			'body' => [
				'client_id'     => $app_id,
				'client_secret' => $app_secret,
				'grant_type'    => 'authorization_code',
				'redirect_uri'  => $redirect_uri,
				'code'          => $code,
			],
			'timeout' => 45,
		] );
	}

	if ( is_wp_error( $response ) ) {
		return [
			'type'    => 'error',
			'message' => sprintf(
				/* translators: %s: error message */
				__( 'Error while exchanging the code: %s', 'instaflow-connect' ),
				$response->get_error_message()
			),
		];
	}

	$body = json_decode( wp_remote_retrieve_body( $response ) );

	// La nuova Instagram Login API può restituire i dati incapsulati in "data".
	if ( $account_type !== 'business' && isset( $body->data[0]->access_token ) ) {
		$body = $body->data[0];
	}

	if ( isset( $body->access_token ) ) {
		// Exchange/refresh to long-lived
		if ( $account_type === 'business' ) {
			$long_lived_response = wp_remote_get( "https://graph.facebook.com/v18.0/oauth/access_token?grant_type=fb_exchange_token&client_id={$app_id}&client_secret={$app_secret}&fb_exchange_token={$body->access_token}" );
		} else {
			$long_lived_response = wp_remote_get( "https://graph.instagram.com/access_token?grant_type=ig_exchange_token&client_secret={$app_secret}&access_token={$body->access_token}" );
		}

		$final_token = null;
		$message     = '';
		if ( ! is_wp_error( $long_lived_response ) ) {
			$long_lived_body = json_decode( wp_remote_retrieve_body( $long_lived_response ) );
			if ( isset( $long_lived_body->access_token ) ) {
				$final_token = $long_lived_body->access_token;
				$message     = __( 'Token generated successfully!', 'instaflow-connect' );
			}
		}

		// Fallback se il long-lived fallisce
		if ( null === $final_token ) {
			$final_token = $body->access_token;
			$message     = __( 'Short-lived token generated (long-lived exchange failed).', 'instaflow-connect' );
		}

		update_option( 'ig-access-token', $final_token );
		update_option( 'ig-token-time', time() );
		twp_ig_store_account_username( $final_token );

		return [ 'type' => 'success', 'message' => $message ];
	} elseif ( isset( $body->error_message ) ) {
		return [
			'type'    => 'error',
			/* translators: %s: error message returned by Instagram */
			'message' => sprintf( __( 'Instagram error: %s', 'instaflow-connect' ), $body->error_message ),
		];
	} elseif ( isset( $body->error->message ) ) {
		return [
			'type'    => 'error',
			/* translators: %s: error message returned by Instagram */
			'message' => sprintf( __( 'Instagram error: %s', 'instaflow-connect' ), $body->error->message ),
		];
	}

	return [ 'type' => 'error', 'message' => __( 'Unexpected response from Instagram while exchanging the code.', 'instaflow-connect' ) ];
}

/**
 * Shortcode per visualizzare la griglia di Instagram
 */
function twp_ig_grid_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'count' => 9,
		'video' => 'no',
	), $atts, 'twp_instagram_grid' );

	$count = intval( $atts['count'] );
	$video = ( $atts['video'] === 'yes' );

	$ig_post_list = get_twp_ig_posts( null, false, $count, $video );

	if ( ! $ig_post_list ) {
		return '<p>' . esc_html__( 'No posts to show.', 'instaflow-connect' ) . '</p>';
	}

	ob_start();
	?>
    <div id="twp-ig-grid-container">
        <?php foreach ( $ig_post_list as $media ) : ?>
            <div class="twp-ig-post">
                <a href="<?php echo esc_url( $media->permalink ); ?>" target="_blank" rel="noopener">
                    <div class="twp-ig-square" style="background-image: url('<?php echo esc_url( instagram_optimize_img( $media->display_url ) ); ?>');">
                        <?php if ( $media->media_type === 'CAROUSEL_ALBUM' ) : ?>
                            <div class="twp-ig-icon"><span class="dashicons dashicons-images-alt2"></span></div>
                        <?php elseif ( $media->media_type === 'VIDEO' ) : ?>
                            <div class="twp-ig-icon"><span class="dashicons dashicons-video-alt3"></span></div>
                        <?php endif; ?>
                        <div class="twp-ig-overlay">
                            <span class="twp-ig-caption">
                                <?php echo esc_html( wp_trim_words( $media->caption, 15, '...' ) ); ?>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
	<?php
	$username = get_option( 'ig-username' );
	if ( $username ) :
		?>
        <div id="twp-ig-profile-wrapper">
            <a href="https://www.instagram.com/<?php echo esc_attr( $username ); ?>/" target="_blank" rel="noopener" id="ig-profile-link">
                <span class="dashicons dashicons-instagram"></span>
                @<?php echo esc_html( $username ); ?>
            </a>
        </div>
	<?php
	endif;

	return ob_get_clean();
}
add_shortcode( 'twp_instagram_grid', 'twp_ig_grid_shortcode' );

/**
 * @param      $token
 * @param bool $cache
 * @param int  $max_posts
 * @param bool $include_video
 *
 * @return array
 */
function prepare_twp_ig_posts( $token, bool $cache = false, int $max_posts = 6, bool $include_video = false ): array {
	$twp_ig_endpoint = new twp_ig_endpoint();
	$posts_list      = array();
	
	if ( $cache ) {
		//Read cached media list
		$list = $twp_ig_endpoint->instagram_get_media_list_from_cache();
	} else {
		//Update the media list
		$list = $twp_ig_endpoint->instagram_get_media_list( $token );
		
		//Fallback to cache
		if ( ! $list ) {
			$list  = $twp_ig_endpoint->instagram_get_media_list_from_cache();
			$cache = true;
		}
	}
	
	if ( is_array( $list->media->data ) ) {
		
		$i = 1;
		
		foreach ( $list->media->data as $key => $value ) {
			
			if ( $i > $max_posts ) {
				break;
			}
			
			if ( $cache ) {
				$media = $twp_ig_endpoint->instagram_get_media_from_cache( $value->id );
			} else {
				$media = $twp_ig_endpoint->instagram_get_media( $token, $value->id );
			}
			
			if ( ! $media ) {
				continue;
			}

			// Aggiungo supporto per IMAGE, VIDEO (Reel) e CAROUSEL_ALBUM
			if ( in_array( $media->media_type, array( "IMAGE", "VIDEO", "CAROUSEL_ALBUM" ) ) ) {
				if ( $media->media_type === "VIDEO" && ! $include_video ) {
					continue;
				}

				// Per i video e i caroselli, cerco di usare la thumbnail se disponibile
				if ( ( $media->media_type === "VIDEO" || $media->media_type === "CAROUSEL_ALBUM" ) && ! empty( $media->thumbnail_url ) ) {
					$media->display_url = $media->thumbnail_url;
				} else {
					$media->display_url = $media->media_url;
				}

				$i ++;
				$posts_list[] = $media;
			}
		}
	}
	
	return $posts_list;
}

/**
 * @param      $token
 *
 * @param bool $refresh_notification
 *
 * @param int  $max_posts
 *
 * @param bool $video
 *
 * @return array|bool
 */
function get_twp_ig_posts( $token = null, bool $refresh_notification = false, int $max_posts = 10, bool $video = false ) {
	if ( null === $token ) {
		$token = get_option( 'ig-access-token' );
	}

	if ( ! $token ) {
		return false;
	}

	$twp_ig_endpoint = new twp_ig_endpoint();
	
	try {
		$access_token_refresh = $twp_ig_endpoint->instagram_refresh_access_token( $token );
		
		if ( $access_token_refresh ) {
			$last_refresh = get_option( "ig-token-time" );
			
			if ( $refresh_notification ) {
				
				wp_mail(
					get_option( "admin_email" ),
					get_option( "blogname" ) . ': ' . __( 'InstaFlow Connect - Token updated', 'instaflow-connect' ),
					sprintf(
						/* translators: %s: token refresh timestamp */
						__( 'Timestamp: %s', 'instaflow-connect' ),
						$last_refresh
					)
				);
			}
			
		}
	} catch ( Exception $e ) {
		print_r( $e );
	}
	
	try {
		if ( $twp_ig_endpoint->instagram_refresh_cache() ) {
			return prepare_twp_ig_posts( $token, false, $max_posts, $video );
		} else {
			return prepare_twp_ig_posts( "", true, $max_posts, $video );
		}
	} catch ( Exception $e ) {
		print_r( $e );
	}

	return false;

}

/**
 * Poweruser: restituisce i media Instagram come stringa JSON RAW.
 *
 * @param int  $count Numero di post da restituire.
 * @param bool $video Includere video e reel.
 *
 * @return string JSON dei media (array vuoto se non disponibili).
 */
function twp_ig_get_posts_json( $count = 9, $video = false ) {
	$posts = get_twp_ig_posts( null, false, (int) $count, (bool) $video );
	if ( ! is_array( $posts ) ) {
		$posts = array();
	}

	return wp_json_encode( array_values( $posts ) );
}

/**
 * Poweruser: registra l'endpoint REST che restituisce i media in JSON.
 *
 * GET /wp-json/twp-ig/v1/posts?count=9&video=yes
 */
function twp_ig_register_posts_endpoint() {
	register_rest_route( 'twp-ig/v1', '/posts', [
		'methods'             => 'GET',
		'callback'            => 'twp_ig_posts_rest_handler',
		'permission_callback' => '__return_true',
		'args'                => [
			'count' => [
				'default'           => 9,
				'sanitize_callback' => 'absint',
			],
			'video' => [
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
			],
		],
	] );
}
add_action( 'rest_api_init', 'twp_ig_register_posts_endpoint' );

/**
 * Handler dell'endpoint REST dei media.
 *
 * @param WP_REST_Request $request
 *
 * @return WP_REST_Response
 */
function twp_ig_posts_rest_handler( $request ) {
	$count = (int) $request->get_param( 'count' );
	$video = (bool) $request->get_param( 'video' );

	$posts = get_twp_ig_posts( null, false, $count, $video );
	if ( ! is_array( $posts ) ) {
		$posts = array();
	}

	return rest_ensure_response( array_values( $posts ) );
}
