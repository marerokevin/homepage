<x-app-layout>
    <div class="max-w-7xl mx-auto p-6" x-data="{ showModal: false }">

        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200 uppercase tracking-tight">Our Blog Updates</h2>

            @auth
                <button @click="showModal = true"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150 shadow-md">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    New Post
                </button>
            @endauth
        </div>

        <div class="flex flex-col lg:flex-row gap-8">

            <div class="flex-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($posts as $post)
                        <article class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col transition hover:shadow-lg">
                            @if($post->image)
                                <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="h-48 w-full object-cover">
                            @else
                                <div class="h-48 w-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif

                            <div class="p-6 flex-1">
                                <div class="text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-widest mb-2">
                                    {{ $post->created_at->format('M d, Y') }}
                                </div>

                                <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                    {{ $post->title }}
                                </h3>

                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed line-clamp-3">
                                    {{ Str::limit($post->body, 120) }}
                                </p>
                            </div>

                            <div class="px-6 pb-6 mt-auto flex justify-between items-center">
                                <a href="{{ route('posts.show', $post) }}" class="text-indigo-600 dark:text-indigo-400 font-bold text-sm hover:text-indigo-800 flex items-center group">
                                    Read Article
                                    <svg class="w-4 h-4 ms-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>

                                @auth
                                    @if(auth()->id() === $post->user_id)
                                        <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full text-center py-20 bg-gray-50 dark:bg-gray-900/50 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                            <p class="text-gray-500 dark:text-gray-400">No stories found for this period.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            </div>

            <aside class="w-full lg:w-64">
                <div class="sticky top-6 space-y-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Archives</h3>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($archives ?? [] as $archive)
                                <a href="{{ route('updates', ['year' => $archive->year, 'month' => $archive->month]) }}"
                                   class="flex items-center justify-between px-4 py-3 text-sm transition-colors hover:bg-indigo-50 dark:hover:bg-gray-700 group {{ request('month') == $archive->month ? 'bg-indigo-50 dark:bg-gray-700 border-l-4 border-indigo-600' : 'text-gray-600 dark:text-gray-300' }}">
                                    <span class="group-hover:text-indigo-600 dark:group-hover:text-indigo-400">{{ $archive->month }} {{ $archive->year }}</span>
                                    <span class="text-xs font-mono bg-gray-100 dark:bg-gray-600 px-2 py-0.5 rounded text-gray-500 dark:text-gray-300">
                                        {{ $archive->post_count }}
                                    </span>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-sm text-gray-500 italic">No dates available.</div>
                            @endforelse
                        </div>
                        @if(request()->has('month') || request()->has('year'))
                            <a href="{{ route('updates') }}" class="block text-center py-3 text-xs font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors uppercase">
                                Clear Filters
                            </a>
                        @endif
                    </div>

                    <a href="{{ route('updates') }}" class="flex items-center justify-center w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-700 dark:text-gray-300 hover:border-indigo-500 transition-all shadow-sm">
                        View All Posts
                    </a>
                </div>
            </aside>

        </div>

        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
             </div>
    </div>
</x-app-layout>
