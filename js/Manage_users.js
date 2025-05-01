$(document).ready(function() {
  const alertUser = $('.alert_user');
  const userForm = $('#userForm');

  function showAlert(message, type = 'success', duration = 5000) {
      alertUser.html(`<div class="alert alert-${type}">${message}</div>`).fadeIn(300);
      setTimeout(() => alertUser.fadeOut(500), duration);
  }

  function resetForm() {
      userForm[0].reset();
      $('#user_id').val('');
      $('input[name=gender][value="Male"]').prop('checked', true);
  }

  function validateFormData(data) {
      if (!data.name.match(/^[a-zA-Z\s]{2,50}$/)) {
          showAlert('Name must be 2-50 characters (letters and spaces only)', 'danger');
          return false;
      }
      if (!data.number.match(/^\d{1,10}$/)) {
          showAlert('Serial number must be 1-10 digits', 'danger');
          return false;
      }
      if (data.email && !data.email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
          showAlert('Invalid email format', 'danger');
          return false;
      }
      return true;
  }

  function handleUserRequest(action, data) {
      return $.ajax({
          url: "../Manage_users_conf.php", // Adjusted URL to point to the root directory
          method: 'POST',
          dataType: 'json',
          data: data,
          timeout: 10000
      });
  }

  $(document).on('click', '#user_add', function() {
      const formData = {
          Add: 1,
          user_id: $('#user_id').val(),
          name: $('#name').val().trim(),
          number: $('#number').val().trim(),
          email: $('#email').val().trim(),
          gender: $("input[name='gender']:checked").val()
      };

      if (!validateFormData(formData)) return;

      handleUserRequest('Add', formData)
          .then(response => {
              if (response.success) {
                  showAlert('User added successfully!');
                  resetForm();
                  location.reload(); // Reload the page to update the table
              } else {
                  showAlert(response.message || 'Failed to add user', 'danger');
              }
          })
          .catch(error => {
              console.error("Add error:", error);
              showAlert('Connection error occurred', 'danger');
          });
  });

  $(document).on('click', '#user_upd', function() {
      const userId = $('#user_id').val();
      if (!userId) {
          showAlert('No user selected for update', 'warning');
          return;
      }

      const formData = {
          Update: 1,
          user_id: userId,
          name: $('#name').val().trim(),
          number: $('#number').val().trim(),
          email: $('#email').val().trim(),
          gender: $("input[name='gender']:checked").val()
      };

      if (!validateFormData(formData)) return;

      handleUserRequest('Update', formData)
          .then(response => {
              if (response.success) {
                  showAlert('User updated successfully!');
                  resetForm();
                  location.reload(); // Reload the page to update the table
              } else {
                  showAlert(response.message || 'Failed to update user', 'danger');
              }
          })
          .catch(error => {
              console.error("Update error:", error);
              showAlert('Connection error occurred', 'danger');
          });
  });

  $(document).on('click', '#user_rmo', function() {
      const userId = $('#user_id').val();
      if (!userId) {
          showAlert('No user selected for deletion', 'warning');
          return;
      }

      bootbox.confirm({
          message: "Are you sure you want to delete this user?",
          buttons: {
              confirm: { label: 'Yes', className: 'btn-danger' },
              cancel: { label: 'No', className: 'btn-secondary' }
          },
          callback: function(result) {
              if (result) {
                  handleUserRequest('Delete', {
                      delete: 1,
                      user_id: userId
                  })
                      .then(response => {
                          if (response.success) {
                              showAlert('User deleted successfully!');
                              resetForm();
                              location.reload(); // Reload the page to update the table
                          } else {
                              showAlert(response.message || 'Failed to delete user', 'danger');
                          }
                      })
                      .catch(error => {
                          console.error("Delete error:", error);
                          showAlert('Connection error occurred', 'danger');
                      });
              }
          }
      });
  });

  $(document).on('click', '.select_btn', function() {
      const cardUid = $(this).attr("id");
      const row = $(this).closest('tr');

      $.ajax({
          url: "../Manage_users_conf.php", // Adjusted URL to point to the root directory
          method: 'POST',
          dataType: 'json',
          data: { select: 1, card_uid: cardUid },
          success: function(response) {
              if (response.success) {
                  row.css('background', '#70c276').siblings().css('background', '');
                  $('#user_id').val(response.data.id);
                  $('#name').val(response.data.username);
                  $('#number').val(response.data.serialnumber);
                  $('#email').val(response.data.email);
                  $(`input[name=gender][value="${response.data.gender || 'Male'}"]`).prop('checked', true);
                  showAlert('User selected successfully!');
              } else {
                  showAlert(response.message || 'Failed to select user', 'danger');
              }
          },
          error: function(xhr, status, error) {
              console.error("Select error:", error);
              showAlert('Connection error occurred', 'danger');
          }
      });
  });
});