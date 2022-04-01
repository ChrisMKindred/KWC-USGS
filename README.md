# USGS Stream Flow Data WordPress Plugin

![PHP Compatibility 7.0+](https://github.com/ChrisMKindred/KWC-USGS/actions/workflows/php-compatiblity.yml/badge.svg?branch=master)
![Unit Tests](https://github.com/ChrisMKindred/KWC-USGS/actions/workflows/phpunit-tests.yml/badge.svg?branch=master)
![codecov](https://codecov.io/gh/ChrisMKindred/KWC-USGS/branch/master/graph/badge.svg)

This plugin was developed to provide Stream Flow Data to websites I had developed.

Since the origional development it has also turned into a little bit of a playground
for learning new processes while developing for WordPress.

## Testing ##

The tests are scafolded through the `wp scaffold plugin-tests` command. You can find more details on setting up your testing environment in the [WordPress Handbook](https://make.wordpress.org/cli/handbook/misc/plugin-unit-tests/#3-initialize-the-testing-environment-locally).

To setup the testing environment you will need to first run setup the testing environment.  This is done by running the `vendor/bin/install-wp-tests.sh` file including the database name, username, password, host and wordpress version.

```sh
bash vendor/bin/install-wp-tests.sh wordpress_test root '' localhost latest
```

or if you are connecting to MySQL via a socket connection

```sh
bash vendor/bin/install-wp-tests.sh wordpress_test root root localhost:/tmp/socket-address.sock latest
```

```sh
./vendor/bin/phpunit tests/test-sample
```
