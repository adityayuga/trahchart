<?php
/**
* Plugin Name: Trahchart Plugin
* Plugin URI: https://github.com/adityayuga/
* Author: Aditya Yuga
* Author URI: https://github.com/adityayuga/
* Description: A plugin to make a chart
* Version: 1.0
* License: GPLv2
*/

if(!defined('ABSPATH')){
  exit;
}

require 'widget/widget.php';

class Trahchart {
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

  var $option_animation = ['animation_duration', 'animation_easing', 'animation_onProgress', 'animation_onComplete'];
  var $option_arc = ['arc_backgroundcolor', 'arc_bordercolor', 'arc_borderwidth'];
  var $option_line = ['line_tension', 'line_backgroundColor', 'line_borderWidth', 'line_borderColor', 'line_borderCapStyle', 'line_borderDash', 'line_borderDashOffset', 'line_borderJoinStyle', 'line_capBezierPoints', 'line_fill', 'line_stepped'];
  var $option_point = ['point_radius', 'point_pointStyle', 'point_backgroundColor', 'point_borderWidth', 'point_borderColor', 'point_hitRadius', 'point_hoverRadius', 'point_hoverBorderWidth'];
  var $option_rectangle = ['rectangle_backgroundColor', 'rectangle_borderWidth', 'rectangle_borderColor', 'rectangle_borderSkipped'];

  function trahchart(){
    add_action('init', array($this,'AY_register_trahchart_post_type'));
    add_action( 'wp_enqueue_scripts', array($this, 'AY_adding_scripts'));
    add_shortcode( 'trahchart', array($this, 'AY_shortcode'));
    add_action( 'widgets_init', array($this, 'AY_load_widget'));
  }

  function email_api_main()
  {
    echo '<h1>HERE WE ADD THE REST OF THE CODE...</h1>';
  }

  // Register and load the widget
  function AY_load_widget() {
    register_widget( 'Trahchart_Widget' );
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
    //$data = array_slice($data, 2);
    return $data;
  }

