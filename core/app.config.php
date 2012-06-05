<?php

/**
 * Gets converted to a define. Can be over-ridden in
 *  local.config.php, which ideally should not be overwritten
 *  an in update
 *
 * Format:
 * $Config['CONFIG_OPTION'] = 'SETTING';
 *
 * Which can then be accessed as a constant throughout your
 *  application:
 *
 * echo CONFIG_OPTION;
 */

/**
 * The current skin to use. Folder name from under
 *  lib/skins
 */
Config::Add('CURRENT_SKIN', 'default');


/**
 * Whether to run in Debug mode or not
 *  This determines if the bug-library is included or not
 *
 */
Config::Add('DEBUG_MODE', true);
Config::Add('USE_JAVASCRIPT', true);
Config::Add('ERROR_LEVEL', E_ALL ^ E_NOTICE);

/**
 * By default, the engine runs every module on page
 *  load, so it called the Controller() for every module.
 *
 * If you want it to only run the called module, set this to true
 * Otherwise, to run all the modules, set it to false
 *
 * If this is set to false, and a module is specified in the URL
 *  then that module will run first.
 */
Config::Add('RUN_SINGLE_MODULE', true);


/**
 * What is the default module to run,
 * if the RUN_SINGLE_MODULE is set to true
 */
Config::Add('DEFAULT_MODULE', 'Welcome');

/**
 * This is whether to automatically load all of the modules
 * or only the ones specified in ACTIVE_MODULES below
 *
 * If this is true, then ACTIVE_MODULES will be loaded
 *  *AFTER* the autoload, so you can over-ride certain
 *  modules with a new one.
 */

Config::Add('MODULES_AUTOLOAD', true);

/**
 * List of modules to load. Otherwise, it will load all of the modules
 * 	Config::Add('ACTIVE_MODULES', array('Example', 'Welcome'));
 *
 * Just include the name of the module
 */

Config::Add('ACTIVE_MODULES', array());

/**
 * For the rewrite, create an array, with each element being a part of the url:
 *
 * index.php/Blog/2008/07/10
 *
 * Vars::URLRewrite(array('module', 'year', 'month', 'day'));
 *
 * This will redirect it to the module Blog, with the year/month/day
 * You can leave the module out, and it'll direct it to all of
 * the modules:
 *
 * index.php/view/2008/07/10
 * Vars::URLRewrite(array('action', 'year', 'month', 'day'));
 *
 * Then it can be accessed in your module as:
 *
 * $this->get->action;
 * $this->get->year;
 *
 * The module parameter must be specified if RUN_SINGLE_MODULE is true,
 *  otherwise the engine has no way to tell what module is being called.
 *
 * If there is no module parameter and RUN_SINGLE_MODULE is set to true,
 *  the engine will spit out an error.
 */

/*'Welcome'=>array('module', 'page'),
	'ExampleModule'=>array('module', 'boo')*/

Config::Add('URL_REWRITE', 
	array(
		'default'=>array('module', 'page'),
		'Dashboard'=>array('module','page'),
		'InternalFriendManager'=>array('module','page'),
		'ExternalFriendManager'=>array('module','page'),
		'Survey'=>array('module','page')
		//'Search'=>array('module', 'query', 'start', 'limit')
	)
);


/**
 * How long the cache takes to time out. This value is in hours
 */
Config::Add('CACHE_TIMEOUT', 1);

/**
 * Blacklists for HTML tags and attributes. These will be removed 
 * from any input to Vars::Filter(). Useful for protecting against
 * XSS and HTML/JS injection. 
 * 
 * Filter() must be called explicitly, GET/POST is not 
 * sanitized automatically.
 */
Config::Add('TAG_BLACKLIST', 
	array('applet', 'body', 'bgsound', 
		'base', 'basefont', 'embed', 
		'frame', 'frameset', 'head', 
		'html', 'id', 'iframe', 'ilayer', 
		'layer', 'link', 'meta', 'name', 
		'object', 'script', 'style', 
		'title', 'xml')
);

// Note: Vars::Filter() will also auto-strip event handlers like onclick, etc
Config::Add('ATTR_BLACKLIST',
	array('action', 'background', 'codebase', 'dynsrc', 'lowsrc')
);

?>
