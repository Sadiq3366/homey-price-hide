<?php

function homey_enqueue_styles()
{

    // enqueue parent styles
    wp_enqueue_style('homey-parent-theme', get_template_directory_uri() . '/style.css');

    // enqueue child styles
    wp_enqueue_style('homey-child-theme', get_stylesheet_directory_uri() . '/style.css', array('homey-parent-theme'));

    // enqueue child scripts
    wp_enqueue_script('sultan-dates-to-ical', get_stylesheet_directory_uri() . '/js/dates-to-ical.js', array('jquery'), '1.0.0', false);
    wp_enqueue_script('sultan-ical-ajax', get_stylesheet_directory_uri() . '/js/ical-ajax.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'homey_enqueue_styles');

// include php files
require_once('inc/simple_html_dom.php');
require_once('inc/sultan-calendar.php');
require_once( get_template_directory() . '/framework/functions/listings.php' );
require_once( get_template_directory() . '/framework/functions/reservation.php' );
require_once( get_template_directory() . '/framework/functions/search.php' );



// This is your option name where all the Redux data is stored.
$opt_name = "homey_options";

Redux::setSection($opt_name, array(
    'title'  => esc_html__('General', 'homey'),
    'id'     => 'general-options',
    'desc'   => '',
    'icon'   => 'el-icon-home el-icon-small',
    'fields'        => array(
        array(
            'id'       => 'sultan-text-under-map-mobile',
            'type'     => 'text',
            'title'    => esc_html__('Text', 'homey'),
            'desc'     => esc_html__('', 'homey'),
            'subtitle' => esc_html__('This is a field in which you add text you want. This text will appear under map in search result page.', 'homey'),
        )
    )
));


?>