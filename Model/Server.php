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
    protected $memory;
    protected $process;

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
        $this->memory = array(
            'free' => false, 'used' => false,
            'total' => false, '%' => false,
        );
        $this->process = array();
        $this->devicesUsage = array();
        $this->diskUsage = array(
            'free' => false, 'used' => false,
            'total' => false, '%' => false,
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
                $this->memory = array(
                    'used' => trim($memoryUsage[0]),
                    'free' => trim($memoryUsage[1]),
                );
                $this->memory['total'] = $this->memory['used'] + $this->memory['free'];
                if ($this->memory['total']) {
                    $this->memory['%'] = $this->memory['used'] / $this->memory['total'] * 100;
                }
            }
        }
    }

    /**
     * 
     */
    protected function setDiskUsage()
    {
        $cmd = 'df | grep /dev/ | awk \'{print $1 "," $3 "," $4 "|"}\'';
        $_diskUsage = explode('|', $this->exec($cmd));
        if (is_array($_diskUsage)) {
            if (count($_diskUsage)) {
                foreach ($_diskUsage as $_devicesUsage) {
                    $_devicesUsage = explode(',', $this->exec($cmd));
                    if (is_array($_devicesUsage)) {
                        if (count($_devicesUsage)) {
                            $_deviceName = trim($_devicesUsage[0]);
                            $this->devicesUsage[$_deviceName]['used'] = trim($_devicesUsage[1]);
                            $this->devicesUsage[$_deviceName]['free'] = trim($_devicesUsage[2]);
                            $this->devicesUsage[$_deviceName]['total'] = $this->devicesUsage[$_deviceName]['used'] + $this->devicesUsage[$_deviceName]['free'];
                            if ($this->devicesUsage[$_deviceName]['total']) {
                                $this->devicesUsage[$_deviceName]['%'] = $this->devicesUsage[$_deviceName]['used'] / $this->devicesUsage[$_deviceName]['total'] * 100;
                            }
                        }
                    }
                }
                foreach ($this->devicesUsage as $_devicesUsage) {
                    $this->diskUsage['used'] += $_devicesUsage['used'];
                    $this->diskUsage['free'] += $_devicesUsage['free'];
                }
                $this->diskUsage['total'] = $this->diskUsage['used'] + $this->diskUsage['free'];
                if ($this->diskUsage['total']) {
                    $this->diskUsage['%'] = $this->diskUsage['used'] / $this->diskUsage['total'] * 100;
                }
            }
        }
    }

    /**
     * 
     */
    public function setProcess()
    {
        // ps aux | sort -nrk +3 | head -10
        $cmd = 'ps auxh | wc -l';
        $this->process['total'] = $this->exec($cmd);
    }

    /**
     * 
     */
    protected function setCPUUsage()
    {
        
    }

    // servicios levantados
}

$s = new Server();
var_dump($s);