<?php

namespace App\Models;

use App\Rules\Cpf;
use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'cpf', 'birth_date', 'phone', 'email', 'notes'])]
class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Escopo de tenant: todo acesso a pacientes passa por aqui.
     *
     * Camada 2 da defesa em profundidade — mesmo que uma query esqueça o
     * filtro, a Policy e a FK ainda barram o vazamento.
     */
    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    protected function cpf(): Attribute
    {
        // Garante que o banco sempre receba os 11 dígitos, com ou sem máscara.
        return Attribute::make(
            set: fn (string $value) => Cpf::normalizar($value),
        );
    }

    protected function cpfFormatted(): Attribute
    {
        return Attribute::get(fn () => Cpf::formatar((string) $this->cpf));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<MedicalRecord, $this> */
    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class)->orderByDesc('occurred_at');
    }
}
