@extends('layouts.app')

@section('title', 'Updates')

@section('content')
<section class="max-w-5xl mx-auto p-6"
         x-data="{
            showModal: false,
            showEditModal: false,
            editPost: {}
         }">

    <div class="flex items-center justify-between mb-8 border-b border-gray-100 dark:border-gray-700 pb-6">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white uppercase tracking-tight">
                Crestec Philippines - Updates
            </h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Stay updated with our latest news and announcements.</p>
        </div>

        @auth
            <button @click="showModal = true"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-md">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Post
            </button>
        @endauth
    </div>

    <section id="archive-filter" class="mb-8">
        <div class="flex justify-start">
            <div x-data="{ open: false }" @click.away="open = false" class="relative">
                <button @click="open = !open"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-sm text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <span>Sort by Month</span>
                    <svg class="ms-2 -me-0.5 h-4 w-4 transition-transform" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <div x-show="open"
                     style="display: none;"
                     class="absolute left-0 z-50 mt-2 w-52 rounded-md shadow-lg origin-top-left bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                    <div class="py-1">
                        @php $currentYear = null; @endphp
                        @forelse($archives as $archive)
                            @if($currentYear !== $archive->year)
                                <div class="block px-4 py-2 text-xs text-gray-400 uppercase tracking-widest font-bold bg-gray-50 dark:bg-gray-900/50 border-y border-gray-100 dark:border-gray-700">
                                    {{ $archive->year }}
                                </div>
                                @php $currentYear = $archive->year; @endphp
                            @endif
                            <a href="{{ route('updates', ['year' => $archive->year, 'month' => $archive->month]) }}"
                               class="flex justify-between items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-gray-700 transition">
                                <span>{{ $archive->month }}</span>
                                <span class="text-xs font-mono bg-gray-100 dark:bg-gray-600 px-2 py-0.5 rounded text-gray-500 dark:text-gray-300">
                                    {{ $archive->post_count }}
                                </span>
                            </a>
                        @empty
                            <div class="px-4 py-2 text-sm text-gray-500 italic text-center">No dates available</div>
                        @endforelse
                        <div class="border-t border-gray-100 dark:border-gray-700 mt-1"></div>
                        <a href="{{ route('updates') }}" class="block px-4 py-2 text-sm text-indigo-600 font-bold hover:bg-gray-50 dark:hover:bg-gray-900 text-center">
                            View All Posts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($singlePost)
        <article class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
            <a href="{{ route('updates') }}" class="text-indigo-600 dark:text-indigo-400 font-bold text-sm mb-6 inline-flex items-center hover:underline">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back to all updates
            </a>

            <div class="text-xs text-indigo-500 font-bold uppercase tracking-widest mb-2">
                {{ \Carbon\Carbon::parse($singlePost->event_date)->format('M d, Y') }}
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-6 leading-tight">
                {{ $singlePost->title }}
            </h1>

            @if($singlePost->image && is_array($singlePost->image))
                <div x-data="{ activeImage: 0, images: {{ json_encode($singlePost->image) }} }" class="relative group mb-8">

                    <div class="relative overflow-hidden rounded-2xl shadow-lg bg-gray-100 dark:bg-gray-900">
                        <template x-for="(path, index) in images" :key="index">
                            <div x-show="activeImage === index">
                                <style>
                                [x-cloak] { display: none !important; }
                                </style>
                                <img :src="'/storage/' + path"
                                class="w-full h-28 object-contain bg-black"
                                alt=""
                                loading="lazy">
                            </div>
                        </template>

                    <template x-if="images.length > 1">
                        <div class="absolute inset-0 flex justify-between pointer-events-none">

                            <!-- LEFT SIDE -->
                            <button
                                @click="activeImage = activeImage === 0 ? images.length - 1 : activeImage - 1"
                                class="pointer-events-auto h-full w-1/4
                                       flex items-center justify-start pl-6
                                       bg-black/20 hover:bg-black/40
                                       transition opacity-0 group-hover:opacity-100"
                            >
                                <svg class="w-8 h-8 text-white"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <!-- RIGHT SIDE -->
                            <button
                                @click="activeImage = activeImage === images.length - 1 ? 0 : activeImage + 1"
                                class="pointer-events-auto h-full w-1/4
                                       flex items-center justify-end pr-6
                                       bg-black/20 hover:bg-black/40
                                       transition opacity-0 group-hover:opacity-100"
                            >
                                <svg class="w-8 h-8 text-white"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                        </div>
                    </template>

                    </div>

                    <div class="flex justify-center gap-2 mt-4">
                        <template x-for="(path, index) in images" :key="index">
                            <button @click="activeImage = index"
                                    :class="activeImage === index ? 'bg-indigo-600 w-6' : 'bg-gray-300 dark:bg-gray-600 w-2'"
                                    class="h-2 rounded-full transition-all duration-300"></button>
                        </template>
                    </div>
                </div>
            @endif

            <div class="prose prose-indigo dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed text-lg">
                {!! nl2br(e($singlePost->body)) !!}
            </div>
        </article>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($posts as $post)
                <article class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col transition hover:shadow-lg">
                    @if($post->image && is_array($post->image) && count($post->image) > 0)
                    <div class="relative w-full h-20 overflow-hidden rounded-2xl shadow-lg bg-gray-100 dark:bg-gray-900">
                        <img
                            src="{{ asset('storage/' . $post->image[0]) }}"
                            class="h-48 w-full object-contain"
                            alt="{{ $post->title }}"
                            loading="lazy">
                    </div>
                    @else
                        <div class="h-48 w-full bg-gray-200 flex items-center justify-center text-gray-400">
                            No Image Available
                        </div>
                    @endif

                    <div class="p-6 flex-1">
                        <div class="text-xs text-indigo-600 font-bold uppercase mb-2">
                            {{ \Carbon\Carbon::parse($post->event_date)->format('M d, Y') }}
                        </div>
                        <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mb-3">{{ $post->title }}</h3>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                            {{ $post->location ?? 'Location not specified' }}
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 leading-relaxed">
                            {{ Str::limit($post->body, 140) }}
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($post->tags)
                                @foreach($post->tags as $tag)
                                    <span class="text-xs bg-indigo-100 dark:bg-indigo-800 text-indigo-700 dark:text-indigo-200 px-2 py-1 rounded-full">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            @endif
                        </div>
                    </div>


                    <div class="px-6 py-4 mt-auto grid grid-cols-2 items-center border-t border-gray-50 dark:border-gray-700 min-h-20">
                        <!-- Left: Read Article -->
                        <div>
                            <a href="{{ route('updates', $post->slug) }}"
                               class="text-indigo-600 dark:text-indigo-400 font-bold text-sm hover:underline whitespace-nowrap">
                                Read Article →
                            </a>
                        </div>

                        <!-- Right: Edit/Delete buttons -->
                        <div class="flex justify-end space-x-2">
                            @auth
                                @if(auth()->id() === $post->user_id)
                                    <!-- Edit button -->
                                    <button
                                        @click="
                                            showEditModal = true;
                                            editPost = {{ $post->toJson() }};
                                            editPost.tagsString = editPost.tags ? editPost.tags.join(', ') : '';
                                        "
                                        class="text-gray-400 hover:text-indigo-600 p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5h2M12 7v10m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2h-3l-2-2h-4l-2 2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </button>

                                    <!-- Delete button -->
                                    <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 p-1.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>

                </article>
            @empty
                <div class="col-span-full text-center py-20 bg-gray-50 dark:bg-gray-800/50 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <p class="text-gray-500">No updates found for this selection.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif

