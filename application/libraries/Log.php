<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Codeigniter-Monolog integration package
 *
 * (c) Andreas Pfotenhauer <pfote@ypsilon.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Monolog\Logger;
use Monolog\Handler\SyslogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RavenHandler;
use Monolog\Gelf\MessagePublisher;
use Monolog\Handler\GelfHandler;
use Monolog\Formatter\LineFormatter;

define('CONFIG_FILE','monolog.php');

/**
 * replaces CI's Logger class, use Monolog instead
 *
 * see https://github.com/Seldaek/monolog
 *
 */
class CI_Log {

    /* CI log levels */
    protected $_levels	= array('OFF'   => '0',
                                'ERROR' => '1',
                                'DEBUG' => '2',
                                'INFO'  => '3',
                                'ALL'   => '4');
    /* default loglevel $threshold */
    protected $threshold = '4';

    public function __construct()
    {
        /* copied functionality from system/core/Common.php, as the whole CI infrastructure is not available yet */
        if ( ! defined('ENVIRONMENT') OR ! file_exists($file_path = APPPATH.'config/'.ENVIRONMENT.'/'.CONFIG_FILE)) {
            $file_path = APPPATH.'config/' . CONFIG_FILE;
        }

        /* Fetch the config file */
        if (file_exists($file_path))	{
            require($file_path);
        } else {
            /* provide a reasonable standard config */
            $config = array('handler'         => 'syslog',
                            'name'            => 'codeigniter',
                            'channel'         => 'codeigniter',
                            'formatter'       => 'line',
                            'line_format'     => '%channel%.%level_name%: %message%',
                            'syslog_facility' => 'local6',
                            'threshold'       => '4',
                            );
        }
        $this->threshold = $config['threshold'];

        $this->log = new Logger($config['name']);

        /* decide which handler to use */
        switch ($config['handler']) {
        case 'syslog':
            $handler = new SyslogHandler($config['syslog_channel'], $config['syslog_facility']);
            break;
        case 'file':
            $handler = new StreamHandler($config['file_logfile']);
            break;
        case 'gelf':
            $publisher = new MessagePublisher($config['gelf_host'], $config['gelf_port']);
            $handler   = new GelfHandler($publisher);
            break;
        case 'raven':
		    $client = new Raven_Client($config['raven_endpoint']);
			$handler = new RavenHandler($client, Monolog\Logger::ERROR);
            break;
        default:
            exit('not supported log handler: ' . $config['handler']);
        }


        /* formatter selection, righht now only line formatter supported */
        switch ($config['formatter']) {
        case 'line':
            if (! isset($config['line_format'])) {
                $formatter = new LineFormatter("%channel%.%level_name%: %message%");
            } else {
                $formatter = new LineFormatter($config['line_format']);
            }
            break;
        }

        /* set formatter, and logging handler */
        if (isset($formatter)) $handler->setFormatter($formatter);
        $this->log->pushHandler($handler);

        $this->write_log('DEBUG', 'Monolog replacement logger initialized');
    }

    public function write_log($level = 'error', $msg, $php_error = FALSE)
    {
        $level = strtoupper($level);

        /* verify error level */
        if ( ! isset($this->_levels[$level])) {
            $this->log->addError('unknown error level: ' . $level);
            $level = 'ALL';
        }

        if ($this->_levels[$level] <= $this->threshold) {
            switch ($level) {
            case 'ERROR':
                $this->log->addError($msg);
                break;
            case 'DEBUG':
                $this->log->addDebug($msg);
                break;
            case 'ALL':
            case 'INFO':
                $this->log->addInfo($msg);
                break;
            }
        }
        return TRUE;
    }
}