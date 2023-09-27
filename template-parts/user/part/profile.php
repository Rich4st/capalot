<?php

global $current_user;

$unsetoauth = get_response_param('unsetoauth', '', 'get');
// 解绑第三方登录
if ($unsetoauth == 'qq' || $unsetoauth == 'weixin') {
  $un_meta_array = array('openid', 'name', 'avatar');
  foreach ($un_meta_array as $key) {
    $un_meta_key = 'open_' . $unsetoauth . $key;
    delete_user_meta($current_user->ID, $meta_key);
  }
}


?>

<div class=" mb-4 bg-white dark:bg-dark-card p-4 mx-2 rounded">
  <div class=" mb-3 ">
    <h5 class="font-bold"><?php _e('基本信息', 'ripro'); ?></h5>
  </div>
  <div class="card-body">
    <div class="flex justify-between items-start  mb-4">
      <!-- Image -->
      <div class="relative">
        <div class="flex items-center">
          <div class="position me-3">
            <div class="avatar avatar-xl py-2">
              <img class="rounded-full border border-white shadow" src="<?php echo get_avatar_url($current_user->ID); ?>" alt="user">
            </div>
          </div>
          <div class="block">
            <label for="inputAvatarFile" type="button" class="btn rounded py-1 px-2 bg-[#2163e8] bg-opacity-10 text-[#007aff] hover:bg-opacity-90 hover:text-white cursor-pointer"><?php _e('上传头像', 'ripro'); ?></label>
            <input class="hidden" type="file" name="inputAvatarFile" id="inputAvatarFile" accept=".jpg, .gif, .png" resetonclick="true">
          </div>
        </div>
      </div>
    </div>

    <form class="gap-4 grid lg:grid-cols-4 grid-cols-1" id="user-profile">
      <!-- Input item -->
      <div class="lg:col-span-2 col-span-1 col-start-1 pb-2 ">
        <label class="pb-2 block text-gray-500"><?php _e('显示昵称', 'ripro'); ?></label>
        <input type="text" class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="display_name" placeholder="" value="<?php echo $current_user->display_name; ?>">
      </div>
      <!-- Input item -->
      <div class="lg:col-span-2 col-span-1 lg:col-start-3 ">
        <label class="pb-2 block text-gray-500"><?php _e('联系QQ', 'ripro'); ?></label>
        <input type="text" class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="uc_lxqq" value="<?php echo get_user_meta($current_user->ID, 'qq', 1); ?>">
      </div>
      <!-- Textarea item -->
      <div class="lg:col-span-4 col-span-1 col-start-1">
        <label class="pb-2 block text-gray-500"><?php _e('个人介绍', 'ripro'); ?></label>
        <textarea class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" rows="3" name="description"><?php echo get_user_meta($current_user->ID, 'description', 1); ?></textarea>
      </div>
      <!-- Save button -->
      <div class="flex lg:col-start-4 justify-end text-white ">
        <input type="hidden" name="action" value="capalot_update_profile">
        <button type="submit" id="save-submit" class="bg-black px-4 py-1 rounded-lg hover:bg-[#3c3c41]"><?php _e('保存资料', 'ripro'); ?></button>
      </div>
    </form>
  </div>
</div>



