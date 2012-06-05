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

class RSSFeed
{
	protected $feed_contents;
	
	public function __construct($title='', $url='', $description = '')
	{
		
		$last_build_date = $this->LastBuildDate();
		
		$this->feed_contents .= '<title>' . $title . ' RSS Feed</title>'
							 . '<link>' . $url . '</link>'
							 . '<description>' . $description . '</description>'
							 . '<lastBuildDate>' . $last_build_date . '</lastBuildDate>'
							 . '<language>en-us</language>';
	}
	
	/* Two ways to add to the main feed, overloaded depending on
		what was passed
	*/
	public function AddItem($title, $link, $guid='', $description)
	{
		$last_build_date = $this->LastBuildDate();
			
		if($guid == '')
		{
			$guid = $link . '#' . str_replace(' ', '', $title);
		}
		
		$this->feed_contents .= '<item>'
							 . '<title>'.$title .'</title>'
							 . '<link>'.$link.'</link>'
							 . '<guid>'.$guid.'</guid>'
							 . '<pubDate>'.$last_build_date.'</pubDate>'
							 . '<description>'.$description.'</description>'
							 . '</item>';
	}
	
	public function LastBuildDate()
	{
		return date('D, d M Y H:i:s T');
	}
	
	public function BuildFeed($filepath)
	{
		$fp = fopen($filepath, 'w');
		if(!$fp) return false;
		
		$writestring = '<?xml version="1.0" encoding="utf-8"?><rss version="2.0"><channel>';
		
		fwrite($fp, utf8_encode( $writestring), strlen($writestring));
		fwrite($fp, utf8_encode($this->feed_contents), strlen($this->feed_contents));
		
		$writestring = '</channel></rss>';
		
		fwrite($fp, utf8_encode($writestring), strlen($writestring));
		
		fclose($fp);
		
		return true;
	}
}
  
?>