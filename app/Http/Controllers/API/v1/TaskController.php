<?php
namespace App\Http\Controllers\API\v1;


use Illuminate\Http\Request;
use App\Http\Controllers\API\v1\BaseController as BaseController;
use App\Models\Task;
use App\Models\ToDoList;
use Validator;
use App\Http\Resources\v1\TaskResource;
use App\Http\Filters\TaskFilter;

class TaskController extends BaseController
{
    /**
     * Display a tasking of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($listId, TaskFilter $filter){
        $tasks = Task::filter($filter)->where('to_do_list_id',$listId)->get();
    
        return $this->sendResponse(TaskResource::collection($tasks), 'tasks retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($listId,Request $request){
        $input = $request->all();
        $input["to_do_list_id"] = $listId;

        $validator = Validator::make($input, [
            'name' => 'required|max:255',
            'start' => 'required|date_format:H:i:s',
            'end' => 'required|date_format:H:i:s|after:start_date',
            'to_do_list_id' => 'required|exists:to_do_lists,id'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if(ToDoList::find($listId)->user_id != $request->user()->id){
            return $this->sendError('You are not authorized to create this task.');
        }

        if($this->checkIfDatesOverlap($input["start"], $input["end"],$listId)){
            return $this->sendError('Task overlaps with another task.');
        }

        $task = Task::create($input);

        return $this->sendResponse(new TaskResource($task), 'Task created successfully.');
    } 

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $task = Task::find($id);

        if (is_null($task)) {
            return $this->sendError('Task not found.');
        }

        return $this->sendResponse(new TaskResource($task), 'Task retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'max:255',
            'start' => 'date_format:H:i:s',
            'end' => 'date_format:H:i:s|after:start'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
 
        $task = Task::find($id);
        if (is_null($task)) {
            return $this->sendError('Task not found.');
        }

        if($task->to_do_list->user->id != $request->user()->id){
            return $this->sendError('You are not authorized to update this task.');
        }

        if($this->checkIfDatesOverlap($input["start"], $input["end"], $task->to_do_list_id)){
            return $this->sendError('Task overlaps with another task.');
        }

        $task->fill($input)->save();
   
        return $this->sendResponse(new TaskResource($task), 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task){
        $task->delete();

        return $this->sendResponse([], 'Task deleted successfully.');
    }

    private function checkIfDatesOverlap($start_input,$end_input,$listId){
        if(Task::where('start', '<=', $start_input)->where('end', '>=', $start_input)->where('to_do_list_id',$listId)->exists()){
            return true;
        }elseif (Task::where('start', '<=', $end_input)->where('end', '>=', $end_input)->where('to_do_list_id',$listId)->exists()) {
            return true;
        }elseif(Task::where('start', '>=', $start_input)->where('end', '<=', $end_input)->where('to_do_list_id',$listId)->exists()) {
            return true;
        }
        return false;
    }

}
?>