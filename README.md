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

In order to install the software you need to use [Composer](http://getcomposer.org/),
running the following commands:

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

PHPWebBench can be also used with a PHP configuration file. In the example folder you can
find a configuration file test.php that contains the following
values:

```php
return array(
    array(
        'name' => 'Google',
        'url'  => 'http://www.google.com'
    ),
    array(
        'name' => 'Yahoo',
        'url'  => 'http://www.yahoo.com'
    ),
    array(
        'name' => 'Bing',
        'url'  => 'http://www.bing.com'
    )
);
```

In order to execute the benchmark test using a configuration file you need to
use the -b option. For instance, to run the example/test.php file:

    $ php runbench.php -b example/test.php

The previous command execute the test stored in the data/test.php file using 3
requests and only one concurrent client.

### OUTPUT

The output of the script is a collection of information regarding the performance
results. Below is reported an example of execution:

    PHPWebBench 0.1a by Enrico Zimuel.

    Running test Google...done
    Running test Yahoo...done
    Running test Bing...done

    RESULTS:
    --------
    Number of requests   : 3
    Concurrency level    : 1
    Time for tests       : 7.78 sec

    Test name            : Google
    Success response 2xx : 3
    Document size        : 42.38 [Kb] 
    Total transferred    : 129.38 [Kb] 
    Requests per second  : 4.01 [#/sec]
    Time per request     : 248.94 ± 53.66 [msec] (mean)
    Transfer rate        : 173.13 [Kb/sec]

    Test name            : Yahoo
    Success response 2xx : 3
    Document size        : 389.99 [Kb] 
    Total transferred    : 1.14 [Mb] 
    Requests per second  : 0.54 [#/sec]
    Time per request     : 1859.29 ± 660.45 [msec] (mean)
    Transfer rate        : 210.00 [Kb/sec]

    Test name            : Bing
    Success response 2xx : 3
    Document size        : 31.47 [Kb] 
    Total transferred    : 97.28 [Kb] 
    Requests per second  : 2.06 [#/sec]
    Time per request     : 485.84 ± 101.85 [msec] (mean)
    Transfer rate        : 66.73 [Kb/sec]


The information reported in the output are quite similar to the
[Apache benchmark](http://httpd.apache.org/docs/2.2/programs/ab.html) output.

The most important value for performance consideration is the *Time per request*
that give you an idea of the average response time on each HTTP request.

### REQUIREMENT

 - PHP >= 5.3.3

PHPWebBench uses the Zend\Console component of Zend Framework 2 in order to manage the
options of the command line. 

### LICENSE

The files in this archive are released under the
[BSD 3-Clause License](http://opensource.org/licenses/BSD-3-Clause).
