<?php

namespace App\Filament\Pages;

use App\Models\Budget;
use App\Models\Expense;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class BudgetVsActual extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $title           = 'Budget vs Actual';
    protected static ?string $navigationLabel = 'Budget vs Actual';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.budget-vs-actual';

    public int $selectedYear;

    public function mount(): void
    {
        $this->selectedYear = now()->year;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function table(Table $table): Table
    {
        $year = $this->selectedYear;

        return $table
            ->query(Budget::query()->where('year', $year)->orderBy('category'))
            ->columns([
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()->color('gray')
                    ->formatStateUsing(fn ($state) => Budget::categoryOptions()[$state] ?? $state),

                TextColumn::make('department')
                    ->label('Department'),

                TextColumn::make('allocated_amount')
                    ->label('Budget')
                    ->getStateUsing(fn (Budget $record) =>
                        $record->currency . ' ' . number_format((float) $record->allocated_amount, 0)
                    ),

                TextColumn::make('actual_spent')
                    ->label('Actual Spent')
                    ->getStateUsing(function (Budget $record) use ($year) {
                        $actual = Expense::where('category', $record->category)
                            ->whereIn('status', ['approved', 'paid'])
                            ->whereYear('expense_date', $year)
                            ->sum('amount');
                        return $record->currency . ' ' . number_format((float) $actual, 0);
                    }),

                TextColumn::make('variance')
                    ->label('Variance')
                    ->getStateUsing(function (Budget $record) use ($year) {
                        $actual = Expense::where('category', $record->category)
                            ->whereIn('status', ['approved', 'paid'])
                            ->whereYear('expense_date', $year)
                            ->sum('amount');
                        $variance = (float) $record->allocated_amount - (float) $actual;
                        $sign = $variance >= 0 ? '+' : '';
                        return $record->currency . ' ' . $sign . number_format($variance, 0);
                    })
                    ->color(function (Budget $record) use ($year) {
                        $actual = Expense::where('category', $record->category)
                            ->whereIn('status', ['approved', 'paid'])
                            ->whereYear('expense_date', $year)
                            ->sum('amount');
                        return (float) $record->allocated_amount >= (float) $actual ? 'success' : 'danger';
                    }),

                TextColumn::make('utilization')
                    ->label('Used %')
                    ->getStateUsing(function (Budget $record) use ($year) {
                        if ((float) $record->allocated_amount === 0.0) return '—';
                        $actual = Expense::where('category', $record->category)
                            ->whereIn('status', ['approved', 'paid'])
                            ->whereYear('expense_date', $year)
                            ->sum('amount');
                        return round(((float) $actual / (float) $record->allocated_amount) * 100, 1) . '%';
                    }),
            ])
            ->paginated(false);
    }
}
