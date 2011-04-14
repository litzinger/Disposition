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
 * ExpressionEngine Disposition Extension Class
 *
 * @package     ExpressionEngine
 * @subpackage  Extensions
 * @category    Disposition
 * @author      Brian Litzinger
 * @copyright   Copyright 2010 - Brian Litzinger
 * @link        http://boldminded.com/add-ons/disposition
 */
 
class Disposition_ext {
    
    var $settings = array();
    var $name = DISPOSITION_NAME;
    var $version = DISPOSITION_VERSION;
    var $description = DISPOSITION_DESCRIPTION;
    var $docs_url = DISPOSITION_DOCS_URL;
    var $settings_exist = 'y';
    
    function Disposition_ext($settings='')
    {
        $this->EE =& get_instance();
        
        $settings = $this->_get_settings();
        
        // All settings
        $this->global_settings = $settings;
        
        // Site specific settings
        $site_id = $this->EE->config->item('site_id');
        $this->settings = isset($settings[$site_id]) ? $settings[$site_id] : array();
    }
    
    /**
     * Custom settings form
     */
    function settings_form($vars)
    {
        $channels = array();
        $vars = $this->settings;
        
        $this->EE->lang->loadfile('disposition');
        
        $query = $this->EE->db->get('channels');
         
        foreach($query->result_array() as $channel)
        {
            $channels[$channel['channel_id']] = $channel['channel_title'];
        }

        $vars['hidden'] = array('file' => 'disposition');
        $vars['channels'] = $channels;
        $vars['enabled_channels'] = isset($this->settings['enabled_channels']) ? $this->settings['enabled_channels'] : false;
        
        // Load it up and return it to addons_extensions.php for rendering
        return $this->EE->load->view('settings_form', $vars, TRUE);
    }
    
    /**
     * Custom save action
     */
    function save_settings()
    {
        $insert['enabled_channels'] = $this->EE->input->post('enabled_channels');

        // Save our settings to the current site ID for MSM.
        $site_id = $this->EE->config->item('site_id');
        $settings = $this->global_settings;
        $settings[$site_id] = $insert;

        $this->EE->db->where('class', strtolower(__CLASS__));
        $this->EE->db->update('extensions', array('settings' => serialize($settings)));
        
        $this->EE->session->set_flashdata('message_success', $this->EE->lang->line('preferences_updated'));
    }
    
    /**
     * Install the extension
     */
    function activate_extension()
    {
        // Delete old hooks
        $this->EE->db->query("DELETE FROM exp_extensions WHERE class = '". __CLASS__ ."'");
        
        // Add new hooks
        $ext_template = array(
            'class'    => __CLASS__,
            'settings' => '',
            'priority' => 5,
            'version'  => $this->version,
            'enabled'  => 'y'
        );
        
        $extensions = array(
            array('hook'=>'bogus_hook', 'method'=>'bogus_hook'),
        );
        
        foreach($extensions as $extension)
        {
            $ext = array_merge($ext_template, $extension);
            $this->EE->db->insert('exp_extensions', $ext);
        }   
        
        $this->EE->db->where('class', 'Disposition_acc')
                     ->update('accessories', array('controllers' => 'content_edit'));
    }

    /**
     * @param string $current currently installed version
     */
    function update_extension($current = '') 
    {
        if ($current == '' OR $current == $this->version)
        {
            return FALSE;
        }
    }

    /**
     * Uninstalls extension
     */
    function disable_extension() 
    {
        $this->EE->db->delete('extensions', array('class' => __CLASS__)); 
    }
    
    /**
    * Get the site specific settings from the extensions table
    * Originally written by Leevi Graham? Modified for EE2.0
    *
    * @param $force_refresh     bool    Get the settings from the DB even if they are in the session
    * @return array                     If settings are found otherwise false. Site settings are returned by default.
    */
    private function _get_settings($force_refresh = FALSE)
    {
        // assume there are no settings
        $settings = FALSE;
        $this->EE->load->helper('string');

        // Get the settings for the extension
        if(isset($this->cache['settings']) === FALSE || $force_refresh === TRUE)
        {
            // check the db for extension settings
            $query = $this->EE->db->query("SELECT settings FROM exp_extensions WHERE enabled = 'y' AND class = '" . __CLASS__ . "' LIMIT 1");

            // if there is a row and the row has settings
            if ($query->num_rows() > 0 && $query->row('settings') != '')
            {
                // save them to the cache
                $this->cache['settings'] = strip_slashes(unserialize($query->row('settings')));
            }
        }

        // check to see if the session has been set
        // if it has return the session
        // if not return false
        if(empty($this->cache['settings']) !== TRUE)
        {
            $settings = $this->cache['settings'];
        }
        
        

        return $settings;
    }
    
    private function debug($str, $die = false)
    {
        echo '<pre>';
        var_dump($str);
        echo '</pre>';
        
        if($die) die('debug terminated');
    }
}