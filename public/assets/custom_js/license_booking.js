document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const licenseNo = urlParams.get('license_no');

    if (licenseNo) {
        const licenseNoInput = document.getElementById('license_no');
        licenseNoInput.value = licenseNo;
        licenseNoInput.readOnly = true;
        urlParams.delete('license_no');
        const newUrl = window.location.origin + window.location.pathname + urlParams.toString();
        history.replaceState(null, '', newUrl);
    }
});

$(document).ready(function() {
    let countdown;
    let ajaxRequest = null;
    let isRequestInProgress = false;

    $('#license_no').on('keyup', function() {
        var inputField = $(this).val();
        var message = $('#errorMessages');
        var regex = /^[A-Z]{2}[0-9]{8}[C,H,L,M,T,X]{1}[C,H,L,M,T,X,0]{4}[0-9]{1}$/;

        if (regex.test(inputField)) {
            message.addClass('d-none');
        } else {
            message.text('Invalid license number..!!');
            message.removeClass('d-none');
            message.addClass('text-danger');
            message.css('color', 'red');
        }
    });

    function showDynamicModal(data) {
        const { insurance_no, pending_date, barcode_image, barcode, name, road, thana, district, post_code, division, mobile_no, driving_licence_no } = data.response_data;

        const printButton = insurance_no
            ? '<button type="button" disabled class="btn btn-block btn-primary">Print</button>'
            : '<button type="button" class="btn btn-block btn-primary" id="generateInsuranceId">Print</button>';

        const reprintButton = insurance_no
            ? '<button type="button" class="btn btn-block btn-primary" onclick="reprint()" id="enableReprint">Reprint</button>'
            : '<button type="button" class="btn btn-block btn-primary" onclick="reprint()" id="enableReprint" disabled>Reprint</button>';

        const submitButton = '<button type="button" class="btn btn-block btn-primary" disabled id="barcodeSubmitForm">Submit</button>';

        const modalContent = `
            <div class="modal fade" id="barcodeModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content custom-modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Barcode Information</h4>
                        </div>
                        <div class="modal-body border">
                            <div id="loadingOverlayModal" class="loading-overlay" style="display: none;">
                                <div class="text-center">
                                    <div class="spinner-border" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <div class="text-bold pt-2">Loading...</div>
                                </div>
                            </div>
                            <form id="barcodeForm" class="pl-5 pr-5">
                                <div class="d-flex justify-content-between">
                                    <div id="insuranceInfo">
                                        <span style="font-family: 'Kalpurush', Arial, sans-serif !important;" id="pringRegBima">রেজিঃ বীমা: </span> <span id="regBima">${insurance_no || ''}</span><br>
                                        <span style="font-family: 'Kalpurush', Arial, sans-serif !important;" id="pringIssueDate">ইস্যু তারিখ: </span><span id="pendingDate">${pending_date || ''}</span>
                                        <input type="hidden" name="insurance_no" id="insurance_no" value="${insurance_no}">
                                        <input type="hidden" name="barcode_no" id="barcode_no" value="${barcode}">
                                    </div>
                                    <input type="text" class="form-control" name="barcodeScan" id="barcodeScan">
                                    <div id="printInfo">
                                        <div id="barcodeImage">
                                            ${barcode_image}
                                            <p style="text-align: center">${barcode}</p>
                                        </div>
                                        <p id="detailsOfAuthor" style="font-family: 'Kalpurush', Arial, sans-serif !important"><span >প্রাপকঃ </span><br>${name}<br>${road}<br>${thana}, ${district}, ${post_code}<br>${division}<br>Phone: ${mobile_no}</p>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="errorMessage" class="text-danger text-center"></div>
                        <div class="modal-footer d-flex justify-content-between w-100">
                            <div class="d-flex flex-column">
                                <input type="hidden" name="driving_licence_no" id="driving_licence_no" value="${driving_licence_no}">
                                ${printButton}
                                <button type="button" class="btn btn-block btn-primary close-btn">Cancel</button>
                            </div>
                            <div class="d-flex flex-column">
                                ${reprintButton}
                                ${submitButton}
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

        $('body').append(modalContent);

        const barcodeModal = $('#barcodeModal');
        const barcodeScan = $('#barcodeScan');
        const barcodeSubmitForm = $('#barcodeSubmitForm');
        let isRequestInProgress = false;
        let countdown;

        barcodeModal.modal('show');

        $('#generateInsuranceId').on('click', function() {
            const drivingLicenceNo = $('#driving_licence_no').val();
            const insuranceNo = $('#insurance_no').val();
            const barcodeNo = $('#barcode_no').val();

            $('#loadingOverlayModal').show();

            $.ajax({
                url: `/dl_bookings/generate_insurance_id/${drivingLicenceNo}`,
                type: 'PUT',
                data: {
                    insurance_no: insuranceNo,
                    barcode_no: barcodeNo
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#loadingOverlayModal').hide();

                    const updatedInsuranceNo = response.license_data.insurance_no || '';
                    const issueDate = response.license_data.pending_date || '';

                    $('#regBima').text(updatedInsuranceNo);
                    $('#pendingDate').text(issueDate);
                    $('#insurance_no').val(updatedInsuranceNo);

                    const responseTimes = {
                        insurance_id_generate_time: response.license_data.responseTime
                    };
                    calculateResponseTimes(responseTimes);

                    $('#responseTimes ul').append(`<li>Insurance Id Generate Time: <span id="insuranceGnTime">${response.license_data.responseTime ?? (0.00)} seconds</span></li>`);

                    $('#generateInsuranceId').prop('disabled', true);
                    $('#enableReprint').prop('disabled', false);

                    window.print();

                },
                error: function() {
                    $('#loadingOverlayModal').hide();
                    alert('Error submitting form. Please try again.');
                }
            });
        });

        barcodeScan.on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                barcodeSubmitForm.click();
            }
        });

        barcodeModal.on('shown.bs.modal', function() {
            barcodeScan.focus();
            const focusInterval = setInterval(() => {
                if (!barcodeScan.is(':focus')) {
                    barcodeScan.focus();
                }
            }, 100);

            barcodeModal.on('hidden.bs.modal', function() {
                clearInterval(focusInterval);
            });
        });

        barcodeSubmitForm.on('click', function(e) {
            e.preventDefault();
            const barcode = barcodeScan.val();

            if (!barcode) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please scan barcode',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (isRequestInProgress) {
                clearInterval(countdown);
                countdown = null;
                submitBarcode(barcode);
                return;
            }

            barcodeScan.prop('disabled', true);
            barcodeSubmitForm.prop('disabled', false);
            let timer = 5;
            barcodeSubmitForm.text(`Submitting in ${timer} seconds`);
            isRequestInProgress = true;

            countdown = setInterval(function() {
                timer--;
                barcodeSubmitForm.text(`Submitting in ${timer} seconds`);
                if (timer <= 0) {
                    clearInterval(countdown);
                    countdown = null;
                    submitBarcode(barcode);
                }
            }, 1000);
        });

        function submitBarcode(barcode) {
            barcodeSubmitForm.text('Submitting...');
            $('#loadingOverlayModal').show();
            $.ajax({
                url: `/dl_bookings/submit/${barcode}`,
                type: 'PUT',
                data: { barcode: barcode },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    const responseTimes = {
                        ws_login_time: response.ws_login_time,
                        booking_req_time: response.bookingreq_time,
                        database_update_time: response.database_update_time,
                        brta_status_push_time: response.brta_status_push_time,
                        brta_dls_push_time: response.brta_dls_push_time
                    };

                    calculateResponseTimes(responseTimes);

                    $('#responseTimes ul').append(`<li>Ws Login Time: <span id="ws_login_time">${response.ws_login_time ?? (0.00)} seconds</span></li>`);
                    $('#responseTimes ul').append(`<li>Booking Request Time: <span id="bookingReqTime">${response.bookingreq_time ?? (0.00)} seconds</span></li>`);
                    $('#responseTimes ul').append(`<li>Booking Database Update Time: <span id="databaseUpdateTime">${response.database_update_time ?? (0.00)} seconds</span></li>`);
                    $('#responseTimes ul').append(`<li>BRTA Status Push Time: <span id="brtaStatusPushTime">${response.brta_status_push_time ?? (0.00) } seconds</span></li>`);
                    $('#responseTimes ul').append(`<li>BRTA DLS Push Time: <span id="brtaStatusPushTime">${response.brta_dls_push_time ?? (0.00)} seconds</span></li>`);
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#license_no').focus();
                            $('#license_no').val('');
                            $('#license_no').prop('readOnly', false);
                        });

                        barcodeModal.modal('hide');
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }

                    barcodeSubmitForm.prop('disabled', false).text('Submit');
                    barcodeScan.val('');
                    isRequestInProgress = false;
                },
                error: function(response) {
                    $('#loadingOverlayModal').hide();
                    const errorMessage = response.responseJSON?.message || 'An unexpected error occurred.';
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });

                    barcodeSubmitForm.prop('disabled', false).text('Submit');
                    barcodeScan.val('');
                    isRequestInProgress = false;
                }
            });
        }

        $('.close-btn').on('click', function() {
            clearInterval(countdown);
            $('#barcodeModal').modal('hide');
            barcodeSubmitForm.prop('disabled', false).text('Submit');
            barcodeScan.val('');
            $('.main-content').removeClass('disabled-content');
        });

        barcodeModal.on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    $('#licenseBookingForm').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        var errorMessages = $('#errorMessages');
        var actionUrl = $(this).attr('action');
        $('#responseTimes ul').empty();
        $('#totalResTime').empty();
        $('#loadingOverlay').show();
        $('.main-content').addClass('disabled-content');

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                $('#loadingOverlay').hide();
                $('.main-content').removeClass('disabled-content'); // Add this line to remove class on success as well
                if (data.status === 'success') {

                    const responseTimes = {
                        brta_api_response_time: data.response_data.dlBrtaApiResponseTime,
                        barcode_api_response_time: data.response_data.dlBarcodeResponseTime,
                        db_booking_response_time: data.response_data.dbBookingResponseTime,
                        barcode_generate_response_time: data.response_data.responseTimeBarcode
                    };
                    calculateResponseTimes(responseTimes);

                    $('#responseTimes ul').append(`<li>BRTA API Response Time: <span id="brtaApiResponseTime">${data.response_data.dlBrtaApiResponseTime ?? '0.00'} seconds</span></li>`);
                    $('#responseTimes ul').append(`<li>Barcode API Response Time: <span id="barcodeApiResponseTime">${data.response_data.dlBarcodeResponseTime ?? '0.00'} seconds</span></li>`);
                    $('#responseTimes ul').append(`<li>Database Booking Response Time: <span id="dbBookingResponseTime">${data.response_data.dbBookingResponseTime ?? '0.00'} seconds</span></li>`);
                    $('#responseTimes ul').append(`<li>Barcode Generate Response Time: <span id="responseTimeBarcode">${data.response_data.responseTimeBarcode ?? '0.00'} seconds</span></li>`);
                    $('#responseTimes').show();

                    showDynamicModal(data);
                } else if (data.status === 'input_required') {
                    const options = data.check_assign_box.map(boxNo => `<option value="${boxNo}">Box No: ${boxNo}</option>`).join('');
                    Swal.fire({
                        title: "Alert!",
                        html: `
                            <p>${data.message}</p>
                            <select id="assignBoxNoSelect" class="form-control select2bs4">
                                ${options}
                            </select>`,
                        confirmButtonText: 'Submit'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var selectedBoxNo = $('#assignBoxNoSelect').val();
                            submitBoxNo(data.license_no, data.actual_box_no, selectedBoxNo);
                        }
                    });
                } else {
                    errorMessages.empty().removeClass('d-none').html(data.message);
                    $('.main-content').removeClass('disabled-content');
                }
            },
            error: function(response) {
                $('#loadingOverlay').hide();
                errorMessages.empty().removeClass('d-none');
                if (response.responseJSON && response.responseJSON.errors) {
                    $.each(response.responseJSON.errors, function(key, value) {
                        errorMessages.append('<li>' + value + '</li>');
                    });
                } else if (response.responseJSON && response.responseJSON.message) {
                    errorMessages.append('<li>' + response.responseJSON.message + '</li>');
                } else {
                    errorMessages.append('<li>An unexpected error occurred. Please try again.</li>');
                }
                $('.main-content').removeClass('disabled-content');
            }
        });
    });

    function submitBoxNo(license_no, actual_box_no, box_no) {
        $.ajax({
            url: '/dl_bookings/mismatch_license',
            type: 'POST',
            data: {
                license_no: license_no,
                actual_box_no: actual_box_no,
                box_no: box_no
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            },
            error: function(response) {
                let errorMessage = response.responseJSON && response.responseJSON.message ? response.responseJSON.message : 'An unexpected error occurred.';
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
});

function calculateResponseTimes(responseTimes) {
    let totalResponseTime = 0;

    for (let key in responseTimes) {
        if (responseTimes.hasOwnProperty(key)) {
            totalResponseTime += parseFloat(responseTimes[key]) || 0;
        }
    }

    let totalResTimeElement = $('#totalResTime');
    let previousTotalTime = 0;

    if (totalResTimeElement.length && totalResTimeElement.text()) {
        let previousText = totalResTimeElement.text();
        previousTotalTime = parseFloat(previousText.replace('Total Time: ', '').replace(' seconds', '')) || 0;
    }

    totalResponseTime += previousTotalTime;

    totalResTimeElement.text(`Total Time: ${totalResponseTime.toFixed(2)} seconds`);
}

function reprint() {
    window.print();
}
