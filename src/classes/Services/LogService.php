<?php
namespace App\Services;

class LogService{
    
    private $logPath;
    private $rootFolder;

    public function __construct() {
        $this->rootFolder = getcwd();
        $this->logPath = $this->rootFolder . '/logs/log.txt';
        if(!file_exists($this->rootFolder.'/logs')) mkdir($this->rootFolder.'/logs', 0775);
    }

    // clear log
    public function clearLog(){        
        file_put_contents($this->logPath, "");
    }

     // write log
    public function writeLog($message,$clear=false){
        
        if($clear){
            $this->clearLog();
        }

        $timestamp = date("Y-m-d H:i:s");
        file_put_contents($this->logPath, "[$timestamp] $message<br>\n", FILE_APPEND);
    }

    // read log
    public function readLog(){
        if (!file_exists($this->logPath)) {
            file_put_contents($this->logPath, "");
        }
        return file_get_contents($this->logPath);
    }
}
?>
