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
            $action_url = $this->EE->config->item('site_url') .'?ACT='. $this->EE->cp->fetch_action_id('Disposition', 'update_entry_date');
            
            $js = '
            var fixHelper = function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;
            };
            
            $(".dataTables_wrapper").ajaxSuccess(function(e, xhr, settings)
            {
                url = settings.url;
                var regex = /(M=edit_ajax_filter)/g; 
                
                console.log($(".mainTable tbody tr").length);
                
                if(regex.test(url) && $(".mainTable tbody tr").length > 1) 
                {
                    $(".mainTable tbody tr").each(function(){
                        $(this).find("td:eq(0)").wrapInner(\'<div></div>\');
                        $(this).find("td:eq(0)").find(\'div\').prepend(\'<span class="disposition_handle"></span>\');
                    });
                    
                    $(".mainTable tbody").sortable({
                        axis: "y",
                        placeholder: "ui-state-highlight",
                        distance: 5,
                        forcePlaceholderSize: true,
                        items: "tr",
                        helper: fixHelper,
                        handle: ".disposition_handle",
                        update: function(event, ui){
                    
                            ids = new Array();
                            $(".mainTable tbody tr").each(function(){
                                ids.push($(this).find("td:eq(0)").text());
                            });
                    
                            dragged = ui.item.find("td:eq(0)").text();
                    
                            sort_order = $(".mainTable thead tr th:eq(5)").attr("class");
                            sort_order = sort_order == "headerSortDown" ? "desc" : "asc";
                    
                            $(this).find("tr:odd").removeClass("odd even").addClass("odd");
                            $(this).find("tr:even").removeClass("odd even").addClass("even");
                    
                            $.ajax({
                                type: "POST",
                                url: "'. $action_url .'",
                                data: "sort_order="+ sort_order +"&dragged="+ dragged +"&ids="+ ids.toString()
                            });
                        }
                    });
                }
            });
            ';
            
            $css = '
                <style type="text/css">
                    .disposition_handle { 
                        width: 14px; 
                        height: 20px;
                        background-color: red;
                        position: absolute;
                        top: -4px;
                        left: -20px;
                        cursor: move;
                    }
                    
                    .mainTable tbody tr td:first-child div {
                        position: relative;
                    }
                </style>
            ';
            
            // Output JS, and remove extra white space and line breaks
            $out = str_replace('</head>', $css . '</head>', $out);
            
            $scripts = '
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
            array('hook'=>'sessions_end', 'method'=>'sessions_end'),
            array('hook'=>'show_full_control_panel_end', 'method'=>'show_full_control_panel_end')
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