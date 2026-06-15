<?php
class twp_ig_endpoint {
	private $refresh_minutes = 15;

	public function __construct() {
		$this->ensure_cache_dir();
	}

	private function ensure_cache_dir() {
		$cache_dir = IG_PLUGIN_PATH . 'instagram-cache';
		$media_dir = $cache_dir . '/media';

		if ( ! file_exists( $cache_dir ) ) {
			wp_mkdir_p( $cache_dir );
			file_put_contents( $cache_dir . '/index.php', '<?php // Silence is golden' );
		}

		if ( ! file_exists( $media_dir ) ) {
			wp_mkdir_p( $media_dir );
			file_put_contents( $media_dir . '/index.php', '<?php // Silence is golden' );
		}
	}

	private function instagram_get_by_curl( $url ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_NOSIGNAL, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT_MS, 5000 );
		$output     = curl_exec( $ch );
		$curl_errno = curl_errno( $ch );
		curl_close( $ch );
		if ( $curl_errno > 0 ) {
			return false;
		}

		return $output;
	}

	function instagram_get_media_list( $token ) {
		$account_type = get_option( 'ig-account-type', 'business' );
		if ( $account_type === 'basic' ) {
			// Basic Display API: usa l'endpoint graph.instagram.com/me/media
			$url = 'https://graph.instagram.com/me/media?fields=id,media_type,media_url,thumbnail_url,caption,permalink&access_token=' . $token;
		} else {
			// Business: serve l'IG User ID collegato
			$ig_user_id = get_option( 'ig-user-id' );
			if ( ! $ig_user_id ) {
				return false;
			}
			$url = 'https://graph.facebook.com/v18.0/' . $ig_user_id . '/media?fields=id,media_type,media_url,thumbnail_url,caption,permalink&access_token=' . $token;
		}
		$json = $this->instagram_get_by_curl( $url );
		if ( $json ) {
			$data = json_decode( $json );
			if ( isset( $data->data ) ) {
				$formatted              = new stdClass();
				$formatted->media       = new stdClass();
				$formatted->media->data = $data->data;
				file_put_contents( IG_PLUGIN_PATH . '/instagram-cache/last_download.json', json_encode( $formatted ) );

				return $formatted;
			}
		}

		return false;
	}

	function instagram_get_media_list_from_cache() {
		$file = IG_PLUGIN_PATH . '/instagram-cache/last_download.json';
		if ( ! file_exists( $file ) ) {
			return false;
		}
		$json = file_get_contents( $file );

		return json_decode( $json );
	}

	function instagram_get_media( $token, $media_id, $fields = 'media_url,thumbnail_url,media_type,caption,permalink' ) {
		$account_type = get_option( 'ig-account-type', 'business' );
		$host = ($account_type === 'basic') ? 'https://graph.instagram.com/' : 'https://graph.facebook.com/v18.0/';
		$url  = $host . $media_id . '?fields=' . $fields . '&access_token=' . $token;
		$json = $this->instagram_get_by_curl( $url );
		if ( $json ) {
			file_put_contents( IG_PLUGIN_PATH . '/instagram-cache/media/' . $media_id . '.json', $json );

			return json_decode( $json );
		}

		return false;
	}

	function instagram_get_media_from_cache( $media_id ) {
		$file = IG_PLUGIN_PATH . '/instagram-cache/media/' . $media_id . '.json';
		if ( ! file_exists( $file ) ) {
			return false;
		}
		$json = file_get_contents( $file );

		return json_decode( $json );
	}

	function instagram_refresh_access_token( $token, $refresh_after_days = 30 ) {
		$last_refresh = get_option( 'ig-token-time', 0 );
		$now          = time();
		if ( ( $now - $last_refresh ) > ( $refresh_after_days * 86400 ) ) {
			// Per entrambi i tipi si usa l'endpoint di refresh di Instagram Graph
			$url  = 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . $token;
			$json = $this->instagram_get_by_curl( $url );
			if ( $json ) {
				$data = json_decode( $json );
				if ( isset( $data->access_token ) ) {
					update_option( 'ig-access-token', $data->access_token );
					update_option( 'ig-token-time', $now );

					return true;
				}
			}
		}

		return false;
	}

	function instagram_refresh_cache() {
		$now          = time();
		$last_refresh = get_option( 'ig-cache-time', 0 );
		if ( ( $now - $last_refresh ) > ( $this->refresh_minutes * 60 ) ) {
			update_option( 'ig-cache-time', $now );

			return true;
		}

		return false;
	}

	public function setRefreshMinutes( $refresh_minutes ) {
		$this->refresh_minutes = $refresh_minutes;
	}
}

function instagram_optimize_img( $url, $width = 400, $height = 400 ) {
	$path     = parse_url( $url, PHP_URL_PATH );
	$filename = basename( $path );
	$cache_dir = IG_PLUGIN_PATH . 'instagram-cache/';
	
	if ( ! file_exists( $cache_dir ) ) {
		wp_mkdir_p( $cache_dir );
		file_put_contents( $cache_dir . '/index.php', '<?php // Silence is golden' );
	}

	if ( ! file_exists( $cache_dir . $filename ) ) {
		$img_data = @file_get_contents( $url );
		if ( $img_data ) {
			file_put_contents( IG_PLUGIN_PATH . 'instagram-cache/' . $filename, $img_data );
		}
	}

	return IG_PLUGIN_URL . 'instagram-cache/' . $filename;
}

function get_hashtags( $string, $str = true ) {
	preg_match_all( '/#(\w+)/', $string, $matches );
	if ( $str ) {
		return implode( ', ', $matches[1] );
	}

	return $matches[1];
}

function strip_hashtags( $string ) {
	return preg_replace( '/#[\w_]+[ \t]*/', '', $string );
}

function print_hashtags( $hashtags ) {
	foreach ( (array) $hashtags as $hashtag ) {
		echo '<div>#' . $hashtag . '</div>';
	}
}