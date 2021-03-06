<?php
namespace Qazana;

/**
 * Qazana Template Functions
 *
 * This file contains functions necessary to mirror the WordPress core widget
 * loading process. Many of those functions are not filterable, and even then
 * would not be robust enough to predict where Qazana widgets might exist.
 *
 * @package Qazana
 * @subpackage TemplateFunctions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

Class Loader {

    private $_stack = array();

    private $_stack_uri = array();

    private $_locations = array();

    /**
     * Initialize Loader
     */
    public function __construct() {  }

    /**
    * Add a new extension path
    */
    public function add_stack( $base, $locations ) {
        $this->_stack[] = $base['path'];
        $this->_stack_uri[] = $base['uri'];

        foreach ( $locations as $location ) {
            $this->_locations[] = $location;
        }
    }

    /**
     * Call the functions added to the 'qazana_widget_stack' filter hook, and return
     * an array of the widget locations.
     *
     * @since 1.0.0
     *
     * @return array The filtered value after all hooked functions are applied to it.
     */
    protected function get_file_locations() {
        return array_unique( $this->_locations );
    }

    /**
     * Call the functions added to the 'qazana_widget_stack' filter hook, and return
     * an array of the widget locations.
     *
     * @since 1.0.0
     *
     * @return array The filtered value after all hooked functions are applied to it.
     */
    protected function get_file_stack() {
        return array_unique( $this->_stack );
    }

    /**
     * Call the functions added to the 'qazana_widget_stack' filter hook, and return
     * an array of the widget locations.
     *
     * @since 1.0.0
     *
     * @return array The filtered value after all hooked functions are applied to it.
     */
    protected function get_file_stack_uri() {
        return array_unique( $this->_stack_uri );
    }

    public function locate_folder( $folder_names ) {

        // No file found yet
    	$located            = false;
    	$folder_locations = $this->merge_files_stack_locations();

    	// Try to find a widget file
    	foreach ( (array) $folder_names as $folder_name ) {

    		// Continue if widget is empty
    		if ( empty( $folder_name ) ) {
    			continue;
    		}

    		// Loop through widget stack
    		foreach ( (array) $folder_locations as $folder_location ) {

    			// Continue if $file_location is empty
    			if ( empty( $folder_location ) ) {
    				continue;
    			}

    			// Check child theme first
    			if ( is_dir( trailingslashit( $folder_location ) . $folder_name ) ) {
    				$located = trailingslashit( $folder_location ) . $folder_name;
    				break 2;
    			}
    		}
        }

        return $located;

    }

    /**
     * Retrieve the name of the highest priority widget file that exists.
     *
     * Searches in the child theme before parent theme so that themes which
     * inherit from a parent theme can just overload one file. If the widget is
     * not found in either of those, it looks in the theme-compat folder last.
     *
     * @since 1.0.0
     *
     * @param string|array $file_names Template file(s) to search for, in order.
     * @param bool $load If true the widget file will be loaded if it is found.
     * @param bool $require_once Whether to require_once or require. Default true.
     *                            Has no effect if $load is false.
     * @return string The widget filename if one is located.
     */
    public function locate_widget( $file_names, $load = false, $require_once = true ) {

    	// No file found yet
    	$located            = false;
    	$file_locations = $this->merge_files_stack_locations();

    	// Try to find a widget file
    	foreach ( (array) $file_names as $file_name ) {

    		// Continue if widget is empty
    		if ( empty( $file_name ) ) {
    			continue;
    		}

    		// Loop through widget stack
    		foreach ( (array) $file_locations as $file_location ) {

    			// Continue if $file_location is empty
    			if ( empty( $file_location ) ) {
    				continue;
    			}

    			// Check child theme first
    			if ( file_exists( trailingslashit( $file_location ) . $file_name ) ) {
    				$located = trailingslashit( $file_location ) . $file_name;
    				break 2;
    			}
    		}
    	}

    	// Maybe load the widget if one was located
    	if ( ( true === $load ) && ! empty( $located ) ) {
            if ( $require_once ) {
                require_once( $located );
            } else {
                require( $located );
            }
    	}

    	return $located;
    }

    /**
     * Retrieve the name of the highest priority widget file that exists.
     *
     * Searches in the child theme before parent theme so that themes which
     * inherit from a parent theme can just overload one file. If the widget is
     * not found in either of those, it looks in the theme-compat folder last.
     *
     * @since 1.0.0
     *
     * @param string|array $file_names Template file(s) to search for, in order.
     * @param bool $load If true the widget file will be loaded if it is found.
     * @param bool $require_once Whether to require_once or require. Default true.
     *                            Has no effect if $load is false.
     * @return string The widget filename if one is located.
     */
    public function locate_widget_url( $file_names, $folder_url = false ) {

    	// No file found yet
    	$located            = false;
        $file_locations     = $this->merge_files_stack_locations();
        $file_url_stack     = $this->merge_files_stack_urls();

        // Try to find a widget file
    	foreach ( (array) $file_names as $file_name ) {

    		// Continue if widget is empty
    		if ( empty( $file_name ) ) {
    			continue;
    		}

            $count = 0;

    		// Loop through widget stack
    		foreach ( (array) $file_locations as $file_location ) {

    			// Continue if $file_location is empty
    			if ( empty( $file_location ) ) {
    				continue;
    			}

    			// Check child theme first
    			if ( file_exists( trailingslashit( $file_location ) . $file_name ) ) {
                    $located = trailingslashit( $file_url_stack [ $count ] ) . $file_name;
    				break 2;
    			} elseif ( $folder_url && is_dir( trailingslashit( $file_location ) ) ) {
                    $located = trailingslashit( $file_url_stack [ $count ] );
    				break 2;
    			}

                $count++;
    		}
    	}

    	return $located;
    }

    /**
     * Add widget locations to widget files being searched for
     *
     * @since 1.0.0
     *
     * @param array $widgets
     * @return array()
     */
    public function merge_files_stack_locations() {

        $retval = array();

        // Get widget locations
        $stacks = $this->get_file_stack();
    	// Get alternate locations
        $locations = $this->get_file_locations();

    	// Loop through locations and stacks and combine
    	foreach ( (array) $stacks as $stack ) {
    		foreach ( (array) $locations as $location ) {
    			$retval[] = untrailingslashit( trailingslashit( $stack ) . $location );
            }
        }

    	return array_unique( $retval );
    }

    /**
     * Add widget locations to widget files being searched for
     *
     * @since 1.0.0
     *
     * @param array $widgets
     * @return array()
     */
    public function merge_files_stack_urls( ) {

        $retval = array();

        // Get widget locations
        $stacks = $this->get_file_stack_uri();
    	// Get alternate locations
    	$locations = $this->get_file_locations();

    	// Loop through locations and stacks and combine
    	foreach ( (array) $stacks as $stack ) {
    		foreach ( (array) $locations as $custom_location ) {
    			$retval[] = untrailingslashit( trailingslashit( $stack ) . $custom_location );
            }
        }

    	return array_unique( $retval );
    }
}
