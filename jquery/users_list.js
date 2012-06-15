$.users_list = function() {
  var $users_form = $('#users-form'),
      $list = $('table.actions-list'),
      $invite_users = $('#invite-users');
    
  var $obj = {
    init: function() {
      $('.change-status, .invitation-resend, .invitation-delete', $list).live('click', $obj.change_status);
      
      $invite_users
        .click(function(){$(this).addClass('loading-16')})
        .bind('got_template', function(e,t) {$(t).invite_users(); $invite_users.removeClass('loading-16')})
        .link_form_rich({width: 500}, {
          redirect: function(){ location.reload() },
          after_success: function(t) { $(document).trigger('decorate_invite_users'); }
        });
      $(':input', $users_form).change($obj.handle_users_list_change);
      $users_form.submit($obj.submit_users_form);
    },
    change_status: function(e) {
      e.preventDefault();
      var $link = $(this);
      if ($link.parents('.loading-16').length) return false;
      $.ajax({
        url: $link.attr('href'),
        beforeSend: function() { $link.parents('td:first').addClass('loading-16')},
        success: function(r) {
          $link.parents('tr:first').html($(r).html());
        }
      });
    },
    submit_users_form: function() {
      if ($list.is('.sending-data')) return false;
      var $users_data = [];
      $('tbody tr.changed', $list).each(function(){
        var $row = $(this), $row_data = {};
        $(':input', $row).each(function(){ var $input = $(this); $row_data[$input.attr('name')] = $input.val(); });
        $users_data.push($row_data);
      });
      $.ajax({
        url: $users_form.attr('action'),
        type: $users_form.attr('method'),
        data: {users: $users_data},
        beforeSend: function() { $list.addClass('sending-data');},
        success: function() { location.reload(); }
      });
      return false; 
    },
    handle_users_list_change: function() {
      var $input = $(this), $row = $input.parents('tr:first');
      $row.addClass('changed');
      $('#userslist-form-actions', $users_form).show();
    }
  };
  
  $obj.init();
  return this;
};

$(function() {$.users_list();});
