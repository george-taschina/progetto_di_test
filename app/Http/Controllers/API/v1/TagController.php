<?php
namespace App\Http\Controllers\API\v1;


use Illuminate\Http\Request;
use App\Http\Controllers\API\v1\BaseController as BaseController;
use App\Models\Tag;
use App\Models\Task;
use Validator;
use App\Http\Resources\v1\TagResource;

class TagController extends BaseController
{
    /**
     * Display a taging of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $tags = Tag::all();
    
        return $this->sendResponse(TagResource::collection($tags), 'tags retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|max:255|unique:tags'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $tag = Tag::create($input);

        return $this->sendResponse(new TagResource($tag), 'Tag created successfully.');
    } 

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $tag = Tag::find($id);

        if (is_null($tag)) {
            return $this->sendError('Tag not found.');
        }

        return $this->sendResponse(new TagResource($tag), 'Tag retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag){
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|max:255|unique:tags'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $tag->name = $input['name'];
        $tag->save();
   
        return $this->sendResponse(new TagResource($tag), 'Tag updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag){
        $tag->tasks()->detach();
        $tag->delete();

        return $this->sendResponse([], 'Tag deleted successfully.');
    }

    public function attach($taskId, $tagId){
        $task = Task::find($taskId);
        $tag = Tag::find($tagId);

        if (is_null($task)) {
            return $this->sendError('Task not found.');
        }

        if (is_null($tag)) {
            return $this->sendError('Tag not found.');
        }

        $task->tags()->attach($tag);

        return $this->sendResponse([], 'Tag attached successfully.');
    }

    public function detach($taskId, $tagId){
        $task = Task::find($taskId);
        $tag = Tag::find($tagId);

        if (is_null($task)) {
            return $this->sendError('Task not found.');
        }

        if (is_null($tag)) {
            return $this->sendError('Tag not found.');
        }

        $task->tags()->detach($tag);

        return $this->sendResponse([], 'Tag detached successfully.');
    }

}
?>