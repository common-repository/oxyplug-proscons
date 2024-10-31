jQuery(document).ready(function ($) {
  // Add new con/pro
  $('#oxyplug-proscons-add-pro, #oxyplug-proscons-add-con').on('click', function () {
    let name;
    let placeholder;
    let class_name;
    let target;

    if ($(this).attr('id') == 'oxyplug-proscons-add-con') {
      name = 'oxyplug_proscons[cons][]';
      placeholder = oxyplug_proscons_trans.enter_negative_point;
      class_name = 'oxyplug-proscons-remove-con';
      target = 'oxyplug-proscons-cons';
    } else {
      name = 'oxyplug_proscons[pros][]';
      placeholder = oxyplug_proscons_trans.enter_positive_point;
      class_name = 'oxyplug-proscons-remove-pro';
      target = 'oxyplug-proscons-pros';
    }
    const new_item =
      `<div>
        <input type="text" name="${name}" value="" placeholder="${placeholder}">
        <button class="button button-danger ${class_name}" type="button">
          <i class="dashicons dashicons-trash"></i>
        </button>
      </div>`;
    $(`#${target} > div:last-of-type`).after(new_item);
  });

  // Remove a con/pro
  $(document).on('click', '.oxyplug-proscons-remove-pro, .oxyplug-proscons-remove-con', function () {
    const confirmed = confirm(oxyplug_proscons_trans.are_you_sure);
    if (confirmed) {
      $(this).parent().remove();
    }
  });

  // Save metabox data
  $('#oxyplug-proscons-save').on('click', function () {
    let data = $('#oxyplug-proscons :input').serialize();
    data = data + "&action=oxyplug_proscons_save";

    $.ajax({
      url: ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: data,
      success(response) {
        alert(response.data.message);
      },
      error(response) {
        alert(response.responseJSON.data.message);
      },
    });

  });

  // Save settings
  $('#oxyplug-proscons-settings-save').on('click', function (e) {
    e.preventDefault();
    const data = new FormData($('#oxyplug-proscons-settings')[0]);
    $.ajax({
      url: ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: data,
      processData: false,
      contentType: false,
      success(response) {
        alert(response.data.message);
      },
      error(response) {
        console.log(response);
      }
    });
  });
});