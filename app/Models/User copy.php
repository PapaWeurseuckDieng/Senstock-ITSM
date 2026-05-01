
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'department',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // ─── Helpers de rôle ─────────────────────────────────────────────────────

    public function isAdministrateur(): bool
    {
        return $this->role === 'administrateur';
    }

    public function isResponsableIT(): bool
    {
        return $this->role === 'responsable_it';
    }

    public function isTechnicien(): bool
    {
        return $this->role === 'technicien';
    }

    public function isUtilisateur(): bool
    {
        return $this->role === 'utilisateur';
    }

    public function isITStaff(): bool
    {
        return in_array($this->role, ['technicien', 'responsable_it', 'administrateur']);
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'administrateur'  => 'Administrateur',
            'responsable_it'  => 'Responsable IT',
            'technicien'      => 'Technicien IT',
            default           => 'Utilisateur',
        };
    }
}
