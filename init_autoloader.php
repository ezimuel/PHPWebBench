<?php
chdir(__DIR__);

// Composer autoloading
if (file_exists('vendor/autoload.php')) {
    include 'vendor/autoload.php';
}

if (!class_exists('\PHPWebBench\Bench')) {
    echo "Unable to load PHPWebBench. Run the following commands:\n";
    echo "$ curl -s https://getcomposer.org/installer | php\n";
    echo "$ php composer.phar install\n";
    exit(2);
}
