<?php
class Trahchart_Widget extends WP_Widget{
  function __construct(){
    parent::__construct('Trahchart_Widget','Trahchart Chart',array(
      'description'=>__("Show charts based on Trahchart charts you've made",'trahchart')
    ));
  }

  public function widget($args, $instance){
    $title          = isset($instance['title']) ? apply_filters('widget_title', $instance['title']) : "";
    $postid         = $instance['postid'];
    $chart_type     = $instance['chart_type'];
    $responsive     = $instance['responsive'];
    $width          = $instance['width'];
    $height         = $instance['height'];
    $colors         = $instance['colors'];

    // before and after widget arguments are defined by themes
    echo $args['before_widget'];
    if ( ! empty( $title ) )
    echo $args['before_title'] . $title . $args['after_title'];

    echo do_shortcode("[trahchart title_text='$title' type='$chart_type' id='$postid' colors='$colors']");
  }

  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = ($new_instance['title']);
    $instance['postid'] = ($new_instance['postid']);
    $instance['chart_type'] = ($new_instance['chart_type']);
    $instance['responsive']     = ($new_instance['responsive']);
    $instance['width']          = ($new_instance['width']);
    $instance['height']         = ($new_instance['height']);
    $instance['colors']         = ($new_instance['colors']);
    return $instance;
  }

  public function form($instance){
    $title          = isset( $instance['title'] ) 		? esc_attr($instance['title']) 			: "";
    $postid        = isset( $instance['postid'] ) 	? esc_attr($instance['postid']) 		: "";
    $chart_type     = isset( $instance['chart_type'] ) 	? esc_attr($instance['chart_type'])		: "";
    $responsive     = isset( $instance['responsive'] ) 	? esc_attr($instance['responsive'])		: "";
    $width          = isset( $instance['width'] ) 	? esc_attr($instance['width'])		: "";
    $height         = isset( $instance['height'] ) 	? esc_attr($instance['height'])		: "";
    $colors         = isset( $instance['colors'] ) 	? esc_attr($instance['colors'])		: "";


    require 'widget-form/widget-form.php';
  }
}
