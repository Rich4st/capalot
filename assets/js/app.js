let currentPage = 1;

let ca = {

  init: function () {
    ca.pay_action();
    ca.pagination();
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
    msg,
    time,
    callback,
  }) {

    Swal.fire({
      html: msg.body,
      showConfirmButton: false
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
        success: (e) => {
          console.log(e);
        },
        complete: () => {
          console.log('完成请求');
        }
      })

      console.log(o);
    });
  },

  // 获取支付选项
  get_pay_select_html: function (e) {
    e.action = 'capalot_get_pay_select_html';

    ca.ajax({
      data: e,
      success: ({ status, msg, data }) => {
        status == 1 ? ca.popup({ msg: data }) : console.log('2222', e, t);
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
          $('.post-wrap').append(response.html);
        }
      });
    }
    );
  }

}

document.addEventListener('DOMContentLoaded', () => {
  ca.init();
})

