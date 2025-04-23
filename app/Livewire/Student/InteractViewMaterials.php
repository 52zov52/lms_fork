<?php

namespace App\Livewire\Student;

use App\Models\AssignmentComment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InteractViewMaterials extends Component
{
    public int $id;

    public function mount($id)
    {
        $this->id = $id;
    }

    public function render()
    {
        $materials = $this->dataTransform(AssignmentComment::leftJoin('assignment_attachments', 'assignment_comments.id', '=', 'assignment_attachments.assignment_comment_id')
            ->join('users_data', 'assignment_comments.teacher_id', '=', 'users_data.user_id')
            ->where('assignment_comments.teacher_assignment_id', '=', $this->id)
            ->select(
                DB::raw("CONCAT(last_name, ' ', SUBSTRING(first_name, 1, 1), '. ', SUBSTRING(middle_name, 1, 1), '.') as fio"),
                'assignment_comments.id as comment_id',
                'assignment_comments.comment',
                'assignment_attachments.path_to_file',
                'assignment_attachments.original_filename',
                'assignment_comments.created_at'
            )
            ->orderBy('comment_id')
            ->get()
            ->toArray());

        return view('livewire.student.interact-view-materials', ['materials' => $materials]);
    }

    private function dataTransform($data)
    {
        $result = [];
        $uniqueCommentIds = array_unique(array_column($data, 'comment_id'));

        foreach ($uniqueCommentIds as $uniqueCommentId) {
            $filteredData = array_filter($data, function ($item) use ($uniqueCommentId) {
                return $item['comment_id'] == $uniqueCommentId;
            });

            $uniqueFio = array_unique(array_column(array_map(function ($item) {
                return ["fio" => $item['fio']];
            }, $filteredData), 'fio'));

            $uniqueComment = array_unique(array_column(array_map(function ($item) {
                return ["comment" => $item['comment']];
            }, $filteredData), 'comment'));

            $uniqueCreatedAt = array_unique(array_column(array_map(function ($item) {
                return ["created_at" => Carbon::parse($item['created_at'])->format('d.m.Y')];
            }, $filteredData), 'created_at'));

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
                'fio' => $uniqueFio[0],
                'comment' => $uniqueComment[0],
                'created_at' => $uniqueCreatedAt[0],
                'files' => $files
            ];
        }

        return $result;
    }
}
