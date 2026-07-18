<?php

namespace App\Rules;

use App\Models\PtoRequest;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Translation\PotentiallyTranslatedString;

class NoOverlappingPto implements ValidationRule
{
    public function __construct(
        private ?string $endDate,
        private ?int $excludeId = null
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->endDate || ! strtotime($this->endDate) || ! strtotime($value)) {
            return;
        }

        $startDate = Carbon::parse($value)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->startOfDay();
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $hasOverlap = PtoRequest::where('user_id', $user->id)
            ->when($this->excludeId, fn ($query) => $query->where('id', '!=', $this->excludeId))
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })->exists();

        if ($hasOverlap) {
            $fail('You already have an overlapping PTO request for these dates.');
        }
    }
}
