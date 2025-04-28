<?php
namespace App;

use App\Services\DataService;
use App\Services\LogService;
use ZipArchive;

class MainController
{      
    public $onReloadMode;
    private $rootFolder;
    
    private $env;
    private $data;
    private $log;

    public $envVariablesList=[
        'FURS_FILE_PATH', 
        'TMP_DIR', 
        'FIRST_ROWS_COUNT', 
        'DATA_PATH', 
        'COLUMNS_WIDTH',
        'COLUMNS_TITLES',
        'FILE_AGE_TIMEOUT',
        'MAX_REQUEST_COUNT'
    ];

    public function __construct($env)
    {

        $this->env = $env;
        $this->log=new LogService();
        $this->data=new DataService($env, $this->log);
        
        $this->rootFolder = getcwd();
        $this->onReloadMode=!$this->data->checkDataAge();
    }

    // entry point
    public function index(){
        
        if($this->onReloadMode){                
            if($this->checkUrlAvailable($this->env['FURS_FILE_PATH'])){                
                $this->runConsoleUpdate();            
            }else{
                $this->log->writeLog("FURS file not available - ".$this->env['FURS_FILE_PATH']);
                $this->log->writeLog("Try to update your data later");
                $this->onReloadMode=false;
            }
        }

        include('view/layout.php');        
    }

    // run update file process as a background process
    private function runConsoleUpdate(){
        if(!$this->processExists("console-update.php")){                            
            $this->log->writeLog("run file update from ".$this->env['FURS_FILE_PATH']."",true);        
            $cmd = "php ".$this->rootFolder."/console-update.php > /dev/null 2>&1 &";
            exec($cmd);
        }
    }

    // run reload file from furs
    public function reloadData(){        
        if($localFile=$this->downloadFile($this->env['FURS_FILE_PATH'])){
            $this->data->copyDataPart($localFile, $this->env['FIRST_ROWS_COUNT']);        
        }
    }

    // download zip file from url
    // unzip it and return the path to the unzipped file
    private function downloadFile($url)
    {
        
        $zipFileName = basename($url);
        $unzipFileName =pathinfo($url, PATHINFO_FILENAME).'.txt';

        $zip = new ZipArchive();
        $filePath = $this->rootFolder.$this->env['TMP_DIR'].$zipFileName;
        
        $done=false;    
        $requestIteration=0;    

        while(!$done){
            if (copy($this->checkUrlRedirect($url), $filePath)) {
                $res = $zip->open($filePath, ZipArchive::CHECKCONS);
                if ($res !== TRUE) {
                    switch($res) {
                        case ZipArchive::ER_NOZIP:                            
                            $this->log->writeLog("zip download error: not a zip archive");
                            break;
                        case ZipArchive::ER_INCONS :
                            $this->log->writeLog("zip download error: consistency check failed");                            
                            break;
                        case ZipArchive::ER_CRC :
                            $this->log->writeLog("zip download error: checksum failed");                            
                            break;
                        default:
                            $this->log->writeLog("zip download error: undifined error - ".$res);                                                        
                            break;
                    }
                } else {
     
                    $zip->extractTo($this->rootFolder.$this->env['TMP_DIR']);
                    $zip->close();
     
                    // Remove the zip file after extraction
                    unlink($filePath);              
                    $done=true;
                }    
            }

            if(!$done) {
                
                if($this->env['MAX_REQUEST_COUNT']<=$requestIteration){
                    $this->log->writeLog("zip download error: max request count reached");
                    return false;
                }
                
                $requestIteration++;
                sleep(5);

                $this->log->writeLog("retrying download....");
                
            }

        }

        $this->log->writeLog("zip file downloaded successfully!");
        return $this->rootFolder.$this->env['TMP_DIR'].$unzipFileName;
    }
    
    // check if url is redirected
    private function checkUrlRedirect($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code == 302) {
            $url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        }
        return $url;
    }

    // check if url is available
    function checkUrlAvailable($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);        

        if ($code == 200 || $code == 302) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }
        
    // check if process exists
    function processExists($processName) {
        $exists= false;
        
        exec("ps -aux | grep -i $processName | grep -v grep", $pids);
        if (count($pids) > 0) {
            $exists = true;
        }
        
        return $exists;
    }
    // check if env variables are set
    function checkEnvSetup(){
 
        foreach ($this->envVariablesList as $var) {
            if (!isset($this->env[$var])) {
                $this->log->writeLog("Environment variable $var is not set");
                return false;
            }
        }

        return true;
    }
}
