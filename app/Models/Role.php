<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $connection = 'game_mysql';
    protected $table = 't_roles';
    protected $primaryKey = 'rid';
    public $timestamps = false;

    protected $fillable = [
        'rname',
        'userid',
        'serverid',
        'level',
        'experience',
        'money',
        'occupation',
        'regtime'
    ];

    protected $casts = [
        'userid' => 'integer',
        'serverid' => 'integer',
        'level' => 'integer',
        'experience' => 'integer',
        'money' => 'integer',
        'occupation' => 'integer',
        'regtime' => 'datetime'
    ];

    // Relationships
    public function account()
    {
        return $this->belongsTo(Account::class, 'userid', 'ID');
    }

    // Helper methods
    public function getOccupationName()
    {
        $occupations = [
            0 => 'Dark Wizard',
            1 => 'Soul Master',
            2 => 'Grand Master',
            3 => 'Dark Knight',
            4 => 'Blade Knight',
            5 => 'Blade Master',
            6 => 'Fairy Elf',
            7 => 'Muse Elf',
            8 => 'High Elf',
            9 => 'Magic Gladiator',
            10 => 'Duel Master',
            11 => 'Dark Lord',
            12 => 'Lord Emperor',
            13 => 'Summoner',
            14 => 'Blood Master',
            15 => 'Dimension Master'
        ];

        return $occupations[$this->occupation] ?? 'Unknown';
    }

    public function getServerName()
    {
        return 'Server ' . $this->serverid;
    }

    public function getFormattedMoney()
    {
        return number_format($this->money);
    }

    public function getFormattedExperience()
    {
        return number_format($this->experience);
    }
}
