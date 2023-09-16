<?php

$default = [
  'type' => 'grid', // grid  grid-overlay list
  'media_class' => 'ratio-3x2', // ratio-1x1  3x2 3x4 4x3 16x9
  'media_size_type' => 'bg-cover',
  'media_fit_type' => 'bg-center',
  'is_vip_icon' => true,
  'is_entry_cat' => true,
  'is_entry_desc' => true,
  'is_entry_meta' => true,
];

$args = wp_parse_args($args, $default);

$post_id = get_the_id();

$post_price = get_post_price_data($post_id)['default'];

// 获取当前文章的格式
$post_format = get_post_format();
$format_icons = [
  'image' => 'fa-regular fa-image',
  'video' => 'fa-solid fa-play',
  'audio' => 'fa-solid fa-music',
];

if ($post_format && isset($format_icons[$post_format])) {
  $post_format_icon = $format_icons[$post_format];
} else {
  $post_format_icon = false;
}

?>

<?php if ($args['type'] == 'grid') : ?>
  <li class="dark:bg-[#191b1d] rounded-xl cursor-pointer shadow-[rgba(0,_0,_0,_0.24)_0px_3px_8px]  md:h-[26rem] h-[17rem] transition-all duration-300 hover:shadow-[0px_4px_16px_rgba(17,17,26,0.1),_0px_8px_24px_rgba(17,17,26,0.1),_0px_16px_56px_rgba(17,17,26,0.1)]  ">
    <article class="post-item item-grid relative">

      <div class="tips-badge absolute w-[2.5rem] text-center top-0 start-0 z-[999] m-2 bg-[#b0adac]  rounded-xl">
        <?php if (is_sticky()) : ?> 
          <div class="text-[0.5rem] bg-opacity-25 text-white">置顶</div>
        <?php endif; ?>
      </div>

      <div class="entry-media ratio  <?php echo esc_attr($args['media_class']); ?>">
        <a target="<?php echo get_target_blank(); ?>" style="background-image: url(<?php echo capalot_get_thumbnail_url(); ?>);" class="block w-full md:h-[18rem] h-[10rem] bg-no-repeat   <?php echo esc_attr($args['media_size_type']); ?> 
        <?php echo esc_attr($args['media_fit_type']); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" data-bg="<?php echo capalot_get_thumbnail_url(); ?>">
          <?php if ($post_format_icon) : ?>
            <div class="post-format-icon"><i class="<?php echo $post_format_icon; ?>"></i></div>
          <?php endif; ?>
        </a>
      </div>

      <div class="entry-wrapper  w-full text-[#a1a1a8] text-[0.75rem] p-[0.5rem] h-[7rem] ">
        <?php if ($args['is_entry_cat']) : ?>
          <div class="entry-cat-dot mb-[0.25rem] "><i class="fa-solid fa-tag pr-2" style="color: #82a6f0;"></i><?php capalot_meta_category(2); ?></div>
        <?php endif; ?>

        <h2 class="entry-title text-[0.95rem] font-bold text-black  dark:text-[#a1a1a8]  whitespace-normal text-ellipsis overflow-hidden">
          <a target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
        </h2>

        <?php if ($args['is_entry_desc']) : ?>
          <div class="entry-desc mp-[0.25rem] h-[1.125rem]  whitespace-normal text-ellipsis overflow-hidden"><?php echo capalot_get_post_excerpt(40); ?></div>
        <?php endif; ?>

        <?php if ($args['is_entry_meta']) : ?>
          <div class="entry-meta flex flex-row justify-between">
            <div>
            <span class="meta-date"><i class="fa-solid fa-clock pr-1"></i>
              <?php capalot_meta_datetime(); ?></span>
            <span class="meta-likes d-none md:inline-block hidden"><i class="fa-solid fa-heart pr-1"></i><?php echo capalot_get_post_likes(); ?></span>
            <span class="meta-fav d-none md:inline-block hidden"><i class="fa-solid fa-star pr-1"></i><?php echo capalot_get_post_favorites(); ?></span>
            <span class="meta-views"><i class="fa-solid fa-eye pr-1"></i><?php echo capalot_get_post_views(); ?></span>
            </div>
            <?php if (is_site_shop() && post_has_pay($post_id)) : ?>
              <span class="meta-price whitespace-normal flex flex-row"><i class="<?php echo get_site_coin_icon(); ?> me-1"></i><?php echo $post_price; ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </article>
  </li>

