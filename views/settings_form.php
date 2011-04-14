<?php echo form_open('C=addons_extensions'.AMP.'M=save_extension_settings', 'id="disposition_settings"', $hidden)?>
    
<?php
$this->table->set_template($cp_table_template);
$this->table->set_heading(
    array('data' => lang('enabled_channels_heading'), 'style' => 'width: 100%;', 'colspan' => '2')
);
$this->table->add_row(
    array('data' => '<p>'. lang('enabled_channels_label') .'</p>', 'style' => 'width: 40%'),
    array('data' => form_multiselect('enabled_channels[]', $channels, $enabled_channels, 'size="6"'), 'style' => 'width: 60%')
);

echo $this->table->generate();
$this->table->clear();
?>

<p class="centerSubmit"><?=form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'))?></p>

<?php echo form_close(); ?>
