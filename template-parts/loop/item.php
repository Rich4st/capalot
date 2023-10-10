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
  'size' => 'md'
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
<!-- 文章展示页中文章布局 -->
<?php if ($args['type'] == 'grid') : ?>
  <li class="dark:bg-dark-card rounded-lg overflow-hidden list-none cursor-pointer bg-white border dark:border-[#222] transition  hover:-translate-y-1 hover:shadow-2xl  duration-500">
    <article class="post-item item-grid relative">

      <div class="tips-badge absolute w-10 text-center top-0 start-0 z-50 m-2 bg-[#b0adac]  rounded-xl">
        <?php if (is_sticky()) : ?>
          <div class="text-[0.5rem] bg-opacity-25 text-white">置顶</div>
        <?php endif; ?>
      </div>

      <div class="entry-media ratio  <?php echo esc_attr($args['media_class']); ?>">
        <a target="<?php echo get_target_blank(); ?>" style="background-image: url(<?php echo capalot_get_thumbnail_url(); ?>);" class="block  bg-no-repeat   <?php echo esc_attr($args['media_size_type']); ?> 
        <?php echo esc_attr($args['media_fit_type']); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" data-bg="<?php echo capalot_get_thumbnail_url(); ?>">
          <?php if ($post_format_icon) : ?>
            <div class="post-format-icon"><i class="<?php echo $post_format_icon; ?>"></i></div>
          <?php endif; ?>
        </a>
      </div>

      <div class="entry-wrapper   text-gray-400  p-2  text-[0.75rem]">
        <?php if ($args['is_entry_cat']) : ?>
          <div class="entry-cat-dot   mb-1 whitespace-nowrap text-ellipsis overflow-hidden"><?php capalot_meta_category(2); ?></div>
        <?php endif; ?>

        <h2 class="font-bold text-gray-700  dark:text-gray-50  text-base whitespace-nowrap text-ellipsis overflow-hidden">
          <a target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
        </h2>

        <?php if ($args['is_entry_desc']) : ?>
          <div class=" text-gray-400 text-[12px] min-h-[1.25rem]  line-clamp-1 my-1 " title="<?php echo capalot_get_post_excerpt(40); ?>"><?php echo capalot_get_post_excerpt(40); ?></div>
        <?php endif; ?>

        <?php if ($args['is_entry_meta']) : ?>
          <div class="entry-meta flex flex-row justify-between  ">
            <div>
              <span class="meta-date"><i class="fa-regular fa-clock pr-1"></i>
                <?php capalot_meta_datetime(); ?></span>
              <span class="meta-likes d-none md:inline-block hidden"><i class="fa-regular fa-heart pr-1"></i><?php echo capalot_get_post_likes(); ?></span>
              <span class="meta-fav d-none md:inline-block hidden"><i class="fa-regular fa-star pr-1"></i><?php echo capalot_get_post_favorites(); ?></span>
              <span class="meta-views"><i class="fa-regular fa-eye pr-1"></i><?php echo capalot_get_post_views(); ?></span>
            </div>
            <?php if (is_site_shop() && post_has_pay($post_id)) : ?>
              <span class="meta-price whitespace-nowrap flex flex-row"><i class="<?php echo get_site_coin_icon(); ?> me-1"></i><?php echo $post_price; ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </article>
  </li>

