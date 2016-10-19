<?php
/**
* Plugin Name: Trahchart Plugin
* Plugin URI: https://github.com/
* Author: Aditya Yuga & Alan Darmasaputra
* Author URI: https://github.com/
* Description: A plugin to make a chart
* Version: 1.0
* License: GPLv2
*/

if(!defined('ABSPATH')){
  exit;
}

class Trahchart {
  var $params = array();
  var $option_general = ['id','type','responsive','width','height','events','onClick','onResize','responsiveAnimationDuration','maintainAspectRatio','legendCallback'];
  var $option_title = ['title_display','title_text','title_position','title_fullWidth','title_fontSize','title_fontFamily','title_fontColor','title_fontStyle','title_padding'];
  var $option_tooltip = [
    'tooltip_enabled','tooltip_custom','tooltip_mode','tooltip_itemSort','tooltip_backgroundColor','tooltip_titleFontFamily','tooltip_titleFontSize','tooltip_titleFontStyle','tooltip_titleFontColor','tooltip_titleSpacing','tooltip_titleMarginBottom','tooltip_bodyFontFamily','tooltip_bodyFontSize','tooltip_bodyFontStyle','tooltip_bodyFontColor',
    'tooltip_bodySpacing','tooltip_footerFontFamily','tooltip_footerFontSize','tooltip_footerFontStyle','tooltip_footerFontColor','tooltip_footerSpacing','tooltip_footerMarginTop',
    'tooltip_xPadding','tooltip_yPadding','tooltip_caretSize','tooltip_cornerRadius','tooltip_multiKeyBackground','tooltip_callbacks'
  ];
  var $option_hover = ['hover_mode','hover_animationDuration','hover_onHover'];
  var $option_legend = [
    'legend_display','legend_position','legend_fullWidth','legend_onClick','legend_labels_boxWidth','legend_labels_fontSize','legend_labels_fontStyle','legend_labels_fontColor',
    'legend_labels_fontFamily','legend_labels_padding','legend_labels_generateLabels','legend_labels_usePointStyle','legend_labels_reverse'
  ];

  function trahchart(){
    add_action('init', array($this,'AY_register_trahchart_post_type'));
    add_action( 'wp_enqueue_scripts', array($this, 'AY_adding_scripts'));
    add_shortcode( 'coba_shortcode', array($this, 'AY_shortcode'));
  }

  //add all script here for this plugin
  function AY_adding_scripts() {
    wp_register_script('chart_script', plugins_url('/source/chart_js/Chart.min.js', __FILE__), array('jquery'),'1.1', true);
    wp_enqueue_script('chart_script');
  }

  function AY_register_trahchart_post_type(){

    $singular = 'Trahchart';
    $plural = 'Trahcharts';
    $slug = 'trahchart';

    $labels = array(
      'name'                => $plural,
      'singular_name'       => $singular,
      'add_name'            => 'Add New',
      'add_new_item'        => 'Add New ' . $singular,
      'edit'                => 'Edit',
      'edit_item'           => 'Edit ' . $singular,
      'new_item'            => 'New ' . $singular,
      'view'                => 'View ' . $singular,
      'view_item'           => 'View ' . $singular,
      'search_item'         => 'Search ' . $plural,
      'parent'              => 'Parent ' . $singular,
      'not_found'           => 'No ' . $plural . ' found',
      'not_found_in_trash'  => 'No ' . $plural . ' in Trash',
    );

    $args = array(
      'labels'                => $labels,
      'public'                => true,
      'publicy_queryable'     => true,
      'exclude_from_search'   => false,
      'show_in_nav_menus'     => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'show_in_admin_bar'     => true,
      'menu_position'         => 10,
      'menu_icon'             => 'dashicons-businessman',
      'can_export'            => true,
      'delete_with_user'      => false,
      'hierarchical'          => false,
      'has_archive'           => true,
      'query_var'             => true,
      'capability_type'       => 'post',
      'map_meta_cap'          => true,
      //capabilities =>
      'rewrite' => array(
        'slug'        => $slug,
        'with_front'  => true,
        'pages'       => true,
        'feeds'       => true,
      ),
      'supports'  => array(
        'title',
        'editor',
        //'author',
        'custom-fields',
        //'thumbnail',
      ),
      'taxonomies'            => array( '' ),
    );
    register_post_type($slug, $args);
  }

