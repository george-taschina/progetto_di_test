<?php
namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\v1\BaseController as BaseController;
use App\Models\ToDoList;
use Validator;
use App\Http\Resources\v1\ToDoListResource;
use App\Http\Filters\ToDoListFilter;
use Illuminate\Validation\Rule;

class ToDoListController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ToDoListFilter $filter){
        $lists = ToDoList::filter($filter)->get();
        
        return $this->sendResponse(ToDoListResource::collection($lists), 'lists retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $input = $request->all();
        $input["user_id"] = $request->user()->id;

        $validator = Validator::make($input, [
            'name' => 'required|max:255',
            'date' => 'required|unique:to_do_lists,date,NULL,id,user_id,' . $request->user()->id //date is unique for the user
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $list = ToDoList::create($input);

        return $this->sendResponse(new ToDoListResource($list), 'List created successfully.');
    } 

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $list = ToDoList::find($id);

        if (is_null($list)) {
            return $this->sendError('List not found.');
        }

        return $this->sendResponse(new ToDoListResource($list), 'List retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ToDoList $list){
        $input = $request->all();
        $input["user_id"] = $request->user()->id;

        $validator = Validator::make($input, [
            'name' => 'max:255',
            'date' => 'unique:to_do_lists,date,NULL,id,user_id,' . $request->user()->id, //date is unique for the user
            'user_id' => ['required',Rule::in($list->user_id),] //user_id is the same as the authenticated user
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $list->fill($input)->save();
   
        return $this->sendResponse(new ToDoListResource($list), 'List updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ToDoList $list){
        $list->delete();

        return $this->sendResponse([], 'List deleted successfully.');
    }

}
?>