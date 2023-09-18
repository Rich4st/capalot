let currentPage = 1;

let ca = {

  init: function () {
    ca.pay_action();
    ca.pagination();
    ca.toggle_dark();
    ca.swiper();
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

  // 对话框
  popup: function ({
    content,
    html,
    title,
    time,
    callback,
    icon = '', // success | info | error
  }) {

    Swal.fire({
      title,
      icon,
      html,
      showCloseButton: true,
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
            ? ca.popup({ title: msg, icon: 'success' })
            : ca.popup({ title: msg, icon: 'error' })
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

  swiper: function () {
    var swiper = new Swiper(".mySwiper", {});
  }

}

document.addEventListener('DOMContentLoaded', () => {
  ca.init();
})

