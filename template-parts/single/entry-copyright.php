<?php
$copyright = _capalot('single_bottom_copyright');
if (empty($copyright)) {
	return;
}
?>
<div class="entry-copyright leading-6 p-2 rounded bg-[#8cdcfe] bg-opacity-20 text-[#a1a1a8] text-sm my-2">
	<?php echo '<i class="fas fa-info-circle me-1"></i>' .wp_kses_post($copyright); ?>
</div>