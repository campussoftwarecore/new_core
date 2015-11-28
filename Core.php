<?php
class Core 
{
    static function Log($string,$filename=NULL)
    {
        $wp=new Core_WebsiteSettings();        
        if(!$filename)
        {
            $filename="system.log";
        }
        $folderPath=Core::createFolder("Errors","L");
        $filename=$folderPath.str_replace(" ","_", $filename).".log";            
        $fp=  fopen($filename,"w");
        fwrite($fp, $string);
        fclose($fp);                
        
    }
    static function createFolder($folderName,$type)
    {
        $wp=new Core_WebsiteSettings();
        $tempPath="";
        switch ($type)
        {
            case    "L" :
                            $folderName="Var/Errors/Core/";
                            break;
            default     :
                            break;
        }
        $tempPath_list=explode("/", $folderName);
        $i=0;
        $tempFolder=$filename=$wp->documentRoot;
        while($i<count($tempPath_list))
        {
            $tempFolder.=$tempPath_list[$i];
            if(!file_exists($tempFolder))
            {
                mkdir($tempFolder, 0755, true);
            }
            $tempFolder.="/";
            $i++;
        }
        return $tempFolder;
    }
}
