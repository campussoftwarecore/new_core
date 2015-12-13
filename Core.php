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
        $filename=$folderPath.str_replace(" ","_", $filename)."";      
        if(!Core::fileExists($filename))
        {
            $fp=  fopen($filename,"w+");
            fwrite($fp, $string);
            fclose($fp);  
        }
        else
        {
            $fp=  fopen($filename,"a");
            fwrite($fp, " \n ");
            fwrite($fp, $string);
            fclose($fp);                
        }
    }
    static function getUploadPath()
    {
        $wp=new Core_WebsiteSettings();
        $folderName=$wp->websiteUrl."uploads/".$wp->identity."/";
        return $folderName;
    }
    static function createFolder($folderName,$type)
    {
        $wp=new Core_WebsiteSettings();
        $tempPath="";
        switch ($type)
        {
            case    "L" :
                            $folderName="Var/Errors/".$wp->identity;
                            break;
            case    "U" :
                            if($folderName)
                            {
                                $folderName="uploads/".$wp->identity."/".$folderName;
                            }
                            else
                            {
                               $folderName="uploads/".$wp->identity;
                            }
                            break;
            default     :
                            break;
        }
        $tempPath_list=explode("/", $folderName);
        $i=0;
        $tempFolder=$wp->documentRoot;
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
    static  function fileExists($file)
    {
        if(file_exists($file))           
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    static function covertStringToMethod($string)
    {
        $method=lcfirst(str_replace(" ","",ucwords(str_replace("_", " ",$string))));        
        return $method;
    }
    static  function methodExists($object,$method)
    {
        if(method_exists($object,$method))           
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    static  function keyInArray($key,$stringNeedToCheckArray)
    {
        if(key_exists($key,$stringNeedToCheckArray))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    static  function isArray($stringNeedToCheckArray)
    {
        if(is_array($stringNeedToCheckArray))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    static  function inArray($key,$stringNeedToCheckArray)
    {
        if(in_array($key,$stringNeedToCheckArray))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    static function covertStringToArray($stringNeedExplode,$delimiter=NULL)
    {
        if(!$delimiter)
        {
            $delimiter="|";
        }
        $output=array();
        if($stringNeedExplode)
        {
            $output= explode($delimiter, $stringNeedExplode);
        }
        return $output;
    }
    static function covertArrayToString($arrayNeedTobeString,$delimiter=NULL)
    {
        if(!$delimiter)
        {
            $delimiter="|";
        }
        $output=$arrayNeedTobeString;
        if(Core::isArray($arrayNeedTobeString))
        {
            $output= implode($delimiter, $arrayNeedTobeString);
        }
        return $output;
    }
    static function redirectUrl($url)
    {
        echo '
            <script>            
                window.location.assign("'.$url.'")            
            </script>';         
        exit;
    }
    static function getDBConfig()
    {
        try
        {
            $fp=  fopen("sitesettings.xml", "r") or ("ramesh");
            $fileContent=  fread($fp,  filesize("sitesettings.xml")); 
            fclose($fp); 
            return Core::convertXmlToArray($fileContent)['Database'];
        }
        catch(Exception $ex)
        {
            Core::Log($ex->getMessage());
        }        
    }  
    static  function convertXmlToArray($xml,$main_heading = '') 
    {
        $deXml = simplexml_load_string($xml);
        $deJson = json_encode($deXml);
        $xml_array = json_decode($deJson,TRUE);
        if (! empty($main_heading)) {
            $returned = $xml_array[$main_heading];
            return $returned;
        } 
        else 
        {
            return $xml_array;
        }
    }
    static function xmlToObject($xml) 
    {
        
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, $xml, $tags);
        xml_parser_free($parser);

        $elements = array();  // the currently filling [child] XmlElement array
        $stack = array();
        foreach ($tags as $tag) {
          $index = count($elements);
          if ($tag['type'] == "complete" || $tag['type'] == "open") {
            $elements[$index] = new Core_XmlData();
            $elements[$index]->name = $tag['tag'];
            $elements[$index]->attributes = $tag['attributes'];
            $elements[$index]->content = $tag['value'];
            if ($tag['type'] == "open") {  // push
              $elements[$index]->children = array();
              $stack[count($stack)] = &$elements;
              $elements = &$elements[$index]->children;
            }
          }
          if ($tag['type'] == "close") {  // pop
            $elements = &$stack[count($stack) - 1];
            unset($stack[count($stack) - 1]);
          }
        }
        return $elements[0];  // the single top-level element
    }
    static function convertJsonToArray($string)
    {
        return json_decode($string,1);
    }
    static function getKeysFromArray($array)
    {
        $output=array();
        if(Core::isArray($array))
        {
            $output=  array_keys($array);
        }
        return $output;
    }
    static function diffArray($firstArray,$secondArray)
    {        
        if(!Core::isArray($firstArray))
        {
            $firstArray=array();
        }
        if(!Core::isArray($secondArray))
        {
            $secondArray=array();
        }
        return array_diff($firstArray,$secondArray);
    }
    static function mergeArrays($firstArray,$secondArray)
    {        
        if(!Core::isArray($firstArray))
        {
            $firstArray=array();
        }
        if(!Core::isArray($secondArray))
        {
            $secondArray=array();
        }
        return array_merge(array_values($firstArray),array_values($secondArray));
    }
    static function countArray($array)
    {                
        return count($array);
    }
}
