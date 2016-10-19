<fieldset>
<legend>
Widget Option
</legend>
<p>
  <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
  <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<p>
    <label for="<?php echo $this->get_field_id('postid'); ?>"><?php _e('Post ID:', 'trahchart'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('postid'); ?>" name="<?php echo $this->get_field_name('postid'); ?>" type="number" value="<?php echo $postid; ?>" />
</p>

<p>
    <label for="<?php echo $this->get_field_id('chart_type'); ?>"><?php _e('Chart Type', 'trahchart'); ?></label>
    <select name="<?php echo $this->get_field_name('chart_type'); ?>" id="<?php echo $this->get_field_id('chart_type'); ?>" class='widefat'>
    <?php
    $options = array('pie', 'doughnut', 'radar', 'line', 'bar', 'polarArea');
    foreach ($options as $option) {
        echo '<option value="' . $option . '" id="' . $option . '"', $chart_type == $option ? ' selected="selected"' : '', '>', $option, '</option>';
    }
    ?>
    </select>
</p>

<p>
    <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', 'trahchart'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $postid; ?>" />
</p>

<p>
    <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', 'trahchart'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $postid; ?>" />
</p>

<p>
    <label for="<?php echo $this->get_field_id('responsive'); ?>"><?php _e('Responsive', 'trahchart'); ?></label>
    <select name="<?php echo $this->get_field_name('responsive'); ?>" id="<?php echo $this->get_field_id('responsive'); ?>" class='widefat'>
    <?php
    $options = array('TRUE', 'FALSE');
    foreach ($options as $option) {
        echo '<option value="' . $option . '" id="' . $option . '"', $responsive == $option ? ' selected="selected"' : '', '>', $option, '</option>';
    }
    ?>
    </select>
</p>

<p>
    <label for="<?php echo $this->get_field_id('colors'); ?>"><?php _e('Colors:', 'trahchart'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('colors'); ?>" name="<?php echo $this->get_field_name('colors'); ?>" type="text" value="<?php echo $postid; ?>" />
</p>


</fieldset>
