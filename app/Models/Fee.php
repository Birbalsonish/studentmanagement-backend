<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'fee_type',
        'amount',
        'paid_amount',
        'pending_amount',
        'due_date',
        'paid_date',
        'status',
        'payment_method',
        'transaction_id',
        'academic_year',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'pending_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Auto-calculate pending amount
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($fee) {
            $fee->pending_amount = $fee->amount - $fee->paid_amount;
            
            // Update status based on payment
            if ($fee->paid_amount >= $fee->amount) {
                $fee->status = 'Paid';
            } elseif ($fee->paid_amount > 0) {
                $fee->status = 'Partial';
            } elseif ($fee->due_date < now()) {
                $fee->status = 'Overdue';
            }
        });
    }
}
