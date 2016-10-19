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
  var $option_general = ['responsive','events','onClick','onResize','responsiveAnimationDuration','maintainAspectRatio','legendCallback'];
  var $option_title = ['title_display','title_text','title_position','title_fullWidth','title_fontSize','title_fontFamily','title_fontColor','title_fontStyle','title_padding'];
  var $option_tooltip = [
    'tooltip_enabled','tooltip_custom','tooltip_mode','tooltip_itemSort','tooltip_backgroundColor','tooltip_titleFontFamily','tooltip_titleFontSize','tooltip_titleFontStyle','tooltip_titleFontColor','tooltip_titleSpacing','tooltip_titleMarginBottom','tooltip_bodyFontFamily','tooltip_bodyFontSize','tooltip_bodyFontStyle','tooltip_bodyFontColor',
    'tooltip_bodySpacing','tooltip_footerFontFamily','tooltip_footerFontSize','tooltip_footerFontStyle','tooltip_footerFontColor','tooltip_footerSpacing','tooltip_footerMarginTop',
    'tooltip_xPadding','tooltip_yPadding','tooltip_caretSize','tooltip_cornerRadius','tooltip_multiKeyBackground','tooltip_callbacks'
  ];
  var $option_hover = ['hover_mode','hover_animationDuration','hover_onHover'];
  var $option_legend = [
    'legend_display','legend_position','legend_fullWidth','legend_onClick'
  ];
  var $option_legend_label = ['legend_labels_boxWidth','legend_labels_fontSize','legend_labels_fontStyle','legend_labels_fontColor','legend_labels_fontFamily','legend_labels_padding','legend_labels_generateLabels','legend_labels_usePointStyle','legend_labels_reverse'];

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
        'type'        => null,
        'responsive'  => null,
        'width'       => null,
        'height'      => null,
        'events'      => null,
        'onClick'     => null,
        'onResize'    => null,
        'responsiveAnimationDuration' => null,
        'maintainAspectRatio'         => null,
        'legendCallback'              => null,
        'title_display'     => null,
        'title_text'        => null,
        'title_position'    => null,
        'title_fullWidth'   => null,
        'title_fontSize'    => null,
        'title_fontFamily'  => null,
        'title_fontColor'   => null,
        'title_fontStyle'   => null,
        'title_padding'     => null,
        'legend_display'    => null,
        'legend_position'   => null,
        'legend_fullWidth'  => null,
        'legend_onClick'    => null,
        'legend_labels_boxWidth'            => null,
        'legend_labels_fontSize'            => null,
        'legend_labels_fontStyle'           => null,
        'legend_labels_fontColor'           => null,
        'legend_labels_fontFamily'          => null,
        'legend_labels_padding'             => null,
        'legend_labels_generateLabels'      => null,
        'legend_labels_usePointStyle'       => null,
        'legend_labels_reverse'             => null,
        'tooltip_enabled'               => null,
        'tooltip_custom'                => null,
        'tooltip_mode'                  => null,
        'tooltip_itemSort'              => null,
        'tooltip_backgroundColor'       => null,
        'tooltip_titleFontFamily'       => null,
        'tooltip_titleFontSize'         => null,
        'tooltip_titleFontStyle'        => null,
        'tooltip_titleFontColor'        => null,
        'tooltip_titleSpacing'          => null,
        'tooltip_titleMarginBottom'     => null,
        'tooltip_bodyFontFamily'        => null,
        'tooltip_bodyFontSize'          => null,
        'tooltip_bodyFontStyle'         => null,
        'tooltip_bodyFontColor'         => null,
        'tooltip_bodySpacing'           => null,
        'tooltip_footerFontFamily'      => null,
        'tooltip_footerFontSize'        => null,
        'tooltip_footerFontStyle'       => null,
        'tooltip_footerFontColor'       => null,
        'tooltip_footerSpacing'         => null,
        'tooltip_footerMarginTop'       => null,
        'tooltip_xPadding'              => null,
        'tooltip_yPadding'              => null,
        'tooltip_caretSize'             => null,
        'tooltip_cornerRadius'          => null,
        'tooltip_multiKeyBackground'    => null,
        'tooltip_callbacks'             => null,
        'hover_mode'                    => null,
        'hover_animationDuration'       => null,
        'hover_onHover'                 => null,
    ), $atts );

    $post_id = $atts['id'];
    if($post_id == null){
      exit;
    }

    $type = $atts['type'];
    $width = $atts['width'];
    $height = $atts['height'];

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

    $general = new StdClass();
    foreach($this->option_general as $opt){
      $general->{$opt} = $atts[$opt];
    }

    $title = new StdClass();
    foreach($this->option_title as $opt){
      $title->{str_replace("title_", "", $opt)} = $atts[$opt];
    }

    $tooltip = new StdClass();
    foreach($this->option_tooltip as $opt){
      $tooltip->{str_replace("tooltip_", "", $opt)} = $atts[$opt];
    }

    $legend = new StdClass();
    foreach($this->option_legend as $opt){
      $legend->{str_replace("legend_", "", $opt)} = $atts[$opt];
    }

    $legend_label = new StdClass();
    foreach($this->option_legend_label as $opt){
      $legend_label->{str_replace("legend_labels_", "", $opt)} = $atts[$opt];
    }

    $hover = new StdClass();
    foreach($this->option_hover as $opt){
      $hover->{str_replace("hover_", "", $opt)} = $atts[$opt];
    }

    var_dump($title);

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
          options: {";

      foreach($general as $key=>$opt){
        if($opt != null){
          $element = $element . $key . " : " . json_encode($opt) . ",";
        }
      }

      $element = $element . "title : {";

      foreach($title as $key=>$opt){
        if($opt != null){
          $element = $element . $key . " : " . json_encode($opt) . ",";
        }
      }

      $element = $element . "}";

      $element = $element . ",legend : {";

      foreach($legend as $key=>$opt){
        if($opt != null){
          $element = $element . $key . " : " . json_encode($opt) . ",";
        }
      }

      $element = $element . "labels : {";

      foreach($legend_label as $key=>$opt){
        if($opt != null){
          $element = $element . $key . " : " . json_encode($opt) . ",";
        }
      }

      $element = $element . "}}";

      $element = $element . ",tooltips : {";

      foreach($tooltip as $key=>$opt){
        if($opt != null){
          $element = $element . $key . " : " . json_encode($opt) . ",";
        }
      }

      $element = $element . "}";

      $element = $element . ",hover : {";

      foreach($hover as $key=>$opt){
        if($opt != null){
          $element = $element . $key . " : " . json_encode($opt) . ",";
        }
      }

      $element = $element . "}";


      /*
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
              legend: {
                legend_labels_text            : ".json_encode($legend_labels_text).",
                legend_labels_fillStyle       : ".json_encode($legend_labels_fillStyle).",
                legend_labels_hidden          : ".json_encode($legend_labels_hidden).",
                legend_labels_lineCap         : ".json_encode($legend_labels_lineCap).",
                legend_labels_lineDash        : ".json_encode($legend_labels_lineDash).",
                legend_labels_lineDashOffset  : ".json_encode($legend_labels_lineDashOffset).",
                legend_labels_lineJoin        : ".json_encode($legend_labels_lineJoin).",
                legend_labels_lineWidth       : ".json_encode($legend_labels_lineWidth).",
                legend_labels_strokeStyle     : ".json_encode($legend_labels_strokeStyle).",
                legend_labels_pointStyle      : ".json_encode($legend_labels_pointStyle).",
                legend_labels_fontColor       : ".json_encode($legend_labels_fontColor).",
                labels: {

                }
              },
              tooltips: {
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
              },
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
            */
      $element = $element . "}
      });
    });
    </script>";

  //return $element
  var_dump($element);
  }

}

$trahchart = new Trahchart();
