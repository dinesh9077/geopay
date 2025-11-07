<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon; 
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // Top Summary Cards
        $cards = $this->getTopCards();

        // Wallet & Commission Snapshot
        $wallet = $this->getWalletSnapshot();

        // Recent Activity (ActivityLog table assumed)
        $recent = DB::table('activity_log')
            ->select('event', 'description', 'created_at')
            ->latest('created_at')->limit(12)->get()->map(fn($r) => (array) $r)->toArray();

        // Top Performing Users/Agents (last 30 days)
        $top = $this->getTopPerformers(30, 8);

        // Compliance Alerts
        $alerts = [];

        return view('admin.dashboard', compact('cards', 'wallet', 'recent', 'top', 'alerts'));
    }

    public function metrics()
    {
        // JSON for refreshing top cards without reloading page
        return response()->json($this->getTopCards());
    }

    public function transactionsSeries(Request $request)
    {
        $days = (int) $request->integer('days', 7);
        return response()->json($this->getTransactionsSeries($days));
    }

    /* --------------------------- private helpers --------------------------- */

    private function getTopCards(): array
    {
        // Total Users
        $totalUsers = DB::table('users')->count();

        // Verified Users: email_verified_at not null (+ optional kyc_verified true)
        $verified = DB::table('users')
            ->whereRole('user')
            ->when($this->schemaHasColumn('users', 'is_kyc_verify'), function ($q) {
                $q->whereNotNull('is_kyc_verify')->where('is_kyc_verify', true);
            }, function ($q) {
                $q->whereNotNull('is_kyc_verify');
            })
            ->count();
         
       
        $transactionsPayCount = (float) DB::table('transactions') 
        ->whereIn('platform_name', ['transfer to mobile', 'transfer to bank', 'international airtime'])
        ->whereIn('txn_status', ['success', 'completed', 'paid']) 
        ->count();

        $transactionsPayAmount = (float) DB::table('transactions') 
            ->whereIn('platform_name', ['transfer to mobile', 'transfer to bank', 'international airtime'])
            ->whereIn('txn_status', ['success', 'completed', 'paid']) 
            ->sum('txn_amount');

        // Commission (choose available column)
        $commissionCol = $this->schemaHasColumn('transactions', 'fees') ? 'fees' :
            ($this->schemaHasColumn('transactions', 'fees') ? 'fees' : null);

        $commissionPay = $commissionCol
        ? (float) DB::table('transactions')
                ->whereIn('platform_name', ['transfer to mobile', 'transfer to bank', 'international airtime'])
                ->whereIn('txn_status', ['success', 'completed', 'paid'])
                ->sum($commissionCol)
        : 0.0;

         $transactionsAddCount = (float) DB::table('transactions') 
        ->whereIn('platform_name', ['add money'])
        ->whereIn('txn_status', ['successful', 'captured', 'completed', 'paid']) 
        ->count();

        $transactionsAddAmount = (float) DB::table('transactions')
            ->whereIn('platform_name', ['add money'])
            ->whereIn('txn_status', ['successful', 'captured', 'completed', 'paid'])
            ->sum('txn_amount');
 
        $commissionAdd = $commissionCol
        ? (float) DB::table('transactions')
                ->whereIn('platform_name', ['add money'])
                ->whereIn('txn_status', ['successful', 'captured', 'completed', 'paid'])
                ->sum($commissionCol)
        : 0.0;

        // Pending KYC / Support Tickets
        $pendingKyc = DB::table('users')->whereRole('user')->where('is_kyc_verify', 0)->count();
        $activeUser = DB::table('users')->whereRole('user')->where(['status' => 1, 'is_kyc_verify' => 1, 'is_mobile_verify' => 1, 'is_email_verify' => 1])->count();
        $totalWallet = DB::table('users')->whereRole('user')->sum('balance');
        $totalMerchant = DB::table('users')->whereRole('merchant')->count();
        $totalMerchantWallet = DB::table('users')->whereRole('merchant')->sum('balance');
        $totalActiveMerchant = DB::table('users')->whereStatus(1)->whereRole('merchant')->count();

        return [
            'total_users' => $totalUsers,
            'verified_users' => $verified,
            'transactions_pay' => ['count' => $transactionsPayCount, 'amount' => $transactionsPayAmount],
            'commission_pay' => $commissionPay,
            'transactions_add' => ['count' => $transactionsAddCount, 'amount' => $transactionsAddAmount],
            'commission_add' => $commissionAdd,
            'pending_kyc' => $pendingKyc,
            'active_user' => $activeUser, 
            'total_wallet' => $totalWallet,
            'total_merchant_wallet' => $totalMerchantWallet,
            'total_active_merchant' => $totalActiveMerchant,
            'total_merchant' => $totalMerchant
        ];
    }

    private function getTransactionsSeries(int $days = 7): array
    {
        $days = max(1, min($days, 90)); // safety clamp
        $start = Carbon::today()->subDays($days - 1);

        $commissionCol = $this->schemaHasColumn('transactions', 'commission_amount') ? 'commission_amount' :
            ($this->schemaHasColumn('transactions', 'commission') ? 'commission' : null);

        $query = DB::table('transactions')
            ->selectRaw('DATE(created_at) as d')
            ->selectRaw('COUNT(*) as c')
            ->selectRaw('SUM(amount) as s')
            ->when($commissionCol, fn($q) => $q->selectRaw('SUM(' . $commissionCol . ') as cm'))
            ->where('status', 'success')
            ->whereDate('created_at', '>=', $start->toDateString())
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $row = $query[$date] ?? null;
            $out[] = [
                'date' => $date,
                'count' => $row?->c ? (int) $row->c : 0,
                'amount' => $row?->s ? (float) $row->s : 0.0,
                'commission' => $commissionCol && $row?->cm ? (float) $row->cm : 0.0,
            ];
        }
        return $out;
    }

    private function getWalletSnapshot(): array
    {
        // Adjust to your schema (wallets table with type + balance)
        return [
            'platform_balance' => 0,
            'escrow_balance' => 0,
            'commission_pool' =>  0,
        ];
    }

    private function getTopPerformers(int $lastDays = 30, int $limit = 8): array
    {
        $from = Carbon::now()->subDays($lastDays)->startOfDay();

        $rows = DB::table('transactions as t')
            ->join('users as u', 'u.id', '=', 't.user_id')
            ->where('t.txn_status', 'success')
            ->where('t.created_at', '>=', $from)
            ->selectRaw('
            t.user_id,
            u.first_name as name,
            u.email,
            COUNT(*) as txn_count,
            SUM(t.txn_amount) as total_amount
        ')
            ->groupBy('t.user_id', 'u.first_name', 'u.email')
            ->orderByDesc('total_amount')
            ->limit($limit)
            ->get();

        return $rows->map(function ($r) {
            return [
                'user_id' => (int) $r->user_id,
                'txn_count' => (int) $r->txn_count,
                'total_amount' => (float) ($r->total_amount ?? 0),
                'user' => ['name' => $r->name, 'email' => $r->email],
            ];
        })->toArray();
    }


    private function schemaHasColumn(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function revenueSeries(Request $request)
    {
        // Inputs: range = today|week|month|year
        $range = $request->get('range', 'month');

        // Adjust these names to your schema
        $table = 'transactions';
        $amountCol = 'txn_amount';          // total amount column
        $commissionCol = 'fees'; // commission column
        $statusCol = 'txn_status';
        $successVal = ['success', 'completed', 'paid'];

        // Range → start & end + group granularity
        [$start, $end, $groupFmt, $phpLabelFmt] = $this->resolveRange($range);

        // Build base query
        $rows = DB::table("$table as t")
            ->whereIn('t.platform_name', ['transfer to mobile', 'transfer to bank', 'international airtime'])
            ->whereIn("t.$statusCol", $successVal)
            ->whereBetween('t.created_at', [$start, $end])
            ->selectRaw("
                DATE_FORMAT(t.created_at, ?) as grp,
                COUNT(*) as txn_count,
                COALESCE(SUM(t.$amountCol), 0) as total_amount,
                COALESCE(SUM(t.$commissionCol), 0) as total_commission
            ", [$groupFmt])
            ->groupBy('grp')
            ->orderBy('grp')
            ->get();

        // Build complete label axis (fill missing dates with zeros)
        $labels = $this->buildLabelRange($start, $end, $range, $phpLabelFmt);

        $byKey = $rows->keyBy('grp');
        $countSeries = [];
        $amountSeries = [];
        $commissionSeries = [];

        foreach ($labels['keys'] as $k) {
            $row = $byKey->get($k);
            $countSeries[] = (int) ($row->txn_count ?? 0);
            $amountSeries[] = (float) ($row->total_amount ?? 0);
            $commissionSeries[] = (float) ($row->total_commission ?? 0);
        }

        return response()->json([
            'labels' => $labels['labels'],     // pretty labels for chart axis
            'counts' => $countSeries,          // transactions count
            'amounts' => $amountSeries,         // sum(txn_amount)
            'commission' => $commissionSeries,     // sum(commission_amount)
            'range' => $range,
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString(),
        ]);
    }

    public function revenueAddSeries(Request $request)
    {
        // Inputs: range = today|week|month|year
        $range = $request->get('range', 'month');

        // Adjust these names to your schema
        $table = 'transactions';
        $amountCol = 'txn_amount';          // total amount column
        $commissionCol = 'fees'; // commission column
        $statusCol = 'txn_status';
        $successVal = ['successful', 'captured', 'completed', 'paid'];

        // Range → start & end + group granularity
        [$start, $end, $groupFmt, $phpLabelFmt] = $this->resolveRange($range);

        // Build base query
        $rows = DB::table("$table as t")
            ->whereIn('t.platform_name', ['add money'])
            ->whereIn("t.$statusCol", $successVal)
            ->whereBetween('t.created_at', [$start, $end])
            ->selectRaw("
                DATE_FORMAT(t.created_at, ?) as grp,
                COUNT(*) as txn_count,
                COALESCE(SUM(t.$amountCol), 0) as total_amount,
                COALESCE(SUM(t.$commissionCol), 0) as total_commission
            ", [$groupFmt])
            ->groupBy('grp')
            ->orderBy('grp')
            ->get();

        // Build complete label axis (fill missing dates with zeros)
        $labels = $this->buildLabelRange($start, $end, $range, $phpLabelFmt);

        $byKey = $rows->keyBy('grp');
        $countSeries = [];
        $amountSeries = [];
        $commissionSeries = [];

        foreach ($labels['keys'] as $k) {
            $row = $byKey->get($k);
            $countSeries[] = (int) ($row->txn_count ?? 0);
            $amountSeries[] = (float) ($row->total_amount ?? 0);
            $commissionSeries[] = (float) ($row->total_commission ?? 0);
        }

        return response()->json([
            'labels' => $labels['labels'],     // pretty labels for chart axis
            'counts' => $countSeries,          // transactions count
            'amounts' => $amountSeries,         // sum(txn_amount)
            'commission' => $commissionSeries,     // sum(commission_amount)
            'range' => $range,
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString(),
        ]);
    }

    private function resolveRange(string $range): array
    {
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                // group by hour
                $groupFmt = '%Y-%m-%d %H:00:00';
                $phpLabelFmt = 'H:i';
                break;

            case 'week':
                $start = $now->copy()->startOfWeek(); // Mon..Sun (depends on locale)
                $end = $now->copy()->endOfWeek();
                // group by day
                $groupFmt = '%Y-%m-%d';
                $phpLabelFmt = 'd M';
                break;

            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                // group by month
                $groupFmt = '%Y-%m-01';
                $phpLabelFmt = 'M Y';
                break;

            case 'month':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                // group by day
                $groupFmt = '%Y-%m-%d';
                $phpLabelFmt = 'd M';
                break;
        }

        return [$start, $end, $groupFmt, $phpLabelFmt];
    }

    private function buildLabelRange(Carbon $start, Carbon $end, string $range, string $phpLabelFmt): array
    {
        $keys = [];
        $labels = [];

        if ($range === 'today') {
            // Hourly
            $period = CarbonPeriod::create($start, '1 hour', $end);
            foreach ($period as $dt) {
                $keys[] = $dt->format('Y-m-d H:00:00');
                $labels[] = $dt->format($phpLabelFmt); // H:i
            }
        } elseif ($range === 'year') {
            // Monthly
            $period = CarbonPeriod::create($start->copy()->startOfMonth(), '1 month', $end);
            foreach ($period as $dt) {
                $keys[] = $dt->format('Y-m-01');
                $labels[] = $dt->format($phpLabelFmt); // M Y
            }
        } else {
            // Daily
            $period = CarbonPeriod::create($start->copy()->startOfDay(), '1 day', $end);
            foreach ($period as $dt) {
                $keys[] = $dt->format('Y-m-d');
                $labels[] = $dt->format($phpLabelFmt); // d M
            }
        }

        return ['keys' => $keys, 'labels' => $labels];
    }
}
