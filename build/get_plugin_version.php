<?php

$version_checks = array(
	"kwcusgs.php" => array(
		'@Version:\s+(.*)\n@' => 'header'
	),
	"README.txt" => array(
		'@Stable tag:\s+(.*)\n@' => 'header'
	)
);

$src_dir = $argv[1] . '/src/' . $argv[2];

$version_check = null;
$messages = null;
foreach ( $version_checks as $file => $regexes ) {
	$file = "$src_dir/$file";

	if ( !file_exists( $file ) ) {
		$messages .= "Whoa! Couldn't find $file\n";
		continue;
	}

	$file_content = file_get_contents( $file );

	if ( !$file_content ) {
		$messages .= "Whoa! Could not read contents of $file\n";
		continue;
	}

	foreach ( $regexes as $regex => $context ) {
		if ( !preg_match( $regex, $file_content, $matches ) ) {
			$messages .= "Whoa! Couldn't find $context version number in $file\n";
			continue;
		}
		if( !$version_check ) {
			$version_check = trim($matches[1]);
		} else {
			if( $version_check !== trim( $matches[1] ) ){
				$messages .= "Whoa! Versions don't match find $context version number in $file\n";
			}
		}
	}
}

if( ! $messages ) {
	echo $version_check;
}
