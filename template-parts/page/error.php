<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>域名错误</title>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/tailwind.css">
</head>

<body>

  <main class="flex flex-col items-center justify-center h-screen">
    <div class="border rounded-md p-2 md:p-8 flex flex-col items-center justify-center">
      <img src='<?php echo get_template_directory_uri() ?>/assets/img/error.gif' alt='error'>
      <p class="text-4xl font-bold text-gray-800 mt-2"><?php _e('域名错误','ripro');?></p>
      <p class="text-gray-600 mt-2"><?php _e('返回主题页面切换主题即可正常使用WordPress','ripro');?></p>
      <!-- 返回外观主题页面 -->
      <a href="<?php echo home_url(); ?>/wp-admin/themes.php" class="mt-5 text-blue-500 hover:text-blue-600"><?php _e('返回','ripro');?></a>
    </div>
  </main>

</body>

</html>
