{{-- ── Post Delete OTP Modal ────────────────────────────────────────────── --}}
{{-- Include this at the bottom of your updates.blade.php / manage-posts view --}}

<div id="postOtpModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center px-4 py-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-sm flex flex-col">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-stone-200 dark:border-gray-700">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Delete Post</h2>
                <p class="text-xs text-stone-400 dark:text-gray-500 mt-0.5">Confirm with a one-time code</p>
            </div>
            <button onclick="closePostOtpModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-stone-100 dark:hover:bg-gray-800 text-stone-400 transition text-lg">&times;</button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p id="postOtpTitle" class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate"></p>

            {{-- Step 1: Send code --}}
            <div id="postOtpStep1">
                <p class="text-sm text-stone-500 dark:text-gray-400 mb-3">
                    A 6-digit code will be sent to your email to confirm deletion.
                </p>
                <div id="postOtpRequestError" class="hidden mb-2 px-3 py-2 rounded-xl bg-red-50 dark:bg-red-900/30 text-xs text-red-600 dark:text-red-400"></div>
                <button onclick="sendPostOtp()" id="sendPostOtpBtn"
                    class="w-full py-2.5 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 font-semibold rounded-xl hover:opacity-80 transition text-sm">
                    Send Code to My Email
                </button>
            </div>

            {{-- Step 2: Enter code --}}
            <div id="postOtpStep2" class="hidden space-y-3">
                <p id="postOtpSentMsg" class="text-xs text-green-600 dark:text-green-400 text-center"></p>
                <input type="text" id="postOtpInput" maxlength="6" placeholder="000000"
                    class="w-full px-4 py-3 text-center text-2xl font-bold tracking-widest rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
                <div id="postOtpVerifyError" class="hidden px-3 py-2 rounded-xl bg-red-50 dark:bg-red-900/30 text-xs text-red-600 dark:text-red-400"></div>
                <button onclick="verifyPostOtp()" id="verifyPostOtpBtn"
                    class="w-full py-2.5 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition text-sm">
                    Confirm Delete
                </button>
                <button onclick="resetPostOtpToStep1()" class="w-full py-1 text-xs text-stone-400 dark:text-gray-500 hover:underline">
                    Resend code
                </button>
            </div>
        </div>
        <div class="px-6 pb-5">
            <button onclick="closePostOtpModal()"
                class="w-full py-2.5 border border-stone-300 dark:border-gray-700 text-stone-600 dark:text-gray-400 font-medium rounded-xl hover:bg-stone-100 dark:hover:bg-gray-800 transition text-sm">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    let postOtpPending = { id: null, title: null, rowId: null };
    const POST_CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    function openPostOtpModal(postId, postTitle, rowId = null) {
        postOtpPending = { id: postId, title: postTitle, rowId };
        document.getElementById('postOtpTitle').textContent = `"${postTitle}"`;
        resetPostOtpToStep1();
        document.getElementById('postOtpModal').classList.remove('hidden');
    }

    function closePostOtpModal() {
        document.getElementById('postOtpModal').classList.add('hidden');
        postOtpPending = { id: null, title: null, rowId: null };
    }

    function resetPostOtpToStep1() {
        document.getElementById('postOtpStep1').classList.remove('hidden');
        document.getElementById('postOtpStep2').classList.add('hidden');
        document.getElementById('postOtpRequestError').classList.add('hidden');
        document.getElementById('postOtpVerifyError').classList.add('hidden');
        document.getElementById('postOtpInput').value = '';
    }

    async function sendPostOtp() {
        const btn = document.getElementById('sendPostOtpBtn');
        btn.disabled = true; btn.textContent = 'Sending...';
        document.getElementById('postOtpRequestError').classList.add('hidden');
        try {
            const res  = await fetch('/otp/send', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': POST_CSRF, 'Accept': 'application/json' },
                body:    JSON.stringify({ type: 'post', target_id: postOtpPending.id }),
            });
            const data = await res.json();
            if (!res.ok) {
                const err = document.getElementById('postOtpRequestError');
                err.textContent = data.error || 'Failed to send code.';
                err.classList.remove('hidden');
                return;
            }
            document.getElementById('postOtpSentMsg').textContent = data.message;
            document.getElementById('postOtpStep1').classList.add('hidden');
            document.getElementById('postOtpStep2').classList.remove('hidden');
            document.getElementById('postOtpInput').focus();
        } catch (e) {
            const err = document.getElementById('postOtpRequestError');
            err.textContent = 'Network error. Please try again.';
            err.classList.remove('hidden');
        } finally {
            btn.disabled = false; btn.textContent = 'Send Code to My Email';
        }
    }

    async function verifyPostOtp() {
        const code = document.getElementById('postOtpInput').value.trim();
        if (code.length !== 6) {
            const err = document.getElementById('postOtpVerifyError');
            err.textContent = 'Please enter the 6-digit code.';
            err.classList.remove('hidden');
            return;
        }
        const btn = document.getElementById('verifyPostOtpBtn');
        btn.disabled = true; btn.textContent = 'Verifying...';
        document.getElementById('postOtpVerifyError').classList.add('hidden');
        try {
            const res  = await fetch('/otp/verify', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': POST_CSRF, 'Accept': 'application/json' },
                body:    JSON.stringify({ type: 'post', target_id: postOtpPending.id, code }),
            });
            const data = await res.json();
            if (!res.ok) {
                const err = document.getElementById('postOtpVerifyError');
                err.textContent = data.error || 'Invalid code.';
                err.classList.remove('hidden');
                return;
            }
            closePostOtpModal();
            // Remove post card from page or reload
            if (postOtpPending.rowId) {
                const el = document.getElementById(postOtpPending.rowId);
                if (el) { el.style.transition = 'opacity 0.3s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }
            } else {
                window.location.reload();
            }
        } catch (e) {
            const err = document.getElementById('postOtpVerifyError');
            err.textContent = 'Network error. Please try again.';
            err.classList.remove('hidden');
        } finally {
            btn.disabled = false; btn.textContent = 'Confirm Delete';
        }
    }
</script>
