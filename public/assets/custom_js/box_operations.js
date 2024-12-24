$(function () {
    $("#box_stock_list_table").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "lengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "All"]],
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});

$('.update-status').on('click', function() {
    var boxOperationId = $(this).data('box-operation-id');
    var status = $(this).val();

    Swal.fire({
        title: "Are you sure?",
        text: "Are you sure you want to Delivery accept this box. You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
    }).then((result) => {
        if (result.isConfirmed) {
            $('#loadingOverlayModal').show();
            var data = {
                status: status
            };

            $.ajax({
                url: '/box_operations/update_status/' + boxOperationId,
                type: "PUT",
                dataType: "JSON",
                data: JSON.stringify(data),
                contentType: "application/json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#loadingOverlayModal').hide();
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

$('.view-licenses').on('click', function() {
    var boxNo = $(this).data('box-no');
    var status = $(this).data('status');
    if(status != 3){
        Swal.fire({
            title: "Error",
            text: "The box has not yet been delivered",
            icon: "error",
            showCancelButton: true,
            cancelButtonText: "Cancel",
            showConfirmButton: true,
            closeOnConfirm: true,
            allowEscapeKey: false
        });

        return false;
    }
    get_all_license(boxNo);
});

function get_all_license(box_no) {
    $('#loadingOverlayModal').show();

    $.ajax({
        url: '/dl_stocks/box_wise_license',
        type: 'GET',
        data: { box_no: box_no },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
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
                let statusContent;

                if (license.status === 1) {
                    statusContent = '<button class="btn btn-primary btn-sm" onclick="bookingAccess(\'' + license.license_no + '\', \'' + box_no + '\')">Accept For booking</button>';
                } else {
                    const statusInfo = statusMapping[license.status] || { label: 'Unknown', class: 'badge-secondary', icon: 'fas fa-question' };
                    statusContent = '<span class="badge ' + statusInfo.class + '">' +
                        '<i class="' + statusInfo.icon + '"></i> ' + statusInfo.label +
                        '</span>';
                }

                content += '<div style="display: flex; justify-content: space-between; align-items: center;">' +
                    '<p>' + license.license_no + '</p>' +
                    '<div class="btn-group btn-group-sm">' + statusContent + '</div></div>';
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

function bookingAccess(license_no, box_no) {
    $('#loadingOverlayModal').show();
    $.ajax({
        url: '/dl_stocks/update_status/' + license_no,
        type: "PUT",
        data: JSON.stringify({ license_no: license_no }),
        contentType: "application/json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#loadingOverlayModal').hide();
            Swal.fire({
                title: "Thank You",
                text: response.message,
                icon: response.status,
                showCancelButton: true,
                cancelButtonText: "Cancel",
                showConfirmButton: true,
                closeOnConfirm: true,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    get_all_license(box_no);
                }
            });
        },
        error: function(xhr) {
            $('#loadingOverlayModal').hide();
            Swal.fire({
                title: "Error",
                text: "An error occurred while updating the status.",
                icon: "error",
                showCancelButton: true,
                cancelButtonText: "Cancel",
                showConfirmButton: true,
                closeOnConfirm: true,
                allowEscapeKey: false
            });
        }
    });
}

document.getElementById('closeModalButton').addEventListener('click', function() {
    $('#licenseViewModal').modal('hide');
    location.reload();
});
