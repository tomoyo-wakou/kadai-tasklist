<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;       // 名前空間使用

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
     // getでtasks/にアクセスされた場合の「一覧表示処理
    public function index()
    {
        $data = [];
        // 認証済みの場合
        if(\Auth::check()) {    
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得
            $tasks = $user->tasks()->orderBy("created_at", "desc")->Paginate(5);
            
            $data = [
                "user" => $user,
                "tasks" => $tasks,
                ];
        }
        
            return view("tasks.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     // getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
    public function create()
    {
        
        $task = new Task;
        // 認証済みの場合
        if (\Auth::check()) {
        // タスク作成ビューを表示
        return view("tasks.create", [
            "task" => $task,
        ]);
        }
        else {
            return redirect("/");
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     // postでtasks/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            "status" => "required|max:10",      // 空欄不可尚且つ10文字以上の文字数不可
            "content" => "required|max:255",        // 空欄での投稿不可
        ]);
        
        // タスクを作成
        $task = new Task;
        $task->status = $request->status;
        $task->content = $request->content;
        
        // User_idとidの紐づけ
        \Auth::user()->tasks()->save($task);
        
        // 前のURLへリダイレクトさせる
        return redirect("/");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     // getでtasks/（任意のid）にアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        
        // idの値でタスクを検索して取得
        $task = \App\Task::findOrFail($id);
        // 認証済みの場合
        if (\Auth::id() === $task->user_id) {
        // タスク詳細ビューでそれを表示
        return view("tasks.show", [
            "task" => $task,
        ]);
        }
        
        // 認証済みユーザ（閲覧者）以外がアクセスした場合は、トップページに
        else {
            return redirect("/");
        
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     // getでtasks/（任意のid）/editにアクセスされた場合の「更新画面表示処理
    public function edit($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        if (\Auth::id() === $task->user_id) {
        // タスク編集ビューでそれを表示
        return view("tasks.edit", [
            "task" => $task,
            ]);
        }
        // 認証済みユーザ（閲覧者）以外がアクセスした場合は、トップページに
        else {
            return redirect("/");
        
        }
        
        
        }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     // putまたはpatchでtasks/（任意のid）にアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
        // バリデーション
        $request->validate([
            "status" => "required|max:10",
            "content" => "required|max:255",
        ]);
        
        // idの値でタスクを検索して取得
        $task = \App\Task::findOrFail($id);
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、タスクを更新
        if (\Auth::id() === $task->user_id) {
            $task->status = $request->status;
            $task->content = $request->content;
        
            $task->save();
        }
        
        // トップページへリダイレクトさせる
        return redirect("/");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     // deleteでtasks/（任意のid）にアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        // idの値でタスクを検索して取得
        $task = \App\Task::findOrFail($id);
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、タスクを削除
        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }
        
        
        // トップページへリダイレクトさせる
        return redirect("/");
    }
}
