let currentPage = 1;
const body = jQuery("body");

let ca = {

  init: function () {
    ca.pay_action();
    ca.pagination();
    ca.toggle_dark();
    ca.add_comment();
    ca.post_tougao();
    ca.add_menus();
    ca.notification();
    ca.swiper();
    ca.account_action();
    ca.social_action();
    ca.captcha_action();
    ca.delete_post();
    ca.sticky_header();

    if (capalot.singular_id !== '0') {
      ca.add_post_views();
      ca.copy_password();
    }
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
            ca.notice({
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
    const share_btn = document.querySelector('.post-share-btn')

    if (!like_btn || !fav_btn || !share_btn) {
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
          is_add: like_btn.dataset.is,
        },
        beforeSend: () => {
          unlike_icon.classList.add('fa-spinner', 'fa-spin');
        },
        success: ({ status, msg }) => {
          status === 1
            ? ca.notice({ title: msg, icon: 'success' })
            : ca.notice({ title: msg, icon: 'error' });

          $('.fa-spinner').addClass('hidden')

          const count_el = document.querySelector('.like-count');
          const count = parseInt(count_el.innerText);

          if (like_btn.dataset.is == '0') {
            like_btn.dataset.is = '1';
            count_el.innerText = count - 1;
          } else {
            like_btn.dataset.is = '0';
            count_el.innerText = count + 1;
          }

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

    // 分享文章
    share_btn.addEventListener('click', () => {

      ca.ajax({
        data: {
          action: 'capalot_add_share_post',
          nonce: capalot.ajax_nonce,
          post_id: capalot.singular_id
        },
        success: ({ msg, status }) => {
          ca.popup({ html: msg.html });
        },
      })
    });
  },

  // 获取验证码
  captcha_action: function () {
    const captcha_code = document.querySelector('#captcha-img');

    if (!captcha_code) return;

    captcha_code.addEventListener('click', function () {
      ca.ajax({
        data: {
          action: 'capalot_get_captcha_code',
          nonce: capalot.ajax_nonce,
        },
        success: ({ status, msg }) => {
          if (status != 1) {
            ca.notice({ title: msg, icon: 'error' });
            return;
          }

          ca.notice({ title: msg, icon: 'success' });
          captcha_code.setAttribute('src', msg)
        }
      })
    })
  },

  /**
   * toast 提示
   * @param {string} title 标题
   * @param {string} icon 图标 'success' | 'error'
   * @param {number} timer 延迟时间 ms后自动关闭
   */
  notice: function ({ title = '成功', icon = 'success', timer = 2000 }) {
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

  /**
   * 对话框
   * @see https://sweetalert2.github.io/#configuration
   */
  popup: function ({
    text,
    html,
    title,
    time,
    callback = function () { },
    icon = '', // success | warning | error
    showCloseButton = true,
    showConfirmButton = false,
    confirmButtonText = '确定',
    showCancelButton = false,
    cancelButtonText = '取消',
    position = 'center',
    width = '240px',
    customClass = {},
  }) {
    const defaultClass = {
      popup: 'dark:bg-dark-card',
      htmlContainer: 'dark:bg-dark-card'
    }

    Swal.fire({
      title,
      text,
      icon,
      html,
      showCloseButton,
      position,
      showConfirmButton,
      confirmButtonText,
      showCancelButton,
      cancelButtonText,
      width,
      customClass: Object.assign(defaultClass, customClass),
    }).then(callback);

  },

  // 付款
  pay_action: function () {
    let o = null;
    let el = document.querySelector('.js-pay-action');

    $('.js-pay-action').on('click', function () {

      o = {
        nonce: capalot.ajax_nonce,
        post_id: $(this).data("id"),
        order_type: $(this).data("type"),
        order_info: $(this).data("info")
      };

      ca.get_pay_select_html(o)
    });

    $('body').on('click', '.pay-item', function () {
      const el = $(this);

      o.pay_type_id = el.data('id');
      o.action = 'capalot_get_pay_action';

      ca.ajax({
        data: o,
        complete: ({ responseJSON }) => ca.pay_callback(responseJSON)
      })

    });
  },

  // 付款回调
  pay_callback: function (payload = {}) {
    const { status, msg, method } = payload;

    if (status === 0) {
      ca.notice({ title: msg, icon: 'error' });
      return;
    }

    if (method === 'url') {
      location.href = msg;
    } else if (method === 'popup') {
      ca.popup({ html: msg, showCloseButton: false, width: '17rem' });
    } else if (method === 'reload') {
      ca.notice({ title: msg, icon: 'success' });
      setTimeout(() => {
        window.location.reload();
      }, 2000);
    }
  },

  // 获取支付选项
  get_pay_select_html: function (e) {
    e.action = 'capalot_get_pay_select_html';

    ca.ajax({
      data: e,
      success: ({ status, msg, data }) => {
        const customClass = {
          closeButton: 'w-8 h-8 absolute -bottom-3 left-0 right-0 mx-auto bg-white hover:bg-white rounded-full text-lg dark:bg-dark-card',
        }

        status == 1
          ? ca.popup({ html: data, customClass, width: '16rem' })
          : ca.popup({ content: msg, customClass, width: '16rem' });
      }
    });
  },

  // 分页
  pagination: function () {
    $('#load-more').on('click', function () {
      const icon = document.querySelector('.more_icon')

      currentPage++;
      const cat = new URL(window.location.href).searchParams.get('cat');
      const s = new URL(window.location.href).searchParams.get('s');

      $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        dataType: 'json',
        data: {
          action: 'capalot_load_more',
          paged: currentPage,
          cat,
          s,
        },
        beforeSend: () => {
          icon.style.display = 'inline-block'
        },
        success: function (response) {
          if (currentPage >= response.max) {
            $('#load-more').hide();
            $('#no-more-button').show();
          }
          $('.posts-wrap').append(response.html);
        },
        complete: () => {
          icon.style.display = 'none'
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
        document.cookie = "theme=light;path=/";
      } else {
        light.addClass('hidden');
        dark.removeClass('hidden');

        root.addClass('dark');
        document.cookie = "theme=dark;path=/";
      }
    }
  },

  // 轮播初始化
  swiper: function () {
    const swipers = document.querySelectorAll('.mySwiper');

    if (swipers.length <= 0) return;

    swipers.forEach((el) => {
      new Swiper(el, JSON.parse(el.dataset.config));
    })
  },

  // 文章阅读数量+1
  add_post_views: function () {
    ca.ajax({
      data: {
        action: 'capalot_add_post_views',
        nonce: capalot.ajax_nonce,
        post_id: capalot.singular_id,
      }
    })
  },

  // 删除文章
  delete_post: function () {
    const delete_icons = document.querySelectorAll('#delete-icon');

    if (!delete_icons) {
      return;
    }

    delete_icons.forEach((el) => {
      el.addEventListener('click', () => {
        const post_id = el.dataset.id;
        const action = el.dataset.action;

        const text = action === 'delete_post' ? '确认彻底删除该投稿吗？删除后不可恢复！' : '确认彻底删除该投稿吗？您可以在回收站恢复该投稿。';

        const callback = (result) => {
          if (result.isConfirmed) {
            window.location.href = `?action=${action}&post_id=${post_id}`;
          }
        }

        ca.popup({ title: '确认删除?', text, width: '20rem', icon: 'warning', showCancelButton: true, showConfirmButton: true, callback })
      })
    })
  },

  // 新增评论
  add_comment: function () {
    const commentform = jQuery("#commentform");
    commentform.find('input[type="submit"]');
    commentform.submit(function (e) {
      e.preventDefault();
      const t = jQuery("#submit");
      const n = t.val();
      jQuery.ajax({
        type: "POST",
        url: capalot.ajax_url,
        data: commentform.serialize() + "&action=capalot_ajax_comment&nonce=" + capalot.ajax_nonce,
        beforeSend: function (e) {
          t.prop("disabled", !0).val(capalot.get_text.__commiting)
        },
        error: function (e, t, n) {
          ca.notice({ title: e.responseText, icon: 'error' })
        },
        success: function (e) {
          ("success" == e) ? (t.val(capalot.get_text.__comment_success), ca.notice(capalot.get_text.__refresh_page), setTimeout(function () {
            window.location.reload()
          }, 2e3)) : ca.notice({ title: e, icon: "error" });
        },
        complete: function (e) {
          t.prop("disabled", !1).val(n)
        }
      })
    });
    var comments_list = jQuery(".comments-list");
    const scroll_button = jQuery(".infinite-scroll-button");
    const scroll_status = jQuery(".infinite-scroll-status");
    const scroll_msg = jQuery(".infinite-scroll-msg");
    scroll_button.length && (
      comments_list.on("request.infiniteScroll", function (e, t) {
        scroll_status.show()
      }),
      comments_list.on("load.infiniteScroll", function (e, t, n) {
        scroll_status.hide()
      }),
      comments_list.on("last.infiniteScroll", function (e, t, n) {
        scroll_button.hide(), scroll_msg.show()
      }),
      comments_list.infiniteScroll({
        append: ".comments-list > *",
        debug: !1,
        hideNav: ".comments-pagination",
        history: !1,
        path: ".comments-pagination a.next",
        prefill: !1,
        scrollThreshold: !1,
        button: ".infinite-scroll-button"
      })
    )
  },

  // 防抖
  debounce: function (fn, wait) {
    let timeout = null;
    return function () {
      if (timeout !== null) clearTimeout(timeout);
      timeout = setTimeout(fn, wait);
    }
  },

  // 投稿
  post_tougao: function () {
    const tougao = jQuery(".tougao_thumbnail");
    tougao.on("click", function () {
      const media = wp.media({
        multiple: !1
      });
      media.on("select", function () {
        var selection = media.state().get("selection").first().toJSON();
        var selection_url = selection.url;
        var selection = selection.id;

        jQuery("#_thumbnail_id").val(selection), tougao.empty(), selection = jQuery("<img>").attr("src", selection_url),
          tougao.append(selection)
      });
      media.open()
    });

    body.on("click", ".add-input-file", function () {
      const input_file = jQuery(this).closest(".input-group").find(".input-file-url"),
        media = wp.media({
          multiple: !1
        });
      media.on("select", function () {
        var select = media.state().get("selection").first().toJSON().url;
        input_file.val(select)
      }), media.open()
    });

    const video_switch = jQuery("#capalot_video_switch");
    const status_switch = jQuery("#capalot_status_switch");
    const price_input = jQuery("#price-input-warp");
    const down_input = jQuery("#down-input-warp");
    const video_input = jQuery("#video-input-warp");

    function r() {
      video_switch.is(":checked") || status_switch.is(":checked") ? price_input.show() : price_input.hide(),
        status_switch.is(":checked") ? down_input.show() : down_input.hide(),
        video_switch.is(":checked") ? video_input.show() : video_input.hide()
    }
    r();
    video_switch.on("change", r);
    status_switch.on("change", r);
    // 添加
    jQuery(".meta-input-item-add").on("click", function () {
      var input_warp = jQuery(this).closest(".meta-input-warp").find(".meta-input-group");
      let input_item = input_warp.find(".meta-input-item").length;
      var first_input_item = input_warp.find(".meta-input-item:first").clone();

      first_input_item.find("input").each(function () {
        var e = jQuery(this).attr("name").replace(/\[\d+\]/g, "[" + input_item + "]");
        jQuery(this).attr("name", e), jQuery(this).val("")
      });
      input_warp.append(first_input_item)
    });
    // 删除

    jQuery(".meta-input-group").on("click", ".meta-input-item-remove", function () {
      var input_item = jQuery(this).closest(".meta-input-item");
      0 !== input_item.index() && input_item.remove()
    })
  },

  // 公告
  notification: function () {
    const notification_btn = document.querySelector('.toggle-notify');

    if (!notification_btn) return;

    notification_btn.addEventListener('click', function () {
      ca.ajax({
        data: {
          action: 'capalot_get_site_notify',
          nonce: capalot.ajax_nonce,
        },
        success: ({ status, title, desc }) => {
          status === 1
            ? ca.popup({ title, text: desc, width: '20rem', icon: 'info' })
            : ca.popup({ title, text: desc, width: '20rem', icon: 'error' })
        }
      })


    })
  },

  // 固定头部
  sticky_header: function () {
    var prevScrollpos = window.pageYOffset;
    var navbar = document.querySelector('.site-header');
    // 侧边固定滚轴
    var footer_rollbar = document.querySelector('.site-footer-rollbar');

    window.addEventListener('scroll', function () {
      var currentScrollPos = window.pageYOffset;
      var scrolled = window.scrollY;
      if (prevScrollpos > currentScrollPos) {//向上

        if (this.window.innerWidth > 768) {
          footer_rollbar.classList.add('md:block');
          footer_rollbar.classList.remove('hidden');
        }
        gsap.to(navbar, { y: 0, duration: 0.3, ease: "power1.out" });
        if (scrolled == 0) {
          navbar.classList.remove('navbar-sticky');
        } else {
          navbar.classList.add('fixed');
          navbar.classList.add('navbar-sticky');
        }
      } else {
        gsap.to(navbar, { y: -100, duration: 0.3, ease: "power1.out" });
        navbar.classList.remove('fixed');
        footer_rollbar.classList.add('hidden');
        footer_rollbar.classList.remove('md:block');
      }
      prevScrollpos = currentScrollPos;
    });
  },

  // 导航菜单
  add_menus: function () {
    let main_menu = document.querySelector('.sidebar-main-menu');
    let warp_menu = document.querySelector('.sidebar-menu-warp');

    if (body.hasClass("uc-page")) {
      warp_menu.classList.remove('hidden')
      main_menu.classList.add('hidden')
    }
  },

  // 复制密码
  copy_password: function () {
    const copy_buttons = document.querySelectorAll('#copy_btn');
    const copy_spans = document.querySelectorAll('#copy_span');

    if (copy_buttons.length <= 0) return;

    copy_buttons.forEach(btn => {
      btn.addEventListener('click', this.debounce(this.copy.bind(this, btn), 300));
    })

    copy_spans.forEach(span => {
      span.addEventListener('click', this.debounce(this.copy.bind(this, span), 300));
    })
  },
  copy: function (el) {
    let content = '';

    if (el.dataset.pwd) {
      content = el.dataset.pwd;
    } else {
      const span = el.previousElementSibling;
      content = span.dataset.pwd
    }

    navigator.clipboard.writeText(content)
      .then(() => {
        ca.notice({ title: '复制成功', icon: 'success' });
      })
      .catch(() => {
        ca.notice({ title: '复制失败', icon: 'error' });
      })
  }
}

document.addEventListener('DOMContentLoaded', () => {
  ca.init();
})