  function AY_random_color() {
      //return '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
    $characters  = 'ABCDEF0123456789';
    $hexadecimal = '#';
    for ($i = 1; $i <= 6; $i++) {
      $position     = rand(0, strlen($characters) - 1);
      $hexadecimal .= $characters[$position];
    }
    return $hexadecimal;
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
        'onclick'     => null,
        'onresize'    => null,
        'colors'      => null,
        'responsiveanimationduration' => null,
        'maintainaspectratio'         => null,
        'legendcallback'              => null,
        'title_display'     => null,
        'title_text'        => null,
        'title_position'    => null,
        'title_fullwidth'   => null,
        'title_fontsize'    => null,
        'title_fontfamily'  => null,
        'title_fontcolor'   => null,
        'title_fontstyle'   => null,
        'title_padding'     => null,
        'legend_display'    => null,
        'legend_position'   => null,
        'legend_fullwidth'  => null,
        'legend_onclick'    => null,
        'legend_labels_boxwidth'            => null,
        'legend_labels_fontsize'            => null,
        'legend_labels_fontstyle'           => null,
        'legend_labels_fontcolor'           => null,
        'legend_labels_fontfamily'          => null,
        'legend_labels_padding'             => null,
        'legend_labels_generatelabels'      => null,
        'legend_labels_usepointstyle'       => null,
        'legend_labels_reverse'             => null,
        'tooltip_enabled'               => null,
        'tooltip_custom'                => null,
        'tooltip_mode'                  => null,
        'tooltip_itemsort'              => null,
        'tooltip_backgroundcolor'       => null,
        'tooltip_titlefontfamily'       => null,
        'tooltip_titlefontsize'         => null,
        'tooltip_titlefontstyle'        => null,
        'tooltip_titlefontcolor'        => null,
        'tooltip_titlespacing'          => null,
        'tooltip_titlemarginbottom'     => null,
        'tooltip_bodyfontfamily'        => null,
        'tooltip_bodyfontsize'          => null,
        'tooltip_bodyfontstyle'         => null,
        'tooltip_bodyfontcolor'         => null,
        'tooltip_bodyspacing'           => null,
        'tooltip_footerfontfamily'      => null,
        'tooltip_footerfontsize'        => null,
        'tooltip_footerfontstyle'       => null,
        'tooltip_footerfontcolor'       => null,
        'tooltip_footerspacing'         => null,
        'tooltip_footermargintop'       => null,
        'tooltip_xpadding'              => null,
        'tooltip_ypadding'              => null,
        'tooltip_caretsize'             => null,
        'tooltip_cornerradius'          => null,
        'tooltip_multikeybackground'    => null,
        'tooltip_callbacks'             => null,
        'hover_mode'                    => null,
        'hover_animationduration'       => null,
        'hover_onhover'                 => null,
        'animation_duration'            => null,
        'animation_easing'              => null,
        'animation_onprogress'          => null,
        'animation_oncomplete'          => null,
        'arc_backgroundcolor'           => null,
        'arc_bordercolor'               => null,
        'arc_borderwidth'               => null,
        'line_tension'                  => null,
        'line_backgroundcolor'          => null,
        'line_borderwidth'              => null,
        'line_bordercolor'              => null,
        'line_bordercapstyle'           => null,
        'line_borderdash'               => null,
        'line_borderdashoffset'         => null,
        'line_borderjoinstyle'          => null,
        'line_capbezierpoints'          => null,
        'line_fill'                     => null,
        'line_stepped'                  => null,
        'point_radius'                  => null,
        'point_pointstyle'              => null,
        'point_backgroundcolor'         => null,
        'point_borderwidth'             => null,
        'point_bordercolor'             => null,
        'point_hitradius'               => null,
        'point_hoverradius'             => null,
        'point_hoverborderwidth'        => null,
        'rectangle_backgroundcolor'     => null,
        'rectangle_borderwidth'         => null,
        'rectangle_bordercolor'         => null,
        'rectangle_borderskipped'       => null,

    ), $atts );

    $post_id = $atts['id'];
    if($post_id == null){
      exit;
    }

    $type = $atts['type'];
    $width = $atts['width'];
    $height = $atts['height'];
    $colors = $atts['colors'];

    if($colors != "") {
      $colors = explode(',', str_replace(' ','',$colors));
    }

    $datas = $this->AY_getChartData($post_id);
    $chart_label = array();
    $chart_data = array();
    $chart_color = array();

    for($i=0; $i<count($colors); $i++){
      array_push($chart_color, $colors[$i]);
    }

    foreach($datas as $data){
      //echo $data->meta_key . " => " . $data->meta_value . "\n";
      if($data->meta_key != '_edit_last' &&  $data->meta_key != '_edit_lock'){
        array_push($chart_label, $data->meta_key);
        array_push($chart_data, $data->meta_value);
        //array_push($chart_color, $this->AY_random_color());
      }
    }

    while(count($chart_color) < count($chart_data)){
      array_push($chart_color, $this->AY_random_color());
    }

    $general = new StdClass();
    foreach($this->option_general as $opt){
      $general->{$opt} = $atts[strtolower($opt)];
    }

    $title = new StdClass();
    foreach($this->option_title as $opt){
      $title->{str_replace("title_", "", $opt)} = $atts[strtolower($opt)];
    }

    $tooltip = new StdClass();
    foreach($this->option_tooltip as $opt){
      $tooltip->{str_replace("tooltip_", "", $opt)} = $atts[strtolower($opt)];
    }

    $legend = new StdClass();
    foreach($this->option_legend as $opt){
      $legend->{str_replace("legend_", "", $opt)} = $atts[strtolower($opt)];
    }

    $legend_label = new StdClass();
    foreach($this->option_legend_label as $opt){
      $legend_label->{str_replace("legend_labels_", "", $opt)} = $atts[strtolower($opt)];
    }

    $hover = new StdClass();
    foreach($this->option_hover as $opt){
      $hover->{str_replace("hover_", "", $opt)} = $atts[strtolower($opt)];
    }

    $animation = new StdClass();
    foreach($this->option_animation as $opt){
      $animation->{str_replace("animation_", "", $opt)} = $atts[strtolower($opt)];
    }

    $arc = new StdClass();
    foreach($this->option_arc as $opt){
      $arc->{str_replace("arc_", "", $opt)} = $atts[strtolower($opt)];
    }

    $animation = new StdClass();
    foreach($this->option_animation as $opt){
      $animation->{str_replace("animation_", "", $opt)} = $atts[strtolower($opt)];
    }

    $line = new StdClass();
    foreach($this->option_line as $opt){
      $line->{str_replace("line_", "", $opt)} = $atts[strtolower($opt)];
    }

    $point = new StdClass();
    foreach($this->option_point as $opt){
      $point->{str_replace("point_", "", $opt)} = $atts[strtolower($opt)];
    }

    $rectangle = new StdClass();
    foreach($this->option_rectangle as $opt){
      $rectangle->{str_replace("rectangle_", "", $opt)} = $atts[strtolower($opt)];
    }

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
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            },";

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

      $element = $element . "}, elements : {";

      if($type == 'polar_area' || $type="doughnut" || $type="pie"){
        $element = $element . "arc : {";
        foreach($arc as $key=>$opt){
          if($opt != null){
            $element = $element . $key . " : " . json_encode($opt) . ",";
          }
        }
        $element = $element . "},";
      }

      if($type == 'line'){
        $element = $element . "line : {";
        foreach($line as $key=>$opt){
          if($opt != null){
            $element = $element . $key . " : " . json_encode($opt) . ",";
          }
        }
        $element = $element . "},";
      }

      if($type == 'line' || $type == 'bubble'){
        $element = $element . "point : {";
        foreach($point as $key=>$opt){
          if($opt != null){
            $element = $element . $key . " : " . json_encode($opt) . ",";
          }
        }
        $element = $element . "},";
      }

      if($type == 'bar'){
        $element = $element . "rectangle : {";
        foreach($rectangle as $key=>$opt){
          if($opt != null){
            $element = $element . $key . " : " . json_encode($opt) . ",";
          }
        }
        $element = $element . "},";
      }

      $element = $element . "}}
      });
    });
    </script>";

  return $element;
  }

}

