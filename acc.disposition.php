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
 * ExpressionEngine Disposition Accessory Class
 *
 * @package     ExpressionEngine
 * @subpackage  Accessories
 * @category    Disposition
 * @author      Brian Litzinger
 * @copyright   Copyright 2010 - Brian Litzinger
 * @link        http://boldminded.com/add-ons/disposition
 */
 
class Disposition_acc {

    var $name           = DISPOSITION_NAME;
    var $id             = 'disposition';
    var $version        = DISPOSITION_VERSION;
    var $description    = DISPOSITION_DESCRIPTION;
    var $sections       = array();

    /**
     * Constructor
     */
    function Disposition_acc()
    {}
    
    function set_sections()
    {
        $this->EE =& get_instance();
        
        // Remove the tab. This is lame.
        $script = '
            $("#'. $this->id .'.accessory").remove();
            $("#accessoryTabs").find("a.'. $this->id .'").parent("li").remove();
        ';
        
        if(REQ == 'CP' AND $this->EE->input->get('C') == 'content_edit')
        {
            $this->EE->load->library('javascript');
            $action_url = $this->EE->config->item('site_url') .'?ACT='. $this->EE->cp->fetch_action_id('Disposition', 'update_entry_date');
        
            $settings = $this->EE->db->select('settings')
                                     ->where('class', 'Disposition_ext')
                                     ->get('extensions')
                                     ->row('settings');
                                 
            $site_id = $this->EE->config->item('site_id');
            $settings = unserialize($settings);
            $settings = isset($settings[$site_id]['enabled_channels']) ? implode('","', $settings[$site_id]['enabled_channels']) : false;

            // If we have no settings, stop here
            if(!$settings)
                return;

            $script .= '
			
            var fixHelper = function(e, ui) {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;
            };

			//Using "mainTable" because EE2.4 removed the old hook
            $(".mainTable").ajaxSuccess(function(e, xhr, settings)
            {
                
				url = settings.url;
                
                //Check if we have added handles to this thing because the 
                //AJAX url check does not work in EE2.4
                var handlecount = $(".disposition_handle").length; 
                
                channel_id = $("#f_channel_id").val();
                settings = new Array("'. $settings .'");
			    			    
                if($(".mainTable tbody tr").length > 1 && handlecount == 0 && $.inArray(channel_id, settings) > -1) 
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

			//init (this may not be necessary... but it does not fire automatically in 2.4)
			$.ajax({type: "POST",url: ""});
            ';
  
            // Leave linebreaks etc because EE2.4 seems to need them..!            
            $this->EE->javascript->output($script);                
            $this->EE->javascript->compile();
        
            $css = '
                .disposition_handle { 
                    width: 14px; 
                    height: 20px;
                    background: url(data:image/gif;base64,R0lGODlhEwATAIABAKKiosrklCH5BAEAAAEALAAAAAATABMAQAIXjI+pCO2wopy02steq3rjD4biSJbmKRYAOw%3D%3D) 50% 50% no-repeat;
                    position: absolute;
                    top: -4px;
                    left: -5px;
                    cursor: move;
                }
            
                .mainTable tbody tr td:first-child div {
                    position: relative;
                    padding-left: 12px;
                }
            ';
        
            // Output CSS, and remove extra white space and line breaks
            $this->EE->cp->add_to_head('<!-- BEGIN Disposition assets --><style type="text/css">'. preg_replace("/\s+/", " ", $css) .'</style><!-- END Disposition assets -->');
        }
    }
    
    private function debug($str, $die = false)
    {
        echo '<pre>';
        var_dump($str);
        echo '</pre>';
        
        if($die) die('debug terminated');
    }
}