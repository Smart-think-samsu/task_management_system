$(function () {
    bsCustomFileInput.init();
});

$(function () {
    $("#chalan_list_table").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "lengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "All"]],
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});

$(document).ready(function() {
    $(document).on('submit', '[id^="chalanForm"]', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var formId = $(this).attr('id');
        var modalId = '#chalan' + $(this).data('chalan-id') + 'editModal';
        var errorMessages = $(modalId).find('#errorMessages');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(document.getElementById(formId))[0].reset();
                            $(modalId).modal('hide');
                            window.location.reload();
                        }
                    });
                } else {
                    errorMessages.removeClass('d-none').html(data.message);
                }
            },
            error: function(response) {
                errorMessages.removeClass('d-none').empty();
                if (response.responseJSON && response.responseJSON.errors) {
                    $.each(response.responseJSON.errors, function(key, value) {
                        errorMessages.append('<p>' + value + '</p>');
                    });
                } else if (response.responseJSON && response.responseJSON.message) {
                    errorMessages.append('<p>' + response.responseJSON.message + '</p>');
                } else {
                    errorMessages.append('<p>An unexpected error occurred. Please try again.</p>');
                }
            }
        });
    });
});
