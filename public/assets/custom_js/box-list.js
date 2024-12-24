$(function () {
    $("#user_wise_box_list_table").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "lengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "All"]],
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});

$(document).ready(function() {
    // View license details
    $('.view-licenses').on('click', function() {
        var boxNo = $(this).data('box-no');
        get_all_license(boxNo);
    });
});

$('.update-status').on('click', function() {
    var boxOperationId = $(this).data('box-operation-id');
    var status = $(this).val();

    var confirmationTexts = {
        1: "Are you sure you want to accept this box? You won't be able to revert this!",
        2: "Are you sure you want to reject this box? You won't be able to revert this!",
        3: "Are you sure you want to deliver this box? You won't be able to revert this!"
    };

    var confirmationText = confirmationTexts[status] || "Are you sure you want to proceed? You won't be able to revert this!";
    var showReasonInput = (status == 2);

    // Prepare rejection reason input field if status requires it
    var rejectionReasonHtml = `<input type="text" id="rejection-reason" class="form-control" style="margin-top: 10px;" placeholder="Enter the reason for rejection">`;

    // Use the `html` option in SweetAlert to add additional input fields if needed
    var htmlContent = showReasonInput ? rejectionReasonHtml : '';

    Swal.fire({
        title: "Are you sure?",
        text: confirmationText,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        html: htmlContent,
        preConfirm: () => {
            if (showReasonInput) {
                var reason = $('#rejection-reason').val();
                if (!reason) {
                    Swal.showValidationMessage('Reason is required for rejection!');
                }
                return { status: status, reason: reason };
            } else {
                return { status: status };
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/box_operations/update_status/' + boxOperationId,
                type: "PUT",
                dataType: "JSON",
                data: JSON.stringify(result.value),
                contentType: "application/json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        title: "Thank You",
                        text: response.message,
                        icon: response.status,
                        showCancelButton: true,
                        cancelButtonText: "Cancel",
                        showConfirmButton: true,
                        closeOnConfirm: true,
                        allowEscapeKey: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
});

function get_all_license(box_no) {
    $('#loadingOverlayModal').show();

    $.ajax({
        url: '/dl_stocks/box_wise_license',
        type: 'GET',
        data: { box_no: box_no },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {

            const statusMapping = {
                0: { label: 'Initial', class: 'badge-secondary', icon: 'fas fa-hourglass-start' },
                1: { label: 'Request for Booking', class: 'badge-info', icon: 'fas fa-calendar-check' },
                2: { label: 'Booked', class: 'badge-success', icon: 'fas fa-check' },
                3: { label: 'Request Accept for Booking', class: 'badge-warning', icon: 'fas fa-thumbs-up' },
                4: { label: 'DNF', class: 'badge-danger', icon: 'fas fa-times' }
            };

            let content = '';

            content += '<p>Total licenses successfully Booked: ' + response.status_zero_count + '</p>';

            response.licenses.forEach(function(license) {
                const statusInfo = statusMapping[license.status] || { label: 'Unknown', class: 'badge-secondary', icon: 'fas fa-question' };
                content += '<div style="display: flex; justify-content: space-between; align-items: center;">' +
                    '<p>' + license.license_no + '</p>' +
                    '<div class="btn-group btn-group-sm">' +
                    '<span class="badge ' + statusInfo.class + '">' +
                    '<i class="' + statusInfo.icon + '"></i> ' + statusInfo.label +
                    '</span></div></div>';
            });

            $('#license-details').html(content);
            $('#loadingOverlayModal').hide();
        },
        error: function(xhr) {
            $('#license-details').html('<p>An error occurred while fetching licenses.</p>');
            $('#loadingOverlayModal').hide();
        }
    });
}