<div class="mb-4 bg-white dark:bg-dark-card p-4 mx-2 rounded">
  <!-- Content -->
  <div class="mb-3">
    <h5 class="font-bold"><?php _e('账户绑定', 'ripro'); ?></h5>
  </div>
  <!-- Button -->

  <form class="gap-4 grid lg:grid-cols-4 grid-cols-1" id="edit-email-form">
    <div class="lg:col-span-2 col-span-1 col-start-1 pb-2 ">
      <label class="pb-2 block text-gray-500"><?php _e('当前邮箱', 'ripro'); ?></label>
      <input type="text" class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="user_email" value="<?php echo $current_user->user_email; ?>" disabled>
    </div>

    <div class="lg:col-span-2 col-span-1 lg:col-start-3">
      <label class="pb-2 block text-gray-500"><?php _e('新邮箱地址', 'ripro'); ?></label>
      <input type="email" class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="new_user_email" value="" autocomplete="off">
    </div>

    <div class="lg:col-span-4 col-span-1 col-start-1">
      <?php if (_capalot('is_sns_qq', false) || _capalot('is_sns_weixin', false)) : ?>
        <div class="position-relative my-4">
          <hr>
          <p class="small bg-white position-absolute top-50 start-50 translate-middle px-2"><?php _e('快捷登录绑定信息', 'ripro'); ?></p>
        </div>
        <div class="d-grid gap-2 d-md-block text-center">

          <?php if (_capalot('is_sns_qq', false)) {
            if (!empty(get_user_meta($current_user->ID, 'open_qq_openid', true))) {
              echo '<a href="' . get_uc_menu_link('profile') . '?unsetoauth=qq" class="btn btn-danger mx-2"><i class="fab fa-qq me-1"></i>' . __('解绑QQ登录', 'ripro') . '</a>';
            } else {
              echo '<a href="' . get_oauth_permalink('qq') . '" class="btn btn-info mx-2"><i class="fab fa-qq me-1"></i>' . __('绑定QQ登录', 'ripro') . '</a>';
            }
            if (!empty(get_user_meta($current_user->ID, 'open_weixin_openid', true))) {
              echo '<a href="' . get_uc_menu_link('profile') . '?unsetoauth=weixin" class="btn btn-danger mx-2"><i class="fab fa-qq me-1"></i>' . __('解绑微信登录', 'ripro') . '</a>';
            } else {
              echo '<a href="' . get_oauth_permalink('weixin') . '" class="btn btn-success mx-2"><i class="fab fa-weixin me-1"></i>' . __('绑定微信登录', 'ripro') . '</a>';
            }
          } ?>

        </div>
      <?php endif; ?>
    </div>

    <!-- Save button -->
    <div class="flex lg:col-start-4 justify-end text-white ">
      <input type="hidden" name="action" value="capalot_update_new_email">
      <button type="submit" id="edit-email" class="bg-[#d6293e] p-2 text-white rounded-lg hover:bg-[#b62335]"><?php _e('确认修改邮箱', 'ripro'); ?></button>
    </div>
  </form>




</div>



<div class="bg-white dark:bg-dark-card p-4 mx-2 rounded">
  <!-- Content -->
  <div class="mb-3">
    <h5 class="font-bold"><?php _e('密码修改', 'ripro'); ?></h5>
  </div>
  <!-- Button -->

  <form class="gap-4 grid lg:grid-cols-4 grid-cols-1" id="edit-password-form">
    <div class="lg:col-span-2 col-span-1 col-start-1 pb-2">
      <label class="pb-2 block text-gray-500"><?php _e('邮箱信息', 'ripro'); ?></label>
      <input type="text" class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="user_email" value="<?php echo $current_user->user_email; ?>" disabled>
    </div>

    <div class="lg:col-span-2 col-span-1 lg:col-start-3">
      <label class="pb-2 block text-gray-500"><?php _e('旧密码', 'ripro'); ?></label>
      <?php if (user_is_oauth_password($current_user->ID)) : ?>
        <input type="password" class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="old_password" value="第三方注册，请设置新密码" autocomplete="off" disabled>
      <?php else : ?>
        <input type="password" class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="old_password" value="" autocomplete="off">
      <?php endif; ?>
    </div>
    <div class="lg:col-span-2 col-span-1 col-start-1 pb-2">
      <label class="pb-2 block text-gray-500"><?php _e('新密码', 'ripro'); ?></label>
      <input type="password" class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="new_password" value="" autocomplete="off">
    </div>
    <div class="lg:col-span-2 col-span-1 lg:col-start-3">
      <label class="pb-2 block text-gray-500"><?php _e('确认新密码', 'ripro'); ?></label>
      <input type="password" class="w-full  dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" name="new_password2" value="" autocomplete="off">
    </div>
    <!-- Save button -->
    <div class="flex lg:col-start-4 justify-end text-white">
      <input type="hidden" name="action" value="capalot_update_password">
      <button type="submit" id="edit-password" class="bg-[#d6293e] p-2 text-white rounded-lg hover:bg-[#b62335]"><?php _e('确认修改密码', 'ripro'); ?></button>
    </div>
  </form>

