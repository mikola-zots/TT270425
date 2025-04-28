<?php
namespace App\Services;

class DataService
{
    private $env;
    private $rootFolder;

    public function __construct($env)
    { 
        $this->rootFolder = getcwd();
        $this->env = $env;        
    }

    // get data from file
    public function getData(){

        $rows=[];        
        
        $inputFile = $this->rootFolder.$this->env['DATA_PATH'];
        $columnsWidths = explode(",",$this->env['COLUMNS_WIDTH']);
        $columnsTitles = explode(",",$this->env['COLUMNS_TITLES']);

        if (file_exists($inputFile)) {
            $handle = fopen($inputFile, 'r');
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $offset = 0;
                    $fields = [];

                    foreach ($columnsWidths as $ind=>$width) {
                        $fields[$columnsTitles[$ind]] = trim(substr($line, $offset, $width));
                        $offset += $width;
                    }
     
                    $rows[]=$fields;
                }
                fclose($handle);
            }
        }
        return $rows;
    }

    // check if data is older than 24 hours
    public function checkDataAge(){
        $file = $this->rootFolder.$this->env['DATA_PATH'];        
        
        if (file_exists($file)) {
            $lastModified = filemtime($file);
            $now = time();
            if (($now - $lastModified) > $this->env['FILE_AGE_TIMEOUT'] * 60 * 60) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
    
    // copy part of file to another file
    public function copyDataPart($filePath,$firstRowsCount)
    {
        if(!file_exists($this->rootFolder.'data')) mkdir($this->rootFolder.'data', 0775);        

        $in = fopen($filePath, 'r');
        $out = fopen($this->rootFolder.$this->env['DATA_PATH'], 'w');

        if ($in && $out) {
            $count = 0;
            while (!feof($in) && $count < $firstRowsCount) {
                $line = fgets($in);
                if ($line === false) break;
                fwrite($out, $line);
                $count++;
            }
            fclose($in);
            fclose($out);

            unlink($filePath);  
            return true;

        } else {                                      
            return false;
        }
    }
}
