<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\studentsTasks;
use App\Models\Task;
use App\Models\User;
use App\Models\Notification as Noti;
use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class TaskController extends Controller
{
    function addTask(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required',
            'deadline' => 'required',
            'description' => 'required',
            'trainer_id' => 'required',
            'program_id' => 'required',
        ], [
            'required' => 'field-required'
        ]);

        $task = Task::create([
            'taTitle' => $fields['title'],
            'taStatus' => 0,
            'taDescription' => $fields['description'],
            'taDeadline' => $fields['deadline'],
            'taDescription' => $fields['description'],
            'program_id' => $fields['program_id'],
            'trainer_id' => $fields['trainer_id'],
        ]);
        $task->save();

        // Retrieve the student IDs associated with the program
        $studentIds = Student::where('program_id', $fields['program_id'])->pluck('id')->toArray();

        // Attach the students to the task
        $task->student()->attach($studentIds);
        $tokens = User::pluck('verification_token')->toArray();
        foreach ($tokens as $token) {
            error_log($token);
        }
        // Send notification to students
        $this->sendTaskNotification($task);

        $response = [
            'task' => $task,
            ['message' => 'Task created and notification sent.']
        ];

        return response($response, 201);
    }



    function getTrainerTasks($trainer_id)
    {
        $tasks = Task::where('tasks.trainer_id', $trainer_id)->get();

        $response = $tasks;

        return response($response, 201);
    }


    function getProgramTasks($program_id)
    {
        $tasks = Task::where('tasks.program_id', $program_id)->get();

        $response = $tasks;

        return response($response, 201);
    }


    function getStudentTasks($student_id)
    {
        $studentTasks = studentsTasks::where('students_tasks.student_id', $student_id)->pluck('task_id');

        $tasks = Task::whereIn('tasks.id', $studentTasks)
        ->where('tasks.taStatus',0)
        ->join('programs','programs.id','=','tasks.program_id')
        ->join('trainers','trainers.id','=','tasks.trainer_id')
        ->join('users','users.id','=','trainers.user_id')
        ->select('tasks.*','programs.pTitle','users.first_name','users.last_name')->get();

        $response = [
            'tasks' => $tasks,
        ];

        return response($response, 201);
    }

    function deleteTask($task_id)
    {

        $tasks = Task::find($task_id)->delete();

        $response = 'Task deleted';

        return response($response, 201);
    }

    function taskDone($task_id)
    {
        $task = Task::find($task_id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->taStatus = 1;
        $task->save();

        $this->sendTaskNotification($task);

        return response()->json(['message' => 'Task done succefully'], 201);
    }


    function getDoneTask($student_id)
    {
        $studentTasks = studentsTasks::where('students_tasks.student_id', $student_id)->pluck('task_id');

        $tasks = Task::whereIn('tasks.id', $studentTasks)
        ->where('tasks.taStatus',1)->get();

        $response = [
            'tasks' => $tasks,
        ];

        return response($response, 201);
    }

    public function sendTaskNotification(Task $task)
    {

        // Retrieve student tokens from the database
        $tokens = User::pluck('verification_token')->toArray();

        $SERVER_API_KEY = 'AAAAexnYYC8:APA91bEeYQkJrDzQwpGbVwbFFOH7pv5QuoU9BcVTv1FJCpkZmgCp4Qd2El0H_LbxNyMFdlpJLdUUZschLvgmrbT02v4Zt0Nmpwb3S9XNje-lhGI1BG3ekB2m2dMYdRpYggnjcpRVLK7W';

        $token_1 = 'dq5nPlheTDGaoJSCKuwIhu:APA91bFBsgITYzbxhyYphGBQDbA5qmq17WFSIARqNViBDNHXOS9Xq1INUTiLF58U2LL3vNKi9hNocr_RhN9JZzRCyMiITVHbufPErEzYKdrL05bJ-rPKmD5GoDq-4eAF6rmdmY67-cRK';

        $data = [
            "registration_ids" => [
                $token_1,
                // $tokens
            ],
            "notification" => [

                "title" => 'New Task Added',
                "body" => $task->taTitle . ' ' . $task->taDescription . ' ' . ' '
                    . 'Deadline on: ' . $task->taDeadline,
                "sound" => "default"
            ],

        ];
       // Create a notification and add it to the database
       $notification = new Noti();
       $notification->title = 'New Task Added';
       $notification->body = $task->taTitle . ' ' . $task->taDescription . ' ' . ' ' . 'Deadline on: ' . $task->taDeadline;
       $notification->save();

        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        dd($response);
    }
}
