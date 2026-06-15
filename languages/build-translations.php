<?php
/**
 * Generatore dei file di traduzione per InstaFlow Connect.
 *
 * Produce, da un'unica sorgente:
 *   - instaflow-connect.pot        (template, msgstr vuoti)
 *   - instaflow-connect-it_IT.po   (traduzione italiana, sorgente editabile)
 *   - instaflow-connect-it_IT.mo   (binario caricato a runtime)
 *
 * Uso:  php languages/build-translations.php
 *
 * NB: dopo aver modificato le traduzioni qui sotto, rilancia lo script.
 *     In alternativa puoi modificare il .po e ricompilare con:
 *       wp i18n make-mo languages/   (oppure msgfmt)
 */

// Eseguibile solo da riga di comando: nessun effetto se richiamato via web.
if ( PHP_SAPI !== 'cli' ) {
	exit;
}

$domain = 'instaflow-connect';
$dir    = __DIR__;

// msgid (inglese, sorgente nel codice)  =>  msgstr (italiano)
$translations = [
	'Settings' => 'Impostazioni',
	'Shortcode Guide' => 'Guida Shortcode',
	'Shortcode Usage Guide' => "Guida all'utilizzo dello Shortcode",
	'Use the following shortcode to display your Instagram grid on any page or post.' => 'Usa il seguente shortcode per visualizzare la tua griglia Instagram in qualsiasi pagina o articolo.',
	'Basic Shortcode' => 'Shortcode base',
	'Displays the latest 9 posts (videos/reels excluded by default).' => 'Visualizza gli ultimi 9 post (video/reel esclusi per impostazione predefinita).',
	'Available Parameters' => 'Parametri disponibili',
	'Parameter' => 'Parametro',
	'Default Value' => 'Valore predefinito',
	'Description' => 'Descrizione',
	'The number of posts to display in the grid.' => 'Il numero di post da visualizzare nella griglia.',
	'Set <code>yes</code> to include Videos and Reels in the grid.' => 'Inserisci <code>yes</code> per includere Video e Reel nella griglia.',
	'Advanced Examples' => 'Esempi avanzati',
	'Show 12 posts including videos:' => 'Mostra 12 post inclusi i video:',
	'Show only 3 posts:' => 'Mostra solo 3 post:',
	'Power-user mode' => 'Modalità Power-user',
	'Need the raw data? Fetch your media as raw JSON, from PHP or via the REST API.' => 'Ti servono i dati grezzi? Ottieni i tuoi media come JSON RAW, da PHP o tramite la REST API.',
	'PHP function' => 'Funzione PHP',
	'Returns a JSON string of the media.' => 'Restituisce una stringa JSON dei media.',
	'REST endpoint' => 'Endpoint REST',
	'Public read-only endpoint that returns the same media as JSON.' => 'Endpoint pubblico in sola lettura che restituisce gli stessi media in JSON.',
	'Parameters' => 'Parametri',
	'Number of posts to return (default: 9).' => 'Numero di post da restituire (default: 9).',
	'Set to true/yes to include videos and reels (default: false).' => 'Imposta true/yes per includere video e reel (default: false).',
	'InstaFlow Configuration' => 'Configurazione InstaFlow',
	'Account type' => 'Tipo di account',
	'App ID' => 'App ID',
	'App Secret' => 'App Secret',
	'Webhook Verify Token' => 'Verify Token Webhook',
	'Access Token' => 'Access Token',
	'Instagram Username' => 'Username Instagram',
	'Enter the <strong>App ID</strong> found in your Facebook App basic settings.' => "Inserisci l'<strong>App ID</strong> che trovi nelle Impostazioni di base della tua App Facebook.",
	'Enter the <strong>Instagram App ID</strong> found under <strong>Instagram > API setup with Instagram login</strong> (NOT the generic Facebook App ID).' => "Inserisci l'<strong>Instagram App ID</strong> che trovi in <strong>Instagram > Configurazione API con login di Instagram</strong> (NON l'App ID generico di Facebook).",
	'Enter the <strong>App Secret</strong> found in your Facebook App basic settings.' => "Inserisci l'<strong>App Secret</strong> che trovi nelle Impostazioni di base della tua App Facebook.",
	'Enter the <strong>Instagram App Secret</strong> found under <strong>Instagram > API setup with Instagram login</strong>.' => "Inserisci l'<strong>Instagram App Secret</strong> che trovi in <strong>Instagram > Configurazione API con login di Instagram</strong>.",
	'Business / Facebook Login (Instagram Graph API)' => 'Business / Facebook Login (Instagram Graph API)',
	'Regular account (Instagram API with Instagram Login)' => 'Account normale (Instagram API con login di Instagram)',
	'Choose the integration type: Business uses Facebook Login and supports Webhooks; "Regular account" uses the Instagram API with Instagram Login (which replaced the deprecated Basic Display API).' => 'Scegli il tipo di integrazione: Business usa Facebook Login e supporta i Webhook; "Account normale" usa l\'Instagram API con login di Instagram (che ha sostituito la dismessa Basic Display API).',
	'Enter a string of your choice to use as the Verify Token in the Facebook Webhook settings.' => 'Inserisci una stringa a tua scelta da usare come Verify Token nelle impostazioni Webhook di Facebook.',
	'Enter here the token generated through Facebook for Developers (User Token).' => 'Inserisci qui il token generato tramite Facebook for Developers (User Token).',
	'Enter the Instagram username to display.' => "Inserisci l'username di Instagram da visualizzare.",
	'Connection status' => 'Stato connessione',
	'Connected' => 'Collegato',
	'as %s' => 'come %s',
	'Token updated on %s.' => 'Token aggiornato il %s.',
	'Not connected' => 'Non collegato',
	'use the button below to authorize the account.' => "usa il pulsante qui sotto per autorizzare l'account.",
	'Authentication' => 'Autenticazione',
	'Login with Facebook/Instagram (Business)' => 'Login con Facebook/Instagram (Business)',
	'Use Facebook Login to authorize the app to access Business data. Make sure the URL <code>%s</code> is listed among the Valid OAuth Redirect URIs in Facebook Login.' => "Usa Facebook Login per autorizzare l'app ad accedere ai dati Business. Assicurati che l'URL <code>%s</code> sia presente tra i Valid OAuth Redirect URIs in Facebook Login.",
	'Login with Instagram' => 'Login con Instagram',
	'Authorize via Instagram Login. Enter exactly this URL as the <strong>OAuth Redirect URI</strong> under <strong>Instagram > API setup with Instagram login</strong>: <code>%s</code> (without query parameters, because Instagram strips them).' => 'Autorizza tramite Instagram Login. Inserisci esattamente questo URL come <strong>Redirect URI di OAuth</strong> in <strong>Instagram > Configurazione API con login di Instagram</strong>: <code>%s</code> (senza parametri di query, perché Instagram li rimuove).',
	'Webhook Configuration' => 'Configurazione Webhook',
	'Callback URL:' => 'Callback URL:',
	'Verify Token:' => 'Verify Token:',
	'Copy this data into the Webhook settings of your App on Facebook for Developers for the <strong>Instagram</strong> product. When Meta asks you to verify the URL, make sure you have saved the plugin settings with the same Verify Token.' => "Copia questi dati nelle impostazioni Webhook della tua App su Facebook for Developers per il prodotto <strong>Instagram</strong>. Quando Meta ti chiede di verificare l'URL, assicurati di aver salvato le impostazioni nel plugin con lo stesso Verify Token.",
	'Error while exchanging the code: %s' => 'Errore durante lo scambio del codice: %s',
	'Token generated successfully!' => 'Token generato con successo!',
	'Short-lived token generated (long-lived exchange failed).' => 'Token di breve durata generato (scambio del long-lived fallito).',
	'Instagram error: %s' => 'Errore Instagram: %s',
	'Unexpected response from Instagram while exchanging the code.' => 'Risposta inattesa da Instagram durante lo scambio del codice.',
	'No posts to show.' => 'Nessun post da mostrare.',
	'InstaFlow Connect - Token updated' => 'InstaFlow Connect - Token aggiornato',
	'Timestamp: %s' => 'Timestamp: %s',
];

