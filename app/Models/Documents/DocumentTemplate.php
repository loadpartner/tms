<?php

namespace App\Models\Documents;

use App\Enums\Documents\DocumentTemplateType;
use App\Models\Organizations\Organization;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentTemplate extends Model
{
    use HasTimestamps, HasFactory, HasOrganization;

    protected $fillable = [
        'organization_id',
        'template_type',
        'template',
    ];

    protected $casts = [
        'template_type' => DocumentTemplateType::class,
    ];

    /**
     * Get the organization that owns this document template.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
} 