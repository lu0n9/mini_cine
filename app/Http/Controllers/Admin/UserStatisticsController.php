<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserStatisticsController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | TỔNG USER
        |--------------------------------------------------------------------------
        */

        $totalUsers = User::count();


        /*
        |--------------------------------------------------------------------------
        | USER MỚI TRONG THÁNG HIỆN TẠI
        |--------------------------------------------------------------------------
        */

        $newUsersThisMonth = User::query()
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->count();


        /*
        |--------------------------------------------------------------------------
        | PREMIUM
        |--------------------------------------------------------------------------
        |
        | Chỉ dùng nếu users.role có giá trị "premium".
        |
        */

        $premiumUsers = User::query()
            ->where('role', 'premium')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | USER HOẠT ĐỘNG HÔM NAY
        |--------------------------------------------------------------------------
        |
        | Dựa vào last_login_at.
        |
        */

        $activeUsersToday = User::query()
            ->whereNotNull('last_login_at')
            ->whereBetween('last_login_at', [
                Carbon::today(),
                Carbon::tomorrow(),
            ])
            ->count();


        /*
        |--------------------------------------------------------------------------
        | NGƯỜI DÙNG MỚI THEO 7 THÁNG GẦN NHẤT
        |--------------------------------------------------------------------------
        */

        $startDate = Carbon::now()
            ->subMonths(6)
            ->startOfMonth();

        $monthlyUsers = User::query()
            ->select([
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total'),
            ])
            ->where('created_at', '>=', $startDate)
            ->groupBy(
                DB::raw('YEAR(created_at)'),
                DB::raw('MONTH(created_at)')
            )
            ->orderBy('year')
            ->orderBy('month')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ĐẢM BẢO ĐỦ 7 THÁNG
        |--------------------------------------------------------------------------
        |
        | Nếu tháng nào không có user mới thì vẫn hiển thị cột = 0.
        |
        */

        $monthlyChart = collect();

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::now()
                ->subMonths($i)
                ->startOfMonth();

            $item = $monthlyUsers->first(function ($row) use ($date) {
                return (int) $row->year === (int) $date->year
                    && (int) $row->month === (int) $date->month;
            });

            $monthlyChart->push([
                'label' => 'T' . $date->month,
                'year' => $date->year,
                'month' => $date->month,
                'total' => $item
                    ? (int) $item->total
                    : 0,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | TÍNH CHIỀU CAO BAR
        |--------------------------------------------------------------------------
        */

        $maxMonthlyUsers = $monthlyChart->max('total');

        $monthlyChart = $monthlyChart->map(function ($item) use ($maxMonthlyUsers) {

            $item['height'] = $maxMonthlyUsers > 0
                ? round(
                    ($item['total'] / $maxMonthlyUsers) * 100
                )
                : 0;

            return $item;
        });


        return view(
            'admin.pages.statistical.user_stats',
            compact(
                'totalUsers',
                'newUsersThisMonth',
                'premiumUsers',
                'activeUsersToday',
                'monthlyChart'
            )
        );
    }
}