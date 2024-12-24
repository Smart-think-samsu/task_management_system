<?php

namespace App\Http\Controllers;

use App\Models\BoxOperation;
use App\Models\Chalan;
use App\Models\DlStock;
use App\Models\LicenseBooking;
use App\Models\LicenseNotFound;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }
    private function getAdminData($userId, $today)
    {
        return [

//            'totalLicenseUpload' => DlStock::count(),

        ];
    }
    private function getInspactorData($userId, $today)
    {
        return [
            'totalLicenseUpload' => DlStock::count(),
            'totalBoxStock' => DlStock::distinct('box_no')->count('box_no'),
            'totalBoxStockToday' => DlStock::whereDate('created_at', $today)
                ->distinct('box_no')
                ->count('box_no'),
            'totalChalan' => Chalan::count(),
            'totalChalanToday' => Chalan::whereDate('created_at', $today)->count(),
            'totalChalanUploaded' => Chalan::where('receiver_id', $userId)->count(),
            'totalChalanUploadedToday' => Chalan::where('receiver_id', $userId)
                ->whereDate('created_at', $today)
                ->count(),
            'totalBoxAssign' => BoxOperation::where('assign_by_user_id', $userId)->count(),
            'totalBoxAssignToday' => BoxOperation::where('assign_by_user_id', $userId)
                ->whereDate('created_at', $today)
                ->count(),
        ];
    }
    private function getOperatorData($userId, $today)
    {
        return [
//            'totalIssuedLicense' => $this->getLicenseCount($userId, 0),
//            'totalIssuedLicenseToday' => $this->getLicenseCount($userId, 0, $today),
//
//            'totalBookedLicense' => $this->getLicenseCount($userId, 2),
//            'totalBookedLicenseToday' => $this->getLicenseCount($userId, 2, $today),
//
//            'totalPendingLicense' => $this->getLicenseCount($userId, 1),
//            'totalPendingLicenseToday' => $this->getLicenseCount($userId, 1, $today),
//
//            'totalDeliveredLicense' => $this->getLicenseCount($userId, 3),
//            'totalDeliveredLicenseToday' => $this->getLicenseCount($userId, 3, $today),
        ];
    }
    private function getUploaderData($userId, $today)
    {
        return [
            'totalDlUpload' => DlStock::where('user_id', $userId)->count(),
            'totalBoxUpload' => DlStock::where('user_id', $userId)->distinct('box_no')->count('box_no'),
            'dailyTotalDlUpload' => DlStock::where('user_id', $userId)->whereDate('created_at', $today)->count(),
            'dailyTotalBoxUpload' => DlStock::where('user_id', $userId)->whereDate('created_at', $today)->distinct('box_no')->count('box_no'),
        ];
    }
    private function getbiliOperatorData($userId, $today)
    {
        return [
            'totalOrder' => Order::where('user_id', $userId)->count(),
            'totalBooking' => Order::where('user_id', $userId)->where('status','1')->count(),
            'dailyTotalBooking' => Order::where('user_id', $userId)->where('status','1')->whereDate('booking_date', $today)->count(),
            'TotalDelivered' => Order::where('user_id', $userId)->where('status','3')->count(),
        ];
    }
    private function getLicenseCount($userId, $status, $date = null)
    {
        $query = LicenseBooking::where('user_id', $userId)->where('booking_status', $status);

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        return $query->count();
    }
    public function graphData()
    {
        $data = [];
        $data['totalChalanUploadedlicense'] = chalan::where('status', 1)->sum('total_dl_match');
        $data['totalChalanUploadedlicenseToday'] = chalan::where('status', 1)->whereDate('chalan_upload_at', Carbon::today())->sum('total_dl_match');
        $data['totalChalanUploadedlicenseThisWeek'] = chalan::where('status', 1)->whereBetween('chalan_upload_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_dl_match');
        $data['totalChalanUploadedlicenseLastWeek'] = chalan::where('status', 1)->whereBetween('chalan_upload_at', [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()])->sum('total_dl_match');
        $data['totalChalanUploadedlicenseThisMonth'] = chalan::where('status', 1)->whereMonth('chalan_upload_at', Carbon::now()->month)->sum('total_dl_match');

        $data['totalBookingLicense'] = LicenseBooking::whereIn('booking_status', [2,3,4])->count();
        $data['totalBookingLicenseToday'] = LicenseBooking::whereIn('booking_status', [2,3,4])->whereDate('created_at', Carbon::today())->count();
        $data['totalBookingLicenseThisWeek'] = LicenseBooking::whereIn('booking_status', [2,3,4])->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $data['totalBookingLicenseLastWeek'] = LicenseBooking::whereIn('booking_status', [2,3,4])->whereBetween('created_at', [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()])->count();
        $data['totalBookingLicenseThisMonth'] = LicenseBooking::whereIn('booking_status', [2,3,4])->whereMonth('created_at', Carbon::now()->month)->count();


        $data['totalDeliveredLicense'] = LicenseBooking::where('booking_status', 4)->count();
        $data['totalUndeliveredLicense'] = LicenseBooking::where('booking_status', 2)->count();

        $excludedLicenses = LicenseNotFound::select('license_no')
            ->where('status', 1)
            ->whereIn('license_no', function ($query) {
                $query->select('license_no')
                    ->from('license_not_founds')
                    ->where('status', 0);
            })
            ->distinct()
            ->pluck('license_no');

        $data['totalLicenseNotFound'] = LicenseNotFound::whereNotIn('license_no', $excludedLicenses)
            ->distinct('license_no')
            ->count();

        $data['totalLicenseReturned'] = LicenseBooking::where('booking_status', 3)->count();


        return response()->json($data);
    }

}
