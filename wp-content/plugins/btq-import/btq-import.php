<?php
/**
 * Plugin Name: BTQ Import
 * Plugin URI: http://btqdesign.com/plugins/btq-import/
 * Description: Importando post de otros sistemas.
 * Version: 0.1.0
 * Author: BTQ Import
 * Author URI: http://btqdesign.com/
 * Requires at least: 4.9.7
 * Tested up to: 4.9.7
 * 
 * Text Domain: btq-import
 * Domain Path: /languages
 * 
 * @package btq-import
 * @category Core
 * @author BTQ Design
 */


// Exit if accessed directly
defined('ABSPATH') or die('No script kiddies please!');

/** 
 * Establece el dominio correcto para la carga de traducciones
 */
load_plugin_textdomain('btq-import', false, basename( dirname( __FILE__ ) ) . '/languages');

/**
 * Almacena en un archivo el contenido de una variable.
 *
 * Con el proposito de poder depurar el código es necesario poder
 * conocer el resultado de cadenas de caracteres, arreglos, consultas 
 * y funciones, por ello esta funcion almacena en un archvo adentro
 * adentro de la carpeta "log" del plugin el contenido de la 
 * variable que se indique en el paramento $var .
 *
 * @author Saúl Díaz
 * @access public
 * @param string $file_name Nombre del archivo .log
 * @param string $var Variable a depurar, si es diferente a tipo 
 *		cadena la pasa por la función var_export
 * @param bool $same_file El valor predeterminado false indica que 
 * 		cada vez que se llama la función crea un nuevo archivo .log . 
 * 		El valor true utiliza el mismo archivo .log .
 * @return file Escribe en el archivo .log el contenido de la variable.
 */ 
function btq_import_log($file_name, $var, $same_file = false){
	$log_dir = plugin_dir_path( __FILE__ ) . 'log' ;
	
	if (!file_exists($log_dir)) {
		mkdir($log_dir, 0755);
	}
	
	if(is_string($var)){
		$string = $var;
	}
	else {
		$string = var_export($var, TRUE);
	}
	
	if ($same_file){
		$file_path = $log_dir . DIRECTORY_SEPARATOR . $file_name . '.log';
		file_put_contents($file_path, date('[Y-m-d H:i:s] ') . $string . "\n", FILE_APPEND | LOCK_EX);
	}
	else {
		$file_path = $log_dir . DIRECTORY_SEPARATOR . $file_name . date('-Ymd-U'). '.log';
		file_put_contents($file_path, $string);
	}
}

/**
 * Genera un elemento en el menú del escritorio del wp-admin de WordPress.
 *
 * El menú generado llama la funcion que genera la página de ajustes y la
 * página del depurador.
 * 
 * @author Saúl Díaz
 * @return void Genera el menú y sub-menú en el escritorio del wp-admin de
 * 		WordPress.
 */
function btq_import_admin_menu() {
    add_menu_page(
        __('BTQ Import', 'btq-booking-tc'),
        __('BTQ Import', 'btq-booking-tc'),
        'manage_options',
        'btq_import',
        'btq_import_admin_page',
        'dashicons-admin-generic',
        100
    );
}
add_action( 'admin_menu', 'btq_import_admin_menu' );

function btq_import_admin_page(){
	global $wpdb;
	$dbcn = new wpdb(DB_USER, DB_PASSWORD, 'emfocoyd_fip', DB_HOST);
	$results = $dbcn->get_results('SHOW TABLES');
	
	foreach ( $results as $row ) 
	{
		echo $row->Tables_in_wpemf_db;
	}
	
}