<?php

get_header();

$archive_item_config = get_posts_style_config();

?>


<?php get_template_part('template-parts/archive-hero'); ?>

<div class=" dark:bg-dark">

	<section class=" lg:max-w-[80rem] m-auto px-2 lg:px-0 py-8 ">
		<?php do_action('ripro_ads', 'ad_archive_top'); ?>

		<div class="posts-wrap row grid lg:grid-cols-4 grid-cols-2 lg:gap-4 gap-2 <?php echo esc_attr($archive_item_config['row_cols_class']); ?>">
			<?php if (have_posts()) :
				while (have_posts()) : the_post();
					get_template_part('template-parts/loop/item', get_post_format(), $archive_item_config);
				endwhile;
			else :
				get_template_part('template-parts/loop/item', 'none');
			endif;
			?>
		</div>

		<?php do_action('ripro_ads', 'ad_archive_bottum'); ?>


		<?php capalot_pagination(array(
			'range'  => 4,
			'nav_class' => 'page-nav mt-4',
			// 'nav_type'  => _cao('site_page_nav_type', 'click'),
		)); ?>

	</section>
</div>

<?php
get_footer();
