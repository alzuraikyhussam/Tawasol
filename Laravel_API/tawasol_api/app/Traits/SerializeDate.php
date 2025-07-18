<?php

namespace App\Traits;

use Carbon\Carbon;
use DateTimeInterface;

trait SerializeDate
{
    /**
     * يفرض تحويل التاريخ من UTC إلى توقيت التطبيق قبل السيريال.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        // نحول الـ DateTimeInterface إلى Carbon
        $carbon = Carbon::instance($date)
                        ->setTimezone(config('app.timezone'));

        // نُخرِج بالتنسيق ISO‑8601 مع التعويض الزمني
        return $carbon->format('Y-m-d\TH:i:sP');
    }
}
