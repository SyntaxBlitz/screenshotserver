<?php
// The idea behind this script is that it pretends to be imgur's server and responds like imgur's API would.

require_once( "base62.php" );

if ( !isset( $_POST ) ||
	!isset( $_POST['key'] ) ||
	!isset( $_POST['image'] ) ) {

	die();
}

if ($_POST['key'] != "8116a978913f3cf5dfc8e1117a055056" ) {		// This is the imgur API key that Greenshot's imgur plugin uses. Feel free to change it if it's been updated!
	die();
}

require_once( "configuration.php" );

mysql_connect( MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD );
mysql_select_db( MYSQL_DATABASE );

if ( mysql_error() ) {
	die();
}

$Q_createImage = mysql_query( "INSERT INTO " . MYSQL_TEMPTABLENAME . " VALUES( null, '', 0 )" ); // [id (auto-inc), singleByteName, timestamp]
$Q_getId = mysql_query( "SELECT LAST_INSERT_ID()" );

if ( mysql_error() ||
		!( $A_getId = mysql_fetch_array( $Q_getId ) ) ) {
	die();
}
$id = $A_getId[0];

$singleByteName = base62_encode( ( $id - 1 ) % 62 );	// Compensate for auto-increment one-indexing by subtracting 1, then take mod so that the temporary location is always one character.
$timeStamp = time();									// Note that when images are made permanent, they still keep their temporary URLs until they're overwritten.

$Q_checkForOthersWithSameSingleByteName = mysql_query("SELECT * FROM " . MYSQL_TEMPTABLENAME . " WHERE singleByteName='" . $singleByteName . "'"); // sorry about the variable name. but not really.
if ( mysql_num_rows($Q_checkForOthersWithSameSingleByteName) > 0 ) {
	$A_checkForOthersWithSameSingleByteName = mysql_fetch_array( $Q_checkForOthersWithSameSingleByteName );
	$otherId = $A_checkForOthersWithSameSingleByteName['id'];
	if ( is_numeric($id) ) {	// Let's just... be VERY sure about this. I feel bad about concatenating SQL instead of using prepared statements, so I like to be especially safe.
		mysql_query("DELETE FROM " . MYSQL_TEMPTABLENAME . " WHERE singleByteName='" . $singleByteName . "'");	// Go ahead and overwite the database entry.
	}
}

mysql_query( "UPDATE temporaryImages SET singleByteName='" . $singleByteName . "', timestamp=" . $timeStamp . "   WHERE id=" . $id );
$size = file_put_contents( 'temporaryImages/' . $singleByteName . '.png', base64_decode( $_POST['image'] ) );	// SECURITY WARNING! This just starts writing stuff to your disc. If PHP is configured to execute arbitrary code in the wrong format, you're opening yourself up to attacks. DO NOT FORGET that this entire system works without authentification, and is probably not something you should be using unchanged on a production server.
// The greenshot plugin must be configured to upload as pngs, because we're saving the images as pngs and serving them with the png content type.

// From here, we just form a response the way the actual imgur server would form one, which allows greenshot to figure out the image URL for copying it into the user's clipboard.
?><?xml version="1.0" encoding="utf-8"?>

<upload><image><name/><title/><caption/><hash><?php echo time().$singleByteName; ?></hash><deletehash>null</deletehash><datetime><?php print date('Y-m-d H:i:s'); ?></datetime><type>image/png</type><animated>false</animated><width>null</width><height>null</height><size><?php echo $size; ?></size><views>0</views><bandwidth>0</bandwidth></image><links><original><?php echo FULL_BASE_URL . $singleByteName; ?>.png</original><imgur_page><?php echo echo FULL_BASE_URL . $singleByteName; ?>.png</imgur_page><delete_page>null</delete_page><small_square>null</small_square><large_thumbnail>null</large_thumbnail></links></upload>