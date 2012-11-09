<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Codeigniter-Monolog integration package
 * 
 * (c) Andreas Pfotenhauer <pfote@ypsilon.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/* GENERAL OPTIONS */

$config['handler']     = 'file';    /* valid handlers are syslog|file|gelf */
$config['name']        = 'codeigniter';
$config['threshold']   = '4';    /* log all */
$config['formatter']   = 'line';
$config['line_format'] = '[%datetime%] %channel%.%level_name%: %message%';
/* use this of you log to syslog */
/* $config['line_format'] = '%channel%.%level_name%: %message%'; */

/* syslog handler options */
$config['syslog_channel']  = 'codeigniter';
$config['syslog_facility'] = 'local6';

/* file handler options */
$config['file_logfile'] = '/var/tmp/ci.log';

/* GELF options */
$config['gelf_host'] = 'localhost';
$config['gelf_port'] = '12201';
