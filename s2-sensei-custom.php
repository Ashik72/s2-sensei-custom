<?php
/*
Plugin Name: s2-Sensei integration Custom Plugin
Plugin URI: https://www.upwork.com/companies/~01caf98798b24dd9af
Description: s2-Sensei integration Custom Plugin
Version: 0.0.1
Author: Ashik72
Author URI: https://www.upwork.com/companies/~01caf98798b24dd9af
License: GPLv2 or later
Text Domain: s2-sensei-custom
*/

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));


define( 's2_sensei_custom__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( 'titan-framework-checker.php' );
require_once( 'titan-framework-options.php' );


add_action('template_redirect', 'add_with_utils_class_custom', 1000 );

function add_with_utils_class_custom() {

    if (empty($_GET['s2_sensei_custom']))
        return;

    if (empty($_GET['uiq']))
        return;

    if (empty($_GET['uid']))
        return;

    $titan = TitanFramework::getInstance( 's2_sensei_custom-options' );

    if (empty($titan->getOption('s2_sensei_custom_unique_key')))
        return;

$uiq_s2_custom = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags(sanitize_text_field($titan->getOption('s2_sensei_custom_unique_key'))))))));

$uiq_s2_custom = preg_replace('/\s+/', '', $uiq_s2_custom);

    if (strcmp($_GET['uiq'], $uiq_s2_custom) !== 0)
        return;

    if(!class_exists('Sensei_Utils'))
        return;


    $s2_sensei_custom_level_cid = (empty($titan->getOption('s2_sensei_custom_level_cid')) ? "" : $titan->getOption('s2_sensei_custom_level_cid'));

    $s2_sensei_custom_level_cid = explode(PHP_EOL, $s2_sensei_custom_level_cid);

    if (!is_array($s2_sensei_custom_level_cid))
        $s2_sensei_custom_level_cid = array();

    $data_array = array();

    foreach ($s2_sensei_custom_level_cid as $key => $value) {
        $value = explode("|", $value);

        if (!empty($data_array[$value[0]]))
            array_push($data_array[$value[0]], $value[1]);
        else
            $data_array[$value[0]] = array($value[1]);

        
    }

    $user_id = (int) $_GET['uid'];

    $user_meta = get_user_meta( $user_id );

    $user_level = get_user_field('s2member_access_level', $user_id);

    if (!array_key_exists($user_level, $data_array))
        return;

    $course_ids = $data_array[$user_level];

    var_dump($course_ids);

    foreach ($course_ids as $key => $value) {
       
        Sensei_Utils::update_course_status($user_id , (int) $value, 'in-progress', array('start' => date("F j, Y, g:i a", time()) ));

    }


    wp_die();

}


?>