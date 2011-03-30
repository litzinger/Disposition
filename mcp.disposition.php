<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (! defined('DISPOSITION_VERSION'))
{
    // get the version from config.php
    require PATH_THIRD.'disposition/config.php';
    define('DISPOSITION_VERSION', $config['version']);
    define('DISPOSITION_NAME', $config['name']);
    define('DISPOSITION_DESCRIPTION', $config['description']);
    define('DISPOSITION_DOCS_URL', $config['docs_url']);
}

/**
 * ExpressionEngine Disposition Module Class
 *
 * @package     ExpressionEngine
 * @subpackage  Modules
 * @category    Disposition
 * @author      Brian Litzinger
 * @copyright   Copyright 2011 - Brian Litzinger
 * @link        http://boldminded.com/add-ons/disposition
 */

class Disposition_mcp {
    
    function Disposition_mcp()
    {
        $this->EE =& get_instance();
    }
    
    function index() {}
    
}