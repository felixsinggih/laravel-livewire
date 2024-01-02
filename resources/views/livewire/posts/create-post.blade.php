<div>
    @if (session('success'))
        <div id="flash-message" class="row">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {{-- wire:offline digunakan untuk menampilkan elemen jika koneksi internet terputus --}}
    <div wire:offline>
        <div id="offline-warning" class="row">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                You're offline. Please check your internet connection.
            </div>
        </div>
    </div>

    {{--
        wire:poll digunakan untuk mengaktifkan fitur polling, misalnya untuk menampilkan data secara realtime
        wire:poll.5s artinya polling akan dijalankan setiap 5 detik
        default wire:poll adalah 2.5 detik
        wire:poll.keep-alive digunakan untuk menjaga polling tetap berjalan meskipun ada perubahan pada komponen lain
        wire:poll.visible digunakan untuk menjalankan polling hanya ketika komponen terlihat
    --}}
    {{--
        wire.poll dinonaktikan karena mengganggu loading state
        jika diaktikan akan mengakibatkan loading state selalu berjalan ketika melakukan wire:model pada suatu elemen
    --}}
    {{-- <div wire:poll.5s class="row"> --}}
    <div class="row">
        <div class="col-md-6">
            <div id="post-create">
                <form wire:submit="newPost">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input wire:model="title" type="text"
                            class="form-control @error('title') is-invalid @enderror">
                        @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea wire:model="content" class="form-control @error('content') is-invalid @enderror" rows="3"></textarea>
                        @error('content')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Published</label>
                        <select wire:model="is_published"
                            class="form-select @error('is_published') is-invalid @enderror"
                            aria-label="Default select example">
                            <option selected>Select</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        @error('is_published')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Featured Image</label>
                        <input wire:model="featured_image" accept="image/png, image/jpeg"
                            class="form-control @error('featured_image') is-invalid @enderror" type="file">
                        @error('featured_image')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- menampilkan loading state gambar yang diupload --}}
                        <div wire:loading wire:target="featured_image">
                            <span class="text-primary">Uploading image...</span>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input wire:model="is_featured" type="checkbox"
                            class="form-check-input @error('is_featured') is-invalid @enderror">
                        <label class="form-check-label">Featured</label>
                        @error('is_featured')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{--
                        wire:loading.attr digunakan untuk menambahkan atribut pada suatu elemen ketika proses loading
                        wire:loading.remove digunakan untuk menghapus suatu elemen ketika proses loading

                        secara otomatis walau tanpa menambahkan wire:loading.attr jika tombol berada di dalam form
                        maka secara otomatis tombol akan dinonaktifkan ketika proses submit form
                    --}}
                    <button wire:loading.attr="disabled" type="submit" class="btn btn-primary">Submit</button>

                    {{--
                        menampilkan loading state ketika proses submit form
                        delay digunakan untuk menunda loading state selama 200ms
                        wire:loading.delay.longer
                        masih banyak parameter lain yang bisa digunakan untuk wire:loading
                    --}}
                    <div wire:loading.delay>
                        <span class="text-primary">Sending data...</span>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6 mt-3">
            <div id="search-box" class="input-group mb-3">
                <span class="input-group-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-search" viewBox="0 0 16 16">
                        <path
                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                </span>
                {{--
                    live digunakan untuk mengaktifkan fitur live wire
                    debounce digunakan untuk mengatur waktu delay sebelum proses search dijalankan
                    debounce.500ms artinya proses search akan dijalankan setelah 500ms
                    default debounce adalah 150ms
                --}}
                <input wire:model.live.debounce.500ms="search" type="text" class="form-control"
                    placeholder="Search post">
            </div>

            <div id="posts-list">
                @forelse ($posts as $post)
                    <div wire:key="{{ $post->id }}" class="card mb-3">
                        <div class="align-items-center d-flex px-3 pt-3">
                            <div class="col-10">
                                <h5 class="card-title">{{ $post->title }}</h5>
                            </div>
                            <div class="col-2">
                                {{-- $post->id bisa diganti dengan $post jika menggunakan model binding --}}
                                <button wire:click="delete({{ $post->id }})" class="btn btn-outline-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                        <path
                                            d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ $post->created_at }}</p>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning" role="alert">
                        No posts found.
                    </div>
                @endforelse

                <div class="text-right">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
