<?php
// echo "Publish to WP.org? (Y/n) ";
// if ( 'Y' == trim( fgets( STDIN ) ) ) {
// 	echo `svn co -q http://svn.wp-plugins.org/usgs-stream-flow-data svn`;
// 	echo `rm -R svn/trunk`;
// 	echo `mkdir svn/trunk`;
// 	echo `mkdir svn/tags/$version`;
// 	echo `rsync -r $plugin_slug/* svn/trunk/`;
// 	echo `rsync -r $plugin_slug/* svn/tags/$version`;
// 	echo `svn stat svn/ | grep '^\?' | awk '{print $2}' | xargs -I x svn add x@`;
// 	echo `svn stat svn/ | grep '^\!' | awk '{print $2}' | xargs -I x svn rm --force x@`;
// 	echo `svn stat svn/`;

// 	echo "Commit to WP.org? (Y/n)? ";
// 	if ( 'Y' == trim( fgets( STDIN ) ) ) {
// 		echo `svn ci svn/ -m "Deploy version $version"`;
// 	}
// }