  //function to get data from DB, id is postID
  function AY_getChartData($id){
    global $wpdb;
    $data = $wpdb->get_results( "SELECT meta_key, meta_value FROM wp_postmeta WHERE post_id = " . intval($id) );
    $data = array_slice($data, 3);
    return $data;
  }

  function AY_random_color_part() {
      return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
  }

  function AY_random_color() {
      return $this->AY_random_color_part() . $this->AY_random_color_part() . $this->AY_random_color_part();
  }

  function AY_shortcode($atts){
    //shortcode attributes
    $atts = shortcode_atts( array(
        'id'          => null,
        'type'        => 'bar',
        'responsive'  => 'true',
        'width'       => '400',
        'height'      => '400',
        'events'      => ["mousemove", "mouseout", "click", "touchstart", "touchmove", "touchend"],
        'onClick'     => null,
        'onResize'    => null,
        'responsiveAnimationDuration' => 0,
        'maintainAspectRatio'         => true,
        'legendCallback'              => 'function (chart) { }',
        'title_display'     => false,
        'title_text'        => "",
        'title_position'    => 'top',
        'title_fullWidth'   => true,
        'title_fontSize'    => 12,
        'title_fontFamily'  => "'Helvetica Neue', 'Helvetica', 'Arial', 'sans-serif'",
        'title_fontColor'   => "#666",
        'title_fontStyle'   => "bold",
        'title_padding'     => 10,
        'legend_display'    => true,
        'legend_position'   => 'top',
        'legend_fullWidth'  => true,
        'legend_onClick'    => 'function(event, legendItem) {}',
        'legend_labels_boxWidth'            => 40,
        'legend_labels_fontSize'            => 12,
        'legend_labels_fontStyle'           => "normal",
        'legend_labels_fontColor'           => "$666",
        'legend_labels_fontFamily'          => "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
        'legend_labels_padding'             => 10,
        'legend_labels_generateLabels'      => "function(chart){}",
        'legend_labels_usePointStyle'       => false,
        'legend_labels_reverse'             => false,
        /*'tooltip_enabled'               => true,
        'tooltip_custom'                => null,
        'tooltip_mode'                  => 'single',
        'tooltip_itemSort'              => null,
        'tooltip_backgroundColor'       => 'rgba(0,0,0,0.8)',
        'tooltip_titleFontFamily'       => "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
        'tooltip_titleFontSize'         => 12,
        'tooltip_titleFontStyle'        => 'bold',
        'tooltip_titleFontColor'        => '#fff',
        'tooltip_titleSpacing'          => 2,
        'tooltip_titleMarginBottom'     => 6,
        'tooltip_bodyFontFamily'        => "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
        'tooltip_bodyFontSize'          => 12,
        'tooltip_bodyFontStyle'         => 'normal',
        'tooltip_bodyFontColor'         => '#fff',
        'tooltip_bodySpacing'           => 2,
        'tooltip_footerFontFamily'      => "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
        'tooltip_footerFontSize'        => 12,
        'tooltip_footerFontStyle'       => 'bold',
        'tooltip_footerFontColor'       => '#fff',
        'tooltip_footerSpacing'         => 2,
        'tooltip_footerMarginTop'       => 6,
        'tooltip_xPadding'              => 6,
        'tooltip_yPadding'              => 6,
        'tooltip_caretSize'             => 5,
        'tooltip_cornerRadius'          => 6,
        'tooltip_multiKeyBackground'    => '#fff',
        'tooltip_callbacks'             => null,
        'hover_mode'                    => 'single',
        'hover_animationDuration'       => 400,
        'hover_onHover'                 => null,
        */
    ), $atts );


    $post_id = $atts['id'];
    if($post_id == null){
      exit;
    }

    $datas = $this->AY_getChartData($post_id);
    $chart_label = array();
    $chart_data = array();
    $chart_color = array();
    foreach($datas as $data){
      echo $data->meta_key . " => " . $data->meta_value . "\n";
      array_push($chart_label, $data->meta_key);
      array_push($chart_data, $data->meta_value);
      array_push($chart_color, $this->AY_random_color());
    }

    $type       = $atts['type'];
    $responsive = $atts['responsive'];
    $width      = $atts['width'];
    $height     = $atts['height'];
    $events     = $atts['events'];
    $onClick    = $atts['onClick'];
    $onResize   = $atts['onResize'];
    $responsiveAnimationDuration = $atts['responsiveAnimationDuration'];
    $maintainAspectRatio         = $atts['maintainAspectRatio'];
    $legendCallback              = $atts['legendCallback'];
    $title_display     = $atts['title_display'];
    $title_text        = $atts['title_text'];
    $title_position    = $atts['title_position'];
    $title_fullWidth   = $atts['title_fullWidth'];
    $title_fontSize    = $atts['title_fontSize'];
    $title_fontFamily  = $atts['title_fontFamily'];
    $title_fontColor   = $atts['title_fontColor'];
    $title_fontStyle   = $atts['title_fontStyle'];
    $title_padding     = $atts['title_padding'];
    $legend_display    = $atts['legend_display'];
    $legend_position   = $atts['legend_position'];
    $legend_fullWidth  = $atts['legend_fullWidth'];
    $legend_onClick    = $atts['legend_onClick'];
    /*$tooltip_enabled               = $atts['tooltip_enabled'];
    $tooltip_custom                = $atts['tooltip_custom'];
    $tooltip_mode                  = $atts['tooltip_mode'];
    $tooltip_itemSort              = $atts['tooltip_itemSort'];
    $tooltip_backgroundColor       = $atts['tooltip_backgroundColor'];
    $tooltip_titleFontFamily       = $atts['tooltip_titleFontFamily'];
    $tooltip_titleFontSize         = $atts['tooltip_titleFontSize'];
    $tooltip_titleFontStyle        = $atts['tooltip_titleFontStyle'];
    $tooltip_titleFontColor        = $atts['tooltip_titleFontColor'];
    $tooltip_titleSpacing          = $atts['tooltip_titleSpacing'];
    $tooltip_titleMarginBottom     = $atts['tooltip_titleMarginBottom'];
    $tooltip_bodyFontFamily        = $atts['tooltip_bodyFontFamily'];
    $tooltip_bodyFontSize          = $atts['tooltip_bodyFontSize'];
    $tooltip_bodyFontStyle         = $atts['tooltip_bodyFontStyle'];
    $tooltip_bodyFontColor         = $atts['tooltip_bodyFontColor'];
    $tooltip_bodySpacing           = $atts['tooltip_bodySpacing'];
    $tooltip_footerFontFamily      = $atts['tooltip_footerFontFamily'];
    $tooltip_footerFontSize        = $atts['tooltip_footerFontSize'];
    $tooltip_footerFontStyle       = $atts['tooltip_footerFontStyle'];
    $tooltip_footerFontColor       = $atts['tooltip_footerFontColor'];
    $tooltip_footerSpacing         = $atts['tooltip_footerSpacing'];
    $tooltip_footerMarginTop       = $atts['tooltip_footerMarginTop'];
    $tooltip_xPadding              = $atts['tooltip_xPadding'];
    $tooltip_yPadding              = $atts['tooltip_yPadding'];
    $tooltip_caretSize             = $atts['tooltip_caretSize'];
    $tooltip_cornerRadius          = $atts['tooltip_cornerRadius'];
    $tooltip_multiKeyBackground    = $atts['tooltip_multiKeyBackground'];
    $tooltip_callbacks             = $atts['tooltip_callbacks']; */
    $hover_mode                    = $atts['hover_mode'];
    $hover_onHover                 = $atts['hover_onHover'];
    $hover_animationDuration       = $atts['hover_animationDuration'];

    $element = null;
    $element = "<canvas id='chart_".$post_id."' width='".$width."' height='".$height."'></canvas>
    <script>
    jQuery(document).ready( function(){
      var ctx = jQuery('#chart_".$post_id."');
      console.log(ctx);
      var myChart = new Chart(ctx, {
          type: ".json_encode($type).",
          data: {
              labels: ".json_encode($chart_label).",
              datasets: [{
                  label: '# of Votes',
                  data: ".json_encode($chart_data).",
                  backgroundColor: ".json_encode($chart_color).",
                  borderColor: ".json_encode($chart_color).",
                  borderWidth: 1
              }]
          },
          options: {
              responsive  : ".json_encode($responsive).",
              events      : ".json_encode($events).",
              onClick     : ".json_encode($onClick).",
              onResize    : ".json_encode($onResize).",
              responsiveAnimationDuration : ".json_encode($responsiveAnimationDuration).",
              maintainAspectRatio         : ".json_encode($maintainAspectRatio).",
              legendCallback              : ".json_encode($legendCallback).",
              title: {
                  display     : ".json_encode($title_display).",
                  text        : ".json_encode($title_text).",
                  position    : ".json_encode($title_position).",
                  fullWidth   : ".json_encode($title_fullWidth).",
                  fontSize    : ".json_encode($title_fontSize).",
                  fontFamily  : ".json_encode($title_fontFamily).",
                  fontColor   : ".json_encode($title_fontColor).",
                  fontStyle   : ".json_encode($title_fontStyle).",
                  padding     : ".json_encode($title_padding).",
              },
              "./*tooltips: {
                enabled               : ".json_encode($tooltip_enabled).",
                custom                : ".json_encode($tooltip_custom).",
                mode                  : ".json_encode($tooltip_mode).",
                itemSort              : ".json_encode($tooltip_itemSort).",
                backgroundColor       : ".json_encode($tooltip_backgroundColor).",
                titleFontFamily       : ".json_encode($tooltip_titleFontFamily).",
                titleFontSize         : ".json_encode($tooltip_titleFontSize).",
                titleFontStyle        : ".json_encode($tooltip_titleFontSize).",
                titleFontColor        : ".json_encode($tooltip_titleFontColor).",
                titleSpacing          : ".json_encode($tooltip_titleSpacing).",
                titleMarginBottom     : ".json_encode($tooltip_titleMarginBottom).",
                bodyFontFamily        : ".json_encode($tooltip_bodyFontFamily).",
                bodyFontSize          : ".json_encode($tooltip_bodyFontSize).",
                bodyFontStyle         : ".json_encode($tooltip_bodyFontStyle).",
                bodyFontColor         : ".json_encode($tooltip_bodyFontColor).",
                bodySpacing           : ".json_encode($tooltip_bodySpacing).",
                footerFontFamily      : ".json_encode($tooltip_footerFontFamily).",
                footerFontSize        : ".json_encode($tooltip_footerFontSize).",
                footerFontStyle       : ".json_encode($tooltip_footerFontStyle).",
                footerFontColor       : ".json_encode($tooltip_footerFontColor).",
                footerSpacing         : ".json_encode($tooltip_footerSpacing).",
                footerMarginTop       : ".json_encode($tooltip_footerMarginTop).",
                xPadding              : ".json_encode($tooltip_xPadding).",
                yPadding              : ".json_encode($tooltip_yPadding).",
                caretSize             : ".json_encode($tooltip_caretSize).",
                cornerRadius          : ".json_encode($tooltip_cornerRadius).",
                multiKeyBackground    : ".json_encode($tooltip_multiKeyBackground).",
                callbacks             : ".json_encode($tooltip_callbacks).",
              },*/"
              scales: {
                  yAxes: [{
                      ticks: {
                          beginAtZero:true
                      }
                  }]
              },
              hover: {
                'mode'                    : ".json_encode($hover_mode).",
                'onHover'                 : ".json_encode($hover_onHover).",
                'animationDuration'       : ".json_encode($hover_animationDuration).",
              }
          }
      });
    });
    </script>"
;

  return $element;
  }

}

$trahchart = new Trahchart();
