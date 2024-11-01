<?php
/*
Plugin Name: viversum: Mondphase
Plugin URI: http://www.viversum.de/widgets/mondphase
Description: Der Mond hat einen enorm großen Einfluss auf die Lebewesen und die Natur auf der Erde. Mit dem Mondphasen-Widget sind Sie immer auf dem Laufenden, in welcher Phase der Mond sich gerade befindet.
Version: 1.0b
Author: viversum GmbH
Author URI: http://www.viversum.de
*/

/*  Copyright 2013 viversum GmbH

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Do not load directly
if (!function_exists('is_admin')) {
  header('Status: 403 Forbidden');
  header('HTTP/1.1 403 Forbidden');
  exit();
}

if (!class_exists('viversumMondphase')) {

  class viversumMondphase extends WP_Widget {
    private $jsSimple = 'http://vivget.com/viv/loader/moonphasesimple/loader.js';
    private $jsAdvanced = 'http://vivget.com/viv/loader/moonphase/color/%s/colorheadline/%s/backgroundcolortop/%s/backgroundcolorbottom/%s/loader.js';
    private $widgetData = array(
      'Name'        => 'viversumMondphase',
      'Title'       => 'viversum: Mondphase',
      'Description' => 'Der Mond hat einen enorm großen Einfluss auf die Lebewesen und die Natur auf der Erde. Mit dem Mondphasen-Widget sind Sie immer auf dem Laufenden, in welcher Phase der Mond sich gerade befindet.',
    );

    private $widgetFormData = array(
      'Title'                 => 'Mondphase von viversum.de',
      'Advanced'              => '',
      'ColorBackgroundTop'    => '#132347',
      'ColorBackgroundBottom' => '#2F4870',
      'ColorTextHeadline'     => '#FFFFDE',
      'ColorText'             => '#FFFFFF'
    );

    public function viversumMondphase() {
      viversumMondphase::__construct();
    }

    public function __construct() {

      $widget_options = array(
        'classname'   => $this->widgetData['Name'],
        'description' => __($this->widgetData['Description'])
      );
      $control_options = array();
      $this->WP_Widget($this->widgetData['Name'], __($this->widgetData['Title']), $widget_options, $control_options);
    }

    /**
     * form
     *
     * @see WP_Widget::form()
     */
    public function form($instance) {
      /**
       * form defaults
       *
       * @var array
       */
      $instance = wp_parse_args((array)$instance, $this->widgetData);
      ?>
      <script type="text/javascript">
        jQuery(document).ready(function ($) {
          $('.color-background-top').wpColorPicker();
          $('.color-background-bottom').wpColorPicker();
          $('.color-text-headline').wpColorPicker();
          $('.color-text').wpColorPicker();
        });
      </script>
      <?php
      // Simple/advanced toggle
      if ($instance['Advanced'] == 1) {
        $defaultChecked = 'checked="checked"';
      } else {
        $defaultChecked = "";
      }
      echo '<p><input id="' . $this->get_field_id('Advanced') . '" name="' . $this->get_field_name('Advanced') . '"type="checkbox" value="1"' . $defaultChecked . '/> Links aktivieren?</p>';

      if ($instance['Advanced'] != 1) {
        echo '<p style="color:#f00;">Links zu viversum aktivieren und Zugriff auf verschiedene Varianten und Farbeinstellungen erhalten</p>';
        echo '<p style="clear:both;"></p>';
      } else {
        // advanced settings

        // title
        echo '<label for="' . $this->get_field_id('Title') . '">' . __('Titel:') . '</label>';
        echo '<p><input id="' . $this->get_field_id('Title') . '" name="' . $this->get_field_name('Title') . '" type="text" value="' . $instance['Title'] . '" /></p>';
        echo '<p style="clear:both;"></p>';

        // color text
        echo '<label for="' . $this->get_field_id('ColorText') . '">' . __('Textfarbe:') . '</label>';
        echo '<p><input class="color-text" id="' . $this->get_field_id('ColorText') . '" name="' . $this->get_field_name('ColorText') . '" type="text" value="' . $instance['ColorText'] . '" /></p>';
        echo '<p style="clear:both;"></p>';

        // color text headline
        echo '<label for="' . $this->get_field_id('ColorTextHeadline') . '">' . __('Textfarbe Überschrift:') . '</label>';
        echo '<p><input class="color-text-headline" id="' . $this->get_field_id('ColorTextHeadline') . '" name="' . $this->get_field_name('ColorTextHeadline') . '" type="text" value="' . $instance['ColorTextHeadline'] . '" /></p>';
        echo '<p style="clear:both;"></p>';

        // color background-top
        echo '<label for="' . $this->get_field_id('ColorBackgroundTop') . '">' . __('Farbe Hintergrund Oben:') . '</label>';
        echo '<p><input class="color-background-top" id="' . $this->get_field_id('ColorBackgroundTop') . '" name="' . $this->get_field_name('ColorBackgroundTop') . '" type="text" value="' . $instance['ColorBackgroundTop'] . '" /></p>';
        echo '<p style="clear:both;"></p>';

        // color background-bottom
        echo '<label for="' . $this->get_field_id('ColorBackgroundBottom') . '">' . __('Farbe Hintergrund Unten:') . '</label>';
        echo '<p><input class="color-background-bottom" id="' . $this->get_field_id('ColorBackgroundBottom') . '" name="' . $this->get_field_name('ColorBackgroundBottom') . '" type="text" value="' . $instance['ColorBackgroundBottom'] . '" /></p>';
        echo '<p style="clear:both;"></p>';



      }
    }

    /**
     * save settings to db
     *
     * @see WP_Widget::update()
     */
    public function update($new_instance, $old_instance) {
      $instance = $old_instance;

      /**
       * defaults
       *
       * @var array
       */
      $new_instance = wp_parse_args((array)$new_instance, $this->widgetFormData);

      foreach ($this->widgetFormData as $key => $keyData) {
        $instance[$key] = (string)strip_tags($new_instance[$key]);
      }
      return $instance;
    }

    /**
     * Widget frontend
     *
     * @see WP_Widget::widget()
     */
    public
    function widget($args, $instance) {
      extract($args);

      echo $before_widget;

      $title = (empty($instance['Title'])) ? '' : apply_filters('my_widget_title', $instance['Title']);

      if (!empty($title)) {
        echo $before_title . $title . $after_title;
      }

      echo $this->viv_widget_html_output($instance);
      echo $after_widget;
    }

    /**
     * Widget output
     *
     * @param array $args
     */
    private
    function viv_widget_html_output($args = array()) {
      /**
       * guess what: output
       */
      if ($args['Advanced'] == 1) {
        $widgetJS = sprintf($this->jsAdvanced, str_replace('#', '', $args['ColorText']), str_replace('#', '', $args['ColorTextHeadline']), str_replace('#', '', $args['ColorBackgroundTop']), str_replace('#', '', $args['ColorBackgroundBottom'])  );
      } else {
        $widgetJS = $this->jsSimple;
      }

      $widgetHTML = sprintf('<script type="text/javascript" src="%s"></script>', $widgetJS);
      if ($args['Advanced'] == 1) {
        $widgetHTML .= '<noscript><a href="http://www.viversum.de">viversum Lebensberatung</a></noscript>';
      }

      return $widgetHTML;
    } // private function viv_widget_html_output($args = array())
  }

  /**
   * adding colorpicker
   */
  add_action('widgets_init', 'viv_moon_enqueue_color_picker');
  function viv_moon_enqueue_color_picker($hook_suffix) {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('viv-script-handle', plugins_url('viv-script.js', __FILE__), array('wp-color-picker'), false, true);
  }

  /**
   * widget initialization
   */
  add_action('widgets_init', create_function('', 'return register_widget("viversumMondphase");'));
}