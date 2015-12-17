<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SendMail
 *
 * @author ramesh
 */
class Core_Email_SendMail extends Core_Email_Mailer
{
    //put your code here
    function sendmail($tomail,$subject,$messages,$email_settings)
    {    
    
        try
        {
          
          $this->IsSMTP();
          $this->SMTPAuth=true;
          $this->SMTPDebug=0;
          $this->SMTPSecure=$email_settings['host_securetype'];
          $this->Port=$email_settings['host_port'];
          $this->Host=$email_settings['host_name'];
          $this->Username=$email_settings['host_username'];
          $this->Password=$email_settings['host_password'];
          $this->SetFrom($email_settings['fromemail'],$email_settings['fromname']);
          $this->AddAddress($tomail);
          $this->Subject = $subject;
          $this->Body = $messages;
          $this->IsHTML(true); 
          $this->Send();
          return "Message Sent OK</p>\n";
        }
        catch (Core_Email_MailerException $e)
        {
          return $e->errorMessage(); //Pretty error messages from PHPMailer
        }
        catch (Exception $e)
        {
          return $e->getMessage(); //Boring error messages from anything else!
        }
        return true;
    }
}