<div x-show="showModal"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200">

    <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
        <div @click="showModal = false" class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-50">
            <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Create New Post</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input type="text" name="title" required class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Date</label>
                        <input type="date" name="event_date"
                               value="{{ date('Y-m-d') }}"
                               class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <input type="text" name="location"
                               class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-indigo-500"
                               placeholder="Location">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Tags</label>
                        <input type="text" name="tags" placeholder="e.g. news, tech, event"
                               class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-indigo-500">
                        <p class="text-[10px] text-gray-500 mt-1 italic">Separate tags with commas.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Content</label>
                        <textarea name="body" rows="5" required class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-indigo-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Images</label>
                        <input type="file" name="image[]" multiple
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-[10px] text-gray-500 mt-1 italic">You can select multiple images.</p>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-black bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md">
                        Publish Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div x-show="showEditModal"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200">

    <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
        <div @click="showEditModal = false"
             class="fixed inset-0 bg-gray-900 bg-opacity-60"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-50">
            <form method="POST"
                  :action="'/manage-posts/' + editPost.id"
                  enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Post</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-1">Title</label>
                        <input type="text"
                               name="title"
                               x-model="editPost.title"
                               class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1">Date</label>
                        <input type="date"
                               name="event_date"
                               :value="editPost.event_date"
                               class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1">Location</label>
                        <input type="text"
                               name="location"
                               x-model="editPost.location"
                               class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1">Tags</label>
                        <input type="text" name="tags"
                               x-model="editPost.tagsString"
                               placeholder="e.g. news, tech, event"
                               class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1">Content</label>
                        <textarea name="body"
                                  rows="5"
                                  x-model="editPost.body"
                                  class="w-full rounded-lg border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-white"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1">Add More Images</label>
                        <input type="file" name="image[]" multiple class="w-full text-sm text-gray-500">
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-3">
                    <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 text-sm font-bold bg-white dark:bg-gray-800 border rounded-lg">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold text-black bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md">
                        Update Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</section>
@endsection
