Codeigniter-Monolog
===================

Integrates the Monolog Package (https://github.com/Seldaek/monolog) into CI by overwriting the CI_Log class.

For now, logging into a File, Syslog, Raven and into a Graylog Server (GELF) is supported.

Installation
------------

* Download Monolog using Composer into your Codeigniter root
* add to your index.php Conposers autoload:
  ```include_once './vendor/autoload.php';```
* copy over the relevant files
* have fun

License
-------

Codeigniter-Monolog is licensed under the MIT License - see the LICENSE file for details
