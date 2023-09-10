let currentPage = 1;

document.addEventListener('DOMContentLoaded', () => {
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
        if(currentPage >= response.max) {
          $('#load-more').hide();
          $('#no-more-button').show();
        }
        $('.post-wrap').append(response.html);
      }
    });
  }
  )
})

