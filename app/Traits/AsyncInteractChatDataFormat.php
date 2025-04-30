<?php

namespace App\Traits;

use Carbon\Carbon;

trait AsyncInteractChatDataFormat
{
    public function dataTransform($data): array
    {
        $result = [];
        $uniqueCommentIds = array_unique(array_column($data, 'comment_id'));

        foreach ($uniqueCommentIds as $uniqueCommentId) {
            $filteredData = array_filter($data, function ($item) use ($uniqueCommentId) {
                return $item['comment_id'] == $uniqueCommentId;
            });

            $uniqueUserData = array_reduce($filteredData, function ($result, $item) {
                static $seen = [];
                if (!isset($seen[$item['user_id']])) {
                    $seen[$item['user_id']] = true;
                    $result[] = [
                        0 => $item['fio'],
                        1 => $item['user_id']
                    ];
                }
                return $result;
            }, []);

            $uniqueComment = array_unique(array_column(array_map(function ($item) {
                return ["comment" => $item['comment']];
            }, $filteredData), 'comment'));

            $uniqueCreatedAt = array_unique(array_column(array_map(function ($item) {
                return ["created_at" => Carbon::parse($item['created_at'])->setTimezone(config('app.timezone'))->format('d.m.Y H:i:s')];
            }, $filteredData), 'created_at'));

            $isToday = Carbon::now()->isSameDay($uniqueCreatedAt[0]);
            $isWithin30Minutes = Carbon::parse($uniqueCreatedAt[0])->diffInMinutes(now()) <= 30;

            $files = array_map(function ($item) {
                return [
                    "path_to_file" => $item['path_to_file'],
                    "original_filename" => $item['original_filename']
                ];
            }, $filteredData);

            $allNull = array_reduce($files, function ($carry, $file) {
                return $carry && is_null($file['path_to_file']) && is_null($file['original_filename']);
            }, true);

            $files = $allNull ? null : $files;

            $result[] = [
                'id' => $uniqueCommentId,
                'fio' => $uniqueUserData[0][0],
                'user_id' => $uniqueUserData[0][1],
                'comment' => $uniqueComment[0],
                'created_at' => $uniqueCreatedAt[0],
                'isToday' => $isToday,
                'isWithin30Minutes' => $isWithin30Minutes,
                'files' => $files
            ];
        }

        return $result;
    }
}