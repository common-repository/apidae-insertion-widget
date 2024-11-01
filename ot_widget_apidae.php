<?php
/**
 * Plugin Name: Insertion de Widget Apidae
 * Plugin URI:  https://www.perouges-bugey-tourisme.com/
 * Description: Ce plugin ajoute un "code court" permettant d'afficher facilement un Widget Apidae. Pour l'utiliser entrer : [wapidae id="XXX"]. Développé par Bugey Plaine de l'Ain Tourisme et Apidae tourisme. Plugin réservé aux partenaires Apidae. En cas de bug, veuillez contacter notre hotline : hotline.dev@apidae-tourisme.com\nVersion 2.0 | Par [Vincent Gaullier, Maxime Berger](https://vincentgaullier.fr/) | [Aller sur le site de l’extension](https://www.perouges-bugey-tourisme.com/)
 * Author:      Vincent Gaullier, Maxime Berger
 * Author URI:  https://vincentgaullier.fr/
 * Licence:     GPL-3.0
 * Licence URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Version:     3.0
 * Text Domain: ot_widget_apidae
 * Domain Path: /languages
 */

/**
 * WAPIDAE est un plugin qui ajoute un shortcode pour générer des widgets Apidae
 * Copyright (C) 2020 GAULLIER, BERGER
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Params plugin
 $ot_iwa_plugin_slug = $ot_iwa_plugin_textdomain = 'ot_widget_apidae';
 $ot_iwa_plugin_name = translate('Insertion de Widget Apidae', $ot_iwa_plugin_textdomain);
 $ot_iwa_plugin_desc = translate('Ce plugin ajoute un shortcode pour générer des widgets Apidae. Pour l\'utiliser entrer : [wapidae id="XXX"]. Développé par "Bugey Plaine de l\'Ain Tourisme" et "Apidae tourisme" pour l\'utilisation des partenaires et collaborateurs. En cas de résolution de bug, veuillez contacter notre hotline : hotline.dev@apidae-tourisme.com', $ot_iwa_plugin_textdomain);

 // Params Apidae, les dynamiser ?
 $ot_iwa_html_apidae = '<div id="widgit"></div>';
 $ot_iwa_url_apidae = 'https://widgets.apidae-tourisme.com/widget/';
 //Basé sur : <script src="https://widgets.apidae-tourisme.com/widget/864.js" async></script>


 /**
  * Textedomain
  */
function ot_iwa_load_plugin_textdomain() {
  global $ot_iwa_plugin_textdomain;
	$locale = apply_filters( 'plugin_locale', get_locale(), $ot_iwa_plugin_textdomain );

	if ( $loaded = load_textdomain( $ot_iwa_plugin_textdomain, trailingslashit( WP_LANG_DIR ) . $ot_iwa_plugin_textdomain . '/' . $ot_iwa_plugin_textdomain . '-' . $locale . '.mo' ) ) {
		return $loaded;
	} else {
		load_plugin_textdomain( $ot_iwa_plugin_textdomain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

}
add_action( 'init', 'ot_iwa_load_plugin_textdomain' );

/**
 * add async
 * @param $tag
 * @param $handle
 * @return string|string[]
 */
function ot_iwa_async_attribute($tag, $handle) {
  global $ot_iwa_plugin_slug;
	if ( $ot_iwa_plugin_slug === $handle ) {
	   $tag = str_replace( ' src', ' async src', $tag );
  }
  return $tag;
}
add_filter('script_loader_tag', 'ot_iwa_async_attribute', 10, 2);


/**
 * Shortcode
 * @param $atts
 * @return string
 */
 function ot_iwa_shortcode($atts){
   global $ot_iwa_plugin_name,
          $ot_iwa_html_apidae,
          $ot_iwa_plugin_slug,
          $ot_iwa_plugin_textdomain,
          $ot_iwa_url_apidae;
    extract(shortcode_atts(
        array(
    	    'id' => 0
    ), $atts));

    if(empty($id) || $id === "0"){
        $admin = false;
        // Si admin, alors affichage
        if ( is_user_logged_in() && current_user_can('administrator') ) {
          $html = '<p style="color:red;">';
          $admin = true;
        }else{
          // Sinon, planqué dans le code.
          $html = '<div class="ot-iwa-debug " style="display:none !important">';
        }

        $html .= '<strong>['.$ot_iwa_plugin_name.']</strong><br>'.__('Vous n\'avez pas renseigné d\'id de widget (à la fin de l\'url d\'intégration)', $ot_iwa_plugin_textdomain);

        if($admin === true) {
          $html .= '</p>';
        }else{
          $html .= '</div>';
        }

    }else{
      wp_register_script($ot_iwa_plugin_slug, $ot_iwa_url_apidae.$id.'.js', array(), null, true);
      wp_enqueue_script($ot_iwa_plugin_slug);
      $html = $ot_iwa_html_apidae;
    }
    return $html;
}
add_shortcode('wapidae', 'ot_iwa_shortcode');
