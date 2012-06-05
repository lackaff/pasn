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

class Mailer
{
	public static $from_name;
	public static $from_email;
		
	/**
	 * Set the origin information for an email which is sent
	 *
	 * @param string $name Name of the sender
	 * @param email $email Email of the sender
	 */
	public static function SetOrigin($name, $email)
	{
		self::$from_name = $name;
		self::$from_email = $email;
	}
	
	
	/**
	 * Send both text and HTML email
	 *
	 * @param email $email
	 * @param string $subject
	 * @param string $message
	 */
	public static function SendEmail($email, $subject, $message)
	{
		$headers = "From: ".SITE_NAME." <".self::$from_email.">\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$boundary = uniqid("CODONMAIL");
		$headers .= "Content-Type: multipart/alternative" .
		"; boundary = $boundary\r\n\r\n";
		$headers .= "This is a MIME encoded message.\r\n\r\n";
		//plain text version of message
		$headers .= "--$boundary\r\n" .
		"Content-Type: text/plain; charset=ISO-8859-1\r\n" .
		"Content-Transfer-Encoding: base64\r\n\r\n";
		$headers .= chunk_split(base64_encode($message));

		//HTML version of message
		$message = nl2br($message);
		$headers .= "--$boundary\r\n" .
					"Content-Type: text/html; charset=ISO-8859-1\r\n" .
					"Content-Transfer-Encoding: base64\r\n\r\n";
		$headers .= chunk_split(base64_encode($message));

		mail($email, $subject, '', $headers);
	}
}
?>