/**
 * Esegue l'escape di una stringa per il formato PO.
 */
function po_escape( $string ) {
	$string = str_replace( '\\', '\\\\', $string );
	$string = str_replace( '"', '\\"', $string );
	$string = str_replace( "\n", '\\n', $string );
	$string = str_replace( "\t", '\\t', $string );

	return $string;
}

/**
 * Scrive un file PO (o POT se $include_translations è false).
 */
function write_po( $path, $translations, $header, $include_translations ) {
	$out = "msgid \"\"\nmsgstr \"\"\n";
	foreach ( $header as $line ) {
		$out .= '"' . po_escape( $line ) . '\n"' . "\n";
	}
	$out .= "\n";

	foreach ( $translations as $msgid => $msgstr ) {
		$out .= 'msgid "' . po_escape( $msgid ) . "\"\n";
		$out .= 'msgstr "' . po_escape( $include_translations ? $msgstr : '' ) . "\"\n\n";
	}

	file_put_contents( $path, $out );
}

/**
 * Compila un array di traduzioni in formato MO binario.
 */
function write_mo( $path, $translations, $header_block ) {
	// La prima voce è l'header (msgid vuoto).
	$entries = [ '' => $header_block ];
	foreach ( $translations as $msgid => $msgstr ) {
		$entries[ $msgid ] = $msgstr;
	}

	// gettext richiede gli originali ordinati.
	$keys = array_keys( $entries );
	sort( $keys, SORT_STRING );

	$count       = count( $keys );
	$ids         = '';
	$strs        = '';
	$id_table    = [];
	$str_table   = [];

	foreach ( $keys as $key ) {
		$id_table[]  = [ strlen( $key ), strlen( $ids ) ];
		$ids        .= $key . "\0";
		$value       = $entries[ $key ];
		$str_table[] = [ strlen( $value ), strlen( $strs ) ];
		$strs       .= $value . "\0";
	}

	$header_size      = 28;
	$id_table_offset  = $header_size;
	$str_table_offset = $id_table_offset + ( $count * 8 );
	$ids_offset       = $str_table_offset + ( $count * 8 );
	$strs_offset      = $ids_offset + strlen( $ids );

	$out  = pack( 'V', 0x950412de ); // magic
	$out .= pack( 'V', 0 );          // revision
	$out .= pack( 'V', $count );     // numero di stringhe
	$out .= pack( 'V', $id_table_offset );
	$out .= pack( 'V', $str_table_offset );
	$out .= pack( 'V', 0 );          // dimensione tabella hash
	$out .= pack( 'V', $strs_offset ); // offset tabella hash (vuota)

	foreach ( $id_table as $entry ) {
		$out .= pack( 'VV', $entry[0], $ids_offset + $entry[1] );
	}
	foreach ( $str_table as $entry ) {
		$out .= pack( 'VV', $entry[0], $strs_offset + $entry[1] );
	}

	$out .= $ids;
	$out .= $strs;

	file_put_contents( $path, $out );
}

$pot_header = [
	'Project-Id-Version: TWP - InstaFlow Connect 1.9',
	'Report-Msgid-Bugs-To: ',
	'MIME-Version: 1.0',
	'Content-Type: text/plain; charset=UTF-8',
	'Content-Transfer-Encoding: 8bit',
	'X-Domain: instaflow-connect',
];

$po_header = array_merge(
	[ 'Project-Id-Version: TWP - InstaFlow Connect 1.9' ],
	[
		'MIME-Version: 1.0',
		'Content-Type: text/plain; charset=UTF-8',
		'Content-Transfer-Encoding: 8bit',
		'Language: it_IT',
		'X-Domain: instaflow-connect',
	]
);

$mo_header_block = "MIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit\nLanguage: it_IT\n";

write_po( $dir . '/instaflow-connect.pot', $translations, $pot_header, false );
write_po( $dir . '/instaflow-connect-it_IT.po', $translations, $po_header, true );
write_mo( $dir . '/instaflow-connect-it_IT.mo', $translations, $mo_header_block );

echo "Generati " . count( $translations ) . " messaggi:\n";
echo " - instaflow-connect.pot\n";
echo " - instaflow-connect-it_IT.po\n";
echo " - instaflow-connect-it_IT.mo\n";