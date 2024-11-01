<?php
/*
Plugin Name: YoQuieroAyudar
Description: Widget para informar del total de donativos recaudados con YoQuieroAyudar. Widget to show total donations and a link to download the app
Author: Víctor Bellés
www.yoquieroayudar.es
Asociación Micro Hucha Solidaria.
Version: 1.0.1
*/

class wp_yoquieroayudar_plugin extends WP_Widget {

	// constructor
	function wp_yoquieroayudar_plugin() {
    parent::WP_Widget(false, $name = __('YoQuieroAyudar', 'wp_widget_plugin') );
	}

  // widget display
  function widget($args, $instance) {
    extract( $args );
    wp_enqueue_style( 'myprefix-style', plugins_url('css/yoquieroayudar.css', __FILE__) );
    // these are the widget options
    $url = "http://api.microhuchasolidaria.org/metrics";
    ?>
    <script type='text/javascript'>
    function request(){
      return jQuery.ajax({
        url: "<?php echo $url ?>",
        success: function(response) {
          localStorage.setItem("totalDonations", response.total_donations);
        }
      });
    }

    jQuery(function(){
      request();
      if(localStorage.getItem("totalDonations") === null) localStorage.setItem("totalDonations", 350);
    });
    </script>
    <?php
    $instance["totalDonations"] = json_decode(wp_remote_get($url)["body"])->{"total_donations"};
    if(strcmp($instance["totalDonations"], NULL) == 0) {
      $instance["totalDonations"] = "<script>document.write(localStorage.getItem('totalDonations'));</script>";
    }

    echo $before_widget;
    // Display the widget
    echo '<div class="widget-text wp_widget_plugin_box yoquieroayudar">';

    // Set title
    echo $before_title . esc_attr("MICRO HUCHA SOLIDARIA") . $after_title;
    echo "<div class='slider'>".
            "<a class='slider-link' target='_blank' href='http://goo.gl/NSbx7g'>".
              "¡Descárgate la app!<br>" .
              '<img src="' . plugins_url( 'images/Logo-YoQuieroAyudar.png', __FILE__ ) . '" > '.
            "</a>".
            "<div class='slider-text'>".
              "<p>¡Ya hemos recaudado<br><span class='money'>".$instance["totalDonations"]." €</span><br>para asociaciones y fundaciones!</p>".
            "</div>".
        "</div>";
    echo $after_widget;
  }

	// widget update
	function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $response = wp_remote_get('http://api.microhuchasolidaria.org/metrics');
    $instance["totalDonations"] = json_decode($response["body"]);

    return $instance;
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_yoquieroayudar_plugin");'));
