<?php

/**
 * This file is part of the default proyect.
 * 
 * Description of Server
 *
 * @author : Daniel González Cerviño <daniel.gonzalez@ideup.com>
 * @file : Server.php , UTF-8
 * @date : Aug 10, 2012 , 9:21:50 AM
 */
class Server
{

    protected $uptime = false;
    protected $loadAverage = false;
    protected $memoryUsage = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * 
     */
    protected function initialize()
    {
        $methods = get_class_methods(__CLASS__);
        foreach ($methods as $method) {
            if (strtolower(substr($method, 0, 3)) == 'set') {
                $this->$method();
            }
        }
    }

    /**
     * 
     * @param type $cmd
     * @return type
     */
    protected function exec($cmd)
    {
        return trim(shell_exec($cmd));
    }

    /**
     * 
     */
    protected function setUptime()
    {
        $cmd = 'uptime | sed "s/.*up\s*\([0-9\:]*\).*/\\1/"';
        $this->uptime = $this->exec($cmd);
    }

    /**
     * 
     */
    protected function setLoadAverage()
    {
        $cmd = 'uptime | sed "s/.*load average: \(.*\)/\\1/"';
        $loadAverage = explode(',', $this->exec($cmd));
        if (is_array($loadAverage)) {
            if (count($loadAverage)) {
                $this->loadAverage = array(
                    '1' => trim($loadAverage[0]),
                    '5' => trim($loadAverage[1]),
                    '15' => trim($loadAverage[2]),
                );
            }
        }
    }

    /**
     * 
     */
    protected function setMemoryUsage()
    {
        $cmd = 'free -m | grep "buffers/cache" | sed -e "s/-\/+ buffers\/cache:\s*\([0-9]*\)\s*\([0-9]*\).*/\\1 \/ \\2/"';
        $memoryUsage = explode('/', $this->exec($cmd));
        if (is_array($memoryUsage)) {
            if (count($memoryUsage)) {
                $this->memoryUsage = array(
                    'used' => trim($memoryUsage[0]),
                    'free' => trim($memoryUsage[1]),
                );
            }
        }
    }

    // CPU
    // uso de disco
    // procesos
    // numero y mapa
    // servicios levantados
}
$s = new Server();
var_dump($s);