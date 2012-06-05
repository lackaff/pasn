<?php

class Welcome extends CodonModule
{
	function Controller()
	{
		
		switch ($this->get->page)
		{
			case '':
				
				Template::Show('welcome.tpl');	
				
				break;
		}
	}
}
?>