<?php
if ( !isset( $_GET['id'] ) ) {
	die();
}

$id = $_GET['id'];

if ( !ctype_alnum( $id ) ) {	// The id fits [0-9A-Za-z]
	die();
}

require_once( "configuration.php" );

mysql_connect( MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD );
mysql_select_db( MYSQL_DATABASE );

if ( mysql_error() ) {
	die();
}

if ( strlen($id) == 1 ) {	// It's a temporary image with a one-character filename.
	$Q_searchImages = mysql_query( "SELECT * FROM " . MYSQL_TEMPTABLENAME . " WHERE singleByteName='" . $id . "'" );
	if ( mysql_num_rows( $Q_searchImages ) > 0 ) {	// There exists an image with this ID.
		header( "Content-type: image/png" );	// refer to upload.php for why this is a png.
		print file_get_contents( "temporaryImages/" . $id . ".png" );
	} else {
		die();	// Nothing to show.
	}
} else {
	$Q_searchImages = mysql_query( "SELECT * FROM " . MYSQQL_PERMANENTTABLENAME . " WHERE longName='" . $id . "'" );
	if ( mysql_num_rows( $Q_searchImages ) > 0 ) {
		header( "Content-type: image/png" );
		print file_get_contents("storedImages/" . $id . ".png");
	} else {
		die();	// No permanent image with this name.
	}
}
?>