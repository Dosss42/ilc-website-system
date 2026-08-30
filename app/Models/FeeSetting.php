<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeSetting extends Model
{
    use HasFactory;

    protected $table = 'fee_settings';

    protected $fillable = [
        'tuition',
        'misc',
        'insurance',
        'electric',
        'books_nursery',
        'books_grade1',
        'books_grade3',
        'books_grade4',
        'option_a_discount',
        'optb_monthly_tuition',
        'optb_monthly_electric',
        'optb_dp_nursery',
        'optb_dp_kinder',
        'optb_dp_grade1',
        'optb_dp_grade3',
        'optb_dp_grade4',
        'optc_monthly_tuition',
        'optc_monthly_misc',
        'optc_monthly_electric',
        'optc_dp_nursery',
        'optc_dp_kinder',
        'optc_dp_grade1',
        'optc_dp_grade3',
        'optc_dp_grade4',
        'optd_monthly_tuition',
        'optd_monthly_misc',
        'optd_monthly_electric',
        'optd_dp_nursery',
        'optd_dp_kinder',
        'optd_dp_grade1',
        'optd_dp_grade3',
        'optd_dp_grade4',
    ];

    protected $casts = [
        'tuition' => 'decimal:2',
        'misc' => 'decimal:2',
        'insurance' => 'decimal:2',
        'electric' => 'decimal:2',
        'books_nursery' => 'decimal:2',
        'books_grade1' => 'decimal:2',
        'books_grade3' => 'decimal:2',
        'books_grade4' => 'decimal:2',
        'option_a_discount' => 'decimal:2',
        'optb_monthly_tuition' => 'decimal:2',
        'optb_monthly_electric' => 'decimal:2',
        'optb_dp_nursery' => 'decimal:2',
        'optb_dp_kinder' => 'decimal:2',
        'optb_dp_grade1' => 'decimal:2',
        'optb_dp_grade3' => 'decimal:2',
        'optb_dp_grade4' => 'decimal:2',
        'optc_monthly_tuition' => 'decimal:2',
        'optc_monthly_misc' => 'decimal:2',
        'optc_monthly_electric' => 'decimal:2',
        'optc_dp_nursery' => 'decimal:2',
        'optc_dp_kinder' => 'decimal:2',
        'optc_dp_grade1' => 'decimal:2',
        'optc_dp_grade3' => 'decimal:2',
        'optc_dp_grade4' => 'decimal:2',
        'optd_monthly_tuition' => 'decimal:2',
        'optd_monthly_misc' => 'decimal:2',
        'optd_monthly_electric' => 'decimal:2',
        'optd_dp_nursery' => 'decimal:2',
        'optd_dp_kinder' => 'decimal:2',
        'optd_dp_grade1' => 'decimal:2',
        'optd_dp_grade3' => 'decimal:2',
        'optd_dp_grade4' => 'decimal:2',
    ];

    /**
     * Get the current fee settings (single row). Creates default if none exists.
     */
    public static function current(): self
    {
        return static::firstOrCreate([], []);
    }
}
