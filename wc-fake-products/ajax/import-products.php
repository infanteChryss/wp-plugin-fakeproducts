<?php if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    $url = 'http://chryss-fakeproducts.epizy.com/';
    
    // API Credentials
    $username = "chryssinfante";
    $password = ".ChRy551Nf@Nt3_";
    
    // initialize headers
    $headers = array(
        'Content-Type: application/json'
    );
    
    // initialize cURL
    $ch = curl_init();
    
    // set cURL options
    curl_setopt($ch, CURLOPT_URL, $url . '?per_page=5');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // return as string
    curl_setopt($ch, CURLOPT_POST, TRUE); // post request

    $vendors = curl_exec($ch);

    if (curl_errno($ch)) {
        echo $ch;
    } else {
        
        foreach(json_decode($vendors) as $vendor) {           
            // init user data
            $userdata = array(
                'user_pass'             => $vendor->password,   //(string) The plain-text user password.
                'user_login'            => $vendor->username,   //(string) The user's login username.
                'user_email'            => $vendor->email,   //(string) The user email address.
                'display_name'          => $vendor->company,   //(string) The user's display name. Default is the user's username.
                'first_name'            => $vendor->firstname,   //(string) The user's first name. For new users, will be used to build the first part of the user's display name if $display_name is not specified.
                'last_name'             => $vendor->lastname,   //(string) The user's last name. For new users, will be used to build the second part of the user's display name if $display_name is not specified.
                'description'           => 'Product Vendor',   //(string) The user's biographical description.
                'role'                  => 'vendor',   //(string) User's role.
             
            );
            // insert vendor as user
            $user_id = wp_insert_user( $userdata ) ;
            // On success.
            if ( ! is_wp_error( $user_id ) ) {
                add_user_meta( $user_id, '_domain', $vendor->domain);
                add_user_meta( $user_id, '_city_prefix', $vendor->cityPrefix);
                add_user_meta( $user_id, '_secondary_address', $vendor->secondaryAddress);
                add_user_meta( $user_id, '_state', $vendor->state);
                add_user_meta( $user_id, '_state_abbr', $vendor->stateAbbr);
                add_user_meta( $user_id, '_city_suffix', $vendor->citySuffix);
                add_user_meta( $user_id, '_street_suffix', $vendor->streetSuffix);
                add_user_meta( $user_id, '_building_number', $vendor->buildingNumber);
                add_user_meta( $user_id, '_city', $vendor->city);
                add_user_meta( $user_id, '_street_name', $vendor->streetName);
                add_user_meta( $user_id, '_street_address', $vendor->streetAddress);
                add_user_meta( $user_id, '_post_code', $vendor->postcode);
                add_user_meta( $user_id, '_address', $vendor->address);
                add_user_meta( $user_id, '_country', $vendor->country);
                add_user_meta( $user_id, '_latitude', $vendor->latitude);
                add_user_meta( $user_id, '_longitude', $vendor->longitude);

                foreach($vendor->products as $product) {       
                    if ( $product->type == 'simple' ) {
                        $objProduct = new WC_Product();
                    } else {
                        $objProduct = new WC_Product_Variable();
                    }
                    $objProduct->set_name($product->name);
                    $objProduct->set_status("publish");  // can be publish,draft or any wordpress post status
                    $objProduct->set_catalog_visibility('visible'); // add the product visibility status
                    $objProduct->set_description($product->description);
                    $objProduct->set_price($product->price); // set product active price
                    $objProduct->set_regular_price($product->price); // set product regular price
                    $objProduct->set_manage_stock(true); // true or false
                    $objProduct->set_stock_quantity($product->quantity);
                    $objProduct->set_stock_status('instock'); // in stock or out of stock value
                    $objProduct->save();
                }
            }
        }
        echo 'success';
    }

    curl_close($ch);
