<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CompetitiveBox
 *
 * @property int $game_id
 * @property int $x
 * @property int $y
 * @property array $competitors
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Game $game
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompetitiveBox whereCompetitors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompetitiveBox whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompetitiveBox whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompetitiveBox whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompetitiveBox whereX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CompetitiveBox whereY($value)
 * @mixin \Eloquent
 */
class CompetitiveBox extends Model
{
    protected $casts = [
        'competitors' => 'array'
    ];
    protected $primaryKey = 'game_id';

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }
    protected $fillable = ['game_id', 'x', 'y', 'competitors'];
    protected $hidden = ['created_at', 'updated_at'];
}
