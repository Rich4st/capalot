<?php

get_header();


$orderby = trim(urldecode(get_response_param('orderby', 'count', 'get'))); //count name
$order = ($orderby == 'count') ? 'desc' : 'asc';
$number = 12; // 每页显示的数目
$page = get_query_var('page') ? get_query_var('page') : 1; // 获取当前页码

$args = array(
	'taxonomy' => array('post_tag', 'category'),
	'orderby'  => $orderby,
	'order'    => $order,
	'number'   => $number,
	'offset'   => ($page - 1) * $number, // 偏移量等于 (页码 - 1) * 每页数目
	'hide_empty' => false // for development
);

$tags = get_terms($args);
$total_pages = ceil(wp_count_terms($args['taxonomy']) / $number); // 计算总页数

$bg_image = get_template_directory_uri() . '/assets/img/bg.jpg';

?>

<div class=" text-center py-8 bg-gray-200 dark:bg-dark-card">
	<div class="" data-bg="<?php echo $bg_image; ?>"></div>
	<div class=" dark:text-gray-50">
		<h1 class=" text-xl font-bold"><i class="fas fa-tags me-1"></i><?php _e('标签云', 'ripro'); ?></h1>

	</div>
</div>

<section class=" bg-[#eee] dark:bg-dark">

	<div class=" lg:max-w-[80rem] m-auto lg:px-0 px-4 py-8">
		<div class=" mb-8 text-center text-sky-400">
			<?php
			$orderbyOptions = [
				'count' => __('按数量排序', 'ripro'),
				'name' => __('按名称排序', 'ripro'),
			];

			foreach ($orderbyOptions as $key => $name) {
				if (!in_array($key, ['count', 'name'])) {
					continue; // 排除非 count 和 name 的选项
				}
				$active = ($key == $orderby) ? 'active' : '';
				printf('<a class="btn btn-sm btn-outline-info px-4 hover:bg-sky-400 hover:text-white mx-1 py-2 border border-sky-400 rounded-full %s" href="%s"><i class="fas fa-sort me-1"></i>%s</a>', $active, esc_url_raw(add_query_arg('orderby', $key)), $name);
			}
			?>
		</div>

		<div class=" grid lg:grid-cols-4 md:grid-cols-3 grid-cols-2 lg:gap-6 gap-2 ">
			<?php if ($tags) : foreach ($tags as $tag) : $color = capalot_get_color_class(mt_rand(1, 6)); ?>

					<div class=" bg-white p-4 rounded-md dark:bg-dark-card">
						<a class=" " href="<?php echo get_tag_link($tag->term_id); ?>" rel="tag" title="<?php echo $tag->name; ?>">
							<div class=" grid lg:grid-cols-7 grid-cols-6  lg:gap-4 gap-2   justify-center items-center">
								<div class=" col-span-2 text-center ">
									<div class=" lg:text-2xl text-xl dark:text-gray-400 transition hover:rotate-[360deg] lg:w-12 lg:h-12 lg:leading-[3rem] w-10 h-10 leading-10 rounded-full  tag-substr bg-opacity-10 bg-<?php echo esc_attr( $color );?> text-<?php echo esc_attr( $color );?>"><?php echo mb_substr($tag->name, 0, 1); ?></div>
								</div>
								<div class=" lg:col-span-5 col-span-4">
									<b class=" text-md font-normal text-gray-600  hover:underline dark:text-gray-50"><?php echo $tag->name; ?></b>
									<p class=" text-sm text-gray-500 dark:text-gray-400"><span class="mr-2"><b class="b mr-1"><?php echo $tag->count; ?></b>个文章</span></p>
								</div>
							</div>
						</a>
					</div>

			<?php endforeach;
			else :
				get_template_part('template-parts/loop/item', 'none');
			endif; ?>
		</div>

		<?php capalot_custom_pagination($page, $total_pages); ?>

	</div>

</section>

<?php get_footer(); ?>