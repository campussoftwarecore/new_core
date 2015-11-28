<?php        
    register_shutdown_function( "fatal_handler" );
    function fatal_handler() 
    {
        $errfile = "unknown file";
        $errstr  = "shutdown";
        $errno   = E_ERROR;
        $errline = 0;

        $error = error_get_last();

        if( $error !== NULL) 
        {
                        $errno   = $error["type"];
			if(!in_array($errno, array("2","8")))
			{
				$errfile = $error["file"];
				$errline = $error["line"];
				$errstr  = $error["message"];
				try
				{
					 
					try 
					{
						echo $errorContent=(format_error( $errno, $errstr, $errfile, $errline));
					   /* $filename=$_SERVER['DOCUMENT_ROOT']."new_core/Var/Errors/".strtotime(date('Y-m-d H:i:s')).".html";
						
						$fp=fopen($filename,"w+") or die($filename);
						fwrite($fp, $errorContent);
						fclose($fp);*/
					} 
					catch (Exception $ex) 
					{
					   
					}              
								  
				}
				catch (Exception $e)
				{
					$e->getMessage();
				}
			}
          
        }
    }
    function format_error( $errno, $errstr, $errfile, $errline ) 
    {
        $trace = print_r(debug_backtrace( false ),true);
        $content = "
        <table>
        <thead><th>Item</th><th>Description</th></thead>
        <tbody>
        <tr>
          <th>Error</th>
          <td><pre>$errstr</pre></td>
        </tr>
        <tr>
          <th>Errno</th>
          <td><pre>$errno</pre></td>
        </tr>
        <tr>
          <th>File</th>
          <td>$errfile</td>
        </tr>
        <tr>
          <th>Line</th>
          <td>$errline</td>
        </tr>
        <tr>
          <th>Trace</th>
          <td><pre>$trace</pre></td>
        </tr>
        </tbody>
        </table>";

        return $content;
    }
    function __autoload($class_name)
    {        
        $filename=  str_replace("_","/", $class_name);
        try
        {
            if(file_exists($filename.".php"))
            {
                require_once($filename.'.php');
                return 1;
            }
            else
            {
                return 0;
            }
            
        }
        catch(Exception $e) 
        {
            echo $e->getMessage();
        }
    }
    $rootObj=new Core_WebsiteSettings();
?>

