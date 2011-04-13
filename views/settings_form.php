<?php echo form_open('C=addons_extensions'.AMP.'M=save_extension_settings', 'id="disposition_settings"', $hidden)?>
    
<?php
// Enable Edit menu tweak in Accessory?
$this->table->set_template($cp_table_template);
$this->table->set_heading(
    array('data' => lang('enable_edit_menu_tweaks'), 'style' => 'width: 80%;', 'colspan' => '2')
);
$this->table->add_row(
    array('data' => '<p>'. lang('enable_edit_menu_tweaks_detail') .'</p>', 'style' => 'width: 80%'),
    array('data' => form_dropdown('enable_edit_menu_tweaks', array('n' => 'No', 'y' => 'Yes'), $enable_edit_menu_tweaks, 'id=enable_edit_menu_tweaks'), 'style' => 'width: 20%')
);

echo $this->table->generate();
$this->table->clear();
?>

<p class="centerSubmit"><?=form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'))?></p>

<?php echo form_close(); ?>
