<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\studentsTasks;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    function addTask(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required',
            'deadline' => 'required',
            'description' =>'required',
            'trainer_id' => 'required',
            'program_id' => 'required',
        ], [
            'required' => 'field-required'
        ]);

        $task = Task::create([
            'taTitle' => $fields['title'],
            'taStatus' => 0,
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

        $response = [
            'task' => $task,
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

        $tasks = Task::whereIn('id', $studentTasks)->get();

        $response = [
            'tasks' => $tasks,
        ];

        return response($response, 200);
    }

    function deleteTask($task_id)
    {

        $tasks = Task::find($task_id)->delete();

        $response = 'Task deleted';

        return response($response, 200);
    }


}
