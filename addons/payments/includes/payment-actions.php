<?php

/**
 * Publish payment on successfull posting
 * 
 * It is executed in adverts_payment_completed action.
 * 
 * @param WP_Post $payment
 * @since 0.1
 * @return null
 */
function adext_payment_completed_publish( WP_Post $payment ) {
    
    $type = get_post_meta( $payment->ID, "_adverts_payment_type", true );
    
    if( $type && $type != "adverts-pricing" ) {
        return;
    }
    
    $object_id = get_post_meta( $payment->ID, "_adverts_object_id", true );
    
    $publish = current_time('mysql');
    $publish_gmt = current_time('mysql', 1);
    
    $meta = maybe_unserialize( get_post_meta( $payment->ID, "_adverts_payment_meta", true ) );
    
    if(isset($meta["pricing"]["visible"])) {
        $visible = $meta["pricing"]["visible"];
    } else {
        $visible = 0;
    }
    

    wp_update_post( array(
        "ID" => $object_id,
        "post_status" => "publish",
        "post_date" => $publish,
        "post_date_gmt" => $publish_gmt
    ));
    
        
    if( $visible > 0) {
        $expiry = strtotime( $publish . " +$visible DAYS" );
        update_post_meta( $object_id, "_expiration_date", $expiry );
    } else {
        delete_post_meta( $object_id, "_expiration_date" );
    }
}

/**
 * Update posting expiration date
 * 
 * It is executed in adverts_payment_completed action.
 * 
 * @param WP_Post $payment
 * @since 1.1.0
 * @return null
 */
function adext_payment_completed_renew( WP_Post $payment ) {

    if( get_post_meta( $payment->ID, "_adverts_payment_type", true ) != "adverts-renewal" ) {
        return;
    }

    $object_id = get_post_meta( $payment->ID, "_adverts_object_id", true );
    
    $publish = current_time('mysql');
    $publish_gmt = current_time('mysql', 1);
    
    $meta = maybe_unserialize( get_post_meta( $payment->ID, "_adverts_payment_meta", true ) );
    
    if(isset($meta["pricing"]["visible"])) {
        $visible = $meta["pricing"]["visible"];
    } else {
        $visible = 0;
    }
    
    $expires = get_post_meta( $object_id, "_expiration_date", true );

    if( $expires ) {
        
        // Udpdate expiration date if the Ad expires
        if( $expires > current_time('timestamp') ) {
            $publish = date( "Y-m-d H:i:s", $expires );
        }

        if( $visible > 0) {
            $expiry = strtotime( $publish . " +$visible DAYS" );
            update_post_meta( $object_id, "_expiration_date", $expiry );
        } else {
            delete_post_meta( $object_id, "_expiration_date" );
        }
    }
    
    wp_update_post( array(
        "ID" => $object_id,
        "post_status" => "publish"
    ) );
}

/**
 * Notify user about successfull payment
 * 
 * This function is run when payment is completed.
 * It is executed in adverts_payment_completed action.
 * 
 * @param WP_Post $payment
 * @since 0.1
 * @return null
 */
function adext_payment_completed_notify_user( WP_Post $payment ) {
    
}

/**
 * Notify admin about successfull payment
 * 
 * This function is run when payment is completed.
 * It is executed in adverts_payment_completed action.
 * 
 * @param WP_Post $payment
 * @since 0.1
 * @return null
 */
function adext_payment_completed_notify_admin( WP_Post $payment ) {
    
}