$trahchart = new Trahchart();

function AY_doc_view(){
  ob_start();
  ?>
  <div><h2>DOCUMENTATION</h2></div>
  <br/>
  <div>
    <h3><b>Quick How To Use:</b></h3>
    <div>
      <p>Chart Data:</p>
      <p>Please create chart data by post in trahchart menu, you can input the data with the label</p>
      <p>The plugin will use the data by inputing the ID of the post</p>
    </div>
  </div>
  <br/>
  <div>
    <h3><b>Example Shortcode Usage</b></h3>
    <div class="postbox" style="width:50%;">
      <h4 style="padding: 1px 12px;">Pie chart:</h4>
      <pre style="padding: 1px 12px;" class="shortcode">[trahchart  type='pie' id='1']</pre>
    </div>
    <div class="postbox" style="width:50%;">
      <h4 style="padding: 1px 12px;">Bar chart:</h4>
      <pre style="padding: 1px 12px;" class="shortcode">[trahchart  type='bar' id='1']</pre>
    </div>
    <div class="postbox" style="width:50%;">
      <h4 style="padding: 1px 12px;">Line chart:</h4>
      <pre style="padding: 1px 12px;" class="shortcode">[trahchart  type='line' id='1']</pre>
    </div>
    <div class="postbox" style="width:50%;">
      <h4 style="padding: 1px 12px;">Bubble chart:</h4>
      <pre style="padding: 1px 12px;" class="shortcode">[trahchart  type='bubble' id='1']</pre>
    </div>
    <div class="postbox" style="width:50%;">
      <h4 style="padding: 1px 12px;">Doughnut chart:</h4>
      <pre style="padding: 1px 12px;" class="shortcode">[trahchart  type='doughnut' id='1']</pre>
    </div>
    <div class="postbox" style="width:50%;">
      <h4 style="padding: 1px 12px;">Polar Area:</h4>
      <pre style="padding: 1px 12px;" class="shortcode">[trahchart  type='polar_area' id='1']</pre>
    </div>
  </div>
  <?php
  $content = ob_get_clean();
  echo $content;
}

function AY_add_sub_menu(){
  add_submenu_page( 'edit.php?post_type=trahchart', 'Documentation', 'Documentation', 'manage_options', 'trahchart-documentation-menu', 'AY_doc_view' );
}
add_action( 'admin_menu', 'AY_add_sub_menu' );
