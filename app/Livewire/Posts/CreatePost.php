<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CreatePost extends Component
{
    // * untuk upload file
    // * use Livewire\WithFileUploads;
    use WithFileUploads;

    // * untuk pagination
    // * use Livewire\WithPagination;
    use WithPagination;

    // * jika ingin menggunakan form model binding
    // * Rule digunakan untuk validasi
    // * use Livewire\Attributes\Rule;
    #[Rule('required|min:3')]
    public $title;

    #[Rule('required|min:3')]
    public $content;

    #[Rule('in:0,1')]
    public $is_published = 0;

    #[Rule('in:0,1')]
    public $is_featured = 0;

    #[Rule('nullable|sometimes|image|max:1024')]
    public $featured_image;

    public $search;

    public function newPost()
    {
        // * validate hanya satu field
        // $validated = $this->validateOnly('title');

        // * validate semua field
        $validated = $this->validate();

        if ($this->featured_image) {
            $validated['featured_image'] = $this->featured_image->store('uploads', 'public');
        }

        Post::create($validated);

        // * reset beberapa field
        // $this->reset('title', 'content', 'is_published', 'is_featured', 'featured_image');

        // * reset semua field
        $this->reset();

        session()->flash('success', 'Post successfully created.');

        // * untuk kembali ke halaman awal pagination
        // $this->resetPage();
    }

    // * bisa menggunakan laravel menggunakan laravel model binding
    // * public function delete(Post $post)
    public function delete($postId)
    {
        $post = Post::find($postId);

        if ($post) {
            $post->delete();
            session()->flash('success', 'Post successfully deleted.');

            // * jika mau kembali ke halaman awal pagination pada saat hapus
            // $this->resetPage();
        }
    }

    public function render()
    {
        $posts = Post::latest()->where('title', 'like', "%{$this->search}%")->paginate(3);

        return view('livewire.posts.create-post', compact('posts'));
    }
}
