<?php
// date time helpers
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if (!function_exists('get_date_time')) {
    function get_date_time()
    {
        $tz_object = new DateTimeZone('Asia/Karachi');
        $datetime = new DateTime();
        $datetime->setTimezone($tz_object);
        return $datetime->format('Y\-m\-d\ H:i:s');
    }
}

if (!function_exists('saveFileToStorage')) {
    function saveFileToStorage($file)
    {
        $fileName = Str::random(10).'_'.Carbon::now()->format('ymdhis') . '.' . $file->getClientOriginalExtension();
        $imagePath = $file->storeAs('public/company/logo', $fileName);
        $path =  request()->getSchemeAndHttpHost() . "/storage/company/logo/$fileName";
        return $path;
    }
}


