<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php echo get_site_default_color_style(); ?>">

<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <?php get_template_part('template-parts/header/menu'); ?>


  <main>


    <div class=" hidden" id="copy_t">
      <div class=" fixed w-full flex h-full justify-center z-50 ">
        <div class="  self-center  bg-black/80 text-white py-2 px-4 rounded-md">密码已复制</div>
      </div>
    </div>
