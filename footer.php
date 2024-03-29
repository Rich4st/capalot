</main>
<!-- **************** MAIN CONTENT END **************** -->

<!-- =======================
Footer START -->
<footer class=" lg:pt-6 lg:pb-6 pt-4 pb-16 bg-white dark:bg-dark">
  <div class=" container lg:max-w-[80rem] m-auto px-4 lg:px-0">

    <?php get_template_part('template-parts/footer/widget'); ?>

    <div class=" text-center  text-sm text-gray-600 py-4 dark:text-gray-400">
      <div><?php echo _capalot('site_copyright_text', 'Copyright © 2023 <a target="_blank" href="http://ritheme.com/">RiPro-V5</a> - All rights reserved'); ?></div>
      <div class=""><?php echo _capalot('site_ipc_text', '') . _capalot('site_ipc2_text', ''); ?></div>
    </div>

    <?php get_template_part('template-parts/footer/links'); ?>

    <?php if (defined('WP_DEBUG') && WP_DEBUG == true) {
      echo '<p id="debug-info" class="m-0 small text-primary w-100 text-center ">' . sprintf('SQL：%s', get_num_queries()) . '<span class="sep"> | </span>' . sprintf('Pages：%ss', timer_stop(0, 5)) . '</p>';
    } ?>

  </div>
</footer>
<!-- =======================
Footer END -->


<?php get_template_part('template-parts/footer/rollbar'); ?>
<div class=" fixed  lg:bottom-12 right-2 bottom-20 z-50 hidden" id="Top_btn">
  <div class=" text-center w-12">
    <div class=" cursor-pointer bg-white dark:bg-dark-card dark:border-[#252529] p-2 rounded-md shadow-lg border border-gray-200 text-2xl text-gray-500 dark:text-gray-400 hover:opacity-70"><i class="fas fa-caret-up"></i></div>
  </div>
</div>

<div class=" fixed top-0 left-0 right-0 bottom-0 backdrop-blur-lg   bg-black/80 z-[99] hidden" id="fixedB"></div>
<?php get_template_part('template-parts/footer/off-canvas'); ?>

<script>
  // 返回顶部
  window.onload = function() {
    var totop = document.getElementById("Top_btn");
    var timer = null;

    totop.onclick = function() {
      timer = setInterval(function() {
        var backTop = document.documentElement.scrollTop || document.body.scrollTop;
        speedTop = backTop / 2;
        document.documentElement.scrollTop = backTop - speedTop;
        if (backTop == 0) {
          clearInterval(timer);
        }
      }, 10)
    }
    var pageHeight = 400;
    window.onscroll = function() {
      var backTop = document.documentElement.scrollTop ||
        document.body.scrollTop;
      if (backTop > pageHeight) {
        totop.style.display = "block";
      } else {
        totop.style.display = "none";
      }

    }
  }

  // 移动端导航
  let menuA = document.getElementById('menuA');
  let fixedB = document.getElementById('fixedB');
  let navBg = document.getElementById('navBg');
  let closeNav = document.getElementById('closeNav');

  menuA.addEventListener('click', function() {
    if (fixedB.classList.contains('hidden')) {
      fixedB.classList.add('block');
      fixedB.classList.remove('hidden');
    }
    $('#navBg').animate({
      width: 'toggle'
    }, 100);
    document.body.style.overflowY = 'hidden';
  });
  fixedB.addEventListener('click', function() {
    fixedB.classList.add('hidden');
    fixedB.classList.remove('block');
    $('#navBg').animate({
      width: 'toggle'
    }, 100);
    document.body.style.overflowY = 'auto';

  });
  closeNav.addEventListener('click', function() {
    fixedB.classList.add('hidden');
    fixedB.classList.remove('block');
    $('#navBg').animate({
      width: 'toggle'
    }, 100);
    document.body.style.overflowY = 'auto';
  });
  window.addEventListener('load', function() {
    window.addEventListener('resize', function() {
      var widthA = window.innerWidth;
      if (widthA >= 1024) {
        navBg.style.display = 'none';
        fixedB.classList.add('hidden');
        document.body.style.overflowY = 'auto';
      }
    });
  });
</script>




<div class=" md:hidden fixed bottom-0 w-full left-0 z-[9988899] bg-white dark:bg-dark-card shadow-[0_-3px_10px_0_rgba(0,0,0,0.05)] border-t dark:border-t-[#222] ">
  <ul class=" flex text-center py-2 justify-around text-gray-600 dark:text-gray-50 ">
    <li><a target="" href="/"><i class="fas fa-home text-lg "></i><span class=" block text-sm "><?php _e('首页', 'ripro'); ?></span></a></li>
    <?php foreach (_capalot('site_footer_widget_link1', array()) as $item) {
      $link_item = '<li><a href="' . $item['href'] . '"  ><i class="fas fa-layer-group text-lg "></i><span class=" block text-sm ">' . __('分类', 'ripro') . '</span></a></li>';

      if ($item['title'] === '标签云' &&  _capalot('is_site_tags_page')) {
        echo  $link_item;
      }
    } ?>
    <?php foreach (_capalot('site_footer_widget_link2', array()) as $item) {
      $link_item = '<li><a href="' . $item['href'] . '"  ><i class="far fa-gem text-lg "></i><span class=" block text-sm ">会员</span></a></li>';

      if ($item['title'] === 'VIP介绍' &&  _capalot('is_site_vip_price_page')) {
        echo  $link_item;
      }
    } ?>
    <li><a target="" href="/user"><i class="fas fa-user text-lg "></i><span class=" block text-sm "><?php _e('我的', 'ripro'); ?></span></a></li>
    <li><a target="" href="https://ritheme.com/"><i class="fab fa-wordpress text-lg "></i><span class=" block text-sm "><?php _e('同款', 'ripro'); ?></span></a></li>
  </ul>
</div>



<?php wp_footer(); ?>
<!-- 自定义js代码 统计代码 -->
<?php if (!empty(_capalot('site_web_js'))) echo _capalot('site_web_js'); ?>
<!-- 自定义js代码 统计代码 END -->
</body>

</html>