</div>






<script type="text/javascript">
  jQuery(function($) {

    $("#inputAvatarFile").change(function(e) {
      var formData = new FormData();
      formData.append("nonce", capalot.ajax_nonce);
      formData.append("action", "capalot_update_avatar");
      formData.append("file", e.currentTarget.files[0]);
      $.ajax({
        url: capalot.ajax_url,
        dataType: 'json',
        type: 'POST',
        async: false,
        data: formData,
        processData: false, // 使数据不做处理
        contentType: false, // 不要设置Content-Type请求头
        success: function({
          msg,
          status,
          icon
        }) {
          status === 1
            ? ca.notice({ title: msg, icon: 'success' })
            : ca.notice({ title: msg, icon: 'error' });        
          if (status == 1) {
            setTimeout(function() {
              window.location.reload()
            }, 2000)
          }
        },
        error: (error) => {
          ca.notice({
            title: error,
            icon: 'error'
          })
        },
        complete: ({
          responseJSON
        }) => {
          const { status, msg } = responseJSON;

          if (status == 0)
            return;
          window.location.reload();
        }
      });

    });


    $("#save-submit").on("click", function(e) {
      e.preventDefault();
      var _this = $(this);
      var formData = $("#user-profile").serializeArray();

      var data = {
        nonce: capalot.ajax_nonce,
      };

      formData.forEach(({
        name,
        value
      }) => {
        data[name] = value;
      });

      ca.ajax({
        data,
        beforeSend: () => {
          _this.attr("disabled", "true")
        },
        success: ({
          status,
          msg,
          icon
        }) => {
          status === 1
            ? ca.notice({ title: msg, icon: 'success' })
            : ca.notice({ title: msg, icon: 'error' });        
          if (status == 1) {
            setTimeout(function() {
              window.location.reload()
            }, 2000)
          }
        },
        complete: () => {
          _this.removeAttr("disabled")
        }
      });

    });

    $("#edit-password").on("click", function(e) {
      e.preventDefault();
      var _this = $(this);
      var formData = $("#edit-password-form").serializeArray();

      var data = {
        nonce: capalot.ajax_nonce,
      };

      formData.forEach(({
        name,
        value
      }) => {
        data[name] = value;
      });
      if (!data.old_password) {
        $('input[name="old_password"]').focus();
        return;
      }
      if (!data.new_password) {
        $('input[name="new_password"]').focus();
        return;
      }
      if (!data.new_password2) {
        $('input[name="new_password2"]').focus();
        return;
      }

      ca.ajax({
        data,
        beforeSend: () => {
          _this.attr("disabled", "true")
        },
        success: ({
          status,
          msg,
          icon
        }) => {
          status === 1
            ? ca.notice({ title: msg, icon: 'success' })
            : ca.notice({ title: msg, icon: 'error' });
          if (status == 1) {
            setTimeout(function() {
              window.location.reload()
            }, 2000)
          }
        },
        complete: () => {
          _this.removeAttr("disabled")
        }
      });

    });

    $("#edit-email").on("click", function(e) {
      e.preventDefault();
      var _this = $(this);
      var formData = $("#edit-email-form").serializeArray();
      var data = {
        nonce: capalot.ajax_nonce,
      };

      formData.forEach(({
        name,
        value
      }) => {
        data[name] = value;
      });

      if (!data.new_user_email) {
        $('input[name="new_user_email"]').focus();
        return;
      }

      ca.ajax({
        data,
        beforeSend: () => {
          _this.attr("disabled", "true")
        },
        success: ({
          status,
          msg,
          icon
        }) => {
          status === 1
            ? ca.notice({ title: msg, icon: 'success' })
            : ca.notice({ title: msg, icon: 'error' });
          if (status == 1) {
            setTimeout(function() {
              window.location.reload()
            }, 2000)
          }
        },
        complete: () => {
          _this.removeAttr("disabled")
        }
      });

    });


  });
</script>