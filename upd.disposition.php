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

class Disposition_upd {

    var $version = DISPOSITION_VERSION;

    function Disposition_upd($switch = TRUE)
    {
        $this->EE =& get_instance();
    }

    function install()
    {
        // Module data
        $data = array(
            'module_name' => DISPOSITION_NAME,
            'module_version' => DISPOSITION_VERSION,
            'has_cp_backend' => 'n',
            'has_publish_fields' => 'n'
        );

        $this->EE->db->insert('modules', $data);
        
        // Insert our Action
        $query = $this->EE->db->get_where('actions', array('class' => DISPOSITION_NAME));

        if($query->num_rows() == 0)
        {
            $data = array(
                'class' => DISPOSITION_NAME,
                'method' => 'update_entry_date'
            );

            $this->EE->db->insert('actions', $data);
        }
        
        return TRUE;
    }
    
    function uninstall()
    {
        $this->EE->db->where('module_name', DISPOSITION_NAME);
        $this->EE->db->delete('modules');
        
        $this->EE->db->where('class', DISPOSITION_NAME)->delete('actions');
        
        return TRUE;
    }
    
    function update($current = '')
    {
        return TRUE;
    }
}