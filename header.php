<!DOCTYPE html>
<html lang="<?php echo Capalot_Cookie::get('lang') ?? 'zh-CN' ?>" class="<?php echo get_site_default_color_style(); ?>">

<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=yes">
<title><?php bloginfo('name'); ?> | <?php the_title() ?></title>
<meta name="description" content="<?php bloginfo('description'); ?>-<?php the_title() ?>">
<meta name="keywords" content="<?php bloginfo('name'); ?>-<?php the_title() ?>">
<meta name="og:title" content="<?php bloginfo('name'); ?>-<?php the_title() ?>">
<meta name="og:description" content="<?php bloginfo('description'); ?>-<?php the_title() ?>">
<meta name="og:keywords" content="<?php bloginfo('name'); ?>-<?php the_title() ?>">
<meta property="og:type" content="<?php bloginfo('name'); ?>-<?php the_title() ?>">
<meta property="og:url" content="<?php bloginfo('url') ?>">
<meta property="og:site_name" content="<?php bloginfo('name') ?>">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <?php get_template_part('template-parts/header/menu'); ?>


  <main>