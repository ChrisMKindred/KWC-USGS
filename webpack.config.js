const path     = require('path');
const defaults = require('@wordpress/scripts/config/webpack.config.js');


module.exports = {
	...defaults,
	entry: {
		...defaults.entry,
		'usgs-admin': path.resolve(__dirname, './assets/admin.js'),
		'usgs-public': path.resolve(__dirname, './assets/public.js'),
	}
};
