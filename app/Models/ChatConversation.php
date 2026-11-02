<?php

namespace App\Models;

use App\Enums\ChatRole;
use Database\Factories\ChatConversationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * An anonymous visitor's chat session with the portfolio assistant.
 *
 * @property int $id
 * @property string $session_id
 * @property string|null $ip_hash
 * @property string|null $user_agent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['session_id', 'ip_hash', 'user_agent'])]
class ChatConversation extends Model
{
    /** @use HasFactory<ChatConversationFactory> */
    use HasFactory;

    /**
     * @return HasMany<ChatMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->oldest('id');
    }

    /**
     * Number of questions the visitor has asked in this conversation.
     */
    public function userTurns(): int
    {
        return $this->messages()->where('role', ChatRole::User)->count();
    }
}
