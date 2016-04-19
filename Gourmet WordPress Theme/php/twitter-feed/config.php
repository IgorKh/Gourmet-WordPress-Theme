<?php
    /**
     * Your Twitter App Info
     */
    
    // Consumer Key
    define('CONSUMER_KEY', 'YOUR_CONSUMER_KEY_HERE');
    define('CONSUMER_SECRET', 'YOUR_CONSUMER_SECRET_HERE');

    // User Access Token
    define('ACCESS_TOKEN', 'YOUR_ACCESS_TOKEN_HERE');
    define('ACCESS_SECRET', 'YOUR_ACCESS_SECRET_HERE');
	
	// Cache Settings
	define('CACHE_ENABLED', false);
	define('CACHE_LIFETIME', 3600); // in seconds
	define('HASH_SALT', md5(dirname(__FILE__)));