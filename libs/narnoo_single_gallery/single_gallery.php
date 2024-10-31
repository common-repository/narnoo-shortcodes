<?php 

global $narnoo_slg_shortcode_count;	
//if ( Narnoo_Distributor_Helper::wp_supports_enqueue_script_in_body() ) {
Narnoo_Shortcodes::load_scripts_for_single_gallery();
// }
if ( ! isset( $narnoo_slg_shortcode_count ) ) {
	$narnoo_slg_shortcode_count = 0;
}
extract( shortcode_atts( array(
	'album' => '',			
	'width' => '200',			// optional width
	'height' => '150',			// optional height
	'thumb' => 'preview'			// optional height
), $atts ) );

if(empty($album)){
	echo $error_msg_prefix . __( 'An album key is required', NARNOO_OPERATOR_SHORTCODE_I18N_DOMAIN );
}

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
			return false;
		} 


	}


} 
?>

<!-- These are the images that will open in Imagebox. Because they have no thumbnails, they can be placed anywhere in the page. -->
				

<?php
//Get the thumbnail image
switch ( lcfirst($thumb) ) {
	case 'preview':
		$thmbImage = $list->data->images[0]->previewImage;
		break;
	case 'thumb':
		$thmbImage = $list->data->images[0]->thumbImage;
		break;
	case 'crop':
		$thmbImage = $list->data->images[0]->cropImage;
		break;
	case 'xcrop':
		$thmbImage = $list->data->images[0]->xcropImage;
		break;
	case 'large':
		$thmbImage = $list->data->images[0]->largeImage;
		break;
	case 'xlarge':
		$thmbImage = $list->data->images[0]->xlargeImage;
		break;
	case 'xxlarge':
		$thmbImage = $list->data->images[0]->xxlargeImage;
		break;
	case '200':
		$thmbImage = $list->data->images[0]->image200;
		break;
	case '400':
		$thmbImage = $list->data->images[0]->image400;
		break;
	case '800':
		$thmbImage = $list->data->images[0]->image800;
		break;
	default:
		$thmbImage = $list->data->images[0]->previewImage;
		break;
}


foreach ($list->data->images as $img) {
           
        echo '<a href="'.$img->xlargeImage.'" title="'.$img->caption.'" rel="imagebox[narnoo_slg'.$narnoo_slg_shortcode_count.']" id="narnoo_single_link_gallery-'.$narnoo_slg_shortcode_count.'"></a>';
 }
 ?>
<a href="javascript:imagebox.open(document.getElementById('narnoo_single_link_gallery-<?php echo $narnoo_slg_shortcode_count;  ?>'));" class="narnoo_single_link_gallery_thumbnail">
	<img src="<?php echo $thmbImage; ?>" alt="Narnoo image" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />
	<span class="narnoo_single_link_gallery_cover"></span>
</a>
<?php $narnoo_slg_shortcode_count++ ?>
		
