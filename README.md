### PHPWebBench

PHPWebBench is a PHP platform to execute performance tests of HTTP web applications.
It performs HTTP requests using different methods (GET, POST, PUT, DELETE) and retrieve
information about the response time, the bytes downloaded, the transfer rate, etc.


### RELEASE INFORMATION

*PHPWebBench 0.1a*

This is the first alpha version of the software. This means that it is *ready enough* to start
testing and vetting the library, but not feature complete and not production ready.

As this software is **ALPHA**, **Use at your own risk**!


## INSTALL

In order to install the software you need to use [Composer][1], running the following commands:

    $ curl -s https://getcomposer.org/installer | php
    $ php composer.phar install

Composer will install the dependencies of the project inside the vendor folder.
After that you can start to use PHPWebBench from the command line using the *runbench.php*
script.

### USAGE

Print the list of all the options.

    $ php runbench.php -h

Execute a test on a single URL (by default it execute 3 requests using 1 client).

    $ php runbench.php -u http://url-to-test

Execute a test on a single URL using 100 requests and 10 concurrent clients.

    $ php runbench.php -n 100 -c 10 -u http://url-to-test

PHPWebBench can be also used with a PHP configuration file.
Usually these files are stored in the data folder and contains the following
values:

```php
return array(
    array(
        'name' => 'first test',
        'url'  => 'http://first-url-to-test'
    ),
    array(
        'name' => 'second test',
        'url'  => 'http://second-url-to-test'
    )
);

In order to execute the benchmark test using a configuration file you need to
use the -b option as showed in the following command:

    $ php runbench.php -b data/test.php -n 100 -c 10

The previous command execute the test stored in the data/test.php file using 100
requests and 10 concurrent clients.

### OUTPUT

The output of the script is a collection of information regarding the performance
results. Below is reported an example:

    PHPWebBench - version 0.1a by Enrico Zimuel

    Number of requests  : 3
    Concurrency level   : 1
    Time for tests      : 1.55 sec

    Test name           : Testing URL
    Complete requests   : 3
    Failed requests     : 0
    HTML transferred    : 332362 bytes 
    Requests per second : 1.93 [#/sec]
    Time per request    : 517.52 [msec] (mean +/- 12.08 [msec])
    Time per request    : 517.52 [msec] (mean, across all concurrent requests)
    Transfer rate       : 627.57 [Kb]/sec

The output is quite similar to the [Apache benchmark][2] output.

### REQUIREMENT

 - PHP >= 5.3.3

PHPWebBench uses the Zend\Console component of Zend Framework 2 in order to manage the
options of the command line. 

### LICENSE

The files in this archive are released under the [BSD 3-Clause License][3].

    [1]: http://getcomposer.org/ "Composer"
    [2]: http://httpd.apache.org/docs/2.2/programs/ab.html "Apache benchmark"
    [3]: http://opensource.org/licenses/BSD-3-Clause "BSD 3-Clause License"