<?php elseif ($args['type'] == 'grid-overlay') : ?>
  <li class="col cursor-pointer rounded-lg overflow-hidden text-[0.75rem]">
    <article class="post-item relative group ">

      <div class="tips-badge absolute w-10 text-center top-0 start-0 z-50 m-2 bg-[#b0adac]  rounded-xl">
        <?php if (is_sticky()) : ?>
          <div class="text-[0.5rem] bg-opacity-25 text-white"><?php _e('置顶', 'ripro'); ?></div>
        <?php endif; ?>
      </div>
      <div class=" p-2 text-white absolute bottom-0 z-50 w-full">
        <?php if ($args['is_entry_meta']) : ?>
          <div class="entry-meta flex flex-row justify-between">
            <div class="">
              <span class="meta-date"><i class="fa-regular fa-clock pr-1"></i>
                <?php capalot_meta_datetime(); ?></span>
              <span class="meta-likes d-none md:inline-block hidden"><i class="fa-regular fa-heart pr-1"></i><?php echo capalot_get_post_likes(); ?></span>
              <span class="meta-fav d-none md:inline-block hidden"><i class="fa-regular fa-star pr-1"></i><?php echo capalot_get_post_favorites(); ?></span>
              <span class="meta-views"><i class="fa-regular fa-eye pr-1"></i><?php echo capalot_get_post_views(); ?></span>
            </div>
            <?php if (is_site_shop() && post_has_pay($post_id)) : ?>
              <span class="meta-price whitespace-nowrap flex flex-row"><i class="<?php echo get_site_coin_icon(); ?> me-1"></i><?php echo $post_price; ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="entry-media ratio  <?php echo esc_attr($args['media_class']); ?>">
        <a target="<?php echo get_target_blank(); ?>" style="background-image: url(<?php echo capalot_get_thumbnail_url(); ?>);" class="block  bg-no-repeat overflow-hidden  <?php echo esc_attr($args['media_size_type']); ?> 
        <?php echo esc_attr($args['media_fit_type']); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" data-bg="<?php echo capalot_get_thumbnail_url(); ?>">
          <?php if ($post_format_icon) : ?>
            <div class="post-format-icon "><i class="<?php echo $post_format_icon; ?>"></i></div>
          <?php endif; ?>
        </a>

        <div class="absolute text-gray-50 bottom-0 left-0 top-0 right-0 hidden group-hover:flex items-center justify-between px-2 w-full h-full  bg-opacity-10 transition-opacity" style="background: linear-gradient(180deg,transparent 62%,rgba(0, 0, 0, 0.00345888) 63.94%,rgba(0, 0, 0, 0.014204) 65.89%,rgba(0, 0, 0, 0.0326639) 67.83%,rgba(0, 0, 0, 0.0589645) 69.78%,rgba(0, 0, 0, 0.0927099) 71.72%,rgba(0, 0, 0, 0.132754) 73.67%,rgba(0, 0, 0, 0.177076) 75.61%,rgba(0, 0, 0, 0.222924) 77.56%,rgba(0, 0, 0, 0.267246) 79.5%,rgba(0, 0, 0, 0.30729) 81.44%,rgba(0, 0, 0, 0.341035) 83.39%,rgba(0, 0, 0, 0.367336) 85.33%,rgba(0, 0, 0, 0.385796) 87.28%,rgba(0, 0, 0, 0.396541) 89.22%,rgba(0, 0, 0, 0.4) 91.17%);border-radius:0 0 0.5rem 0.5rem;">
          <a class="w-full h-full" target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
            <div class="absolute bottom-8 left-2 w-full pr-2">
              <div class="mb-1 whitespace-nowrap  text-ellipsis overflow-hidden">
                <?php if ($args['is_entry_cat']) : ?>
                  <?php capalot_meta_category(2); ?>
                <?php endif; ?>
              </div>

              <h2 class="font-bold text-base  whitespace-nowrap text-ellipsis overflow-hidden">
                <a target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
              </h2>
            </div>
          </a>
        </div>
      </div>
    </article>
  </li>


