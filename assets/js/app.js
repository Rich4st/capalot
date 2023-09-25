let currentPage = 1;
const body = jQuery("body");
let ca = {

  init: function () {
    ca.pay_action();
    ca.pagination();
    ca.toggle_dark();

    const swiperEl = document.querySelector('.swiper');
    if (swiperEl)
      ca.swiper();

    ca.account_action();
    ca.social_action();
  },

  /**
   * ajax请求
   * @param {object} data 请求数据
   * @param {function} beforeSend 发送请求前回调
   * @param {function} success 请求成功回调
   * @param {function} complete 请求完成回调
   */
  ajax: function ({
    data,
    beforeSend,
    success,
    complete,
  }) {

    $.ajax({
      type: 'POST',
      url: capalot.ajax_url,
      dataType: 'json',
      data,
      async: !0,

      beforeSend,
      success,
      complete,
      error: function (e) {
        console.log(e.responseText, 5000);
      }
    });
  },

  // 登录注册操作
  account_action: function () {
    const login_btn = document.querySelector('#click-submit')
    const form = $('#account-from');

    if (login_btn) {
      login_btn.addEventListener('click', (e) => {
        e.preventDefault();
        console.log(123);
        const payload = form.serializeArray();
        let o = decodeURIComponent(location.href.split("redirect_to=")[1] || ""),
          n = {
            nonce: capalot.ajax_nonce
          };
        payload.forEach(({ name: name, value: v }) => {
          n[name] = v
        });

        ca.ajax({
          data: n,
          beforeSend: () => {
            login_btn.classList.add('loading');
          },
          success: ({
            status,
            msg,
            back_url
          }) => {
            ca.popup({
              title: msg,
              icon: status == 1 ? 'success' : 'error',
              showCloseButton: false,
            });

            1 == status && setTimeout(() => {
              (o = window.frames.length !== parent.frames.length ? "" : o)
                ? window.location.href = o
                : back_url ? window.location.href = back_url : window.location.reload()
            }, 2e3)
          },
          complete: () => {
            login_btn.classList.remove('loading');
          }
        })
      })
    }

  },

  // 社交操作
  social_action: function () {
    const storage_key = 'post_like_storage';
    const like_btn = document.querySelector('.post-like-btn');
    const fav_btn = document.querySelector('.post-fav-btn');

    if (!like_btn || !fav_btn) {
      return;
    }

    // 点赞文章
    like_btn.addEventListener('click', (e) => {
      if (localStorage.getItem(storage_key) === capalot.singular_id) {
        return ca.popup({ title: '您已经点过赞了', icon: 'info' });
      }
      like_btn.classList.add('bg-[#fdf0fb]');

      const unlike_icon = like_btn.querySelector('.unlike');
      const liked_icon = like_btn.querySelector('.liked');

      ca.ajax({
        data: {
          action: 'capalot_add_like_post',
          nonce: capalot.ajax_nonce,
          post_id: capalot.singular_id,
        },
        beforeSend: () => {
          unlike_icon.classList.add('fa-spinner', 'fa-spin');
        },
        success: ({ status, msg }) => {
          status === 1
            ? ca.popup({ title: msg, icon: 'success' })
            : ca.popup({ title: msg, icon: 'error' });

          $('.fa-spinner').addClass('hidden')

          liked_icon.classList.remove('hidden');
        }
      })
    });

    // 收藏文章
    fav_btn.addEventListener('click', () => {
      if (localStorage.getItem(storage_key) === capalot.singular_id) {
        return ca.popup({ title: '您已经收藏过了', icon: 'info' });
      }
      fav_btn.classList.add('bg-[#fdf0fb]');

      const unfav_icon = fav_btn.querySelector('.unfav');
      const fav_icon = fav_btn.querySelector('.fav');

      ca.ajax({
        data: {
          action: 'capalot_add_fav_post',
          nonce: capalot.ajax_nonce,
          is_add: fav_btn.dataset.is,
          post_id: capalot.singular_id,
        },
        beforeSend: () => {
          unfav_icon.classList.add('fa-spinner', 'fa-spin');
        },
        success: ({ status, msg }) => {
          status === 1
            ? ca.notice({ title: msg, icon: 'success' })
            : ca.notice({ title: msg, icon: 'error' });

          $('.fa-spinner').addClass('hidden')
          fav_btn.dataset.is == '0' ? fav_btn.dataset.is = '1' : fav_btn.dataset.is = '0';
          fav_icon.classList.remove('hidden');
        }
      })
    });
  },

  // toast 提示
  notice: function ({ title, icon, timer = 2000 }) {
    Swal.fire({
      title,
      position: 'top',
      toast: true,
      timer,
      icon,
      showConfirmButton: false,
      customClass: {
        container: 'mt-10'
      }
    });
  },

  // 对话框
  popup: function ({
    content,
    html,
    title,
    time,
    callback,
    icon = '', // success | info | error
    showCloseButton = true,
    position = 'center',
  }) {

    Swal.fire({
      title,
      icon,
      html,
      showCloseButton,
      position,
      showConfirmButton: false,
      width: '240px',
      customClass: {
        closeButton: 'w-8 h-8 absolute -bottom-2 left-0 right-0 mx-auto bg-white hover:bg-white rounded-full text-[24px]'
      }
    });

  },

  // 付款
  pay_action: function (e) {
    let o = null;
    let el = document.querySelector('.js-pay-action');

    $('.js-pay-action').on('click', function () {
      o = {
        nonce: capalot.ajax_nonce,
        post_id: el.dataset.id,
        order_type: el.dataset.type,
        order_info: el.dataset.info,
      };

      ca.get_pay_select_html(o)
    });

    $('body').on('click', '.pay-item', function () {
      const el = $(this);

      o.pay_type_id = el.data('id');
      o.action = 'capalot_get_pay_action';

      ca.ajax({
        data: o,
        complete: ({ responseJSON }) => {
          const { status, msg } = responseJSON;
          status === 1
            ? ca.notice({ title: msg, icon: 'success' })
            : ca.notice({ title: msg, icon: 'error' })
        }
      })

    });
  },

  // 获取支付选项
  get_pay_select_html: function (e) {
    e.action = 'capalot_get_pay_select_html';

    ca.ajax({
      data: e,
      success: ({ status, msg, data }) => {
        status == 1
          ? ca.popup({ html: data })
          : ca.popup({ content: msg });
      }
    });
  },

  // 分页
  pagination: function () {
    $('#load-more').on('click', function () {

      currentPage++;

      $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        dataType: 'json',
        data: {
          action: 'capalot_load_more',
          paged: currentPage,
        },
        success: function (response) {
          if (currentPage >= response.max) {
            $('#load-more').hide();
            $('#no-more-button').show();
          }
          $('.posts-wrap').append(response.html);
        }
      });
    }
    );
  },

  // 暗黑模式
  toggle_dark: function () {
    const root = $('html')

    const light = $('#light-mode')
    const dark = $('#dark-mode')

    light.on('click', toggleVisibility);
    dark.on('click', toggleVisibility);

    // 切换元素可见性的函数
    function toggleVisibility() {
      if (light.hasClass("hidden")) {
        light.removeClass('hidden');
        dark.addClass('hidden');
        root.removeClass('dark');
      } else {
        light.addClass('hidden');
        dark.removeClass('hidden');
        root.addClass('dark');
      }
    }
  },

  // 轮播初始化
  swiper: function () {
    const el = document.querySelector('.mySwiper');

    var swiper = new Swiper(".mySwiper", JSON.parse(el.dataset.config));
  }

}

document.addEventListener('DOMContentLoaded', () => {
  ca.init();
})

