<?php 
Narnoo_Shortcodes::load_scripts_for_image_gallery();

extract( shortcode_atts( array(
	'album' 	=> '',
	'width' 	=> '200',	// optional width
	'height' 	=> '150',	// optional height
	'speed'		=> 5000,
    'fullWidth' => true
), $atts ) );


if(empty($album)){
	echo $error_msg_prefix . __( 'An album key is required', NARNOO_OPERATOR_SHORTCODE_I18N_DOMAIN );
}

//used incase multiple galleries are on a single page.
$random = rand(1,100); 

$list 			= null;
$current_page 	= 1;
$cache	 		= Narnoo_Operator_Helper::init_noo_cache();
$request        = Narnoo_Operator_Helper::init_api( "new" );


if ( ! is_null( $request ) ) {

	$list = $cache->get('album_'.$album.$current_page);
	

	if(empty($list)){

		try {
			$list = $request->getAlbumImages( $album, $current_page );

	       if ( ! is_array( $list->data->images ) ) {
				throw new Exception( sprintf( __( "Error retrieving album images. Unexpected format in response page #%d.", NARNOO_OPERATOR_SHORTCODE_I18N_DOMAIN ), $current_page ) );
			}

			if(!empty( $list->success ) ){
				$cache->set('album_'.$album.$current_page, $list, 43200);
			}

		} catch ( Exception $ex ) {
			Narnoo_Operator_Helper::show_api_error( $ex );
		} 


	}


} 

?>
<script>
    	 jQuery(document).ready(function() {
			jQuery('#noo-gallery-<?php echo $random; ?>').lightSlider({
                gallery:true,
                item:1,
                thumbItem:8,
                slideMargin: 0,
                pause: <?php echo $speed; ?>,
                speed:800,
                auto:true,
                loop:true,
                onSliderLoad: function() {
                    jQuery('#noo-gallery-<?php echo $random; ?>').removeClass('cS-hidden');
                }  
            });
		});
    </script>
<div class="noo-gallery-holder" <?php

if( !empty($fullWidth) && ( $width == 200) ){
    echo ' style="width:100%"';
}else{
    echo ' style="width:'.$width.'px"';
}


?>>      
            <div class="clearfix" <?php

if(!empty($fullWidth)){
    echo ' style="max-width:100%"';
}else{
    echo ' style="max-width:'.$width.'px"';
}


?>>
                <ul id="noo-gallery-<?php echo $random; ?>" class="list-unstyled cS-hidden">
                    <?php foreach ($list->data->images as $img) {
                    	
                    	echo '<li data-thumb="' .$img->image400 . '"> 
                        		<img src="' . $img->image800 . '" />
                    		</li>';

                    }?>
                </ul>
        </div>
</div>