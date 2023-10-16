<?php

get_header();

$cat_id = get_queried_object_id();

$archive_item_config = get_posts_style_config();

?>

<?php get_template_part('template-parts/archive-hero'); ?>
<?php get_template_part('template-parts/archive-filter');?>

<section class="  dark:bg-dark">
	<div class=" container max-w-[80rem] m-auto py-8 lg:px-0 px-4">

		<?php do_action('capalot_ads', 'ad_archive_top'); ?>

		<ul class="posts-wrap grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 grid-cols-1 md:gap-8 gap-2 mb-4  <?php echo esc_attr($archive_item_config['row_cols_class']); ?>">
			<?php if (have_posts()) :
				while (have_posts()) : the_post();
					get_template_part('template-parts/loop/item', get_post_format(), $archive_item_config);
				endwhile;
			else :
				get_template_part('template-parts/loop/item', 'none');
			endif;
			?>
		</ul>

		<?php do_action('capalot_ads', 'ad_archive_bottom'); ?>

		<?php Capalot_pagination(array(
		'range'     => 4,
		'nav_class' => 'page-nav mt-4',
		'nav_type'  => _capalot('site_page_nav_type', 'click'),
	));?>

	</div>
</section>
<?php
get_footer();
