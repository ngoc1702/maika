<?php
/**
 * Log backtrace khi có doing_it_wrong (ví dụ _load_textdomain_just_in_time quá sớm).
 */
add_action( 'doing_it_wrong_run', function ( $function, $message, $version ) {
    if ( strpos( $message, 'textdomain' ) !== false || strpos( $message, 'dịch' ) !== false || strpos( $message, 'genesis' ) !== false ) {
        error_log( "doing_it_wrong: $function | since $version | $message" );
        if ( function_exists( 'wp_debug_backtrace_summary' ) ) {
            error_log( 'Backtrace: ' . wp_debug_backtrace_summary() );
        }
    }
}, 10, 3 );
