<?php
/**
 * Codon PHP Framework
 *	www.nsslive.net/codon
 * Software License Agreement (BSD License)
 *
 * Copyright (c) 2008 Nabeel Shahzad, nsslive.net
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2.  Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Nabeel Shahzad
 * @copyright Copyright (c) 2008, Nabeel Shahzad
 * @link http://www.nsslive.net/codon
 * @license BSD License
 * @package codon_core
 */

/* Static implementation for TemplateSet
*/


class Template
{
	public static $tplset;
	
	public static function SetTemplatePath($path)
	{
		self::$tplset = new TemplateSet();
		self::$tplset->SetTemplatePath($path);
	}
	
	public static function EnableCaching($bool=true)
	{
		self::$tplset->enable_caching = $bool;
	}
	
	public static function ClearVars()
	{
		self::$tplset->ClearVars();
	}
	
	public static function Set($name, $value)
	{
		return self::$tplset->Set($name, $value);
	}
	
	public static function Show($tpl_name)
	{
		return self::$tplset->ShowTemplate($tpl_name);
	}
	
	public static function ShowTemplate($tpl_name)
	{
		return self::$tplset->ShowTemplate($tpl_name);
	}
	
	public static function GetTemplate($tpl_path, $ret=false)
	{
		return self::$tplset->GetTemplate($tpl_path, $ret);
	}
	
	public static function ShowModule($ModuleName, $Method)
	{
		return self::$tplset->ShowModule($ModuleName, $Method);
	}
	
}

?>