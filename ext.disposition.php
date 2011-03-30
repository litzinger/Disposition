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
 * @link        http://boldminded.com
 */
 
class Disposition_ext {
    
    var $settings = array();
    var $name = DISPOSITION_NAME;
    var $version = DISPOSITION_VERSION;
    var $description = DISPOSITION_DESCRIPTION;
    var $docs_url = DISPOSITION_DOCS_URL;
    var $settings_exist = 'n';
    
    function Disposition_ext($settings='')
    {
        $this->EE =& get_instance();
        $this->settings = $settings;
    }
    
    function show_full_control_panel_end($out)
    {
        if(REQ == 'CP' AND $this->EE->router->class == 'content_edit')
        {
            $this->EE->load->library('javascript');
            // $this->debug($this->EE->router->class);
            
            $js = '
            var fixHelper = function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;
            };
            
            $(".mainTable tbody tr").each(function(){
                $(this).find("td:eq(0)").prepend(\'<span class="disposition_handle" style="width: 10px; height: 10px; background-color: red; display: block;"></span>\');
            });
            
            $(".mainTable tbody").sortable({
                axis: "y",
                placeholder: "ui-state-highlight",
                distance: 5,
                forcePlaceholderSize: true,
                items: "tr",
                helper: fixHelper,
                update: function(event, ui){
                    prev = ui.item.prev();
                    next = ui.item.next();
                    id = ui.item.find("td:eq(0)").text();
                    this_date = ui.item.find("td:eq(5)").text();
                    prev_date = prev.find("td:eq(5)").text();
                    next_date = next.find("td:eq(5)").text();
                    
                    this_date_stamp = Date.parse(this_date);
                    prev_date_stamp = Date.parse(prev_date);
                    next_date_stamp = Date.parse(next_date);
                    
                    if(prev.length > 0)
                    {
                        new_date = prev_date_stamp.addMinutes(1);
                    } 
                    else if(next.length > 0)
                    {
                        new_date = next_date_stamp.addMinutes(-1);
                    }
                    
                    new_date = new_date.toString("MM/dd/yy hh:mm tt").toLowerCase();
                    
                    ui.item.find("td:eq(5)").text(new_date);
                    
                    $(this).find("tr:odd").removeClass("odd even").addClass("odd");
                    $(this).find("tr:even").removeClass("odd even").addClass("even");
                }
            });
            ';
            
            // var data = $(this).sortable("serialize", {key:'order[]'});
            // $.ajax({
            //     type: "POST",
            //     url: "",
            //     data: data
            // });
            
            $scripts = '
                <script type="text/javascript" src="/ee/dev5/third_party/disposition/date.js"></script>
                <script type="text/javascript">$(function(){'. preg_replace("/\s+/", " ", $js) .'});</script>
            ';
            
            // Output JS, and remove extra white space and line breaks
            $out = str_replace('</body>', '</body>'. $scripts, $out);
        }
        
        return $out;
    }
    
    /**
     * Set default setting for variable prefix
     */
    function settings()
    {
        // $settings = array();
        // 
        // $settings['enable_member_vars'] = array('s', array('no' => 'No', 'yes' => 'Yes'), $this->default_settings['enable_member_vars']);
        // $settings['member_prefix'] = $this->default_settings['member_prefix'];
        // $settings['member_suffix'] = $this->default_settings['member_suffix'];
        // 
        // $settings['enable_postget_vars'] = array('s', array('no' => 'No', 'yes' => 'Yes'), $this->default_settings['enable_postget_vars']);
        // $settings['post_prefix'] = $this->default_settings['post_prefix'];
        // $settings['get_prefix'] = $this->default_settings['get_prefix'];
        // $settings['get_default'] = array('t', '', '');
        // $settings['post_default'] = array('t', '', '');
        
        return $settings;
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
            array('hook'=>'sessions_end', 'method'=>'sessions_end')
        );
        
        foreach($extensions as $extension)
        {
            $ext = array_merge($ext_template, $extension);
            $this->EE->db->insert('exp_extensions', $ext);
        }   
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
    
    private function debug($str, $die = false)
    {
        echo '<pre>';
        var_dump($str);
        echo '</pre>';
        
        if($die) die('debug terminated');
    }
}