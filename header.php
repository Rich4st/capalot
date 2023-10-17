<!DOCTYPE html>
<html lang="<?php echo Capalot_Cookie::get('lang') ?? 'zh-CN' ?>" class="<?php echo get_site_default_color_style(); ?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset');?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<?php wp_head();?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <?php get_template_part('template-parts/header/menu'); ?>


  <main>
