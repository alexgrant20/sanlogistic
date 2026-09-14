<?php

namespace App\Models;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ErrorLog extends Model
{
    use HasFactory;

    private const MAX_STACK_TRACE_LINES = 20;

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_agent',
        'message',
        'location_file',
        'file_line',
        'stack_trace',
        'created_at',
        'created_by',
        'created_ip',
    ];

    public static function createLog(Throwable $e)
    {
        $errMessage = "";
        if ($e instanceof RequestException) {
            $errMessage = "Exception Message: " . $e->getMessage();
            $errMessage .= ", Response Content: " . $e->getResponse()->getBody()->getContents();
        } else {
            $errMessage = $e->getMessage();
        }

        $stackTraces = explode("\n", $e->getTraceAsString());
        $trimmedStackTraces = array_slice($stackTraces, 0, self::MAX_STACK_TRACE_LINES);

        $errorLog = new ErrorLog([
            'user_agent' => @($_SERVER['HTTP_USER_AGENT']) ?? 'local',
            'message' => $errMessage,
            'location_file' => $e->getFile(),
            'file_line' => $e->getLine(),
            'stack_trace' => implode("\n", $trimmedStackTraces),
            'created_at' => Carbon::now(),
            'created_by' => Auth::id(),
            'created_ip' => request()->ip() ?? 'local',
        ]);

        if ($errorLog->save()) {
            return $errorLog;
        }

        return null;
    }
}
