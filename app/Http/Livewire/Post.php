<?php

namespace App\Http\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Post extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $title, $content, $postId, $slug, $status, $updatePost = false, $addPost = false;

    protected $listeners = [
        'deletePostListener' => 'deletePost'
    ];

    protected $rules = [
        'title' => 'required',
        'content' => 'required',
        'status' => 'required'
    ];

    public function resetFields()
    {
        $this->title = '';
        $this->content = '';
        $this->status = 1;
    }

    public function render()
    {

        return view('livewire.post', [
            'posts' => \App\Models\Post::paginate(10),
        ]);
    }

    public function addPost()
    {
        $this->resetFields();
        $this->addPost = true;
        $this->updatePost = false;
    }

    public function storePost()
    {
        $this->validate();
        try {
            \App\Models\Post::create([
                'title' => $this->title,
                'content' => $this->content,
                'status' => $this->status,
                'slug' => Str::slug($this->title)
            ]);

            session()->flash('success', 'Post created successfully');
            $this->resetFields();
            $this->addPost = false;
        } catch (\Exception $exception) {
            session()->flash('error', 'Something goes wrong');
        }
    }

    public function editPost($id)
    {
        try {
            $post = \App\Models\Post::findOrFail($id);

            if (! $post) {
                session()->flash('error', 'Post not found');
            } else {
                $this->title = $post->title;
                $this->content = $post->content;
                $this->status = $post->status;
                $this->postId = $post->id;
                $this->updatePost = true;
                $this->addPost = false;
            }
        } catch (\Exception $exception) {
            session()->flash('error', 'Something goes wrong');
        }
    }

    public function updatePost()
    {
        $this->validate();

        try {
            \App\Models\Post::whereId($this->postId)
                ->update([
                    'title' => $this->title,
                    'content' => $this->content,
                    'status' => $this->status,
                    'slug' => Str::slug($this->title)
                ]);

            session()->flash('success', 'Post updated successfully');
            $this->resetFields();
            $this->updatePost = false;

        } catch (\Exception $exception) {
            session()->flash('success', 'Something goes wrong');
        }
    }

    public function cancelPost()
    {
        $this->addPost = false;
        $this->updatePost = false;
        $this->resetFields();
    }

    public function deletePost($id)
    {
        try {
            \App\Models\Post::find($id)->delete();
            session()->flash('success', 'Post deleted successfully');
        } catch (\Exception $exception) {
            session()->flash('error', 'Something goes wrong');
        }
    }
}
