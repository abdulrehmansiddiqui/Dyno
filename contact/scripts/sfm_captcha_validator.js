/*
  -------------------------------------------------------------------------
		      Captcha Validator
              Version 1.0
              Part of Simfatic Forms software
					
	Copyright (C) 2009 Simfatic Solutions. All rights reserved.
    This javascript code is installed as part of Simfatic Forms software.
	You may adapt this script for your own needs, provided these opening credit
    lines (down to the lower dividing line) are kept intact.
    You may not reprint or redistribute this code without permission from 
    Simfatic Solutions.
	http://www.simfatic.com/
    -------------------------------------------------------------------------  
*/


function SFM_CaptchaValidator(captcha_ip)
{
    this.captcha_ip = captcha_ip;

    this.validatedCode=''  

    this.validate = function()
    {
        if(this.validatedCode.length==0 ||
           this.validatedCode != this.captcha_ip.value)
        {
            this.ValidateOnline();
            return false;
        }
        else
        {
            return true;
        }
    }

  this.OnSuccess = function()
  {
     var msg = this.GetResponseText();
     if(msg == 'success')
     {
        this.validatedCode = this.captcha_ip.value;
        if(this.captcha_ip.form.onsubmit())
        {
            this.captcha_ip.form.submit();
        }
     }
     else
     {
        sfm_show_error_msg(msg,this.captcha_ip);
        document.error_disp_handler.FinalShowMsg();
     }
     
  }

  this.ValidateOnline = function()
  {
    var formobj = this.captcha_ip.form;
    var url = formobj.action;
    var postStr = this.captcha_ip.name + "=" + 
    encodeURIComponent( this.captcha_ip.value )+'&sfm_validate_captcha=y';

     this.Init('POST', url);

     this.Send(postStr);
  }
}

SFM_CaptchaValidator.prototype = new SFM_Ajax();

function SFM_Ajax()
{
   var _request = null;
   var _this = null;
       
   this.Init = function(method, url)
   {
      _Init();
      _this = this;
               
      switch (arguments.length)
      {
         case 2:
            _request.open(method, url);
            break;
                               
         case 3:
            _request.open(method, url, arguments[2]);
            break;
      }
               
      if (arguments.length >= 4)
      {
         _request.open(method, url, arguments[2], arguments[3]);
      }

      this.SetRequestHeader("Content-Type", 
            "application/x-www-form-urlencoded; charset=UTF-8");
   }
    
   this.SetRequestHeader = function(field, value)
   {
      if (_request) 
      { 
        _request.setRequestHeader(field, value) 
      };
   }
       
   this.Send = function(data)
   {
      if (_request){ _request.send(data)};
   }

   this.GetResponseText = function()
   {
      return (_request) ? _request.responseText : null;
   }
       
   this.OnSuccess = function() { };
   this.OnFailure = function() { };
       
   function _OnSuccess() 
   { 
        _this.OnSuccess(); 
   }

   function _OnFailure() 
   { 
      _this.OnFailure(); 
   }

   function _Init()
   {
      _request = _GetXmlHttpRequestObject();
      if(_request)
      {
        _request.onreadystatechange = _StateHandler;
      }
   }
       
   function _StateHandler()
   {
      if(4 == _request.readyState )
      {
        if (_request.status == 200)
        {
           _OnSuccess();
        }
        else
        {
           _OnFailure();        
        }
      }
   }
       
   function _GetXmlHttpRequestObject()
   {
     if (window.XMLHttpRequest) 
     {
        return new XMLHttpRequest(); //Mozilla, Safari ...
     } 
     else 
     if (window.ActiveXObject) 
     {
        return new ActiveXObject("Microsoft.XMLHTTP"); //IE
     } 
     else 
     {
        return null;
     }
   }
}

