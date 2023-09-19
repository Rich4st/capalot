<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <?php wp_head(); ?>
</head>

<body>
  <div class="h-[60px] bg-white w-full dark:bg-dark-card">
    <div class="lg:max-w-[1280px] md:max-w-[720px] w-full mx-auto flex flex-row h-[100%] justify-between items-center dark:text-gray-50">
      <div class="w-[100px] h-[60px] flex items-center justify-center  ">
        LOGO
      </div>
      <div class="relative">
        <?php if (_capalot('is_site_dark_toggle', true)) : ?>
          <span id="light-mode" class="absolute cursor-pointer">
            <i class="fa-solid fa-sun"></i>
          </span>
          <span id="dark-mode" class="absolute cursor-pointer hidden dark:text-gray-400">
            <i class="fa-solid fa-cloud-moon"></i>
          </span>
        <?php endif; ?>
      </div>
      <div>
      </div>
      <div>
      </div>
    </div>
  </div>
