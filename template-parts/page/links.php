<?php

get_header();

$args = array(
	'taxonomy' => array('link_category'),
	'orderby'  => 'name',
	'order'    => 'asc',
	'hide_empty' => true // for development
);

$link_category = get_terms($args);

$bg_image = get_template_directory_uri() . '/assets/img/bg.jpg';

?>

<div class=" relative overflow-hidden">
	<div class="absolute left-0 top-0 right-0 w-full h-full z-[-1] " style="background-image: url(<?php echo $bg_image; ?>); background-position:50%;background-size:100%; "></div>
	<div class=" absolute  backdrop-blur-lg h-full w-full bg-black/30"></div>
	<div class=" relative z-50 py-12 text-center text-white">
		<h1 class=" text-xl font-bold "><i class="fab fa-staylinked me-1"></i><?php _e('网址导航', 'ripro'); ?></h1>
	</div>
</div>

<section class=" bg-[#eee] dark:bg-dark">
	<div class=" max-w-[80rem] m-auto lg:px-0 px-4 py-8">

		<?php if ($link_category) : foreach ($link_category as $key => $cat) : $color = capalot_get_color_class($key); ?>
				<h2 class="p-2 bg-red-500 text-white rounded-md inline-block active" href="/tags/?orderby=count">
					<i class="fas fa-desktop me-1"></i><?php echo $cat->name; ?>
					<span>(<?php echo $cat->count; ?>)</span>
				</h2>


				<div id="<?php echo esc_attr($cat->term_id); ?>" class=" grid lg:grid-cols-3 grid-cols-1 gap-4 py-4 mb-4">

					<?php
					$bookmarks = get_bookmarks(array('orderby' => 'link_rating', 'category' => $cat->term_id));
					if ($bookmarks) {

						foreach ($bookmarks as $bookmark) :
							$color = capalot_get_color_class(mt_rand(1, 6));
							$link_image = (!empty($bookmark->link_image)) ? $bookmark->link_image : '';
							$link_nofollow = 'nofollow noopener noreferrer';
					?>

							<div class=" bg-white rounded-md p-4 dark:bg-dark-card ">
								<a target="<?php echo esc_attr($bookmark->link_target); ?>" class="" href="<?php echo home_url('/goto?url=' . $bookmark->link_url); ?>" rel="<?php echo esc_attr($link_nofollow); ?>" title="<?php echo $bookmark->link_name; ?>">
									<div class=" grid grid-cols-5 gap-4 justify-center items-center">
										<div class=" col-span-1 text-center">
											<div class="link-img lazy bg-opacity-10 dark:text-gray-400 bg-<?php echo esc_attr($color); ?> text-<?php echo esc_attr($color); ?>  w-12 h-12 transition hover:rotate-[360deg]  rounded-full  text-2xl leading-[3rem]" data-bg="<?php echo $link_image; ?>">
												<?php echo empty($link_image) ? mb_substr($bookmark->link_name, 0, 1) : '' ?>
											</div>
										</div>
										<div class=" col-span-4">
											<b class=" text-lg line-clamp-1 text-gray-950 dark:text-gray-50 hover:underline font-normal"><?php echo $bookmark->link_name; ?></b>
											<p class=" text-sm line-clamp-2 text-gray-600 dark:text-gray-400"><?php echo $bookmark->link_description; ?></p>
										</div>
									</div>
								</a>
							</div>

					<?php endforeach;
					} else {
						get_template_part('template-parts/loop/item', 'none');
					}
					?>

				</div>

		<?php endforeach;
		else :
			get_template_part('template-parts/loop/item', 'none');
		endif; ?>

	</div>
</section>


<?php get_footer(); ?>