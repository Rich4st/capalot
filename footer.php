<footer class="flex flex-col items-center bg-neutral-100 text-center dark:bg-neutral-600 lg:text-left">
  <div class="container p-6">
    <div class="grid place-items-center md:grid-cols-2 lg:grid-cols-4">
      <div class="mb-6">
        <h5 class="mb-2.5 font-bold uppercase text-neutral-800 dark:text-neutral-200">
          LOGo
        </h5>

        <ul class="mb-0 list-none">
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 1</a>
          </li>
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 2</a>
          </li>
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 3</a>
          </li>
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 4</a>
          </li>
        </ul>
      </div>


      <div class="mb-6">
        <h5 class="mb-2.5 font-bold uppercase text-neutral-800 dark:text-neutral-200">
          快速导航
        </h5>

        <ul class="mb-0 list-none">
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 1</a>
          </li>
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 2</a>
          </li>
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 3</a>
          </li>
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 4</a>
          </li>
        </ul>
      </div>

      <div class="mb-6">
        <h5 class="mb-2.5 font-bold uppercase text-neutral-800 dark:text-neutral-200">
          关于本站
        </h5>

        <ul class="mb-0 list-none">
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 1</a>
          </li>
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 2</a>
          </li>
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 3</a>
          </li>
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 4</a>
          </li>
        </ul>
      </div>

      <div class="mb-6">
        <h5 class="mb-2.5 font-bold uppercase text-neutral-800 dark:text-neutral-200">
          联系我们
        </h5>

        <ul class="mb-0 list-none">
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 1</a>
          </li>
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 2</a>
          </li>
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 3</a>
          </li>
          <li>
            <a href="/" class="text-neutral-800 dark:text-neutral-200">Link 4</a>
          </li>
        </ul>
      </div>
    </div>
  </div>


  <div class="w-full bg-neutral-200 p-4 text-center text-neutral-700 dark:bg-neutral-700 dark:text-neutral-200">
    © 2023 Copyright:
    <a class="text-neutral-800 dark:text-neutral-400" href="/">capalot</a>
  </div>
</footer>



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
    $('#navBg').animate({width:'toggle'},100);
  });
  fixedB.addEventListener('click', function() {
    fixedB.style.display = 'none';
    $('#navBg').animate({width:'toggle'},100);
  });
  closeNav.addEventListener('click', function() {
    fixedB.style.display = 'none';
    $('#navBg').animate({width:'toggle'},100);
  });
  
</script>





<?php wp_footer(); ?>

</body>

</html>