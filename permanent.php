<?php
// When this script is accessed, it makes the requested image permanent and redirects to it.
require_once( "base62.php" );

if ( !isset( $_GET['id'] ) ) {
	die();
}

$id = $_GET['id'];

if ( !ctype_alnum( $id ) ) {
	die();
}

if ( strlen( $id ) != 1 ) {	// Iff an image is permanent, it will always have an ID with two or more characters -> if strlen($id)>1, the image is already permanent.
	die();
}

require_once( "configuration.php" );

mysql_connect( MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD );
mysql_select_db( MYSQL_DATABASE );

if ( mysql_error() ) {
	die();
}

$Q_searchImages = mysql_query( "SELECT * FROM " . MYSQL_TEMPTABLENAME . " WHERE singleByteName='" . $id . "'" );

if ( mysql_num_rows( $Q_searchImages ) > 0 ) {	// Found a temporarily-stored image with this ID
	$A_searchImages = mysql_fetch_array( $Q_searchImages );	// Get the row for this image

	mysql_query( "INSERT INTO " . MYSQL_PERMANENTTABLENAME . " VALUES( null, '', 0 )" );	// [id (auto-inc), longName (the 'proper' id), timestamp]

	$Q_getId = mysql_query("SELECT LAST_INSERT_ID();");

	if ( mysql_error() ||
			!( $A_getId = mysql_fetch_array( $Q_getId ) ) ) {
		die();
	}

	$id = $A_getId[0];
	$longName = base62_encode( ( $id - 1 ) + 62 );	// -1 to deal with one-indexing (from the auto-increment), then we add 62 to make sure it's two characters long.
	$singleByteName = $A_searchImages['singleByteName'];
	$time = time();
	mysql_query( "UPDATE " . MYSQL_PERMANENTTABLENAME . " SET longName='" . $longName . "', timestamp=" . $time . "    WHERE id=" . $id );
	if ( copy( "temporaryImages/" . $singleByteName . ".png", "storedImages/" . $longName . ".png" ) ) {
		header( "Location: /" . $longName . ".png" );
	} else {
		die();
	}
} else {
	die();	// No temp image with this ID
}
?>