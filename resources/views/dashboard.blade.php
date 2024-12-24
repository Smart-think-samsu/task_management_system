@extends('layouts.app')
@section('title')
    Home
@endsection
@php
    $page = "Dashboard";
@endphp

@section('main_content')
    <section class="content">
        <div class="container-fluid">
                <div class="row">
                    <div class="info-box">
                        <div class="col-sm-3 col-6">
                            <div class="description-block border-right">
                                <h5 class="description-header text-success">50</h5>
                                <span class="description-text">Total user</span>
                            </div>
                        </div>

                        <div class="col-sm-3 col-6">
                            <div class="description-block border-right">
                                <h5 class="description-header text-success">50</h5>
                                <span class="description-text">Total Task</span>
                            </div>

                        </div>

                        <div class="col-sm-3 col-6">
                            <div class="description-block border-right">
                                <h5 class="description-header text-warning">50</h5>
                                <span class="description-text">Total Pending task</span>
                            </div>

                        </div>

                        <div class="col-sm-3 col-6">
                            <div class="description-block">
                                <h5 class="description-header text-success">50</h5>
                                <span class="description-text">Total Complete Task</span>
                            </div>
                        </div>
                    </div>
                </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection
@section('add_js')
    <script>
        $(function () {
            $.ajax({
                url: '{{ route('dashboard.graph_data') }}',
                method: 'GET',
                success: function (jsonData) {

                    $('#totalReceived').text(jsonData.totalChalanUploadedlicense);
                    $('#totalReceivedToday').text(jsonData.totalChalanUploadedlicenseToday);
                    $('#thisWeekReceived').text(jsonData.totalChalanUploadedlicenseThisWeek);
                    $('#lastWeekReceived').text(jsonData.totalChalanUploadedlicenseLastWeek);
                    $('#thisMonthReceived').text(jsonData.totalChalanUploadedlicenseThisMonth);

                    $('#totalProcessed').text(jsonData.totalBookingLicense);
                    $('#todayProcessed').text(jsonData.totalBookingLicenseToday);
                    $('#thisWeekProcessed').text(jsonData.totalBookingLicenseThisWeek);
                    $('#lastWeekProcessed').text(jsonData.totalBookingLicenseLastWeek);
                    $('#thisMonthProcessed').text(jsonData.totalBookingLicenseThisMonth);

                    $('#totalDelivered').text(jsonData.totalDeliveredLicense);
                    $('#totalUnDelivered').text(jsonData.totalUndeliveredLicense);
                    $('#totalDnf').text(jsonData.totalLicenseNotFound);
                    $('#totalReturned').text(jsonData.totalLicenseReturned);

                    var donutDataReceived = {
                        labels: [
                            'Total',
                            'Today',
                            'This Week',
                            'Last Week',
                            'This Month'
                        ],
                        datasets: [{
                            data: [
                                jsonData.totalChalanUploadedlicense,
                                jsonData.totalChalanUploadedlicenseToday,
                                jsonData.totalChalanUploadedlicenseThisWeek,
                                jsonData.totalChalanUploadedlicenseLastWeek,
                                jsonData.totalChalanUploadedlicenseThisMonth
                            ],
                            backgroundColor: [
                                '#f56954',
                                '#00a65a',
                                '#f39c12',
                                '#00c0ef',
                                '#3c8dbc'
                            ]
                        }]
                    };

                    var donutDataProcessed = {
                        labels: [
                            'Total',
                            'Today',
                            'This Week',
                            'Last Week',
                            'This Month'
                        ],
                        datasets: [{
                            data: [
                                jsonData.totalBookingLicense,
                                jsonData.totalBookingLicenseToday,
                                jsonData.totalBookingLicenseThisWeek,
                                jsonData.totalBookingLicenseLastWeek,
                                jsonData.totalBookingLicenseThisMonth
                            ],
                            backgroundColor: [
                                '#f56954',
                                '#00a65a',
                                '#f39c12',
                                '#00c0ef',
                                '#3c8dbc'
                            ]
                        }]
                    };

                    var donutDataStatus = {
                        labels: [
                            'Delivered',
                            'Undelivered',
                            'Data Not Found',
                            'Returned'
                        ],
                        datasets: [{
                            data: [
                                jsonData.totalDeliveredLicense,
                                jsonData.totalUndeliveredLicense,
                                jsonData.totalLicenseNotFound,
                                jsonData.totalLicenseReturned,
                            ],
                            backgroundColor: [
                                '#f56954',
                                '#00a65a',
                                '#f39c12',
                                '#00c0ef'
                            ]
                        }]
                    };

                    var donutOptions = {
                        maintainAspectRatio: false,
                        responsive: true,
                        cutoutPercentage: 50,
                        legend: {
                            display: false
                        }
                    };

                    // Initialize the charts with the updated data
                    var donutChartReceivedCanvas = $('#donutChartReceived').get(0).getContext('2d');
                    new Chart(donutChartReceivedCanvas, {
                        type: 'doughnut',
                        data: donutDataReceived,
                        options: donutOptions
                    });

                    var donutChartProcessedCanvas = $('#donutChartProcessed').get(0).getContext('2d');
                    new Chart(donutChartProcessedCanvas, {
                        type: 'doughnut',
                        data: donutDataProcessed,
                        options: donutOptions
                    });

                    var donutChartStatusCanvas = $('#donutChartStatus').get(0).getContext('2d');
                    new Chart(donutChartStatusCanvas, {
                        type: 'doughnut',
                        data: donutDataStatus,
                        options: donutOptions
                    });

                },
                error: function (error) {
                    console.error('Error fetching data: ', error);
                }
            });
        });
    </script>
@endsection