<?php elseif ($args['type'] == 'list') : ?>
  <li class="dark:bg-dark-card  rounded-lg bg-white  cursor-pointer shadow-[rgba(0,_0,_0,_0.24)_0px_3px_8px]  <?php echo $card_size; ?> transition-all duration-300 hover:shadow-[0px_4px_16px_rgba(17,17,26,0.1),_0px_8px_24px_rgba(17,17,26,0.1),_0px_16px_56px_rgba(17,17,26,0.1)]  ">
    <article class="relative flex flex-row p-2">

      <div class="tips-badge absolute w-10 text-center top-0 start-0 z-50 m-2 bg-[#b0adac]  rounded-xl">
        <?php if (is_sticky()) : ?>
          <div class="text-[0.5rem] bg-opacity-25 text-white"><?php _e('置顶', 'ripro'); ?></div>
        <?php endif; ?>
      </div>

      <div class="max-w-[8rem] ratio ratio-16x9 col-auto mr-2 ">
        <a target="<?php echo get_target_blank(); ?>" style="background-image: url(<?php echo capalot_get_thumbnail_url(); ?>);" class="block  bg-no-repeat  <?php echo esc_attr($args['media_size_type']); ?>
        <?php echo esc_attr($args['media_fit_type']); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" data-bg="<?php echo capalot_get_thumbnail_url(); ?>">
          <?php if ($post_format_icon) : ?>
            <div class="post-format-icon"><i class="<?php echo $post_format_icon; ?>"></i></div>
          <?php endif; ?>
        </a>
      </div>
      <div class="entry-wrapper  w-full text-gray-400 text-[0.75rem] ">
        <div class="entry-body h-5/6  ">

          <?php if ($args['is_entry_cat']) : ?>
            <div class=" mb-1 whitespace-nowrap text-ellipsis overflow-hidden"><?php capalot_meta_category(2); ?></div>
          <?php endif; ?>

          <h2 class=" text-black text-base dark:text-gray-50  font-bold  whitespace-nowrap text-ellipsis overflow-hidden">
            <a target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
          </h2>
          <?php if ($args['is_entry_desc']) : ?>
            <div class=" whitespace-normal text-ellipsis overflow-hidden"><?php echo capalot_get_post_excerpt(40); ?></div>
          <?php endif; ?>
        </div>
        <?php if ($args['is_entry_meta']) : ?>
          <div class="entry-meta flex flex-row justify-between ">
            <div>
              <span class="meta-date"><i class="fa-regular fa-clock pr-1"></i>
                <?php capalot_meta_datetime(); ?></span>
              <span class="meta-likes d-none md:inline-block hidden"><i class="fa-regular fa-heart pr-1"></i><?php echo capalot_get_post_likes(); ?></span>
              <span class="meta-fav d-none md:inline-block hidden"><i class="fa-regular fa-star pr-1"></i><?php echo capalot_get_post_favorites(); ?></span>
              <span class="meta-views"><i class="fa-regular fa-eye pr-1"></i><?php echo capalot_get_post_views(); ?></span>
            </div>
            <?php if (is_site_shop() && post_has_pay($post_id)) : ?>
              <span class="meta-price whitespace-nowrap flex flex-row"><i class="<?php echo get_site_coin_icon(); ?> me-1"></i><?php echo $post_price; ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </article>
  </li>

<?php elseif ($args['type'] == 'title') : ?>
  <li class="dark:bg-dark-card  rounded-lg bg-white  cursor-pointer shadow-[rgba(0,_0,_0,_0.24)_0px_3px_8px]  h-32 transition-all duration-300 hover:shadow-[0px_4px_16px_rgba(17,17,26,0.1),_0px_8px_24px_rgba(17,17,26,0.1),_0px_16px_56px_rgba(17,17,26,0.1)] ">
    <article class="item-list relative p-2">

      <div class="absolute w-10 text-center top-0 right-0 z-50 m-2 bg-[#b0adac]  rounded-xl">
        <?php if (is_sticky()) : ?>
          <div class="text-[0.5rem] bg-opacity-25 text-white"><?php _e('置顶', 'ripro'); ?></div>
        <?php endif; ?>
      </div>

      <div class="entry-wrapper text-gray-400 text-[0.75rem]">
        <div class="entry-body h-24">

          <?php if ($args['is_entry_cat']) : ?>
            <div class="entry-cat-dot mb-1 whitespace-nowrap text-ellipsis overflow-hidden"><?php capalot_meta_category(2); ?></div>
          <?php endif; ?>

          <h2 class=" text-base text-black dark:text-gray-50  font-bold  whitespace-nowrap text-ellipsis overflow-hidden">
            <a target="<?php echo get_target_blank(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
          </h2>
          <?php if ($args['is_entry_desc']) : ?>
            <div class="entry-desc whitespace-normal text-ellipsis overflow-hidden"><?php echo capalot_get_post_excerpt(40); ?></div>
          <?php endif; ?>
        </div>
        <?php if ($args['is_entry_meta']) : ?>
          <div class="entry-meta flex flex-row justify-between ">
            <div>
              <span class="meta-date"><i class="fa-regular fa-clock pr-1"></i>
                <?php capalot_meta_datetime(); ?></span>
              <span class="meta-likes d-none md:inline-block hidden"><i class="fa-regular fa-heart pr-1"></i><?php echo capalot_get_post_likes(); ?></span>
              <span class="meta-fav d-none md:inline-block hidden"><i class="fa-regular fa-star pr-1"></i><?php echo capalot_get_post_favorites(); ?></span>
              <span class="meta-views"><i class="fa-regular fa-eye pr-1"></i><?php echo capalot_get_post_views(); ?></span>
            </div>
            <?php if (is_site_shop() && post_has_pay($post_id)) : ?>
              <span class="meta-price whitespace-nowrap flex flex-row"><i class="<?php echo get_site_coin_icon(); ?> me-1"></i><?php echo $post_price; ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </article>
  </li>

<?php endif; ?>