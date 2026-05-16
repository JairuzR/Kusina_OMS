<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Returns a colour key used in Blade to pick the correct badge style.
     * Maps to: bg-{colour}-100 text-{colour}-800
     */
    public function getActionBadgeAttribute(): string
    {
        return match ($this->action) {
            'created', 'POST'          => 'green',
            'updated', 'PUT', 'PATCH'  => 'yellow',
            'deleted', 'DELETE'        => 'red',
            'login'                    => 'blue',
            'logout'                   => 'gray',
            default                    => 'gray',
        };
    }

    // ─── Static helpers ───────────────────────────────────────────────────────

    /**
     * Convenient one-liner for recording audit events anywhere in the app.
     *
     * Usage examples:
     *   AuditLog::record('created', 'orders', $order, null, $order->toArray());
     *   AuditLog::record('updated', 'menu',   $item,  $old,  $item->toArray());
     *   AuditLog::record('login',   'auth');
     *   AuditLog::record('deleted', 'users',  $user,  $user->toArray());
     */
    public static function record(
        string  $action,
        string  $module,
        ?Model  $model       = null,
        ?array  $oldValues   = null,
        ?array  $newValues   = null,
        ?string $description = null
    ): self {
        /** @var \Illuminate\Http\Request $request */
        $request = app(Request::class);

        return static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'module'      => $module,
            'model_type'  => $model ? get_class($model) : null,
            'model_id'    => $model?->getKey(),
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'description' => $description
                ?? (ucfirst($action) . ' ' . ($model ? class_basename($model) . ' #' . $model->getKey() : $module)),
        ]);
    }
}