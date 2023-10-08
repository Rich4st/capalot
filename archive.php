<?php

get_header();

$cat_id = get_queried_object_id();

$archive_item_config = capalot_get_archive_item_config($cat_id);



?>

<?php get_template_part('template-parts/archive-hero');?>

<?php get_template_part('template-parts/archive-filter');?>

<section class=" max-w-[80rem] m-auto lg:px-0 px-4 py-8">
	<?php do_action('ripro_ads', 'ad_archive_top');?>

	<div class=" grid lg:grid-cols-4 gap-4 <?php echo esc_attr($archive_item_config['row_cols_class']); ?>">
		<?php if (have_posts()):
			while (have_posts()): the_post();
				get_template_part('template-parts/loop/item', get_post_format(), $archive_item_config);
			endwhile;
		else:
			get_template_part('template-parts/loop/item', 'none');
		endif;
		?>
	</div>

	<?php do_action('ripro_ads', 'ad_archive_bottum');?>


	<?php capalot_pagination(array(
		'range'     => 4,
		'nav_class' => 'page-nav mt-4',
		'nav_type'  => _capalot('site_page_nav_type', 'click'),
	));?>

</section>

<?php
get_footer();