<?php elseif ($args['type'] == 'grid-overlay') : ?>
  <li class="col ">
    <article class="post-item item-grid grid-overlay">

      <div class="tips-badge position-absolute top-0 start-0 z-1 m-2">
        <?php if (is_sticky()) : ?>
          <div class="badge bg-dark bg-opacity-25"><?php _e('置顶', 'ripro'); ?></div>
        <?php endif; ?>
      </div>

      <div class="entry-media ratio <?php echo esc_attr($args['media_class']); ?>">
        <a target="<?php echo get_target_blank(); ?>" class="media-img lazy <?php echo esc_attr($args['media_size_type']); ?> <?php echo esc_attr($args['media_fit_type']); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" data-bg="<?php echo capalot_get_thumbnail_url(); ?>">
          <?php if ($post_format_icon) : ?>
            <div class="post-format-icon"><i class="<?php echo $post_format_icon; ?>"></i></div>
          <?php endif; ?>
        </a>
      </div>

      <div class="entry-wrapper">
        <?php if ($args['is_entry_cat']) : ?>
          <div class="entry-cat-dot"><?php capalot_meta_category(2); ?></div>
        <?php endif; ?>

        <h2 class="entry-title">
          <a target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
        </h2>

        <?php if ($args['is_entry_desc']) : ?>
          <div class="entry-desc"><?php echo capalot_get_post_excerpt(40); ?></div>
        <?php endif; ?>

        <?php if ($args['is_entry_meta']) : ?>
          <div class="entry-meta">
            <span class="meta-date"><i class="far fa-clock me-1"></i><?php capalot_meta_datetime(); ?></span>
            <span class="meta-likes d-none d-md-inline-block"><i class="far fa-heart me-1"></i><?php echo capalot_get_post_likes(); ?></span>
            <span class="meta-fav d-none d-md-inline-block"><i class="far fa-star me-1"></i><?php echo capalot_get_post_favorites(); ?></span>
            <span class="meta-views"><i class="far fa-eye me-1"></i><?php echo capalot_get_post_views(); ?></span>
            <?php if (is_site_shop() && post_has_pay($post_id)) : ?>
              <span class="meta-price"><i class="<?php echo get_site_coin_icon(); ?> me-1"></i><?php echo $post_price; ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </article>
  </li>


<?php elseif ($args['type'] == 'list') : ?>
  <li class="col">
    <article class="post-item item-list">

      <div class="tips-badge position-absolute top-0 start-0 z-1 m-3 m-md-3">
        <?php if (is_sticky()) : ?>
          <div class="badge bg-dark bg-opacity-25"><?php _e('置顶', 'ripro'); ?></div>
        <?php endif; ?>
      </div>

      <div class="entry-media ratio ratio-3x2 col-auto">
        <a target="<?php echo get_target_blank(); ?>" class="media-img lazy <?php echo esc_attr($args['media_size_type']); ?> <?php echo esc_attr($args['media_fit_type']); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" data-bg="<?php echo capalot_get_thumbnail_url(); ?>">
          <?php if ($post_format_icon) : ?>
            <div class="post-format-icon"><i class="<?php echo $post_format_icon; ?>"></i></div>
          <?php endif; ?>
        </a>
      </div>
      <div class="entry-wrapper">
        <div class="entry-body">

          <?php if ($args['is_entry_cat']) : ?>
            <div class="entry-cat-dot"><?php capalot_meta_category(2); ?></div>
          <?php endif; ?>

          <h2 class="entry-title">
            <a target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
          </h2>
          <?php if ($args['is_entry_desc']) : ?>
            <div class="entry-desc"><?php echo capalot_get_post_excerpt(40); ?></div>
          <?php endif; ?>
        </div>
        <?php if ($args['is_entry_meta']) : ?>
          <div class="entry-footer">
            <div class="entry-meta">
              <span class="meta-date"><i class="far fa-clock me-1"></i><?php capalot_meta_datetime(); ?></span>
              <span class="meta-likes d-none d-md-inline-block"><i class="far fa-heart me-1"></i><?php echo capalot_get_post_likes(); ?></span>
              <span class="meta-fav d-none d-md-inline-block"><i class="far fa-star me-1"></i><?php echo capalot_get_post_favorites(); ?></span>
              <span class="meta-views"><i class="far fa-eye me-1"></i><?php echo capalot_get_post_views(); ?></span>
              <?php if (is_site_shop() && post_has_pay($post_id)) : ?>
                <span class="meta-price"><i class="<?php echo get_site_coin_icon(); ?> me-1"></i><?php echo $post_price; ?></span>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </article>
  </li>

<?php elseif ($args['type'] == 'title') : ?>
  <li class="col">
    <article class="post-item item-list">

      <div class="tips-badge position-absolute top-0 end-0 z-1 m-1">
        <?php if (is_sticky()) : ?>
          <div class="badge bg-dark bg-opacity-25"><?php _e('置顶', 'ripro'); ?></div>
        <?php endif; ?>
      </div>

      <div class="entry-wrapper">
        <div class="entry-body">

          <?php if ($args['is_entry_cat']) : ?>
            <div class="entry-cat-dot"><?php capalot_meta_category(2); ?></div>
          <?php endif; ?>

          <h2 class="entry-title">
            <a target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
          </h2>
          <?php if ($args['is_entry_desc']) : ?>
            <div class="entry-desc"><?php echo capalot_get_post_excerpt(40); ?></div>
          <?php endif; ?>
        </div>
        <?php if ($args['is_entry_meta']) : ?>
          <div class="entry-footer">
            <div class="entry-meta">
              <span class="meta-date"><i class="far fa-clock me-1"></i><?php capalot_meta_datetime(); ?></span>
              <span class="meta-likes d-none d-md-inline-block"><i class="far fa-heart me-1"></i><?php echo capalot_get_post_likes(); ?></span>
              <span class="meta-fav d-none d-md-inline-block"><i class="far fa-star me-1"></i><?php echo capalot_get_post_favorites(); ?></span>
              <span class="meta-views"><i class="far fa-eye me-1"></i><?php echo capalot_get_post_views(); ?></span>
              <?php if (is_site_shop() && post_has_pay($post_id)) : ?>
                <span class="meta-price"><i class="<?php echo get_site_coin_icon(); ?> me-1"></i><?php echo $post_price; ?></span>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </article>
  </li>

<?php endif; ?>