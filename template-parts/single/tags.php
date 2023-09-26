<?php
// 显示文章标签

if (empty(_capalot('single_bottom_tags', true))) {
	return;
}


if (!$tags = get_the_tags()) {
	return;
}

?>

<div class="entry-tags space-x-2">
	<i class="fas fa-tags me-1"></i>
	<?php foreach ($tags as $tag): ?>
		<a href="<?php echo get_tag_link($tag->term_id); ?>" rel="tag"><?php echo $tag->name; ?></a>
	<?php endforeach;?>
</div>
