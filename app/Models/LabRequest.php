<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabRequest extends Model
{
    public const STATUS_ACKNOWLEDGEMENT = 'sample_acknowledgement';
    public const STATUS_RECEIVE = 'sample_receive';
    public const STATUS_REPORT_ENTRY = 'report_entry';
    public const STATUS_AUTHORIZATION = 'authorization';
    public const STATUS_REPORT_DATA = 'report_data';

    protected $fillable = [
        'request_number',
        'visit_id',
        'visit_billing_service_id',
        'lab_test_id',
        'test_name',
        'test_code',
        'lab_department',
        'sample_type',
        'sample_number',
        'status',
        'acknowledged_at',
        'acknowledged_by',
        'acknowledgement_notes',
        'received_at',
        'received_by',
        'receive_notes',
        'result_value',
        'unit',
        'reference_range',
        'result_status',
        'report_notes',
        'reported_at',
        'reported_by',
        'authorized_at',
        'authorized_by',
        'authorization_notes',
        'is_rejected',
        'rejection_reason',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
        'received_at' => 'datetime',
        'reported_at' => 'datetime',
        'authorized_at' => 'datetime',
        'is_rejected' => 'boolean',
    ];

    public static function generateRequestNumber(): string
    {
        $prefix = 'LAB';
        $date = now()->format('Ymd');
        $last = self::where('request_number', 'like', $prefix . $date . '%')->latest('id')->first();
        $sequence = $last ? (int) substr($last->request_number, -4) + 1 : 1;

        return $prefix . $date . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function billingService(): BelongsTo
    {
        return $this->belongsTo(VisitBillingService::class, 'visit_billing_service_id');
    }

    public function labTest(): BelongsTo
    {
        return $this->belongsTo(LabTest::class);
    }

    public function acknowledgedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function receivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function reportedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function authorizedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function getDepartmentLabelAttribute(): string
    {
        return config("diagnosis.lab_departments.{$this->lab_department}.name", ucfirst($this->lab_department));
    }

    public function getStatusLabelAttribute(): string
    {
        return config("diagnosis.lab_workflow_stages.{$this->status}.label", ucfirst(str_replace('_', ' ', $this->status)));
    }

    public function getPatientAttribute(): ?Person
    {
        return $this->visit?->patient;
    }

    public function getWorkflowShowRouteNameAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACKNOWLEDGEMENT => 'laboratory.acknowledgement.show',
            self::STATUS_RECEIVE => 'laboratory.receive.show',
            self::STATUS_REPORT_ENTRY => 'laboratory.report-entry.show',
            self::STATUS_AUTHORIZATION => 'laboratory.authorization.show',
            self::STATUS_REPORT_DATA => 'laboratory.report-data.show',
            default => 'laboratory.index',
        };
    }
}
