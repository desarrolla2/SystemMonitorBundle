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

    protected $initialized_on = false;
    protected $uptime;
    protected $loadAverage;
    protected $memoryUsage;

    /**
     * Constructor
     */
    public function __construct()
    {
        $start_time = $this->getMicroTime();
        $this->initialize();
        $this->initialized_on = $this->getMicroTime() - $start_time;
    }

    /**
     * @return float
     */
    protected function getMicroTime()
    {
        return microtime(true);
    }

    /**
     * 
     */
    protected function initialize()
    {
        $this->uptime = false;
        $this->loadAverage = array(
            '1' => false, '5' => false, '15' => false,
        );
        $this->memoryUsage = array(
            'free' => false, 'used' => false,
            'total' => false, 'percenage' => false,
        );

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
                $this->memoryUsage['total'] = $this->memoryUsage['used'] + $this->memoryUsage['free'];
                if ($this->memoryUsage['total']){
                    $this->memoryUsage['percentage'] = $this->memoryUsage['used'] / $this->memoryUsage['total'] * 100;
                }                
            }
        }
    }

    /**
     * 
     */
    protected function setCPUUsage()
    {
        
    }

    /**
     * 
     */
    protected function setDiskUsage()
    {
        
    }

    // servicios levantados
}

$s = new Server();
var_dump($s);