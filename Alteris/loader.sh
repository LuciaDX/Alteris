#!/usr/bin/env bash
PHPBIN=/root/PHP-Bin
PHP_VERSION=$($PHPBIN/php7/bin/php -r "echo PHP_VERSION;")
echo -e "\n\e[38;5;117mUsing PHP $PHP_VERSION"
EXTENSION_DIR=$(find "$PHPBIN" -name "*debug-zts*")
grep -q '^extension_dir' $PHPBIN/php7/bin/php.ini && sed -i'bak' "s{^extension_dir=.*{extension_dir=\"$EXTENSION_DIR\"{" $PHPBIN/php7/bin/php.ini || echo "extension_dir=\"$EXTENSION_DIR\"" >> $PHPBIN/php7/bin/php.ini
$PHPBIN/php7/bin/php Alteris/Main.php
echo -e "\n\x1b[38;5;202mPacking phar...\n"
yes | $PHPBIN/php7/bin/php -dphar.readonly=0 Alteris/Modified/build/server-phar.php &>/dev/null
echo -e "\x1b[38;5;84mStarting main server...\n"
$PHPBIN/php7/bin/php PocketMine-MP.phar
