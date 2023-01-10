<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RincianInduk extends Model
{
    use HasFactory;
    protected $guarded = [''];


    public function item_rincian_induks()
    {
        return $this->belongsTo(ItemRincianInduk::class, 'kategori_id', 'id')->withDefault([
            'nama_kategori'=> ' ',

    ]);
    }
    public function rabs()
    {
        return $this->hasMany(Rab::class, 'item_id', 'id');
    }

    public function khs()
    {
        return $this->belongsTo(Khs::class, 'khs_id');
    }

    public function satuans()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id', 'id');
    }

    public function paket_pekerjaans()
    {
        return $this->belongsTo(PaketPekerjaan::class, 'item_id', 'id');
    }

    public function order_khs()
    {
        return $this->hasMany(OrderKhs::class, 'item_order');
    }


}
