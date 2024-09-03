<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        if (!empty($this->filters['startDate'])) {
            $startDate = Carbon::parse($this->filters['startDate']);
        }

        if (!empty($this->filters['endDate'])) {
            $endDate = Carbon::parse($this->filters['endDate']);
        }

        $totalIncome = Transaction::where('type', 'income')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $totalExpense = Transaction::where('type', 'expense')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $saldo = $totalIncome - $totalExpense;

        return [
            Stat::make('Total Pemasukan', number_format($totalIncome)),
            Stat::make('Total Pengeluaran', number_format($totalExpense)),
            Stat::make('Total Saldo', number_format($saldo)),
        ];
    }
}
