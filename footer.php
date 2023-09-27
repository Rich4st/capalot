
</main>
<!-- **************** MAIN CONTENT END **************** -->

<!-- =======================
Footer START -->
<footer class=" py-6 dark:bg-dark">
	<div class=" lg:max-w-[80rem] m-auto px-4 lg:px-0">

		<?php get_template_part( 'template-parts/footer/widget');?>

		<div class=" text-center  text-sm text-gray-600 py-4 dark:text-gray-400">
			<div><?php echo _capalot('site_copyright_text','Copyright © 2023 <a target="_blank" href="http://ritheme.com/">RiPro-V5</a> - All rights reserved');?></div>
			<div class=""><?php echo _capalot('site_ipc_text','') . _capalot('site_ipc2_text','');?></div>
		</div>

		<?php get_template_part( 'template-parts/footer/links');?>

		<?php if (defined('WP_DEBUG') && WP_DEBUG == true) {
			echo '<p id="debug-info" class="m-0 small text-primary w-100 text-center ">'.sprintf('SQL：%s',get_num_queries()).'<span class="sep"> | </span>'.sprintf('Pages：%ss',timer_stop(0,5)).'</p>';
		}?>

	</div>
</footer>
<!-- =======================
Footer END -->



<?php get_template_part('template-parts/footer/rollbar'); ?>





<div class=" fixed top-0 left-0 right-0 bottom-0  bg-black/80 z-50 hidden" id="fixedB"></div>
<?php get_template_part('template-parts/footer/off-canvas'); ?>

<script>
  // 移动端导航
  let menuA = document.getElementById('menuA');
  let fixedB = document.getElementById('fixedB');
  let navBg = document.getElementById('navBg');
  let closeNav = document.getElementById('closeNav');
  menuA.addEventListener('click', function() {
    if (fixedB.style.display == 'none' || !fixedB.style.display) {
      fixedB.style.display = 'block';
    } else {
      fixedB.style.display = 'none';
    }
    $('#navBg').animate({
      width: 'toggle'
    }, 100);
  });
  fixedB.addEventListener('click', function() {
    fixedB.style.display = 'none';
    $('#navBg').animate({
      width: 'toggle'
    }, 100);
  });
  closeNav.addEventListener('click', function() {
    fixedB.style.display = 'none';
    $('#navBg').animate({
      width: 'toggle'
    }, 100);
  });
</script>





<?php wp_footer(); ?>
<!-- 自定义js代码 统计代码 -->
<?php if ( !empty(_capalot('site_web_js')) ) echo _capalot('site_web_js');?>
<!-- 自定义js代码 统计代码 END -->
</body>

</html>