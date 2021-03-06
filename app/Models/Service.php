<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Service extends Model
{
    use HasFactory, SoftDeletes, ModelBase, ClearsResponseCache;

    protected $fillable = [
        'origin_id', 'title', 'price', 'comission', 'conversion', 'seance_length', 'master_id', 'deleted_at'
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function records()
    {
        return $this->belongsToMany(Record::class)->withTimestamps()->withPivot(['comission', 'profit']);
    }

    public function currency()
    {
        return $this->master->currency() ?? null;
    }

    public function getRecordsBetweenDates(string $startDate, string $endDate, bool $attendance = true)
    {
        $records = $this->records()
            ->whereBetween(DB::raw('DATE(started_at)'), array($startDate, $endDate))
            ->where('attendance', $attendance)
            ->get();

        return $records;
    }

    public function solveComission(string $startDate, string $endDate, bool $attendance = true)
    {
        $records = $this->records()
            ->whereBetween(DB::raw('DATE(started_at)'), array($startDate, $endDate))
            ->where('attendance', $attendance)
            ->get();

        return Record::solveComission($records, true);
    }

    public static function seed(array $items)
    {
        foreach ($items as $item) {
            if (empty($item["staff"][0]["id"] ?? null)) {
                $service = self::findByOriginId($item["id"] ?? 0);
                if (!empty($service)){
                    $service->master()->dissociate();
                    $service->save();
                }
                continue;
            };

            $staffId = $item["staff"][0]["id"];
            $item["seance_length"] = $item["staff"][0]["seance_length"] ?? null;

            $service = self::createOrUpdate(self::peel($item));

            // find master
            $master = Master::findByOriginId($staffId);

            if (!empty($master)) {
                // associate master
                $service->master()->associate($master)->save();
            }
        }

        note("info", "service:seed", "?????????????????? ???????????? ???? ??????", self::class);
    }

    public static function peel(array $item)
    {
        $data = [];

        if (!empty($item['id'])) {
            $data['origin_id'] = $item['id'];
        }

        if (!empty($item['price_max'])) {
            $data['price'] = $item['price_max'];
        }

        if (!empty($item['title'])) {
            $data['title'] = $item['title'];
        }

        if (!empty($item['seance_length'])) {
            $data['seance_length'] = $item['seance_length'];
        }

        if (empty($item['active']) || $item['active'] != "1") {
            $data['deleted_at'] = date(config('app.iso_datetime'));
        }

        return $data;
    }

    private static function createOrUpdate(array $item)
    {
        if (empty($item['origin_id'])) {
            return null;
        }

        $service = self::findByOriginId($item["origin_id"]);

        if (empty($service)) {
            $service = self::create($item);
        } else {
            $service->update($item);
            $service->refresh();
        }

        if (empty($item['deleted_at'] ?? null) && $service->trashed()) {
            $service->restore();
        }

        return $service;
    }
}
