﻿﻿<?php
global $options,$realty,$wpdb,$post, $zoogooglemaps;
foreach ($options as $value) { if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }
$home_images_path=get_option('siteurl').'/wp-content/uploads/homepage_images/';
?>

<?php get_header(); ?>

<div role="main"<?php echo(!is_front_page('')&&!is_page('property')&&$post->post_parent!=833)?' class="group"':''; ?>>

	<?php if (is_front_page('')) { ?>

        <div class="slideshow cycle-slideshow"  data-cycle-slides="div.slide-img" data-cycle-log="false">
			<?php $images=get_option('homepage_images');
            $image_url=get_option('siteurl').'/wp-content/uploads/homepage_images/';
            if(!empty($images)){
                $photos_upload_url=get_option('siteurl').'/wp-content/uploads/slideshow/';
                $desc=get_option('blogname')." - ".get_option('blogdescription');
                foreach($images as $image){
                    if(!empty($image['image'])){ ?>
                        <div class="slide-img"><img src="<?php echo $image['image']; ?>" alt="<?php echo $desc; ?>" title="<?php echo $desc; ?>" /></div>
                    <?php }
                }
            } ?>
            <div class="opaq-filter"></div>
        </div>
