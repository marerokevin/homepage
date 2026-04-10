@extends('layouts.app')

@section('title', 'Calendar')

@section('content')

<div class="min-h-screen bg-stone-100 dark:bg-gray-950 py-10 px-4">
    <div class="max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Calendar</h1>
            <div class="flex items-center gap-3">
                @if(Auth::user()->is_admin)
                <a href="{{ route('reports.vehicle') }}"
                    class="px-4 py-1.5 text-sm font-semibold border border-stone-300 dark:border-gray-700 rounded-full text-stone-600 dark:text-gray-400 hover:bg-gray-900 hover:text-white dark:hover:bg-gray-100 dark:hover:text-gray-900 transition-all duration-200">
                    📋 Reports
                </a>
                @endif
                <button onclick="goToday()"
                    class="px-4 py-1.5 text-sm font-semibold border border-stone-300 dark:border-gray-700 rounded-full text-stone-600 dark:text-gray-400 hover:bg-gray-900 hover:text-white dark:hover:bg-gray-100 dark:hover:text-gray-900 transition-all duration-200">
                    Today
                </button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-2 mb-6">
            <button id="tab-vehicle" onclick="switchTab('vehicle')"
                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900">
                🚗 Vehicle Trips
            </button>
            <button id="tab-rooms" onclick="switchTab('rooms')"
                class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 border border-stone-300 dark:border-gray-700 text-stone-600 dark:text-gray-400 hover:bg-gray-900 hover:text-white dark:hover:bg-gray-100 dark:hover:text-gray-900">
                🏢 Conference Rooms
            </button>
        </div>

        {{-- Month Navigation --}}
        <div class="flex items-center justify-center gap-6 mb-6">
            <button onclick="changeMonth(-1)"
                class="w-10 h-10 rounded-full border-2 border-stone-300 dark:border-gray-700 flex items-center justify-center text-stone-600 dark:text-gray-400 hover:bg-gray-900 hover:text-white hover:border-gray-900 dark:hover:bg-gray-100 dark:hover:text-gray-900 transition-all duration-200">
                &#8592;
            </button>
            <span id="monthLabel" class="text-2xl font-bold text-gray-900 dark:text-gray-100 w-56 text-center"></span>
            <button onclick="changeMonth(1)"
                class="w-10 h-10 rounded-full border-2 border-stone-300 dark:border-gray-700 flex items-center justify-center text-stone-600 dark:text-gray-400 hover:bg-gray-900 hover:text-white hover:border-gray-900 dark:hover:bg-gray-100 dark:hover:text-gray-900 transition-all duration-200">
                &#8594;
            </button>
        </div>

        {{-- Calendar --}}
        <div class="rounded-2xl overflow-hidden shadow-lg border border-stone-200 dark:border-gray-800">
            <div class="bg-gray-900 dark:bg-gray-950" style="display:grid; grid-template-columns:repeat(7,minmax(0,1fr))">
                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                <div class="text-center py-3 text-xs font-semibold tracking-widest uppercase {{ $day === 'Sun' ? 'text-red-400' : 'text-stone-300' }}">
                    {{ $day }}
                </div>
                @endforeach
            </div>
            <div id="calGrid" class="gap-px bg-stone-200 dark:bg-gray-800" style="display:grid; grid-template-columns:repeat(7,minmax(0,1fr)); grid-auto-rows:1fr;"></div>
        </div>

        {{-- Room legend --}}
        <div id="roomLegend" class="hidden mt-4 flex flex-wrap gap-3">
            <span class="flex items-center gap-1.5 text-xs text-stone-500 dark:text-gray-400"><span class="w-3 h-3 rounded-full bg-purple-400 inline-block"></span> ODP Conference Room</span>
            <span class="flex items-center gap-1.5 text-xs text-stone-500 dark:text-gray-400"><span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span> Admin Conference Room</span>
            <span class="flex items-center gap-1.5 text-xs text-stone-500 dark:text-gray-400"><span class="w-3 h-3 rounded-full bg-pink-400 inline-block"></span> Lobby - A</span>
            <span class="flex items-center gap-1.5 text-xs text-stone-500 dark:text-gray-400"><span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span> Lobby - B</span>
        </div>

    </div>
</div>

{{-- Vehicle Trip Modal --}}
<div id="eventModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center px-4 py-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-xl flex flex-col" style="max-height:90vh;">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-stone-200 dark:border-gray-700 shrink-0">
            <div>
                <h2 id="modalDateTitle" class="text-xl font-bold text-gray-900 dark:text-gray-100"></h2>
                <p class="text-xs text-stone-400 dark:text-gray-500 mt-0.5">Trip / Vehicle Request</p>
            </div>
            <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-stone-100 dark:hover:bg-gray-800 text-stone-400 hover:text-stone-700 dark:hover:text-gray-200 transition text-lg">&times;</button>
        </div>
        <div class="overflow-y-auto px-6 py-4 space-y-5 flex-1">
            <div id="tripList" class="space-y-2"></div>
            <div class="border-t border-stone-200 dark:border-gray-700 pt-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-stone-400 dark:text-gray-500 mb-4">New Trip Request</p>
            </div>
            <div id="tripError" class="hidden px-3 py-2 rounded-xl bg-red-50 dark:bg-red-900/30 text-xs text-red-600 dark:text-red-400"></div>
            <div id="bookedForSection" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">👤 Book on behalf of</label>
                <select id="bookedFor" class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100">
                    <option value="">— Booking for myself —</option>
                </select>
                <p class="text-xs text-stone-400 dark:text-gray-500 mt-1">You will appear as the requester. The manager's name will be noted.</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">📍 Place of Pickup</label>
                <input type="text" id="tripPickup" value="Crestec Philippines, Inc., Lima Technology Center, Lipa City, Batangas"
                    class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">🏁 Destination</label>
                <input type="text" id="tripDestination" placeholder="Enter destination address..."
                    class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">🚗 Vehicle</label>
                    <select id="tripVehicle" class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100">
                        <option value="">Select vehicle...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Driver</label>
                    <select id="tripDriver" class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100">
                        <option value="">Select driver...</option>
                    </select>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">🕐 Departure</label>
                    <input type="time" id="tripDeparture" onchange="detectOvernight()"
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">📥 ETA</label>
                    <input type="time" id="tripETA"
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">🔁 Est. Return</label>
                    <input type="time" id="tripReturn" onchange="detectOvernight()"
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
                </div>
            </div>
            <div id="overnightNotice" class="hidden px-3 py-2 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-xs text-amber-700 dark:text-amber-300">
                🌙 Overnight trip detected — return date set to <strong id="overnightReturnDate"></strong>
                <input type="hidden" id="tripReturnDate" />
            </div>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t border-stone-200 dark:border-gray-700 shrink-0">
            <button onclick="saveTrip()" id="saveTripBtn"
                class="flex-1 py-2.5 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 font-semibold rounded-xl hover:opacity-80 transition text-sm">
                Save Trip Request
            </button>
            <button onclick="closeModal()"
                class="px-5 py-2.5 border border-stone-300 dark:border-gray-700 text-stone-600 dark:text-gray-400 font-medium rounded-xl hover:bg-stone-100 dark:hover:bg-gray-800 transition text-sm">
                Cancel
            </button>
        </div>
    </div>
</div>

{{-- Conference Room Booking Modal --}}
<div id="roomModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center px-4 py-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-xl flex flex-col" style="max-height:90vh;">
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-stone-200 dark:border-gray-700 shrink-0">
            <div>
                <h2 id="roomModalDateTitle" class="text-xl font-bold text-gray-900 dark:text-gray-100"></h2>
                <p class="text-xs text-stone-400 dark:text-gray-500 mt-0.5">Conference Room Booking</p>
            </div>
            <button onclick="closeRoomModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-stone-100 dark:hover:bg-gray-800 text-stone-400 hover:text-stone-700 dark:hover:text-gray-200 transition text-lg">&times;</button>
        </div>
        <div class="overflow-y-auto px-6 py-4 space-y-5 flex-1">
            <div id="roomBookingList" class="space-y-2"></div>
            <div class="border-t border-stone-200 dark:border-gray-700 pt-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-stone-400 dark:text-gray-500 mb-4">New Room Booking</p>
            </div>
            <div id="roomError" class="hidden px-3 py-2 rounded-xl bg-red-50 dark:bg-red-900/30 text-xs text-red-600 dark:text-red-400"></div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">🏢 Conference Room</label>
                <select id="roomName" class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100">
                    <option value="">Select a room...</option>
                    <option value="ODP Conference Room">ODP Conference Room</option>
                    <option value="Admin Conference Room">Admin Conference Room</option>
                    <option value="Lobby - A">Lobby - A</option>
                    <option value="Lobby - B">Lobby - B</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">📝 Purpose / Meeting Title</label>
                <input type="text" id="roomTitle" placeholder="e.g. Weekly Sync, Client Meeting..."
                    class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">🕐 Time</label>
                <div class="flex items-center gap-2">
                    <select id="roomStartTime" onchange="computeDurationLabel()"
                        class="px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100">
                    </select>
                    <span class="text-gray-400 font-bold px-1">–</span>
                    <select id="roomEndTime" onchange="computeDurationLabel()"
                        class="px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100">
                    </select>
                </div>
                <div class="mt-1.5">
                    <span id="roomDurationLabel" class="text-xs text-stone-400 dark:text-gray-500"></span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">👥 Number of Attendees</label>
                <input type="number" id="roomAttendees" min="1" value="1"
                    class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
            </div>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t border-stone-200 dark:border-gray-700 shrink-0">
            <button onclick="saveRoomBooking()" id="saveRoomBtn"
                class="flex-1 py-2.5 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 font-semibold rounded-xl hover:opacity-80 transition text-sm">
                Save Booking
            </button>
            <button onclick="closeRoomModal()"
                class="px-5 py-2.5 border border-stone-300 dark:border-gray-700 text-stone-600 dark:text-gray-400 font-medium rounded-xl hover:bg-stone-100 dark:hover:bg-gray-800 transition text-sm">
                Cancel
            </button>
        </div>
    </div>
</div>

{{-- OTP Cancellation Modal --}}
<div id="otpModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] flex items-center justify-center px-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-4">
        <div class="text-center">
            <div class="text-3xl mb-2">🔐</div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Confirm Cancellation</h3>
            <p id="otpMessage" class="text-xs text-stone-400 dark:text-gray-500 mt-1"></p>
        </div>
        <div id="otpStep1">
            <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-3">
                A 6-digit code will be sent to your email to confirm this cancellation.
            </p>
            <div id="otpRequestError" class="hidden mb-2 px-3 py-2 rounded-xl bg-red-50 dark:bg-red-900/30 text-xs text-red-600 dark:text-red-400"></div>
            <button onclick="requestOtp()" id="requestOtpBtn"
                class="w-full py-2.5 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 font-semibold rounded-xl hover:opacity-80 transition text-sm">
                Send Code to My Email
            </button>
        </div>
        <div id="otpStep2" class="hidden space-y-3">
            <p id="otpSentMsg" class="text-xs text-green-600 dark:text-green-400 text-center"></p>
            <input type="text" id="otpInput" maxlength="6" placeholder="Enter 6-digit code"
                class="w-full px-4 py-3 text-center text-2xl font-bold tracking-widest rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
            <div id="otpVerifyError" class="hidden px-3 py-2 rounded-xl bg-red-50 dark:bg-red-900/30 text-xs text-red-600 dark:text-red-400"></div>
            <button onclick="verifyOtp()" id="verifyOtpBtn"
                class="w-full py-2.5 bg-red-600 text-white font-semibold rounded-xl hover:opacity-80 transition text-sm">
                Confirm Cancel
            </button>
            <button onclick="resetOtpToStep1()" class="w-full py-1 text-xs text-stone-400 dark:text-gray-500 hover:underline">
                Resend code
            </button>
        </div>
        <button onclick="closeOtpModal()"
            class="w-full py-2 border border-stone-300 dark:border-gray-700 text-stone-500 dark:text-gray-400 font-medium rounded-xl hover:bg-stone-100 dark:hover:bg-gray-800 transition text-sm">
            Keep
        </button>
    </div>
</div>

<script>
    const MONTHS           = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const CURRENT_USER_ID  = {{ Auth::id() }};
    const IS_ADMIN         = {{ Auth::user()->is_admin ? 'true' : 'false' }};
    const IS_VEHICLE_ADMIN = {{ Auth::user()->is_vehicle_admin ? 'true' : 'false' }};
    const CSRF_TOKEN       = '{{ csrf_token() }}';
    const DEFAULT_PICKUP   = 'Crestec Philippines, Inc., Lima Technology Center, Lipa City, Batangas';

    const ROOM_COLORS = {
        'ODP Conference Room':   'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
        'Admin Conference Room': 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'Lobby - A':             'bg-pink-100 text-pink-700 dark:bg-pink-900 dark:text-pink-300',
        'Lobby - B':             'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
    };

    let currentDate  = new Date();
    let currentYear  = currentDate.getFullYear();
    let currentMonth = currentDate.getMonth();
    let selectedDate = null;
    let tripsCache   = {};
    let roomsCache   = {};
    let activeTab    = 'vehicle';

    function dateKey(year, month, day) {
        return `${year}-${String(month + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
    }
    function localDateKey(date) {
        return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
    }
    function todayStr() { return localDateKey(new Date()); }

    // ── Tab switching ──────────────────────────────────────────────
    function switchTab(tab) {
        activeTab = tab;
        const active   = 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900';
        const inactive = 'border border-stone-300 dark:border-gray-700 text-stone-600 dark:text-gray-400 hover:bg-gray-900 hover:text-white dark:hover:bg-gray-100 dark:hover:text-gray-900';
        document.getElementById('tab-vehicle').className = `px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 ${tab==='vehicle' ? active : inactive}`;
        document.getElementById('tab-rooms').className   = `px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 ${tab==='rooms' ? active : inactive}`;
        document.getElementById('roomLegend').classList.toggle('hidden', tab !== 'rooms');
        fetchAll();
    }

    // ── Data fetching ──────────────────────────────────────────────
    async function fetchAll() {
        await Promise.all([fetchTrips(), fetchRooms()]);
        renderCalendar();
    }

    async function fetchTrips() {
        try {
            const res  = await fetch(`/vehicle-requests?month=${currentMonth+1}&year=${currentYear}`);
            const data = await res.json();
            tripsCache = {};
            data.forEach(trip => {
                const start = new Date(trip.trip_date+'T00:00:00');
                const end   = new Date((trip.return_date||trip.trip_date)+'T00:00:00');
                const cur   = new Date(start);
                while (cur <= end) {
                    const key = localDateKey(cur);
                    if (!tripsCache[key]) tripsCache[key] = [];
                    if (!tripsCache[key].find(t => t.id===trip.id)) tripsCache[key].push(trip);
                    cur.setDate(cur.getDate()+1);
                }
            });
        } catch(e) { console.error('Failed to load trips',e); }
    }

    async function fetchRooms() {
        try {
            const res  = await fetch(`/room-bookings?month=${currentMonth+1}&year=${currentYear}`);
            const data = await res.json();
            roomsCache = {};
            data.forEach(b => {
                if (!roomsCache[b.booking_date]) roomsCache[b.booking_date] = [];
                roomsCache[b.booking_date].push(b);
            });
        } catch(e) { console.error('Failed to load rooms',e); }
    }

    // ── Calendar rendering ─────────────────────────────────────────
    function renderCalendar() {
        document.getElementById('monthLabel').textContent = `${MONTHS[currentMonth]} ${currentYear}`;
        const grid      = document.getElementById('calGrid');
        grid.innerHTML  = '';
        const firstDay    = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth+1, 0).getDate();
        const daysInPrev  = new Date(currentYear, currentMonth, 0).getDate();
        const today       = new Date();
        const isSameMonth = today.getFullYear()===currentYear && today.getMonth()===currentMonth;
        let col = 0;
        for (let i=firstDay-1; i>=0; i--) grid.appendChild(createCell(daysInPrev-i,true,false,currentYear,currentMonth-1,daysInPrev-i,col++%7));
        for (let d=1; d<=daysInMonth; d++) grid.appendChild(createCell(d,false,isSameMonth&&d===today.getDate(),currentYear,currentMonth,d,col++%7));
        const rem = (7-grid.children.length%7)%7;
        for (let d=1; d<=rem; d++) grid.appendChild(createCell(d,true,false,currentYear,currentMonth+1,d,col++%7));
    }

    function createCell(day,otherMonth,isToday,year,month,d,colIdx) {
        const cell     = document.createElement('div');
        const isSunday = colIdx===0;
        let cls = 'p-2 transition-colors duration-150 h-32 ';
        if (otherMonth)   cls += 'bg-stone-50 dark:bg-gray-900 ';
        else if (isToday) cls += 'bg-amber-50 dark:bg-gray-800 cursor-pointer hover:bg-amber-100 dark:hover:bg-gray-700 ';
        else              cls += 'bg-white dark:bg-gray-900 cursor-pointer hover:bg-stone-50 dark:hover:bg-gray-800 ';
        cell.className = cls;

        const num = document.createElement('div');
        let nc = 'w-7 h-7 flex items-center justify-center rounded-full text-sm font-medium mb-1 ';
        if (isToday)         nc += 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 font-bold';
        else if (otherMonth) nc += isSunday ? 'text-red-300 dark:text-red-900' : 'text-stone-300 dark:text-gray-700';
        else if (isSunday)   nc += 'text-red-500 dark:text-red-400 font-semibold';
        else                 nc += 'text-gray-700 dark:text-gray-300';
        num.className   = nc;
        num.textContent = day;
        cell.appendChild(num);

        const key = dateKey(year, month, d);
        if (activeTab==='vehicle' && tripsCache[key]) {
            tripsCache[key].slice(0,2).forEach(trip => {
                const isON = trip.return_date && trip.return_date!==trip.trip_date;
                const tag  = document.createElement('div');
                tag.className   = `text-xs px-1.5 py-0.5 rounded font-medium truncate mb-0.5 ${isON?'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300':'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300'}`;
                tag.textContent = `🚗 ${trip.destination}`;
                cell.appendChild(tag);
            });
            if (tripsCache[key].length>2) { const m=document.createElement('div'); m.className='text-xs text-stone-400 dark:text-gray-600 px-1'; m.textContent=`+${tripsCache[key].length-2} more`; cell.appendChild(m); }
        }
        if (activeTab==='rooms' && roomsCache[key]) {
            roomsCache[key].slice(0,3).forEach(b => {
                const tag = document.createElement('div');
                tag.className   = `text-xs px-1.5 py-0.5 rounded font-medium truncate mb-0.5 ${ROOM_COLORS[b.room]||'bg-gray-100 text-gray-700'}`;
                tag.textContent = `🏢 ${b.room.replace('Conference Room','').trim()}`;
                cell.appendChild(tag);
            });
            if (roomsCache[key].length>3) { const m=document.createElement('div'); m.className='text-xs text-stone-400 dark:text-gray-600 px-1'; m.textContent=`+${roomsCache[key].length-3} more`; cell.appendChild(m); }
        }

        if (!otherMonth) {
            const isPast = key < todayStr();
            if (isPast) { cell.style.opacity='0.45'; cell.style.cursor='not-allowed'; }
            else { cell.addEventListener('click', () => activeTab==='vehicle' ? openModal(year,month,d) : openRoomModal(year,month,d)); }
        }
        return cell;
    }

    // ── Vehicle Trip Modal ─────────────────────────────────────────
    function openModal(year, month, day) {
        selectedDate = dateKey(year, month, day);
        document.getElementById('modalDateTitle').textContent = `${MONTHS[month]} ${day}, ${year}`;
        document.getElementById('tripPickup').value      = DEFAULT_PICKUP;
        document.getElementById('tripDestination').value = '';
        document.getElementById('tripDeparture').value   = '';
        document.getElementById('tripETA').value         = '';
        document.getElementById('tripReturn').value      = '';
        document.getElementById('tripReturnDate').value  = '';
        document.getElementById('overnightNotice').classList.add('hidden');
        document.getElementById('tripError').classList.add('hidden');
        loadResources();
        if (IS_VEHICLE_ADMIN) { document.getElementById('bookedForSection').classList.remove('hidden'); loadUsersDropdown(); }
        renderTripList();
        document.getElementById('eventModal').classList.remove('hidden');
    }

    let vehicleList = [];
    let driverList  = [];

    let resourcesLoaded = false;

    async function loadResources() {
        if (resourcesLoaded) return;

        try {
            const res = await fetch('/vehicle-requests/resources');
            const data = await res.json();

            vehicleList = data.vehicles;
            driverList  = data.drivers;

            const vSelect = document.getElementById('tripVehicle');
            const dSelect = document.getElementById('tripDriver');

            // Clear existing options and add default
            vSelect.innerHTML = '<option value="">Select vehicle...</option>';
            dSelect.innerHTML = '<option value="">Select driver...</option>';

            // Populate vehicle select with name + plate
            vehicleList.forEach(v => {
                vSelect.appendChild(new Option(`${v.name} - ${v.plate || ''}`, v.id));
            });

            // Populate driver select
            driverList.forEach(d => {
                dSelect.appendChild(new Option(d.name, d.id));
            });

            resourcesLoaded = true;

        } catch (err) {
            console.error('Failed to load resources:', err);
        }
    }

    async function loadUsersDropdown() {
        const sel = document.getElementById('bookedFor');
        if (sel.children.length>1) return;
        try {
            const res   = await fetch('/vehicle-requests/users', {headers:{'Accept':'application/json'}});
            const users = await res.json();
            users.forEach(u => { const o=document.createElement('option'); o.value=u.name; o.textContent=u.name; sel.appendChild(o); });
        } catch(e) { console.error('Failed to load users',e); }
    }

    function closeModal() { document.getElementById('eventModal').classList.add('hidden'); selectedDate=null; }

    function renderTripList() {
        const list = document.getElementById('tripList');
        list.innerHTML = '';
        const trips = tripsCache[selectedDate]||[];
        if (!trips.length) return;
        const h=document.createElement('p'); h.className='text-xs font-semibold uppercase tracking-widest text-stone-400 dark:text-gray-500 mb-2'; h.textContent='Scheduled Trips'; list.appendChild(h);
        trips.forEach(trip => {
            const canCancel = trip.user_id===CURRENT_USER_ID;
            const card=document.createElement('div'); card.className='rounded-xl border border-stone-200 dark:border-gray-700 p-3 text-sm bg-stone-50 dark:bg-gray-800 space-y-1';
            card.innerHTML=`
                <div class="flex justify-between items-start">
                    <div class="font-semibold text-gray-800 dark:text-gray-100">🚗 ${trip.vehicle||''} <span class="text-xs font-normal text-stone-400 ml-1">${trip.driver||''}</span></div>
                    ${canCancel?`<button onclick="deleteTrip(${trip.id},'${trip.trip_date}','${trip.return_date??trip.trip_date}')" class="text-xs text-stone-300 hover:text-red-500 transition font-bold ml-2" title="Cancel">✕</button>`:`<span class="text-xs text-stone-300 ml-2">🔒</span>`}
                </div>
                <div class="text-stone-500 dark:text-gray-400 text-xs">👤 ${trip.user_name}${trip.booked_for?` <span class="text-purple-500">(for ${trip.booked_for})</span>`:''}</div>
                <div class="text-stone-500 dark:text-gray-400 text-xs">📍 ${trip.pickup}</div>
                <div class="text-stone-500 dark:text-gray-400 text-xs">🏁 ${trip.destination}</div>
                <div class="flex flex-wrap gap-4 text-xs text-stone-400 dark:text-gray-500 pt-1">
                    <span>🕐 Dep: <strong>${trip.departure}</strong></span>
                    <span>📥 ETA: <strong>${trip.eta??'—'}</strong></span>
                    <span>🔁 Return: <strong>${trip.return_time??'—'}${trip.return_date&&trip.return_date!==trip.trip_date?' <span class="text-amber-500">+1 day</span>':''}</strong></span>
                </div>`;
            list.appendChild(card);
        });
    }

    function detectOvernight() {
        const dep=document.getElementById('tripDeparture').value, ret=document.getElementById('tripReturn').value;
        const notice=document.getElementById('overnightNotice');
        if (dep&&ret&&ret<dep) {
            const nd=new Date(selectedDate+'T00:00:00'); nd.setDate(nd.getDate()+1);
            document.getElementById('tripReturnDate').value=localDateKey(nd);
            document.getElementById('overnightReturnDate').textContent=nd.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
            notice.classList.remove('hidden');
        } else { document.getElementById('tripReturnDate').value=''; notice.classList.add('hidden'); }
    }

    async function saveTrip() {
        const pickup = document.getElementById('tripPickup').value;
        const destination = document.getElementById('tripDestination').value;
        const vehicle_id = document.getElementById('tripVehicle').value;
        const driver_id = document.getElementById('tripDriver').value;
        const departure = document.getElementById('tripDeparture').value;
        const eta = document.getElementById('tripETA').value;
        const return_time = document.getElementById('tripReturn').value;

        if (!destination || !vehicle_id || !driver_id || !departure) {
            showError('Fill required fields');
            return;
        }

        const res = await fetch('/vehicle-requests', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                pickup,
                destination,
                vehicle_id,
                driver_id,
                trip_date: selectedDate,
                departure,
                eta,
                return_time
            })
        });

        const data = await res.json();

        if (!res.ok) {
            showError(data.error || 'Error');
            return;
        }

        closeModal();
        fetchAll();
    }

    function showTripError(msg) { const el=document.getElementById('tripError'); el.textContent=msg; el.classList.remove('hidden'); }

    // ── Conference Room Modal ──────────────────────────────────────
    function openRoomModal(year,month,day) {
        selectedDate=dateKey(year,month,day);
        document.getElementById('roomModalDateTitle').textContent=`${MONTHS[month]} ${day}, ${year}`;
        document.getElementById('roomName').value=''; document.getElementById('roomTitle').value=''; document.getElementById('roomAttendees').value='1';
        document.getElementById('roomError').classList.add('hidden');
        resetRoomTimePicker(); renderRoomBookingList();
        document.getElementById('roomModal').classList.remove('hidden');
    }
    function closeRoomModal() { document.getElementById('roomModal').classList.add('hidden'); selectedDate=null; }

    function renderRoomBookingList() {
        const list=document.getElementById('roomBookingList'); list.innerHTML='';
        const bookings=roomsCache[selectedDate]||[];
        if (!bookings.length) return;
        const h=document.createElement('p'); h.className='text-xs font-semibold uppercase tracking-widest text-stone-400 dark:text-gray-500 mb-2'; h.textContent='Room Bookings'; list.appendChild(h);
        bookings.forEach(b => {
            const canCancel=b.user_id===CURRENT_USER_ID||IS_ADMIN;
            const cc=ROOM_COLORS[b.room]||'bg-gray-100 text-gray-700';
            const card=document.createElement('div'); card.className='rounded-xl border border-stone-200 dark:border-gray-700 p-3 text-sm bg-stone-50 dark:bg-gray-800 space-y-1';
            card.innerHTML=`
                <div class="flex justify-between items-start">
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full ${cc}">🏢 ${b.room}</span>
                    ${canCancel?`<button onclick="deleteRoomBooking(${b.id})" class="text-xs text-stone-300 hover:text-red-500 transition font-bold ml-2">✕</button>`:`<span class="text-xs text-stone-300 ml-2">🔒</span>`}
                </div>
                <div class="font-semibold text-gray-800 dark:text-gray-100 text-sm">📝 ${b.title}</div>
                <div class="text-stone-500 dark:text-gray-400 text-xs">👤 ${b.user_name}</div>
                <div class="text-stone-500 dark:text-gray-400 text-xs">🕐 ${b.start_time} – ${b.end_time} &nbsp;|&nbsp; 👥 ${b.attendees} attendee${b.attendees>1?'s':''}</div>`;
            list.appendChild(card);
        });
    }

    async function saveRoomBooking() {
        const room=document.getElementById('roomName').value, title=document.getElementById('roomTitle').value.trim();
        const startTime=document.getElementById('roomStartTime').value, endTime=document.getElementById('roomEndTime').value;
        const attendees=document.getElementById('roomAttendees').value;
        if (!room||!title||!startTime||!endTime) { showRoomError('Please fill in all fields.'); return; }
        if (endTime<=startTime) { showRoomError('End time must be after start time.'); return; }
        const btn=document.getElementById('saveRoomBtn'); btn.disabled=true; btn.textContent='Saving...';
        try {
            const res=await fetch('/room-bookings',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},body:JSON.stringify({room,title,booking_date:selectedDate,start_time:startTime,end_time:endTime,attendees:parseInt(attendees)})});
            const data=await res.json();
            if (!res.ok) { showRoomError(data.error||'Failed to save booking.'); return; }
            if (!roomsCache[selectedDate]) roomsCache[selectedDate]=[];
            roomsCache[selectedDate].push(data);
            renderCalendar(); renderRoomBookingList();
            document.getElementById('roomName').value=''; document.getElementById('roomTitle').value=''; document.getElementById('roomAttendees').value='1';
            document.getElementById('roomError').classList.add('hidden');
            resetRoomTimePicker();
        } catch(e) { showRoomError('Network error. Please try again.'); }
        finally { btn.disabled=false; btn.textContent='Save Booking'; }
    }

    function showRoomError(msg) { const el=document.getElementById('roomError'); el.textContent=msg; el.classList.remove('hidden'); }

    // ── Room Time Picker ───────────────────────────────────────────
    function buildTimeOptions(fromMins=0) {
        const opts=[];
        for (let t=fromMins; t<24*60; t+=15) {
            const th=Math.floor(t/60), tm=t%60;
            const val=`${String(th).padStart(2,'0')}:${String(tm).padStart(2,'0')}`;
            const h12=th%12||12, ampm=th<12?'AM':'PM';
            opts.push({val, label:`${h12}:${String(tm).padStart(2,'0')} ${ampm}`});
        }
        return opts;
    }

    function resetRoomTimePicker() {
        const now=new Date(), isToday=selectedDate===todayStr();
        let sh=8, sm=0, from=0;
        if (isToday) {
            let h=now.getHours(), m=Math.ceil(now.getMinutes()/15)*15;
            if (m===60){m=0;h++;} if(h>=24)h=0;
            sh=h; sm=m; from=h*60+m;
        }
        const startSel=document.getElementById('roomStartTime');
        startSel.innerHTML='';
        buildTimeOptions(from).forEach(o=>startSel.appendChild(new Option(o.label,o.val)));
        startSel.value=`${String(sh).padStart(2,'0')}:${String(sm).padStart(2,'0')}`;
        const endSel=document.getElementById('roomEndTime');
        endSel.innerHTML='';
        buildTimeOptions(sh*60+sm+15).forEach(o=>endSel.appendChild(new Option(o.label,o.val)));
        const et=sh*60+sm+60, eh=Math.floor(et/60)%24, em=et%60;
        endSel.value=`${String(eh).padStart(2,'0')}:${String(em).padStart(2,'0')}`;
        document.getElementById('roomDurationLabel').textContent='⏱ 1 hour';
        document.getElementById('roomDurationLabel').className='text-xs text-stone-400 dark:text-gray-500';
    }

    function computeDurationLabel() {
        const sv=document.getElementById('roomStartTime').value, endSel=document.getElementById('roomEndTime');
        if (!sv) { document.getElementById('roomDurationLabel').textContent=''; return; }
        const [sh,sm]=sv.split(':').map(Number), startMins=sh*60+sm, prev=endSel.value;
        endSel.innerHTML='';
        buildTimeOptions(startMins+15).forEach(o=>endSel.appendChild(new Option(o.label,o.val)));
        const de=startMins+60, deh=Math.floor(de/60)%24, dem=de%60, ds=`${String(deh).padStart(2,'0')}:${String(dem).padStart(2,'0')}`;
        endSel.value=(prev&&endSel.querySelector(`option[value="${prev}"]`))?prev:ds;
        const [eh,em]=endSel.value.split(':').map(Number), diff=(eh*60+em)-startMins;
        if (diff<=0) { document.getElementById('roomDurationLabel').textContent='⚠ End time must be after start time'; document.getElementById('roomDurationLabel').className='text-xs text-red-500'; return; }
        const hrs=Math.floor(diff/60), mins=diff%60;
        let lbl=''; if(hrs>0) lbl+=`${hrs} hour${hrs>1?'s':''}`; if(mins>0) lbl+=(hrs>0?' ':'')+`${mins} minute${mins>1?'s':''}`;
        document.getElementById('roomDurationLabel').textContent=`⏱ ${lbl}`;
        document.getElementById('roomDurationLabel').className='text-xs text-stone-400 dark:text-gray-500';
    }

    // ── Shared controls ────────────────────────────────────────────
    function changeMonth(dir) {
        currentMonth+=dir;
        if(currentMonth>11){currentMonth=0;currentYear++;} if(currentMonth<0){currentMonth=11;currentYear--;}
        fetchAll();
    }
    function goToday() { currentYear=new Date().getFullYear(); currentMonth=new Date().getMonth(); fetchAll(); }

    document.getElementById('eventModal').addEventListener('click', function(e){if(e.target===this)closeModal();});
    document.getElementById('roomModal').addEventListener('click',  function(e){if(e.target===this)closeRoomModal();});
    document.getElementById('otpModal').addEventListener('click',   function(e){if(e.target===this)closeOtpModal();});

    fetchAll();

    // ── OTP Delete Flow ────────────────────────────────────────────
    let otpPending = { type: null, id: null, tripDate: null, returnDate: null };

    function deleteTrip(id, tripDate, returnDate) {
        otpPending = { type: 'vehicle', id, tripDate, returnDate };
        document.getElementById('otpMessage').textContent = `Vehicle trip on ${tripDate}`;
        resetOtpToStep1();
        document.getElementById('otpModal').classList.remove('hidden');
    }

    function deleteRoomBooking(id) {
        otpPending = { type: 'room', id, tripDate: null, returnDate: null };
        document.getElementById('otpMessage').textContent = 'Conference room booking';
        resetOtpToStep1();
        document.getElementById('otpModal').classList.remove('hidden');
    }

    function closeOtpModal() {
        document.getElementById('otpModal').classList.add('hidden');
        otpPending = { type: null, id: null, tripDate: null, returnDate: null };
    }

    function resetOtpToStep1() {
        document.getElementById('otpStep1').classList.remove('hidden');
        document.getElementById('otpStep2').classList.add('hidden');
        document.getElementById('otpRequestError').classList.add('hidden');
        document.getElementById('otpVerifyError').classList.add('hidden');
        document.getElementById('otpInput').value = '';
    }

    async function requestOtp() {
        const btn=document.getElementById('requestOtpBtn'); btn.disabled=true; btn.textContent='Sending...';
        document.getElementById('otpRequestError').classList.add('hidden');
        try {
            const res=await fetch('/otp/send',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},body:JSON.stringify({type:otpPending.type,target_id:otpPending.id})});
            const data=await res.json();
            if (!res.ok) { document.getElementById('otpRequestError').textContent=data.error||'Failed to send code.'; document.getElementById('otpRequestError').classList.remove('hidden'); return; }
            document.getElementById('otpSentMsg').textContent=data.message;
            document.getElementById('otpStep1').classList.add('hidden');
            document.getElementById('otpStep2').classList.remove('hidden');
            document.getElementById('otpInput').focus();
        } catch(e) { document.getElementById('otpRequestError').textContent='Network error. Please try again.'; document.getElementById('otpRequestError').classList.remove('hidden'); }
        finally { btn.disabled=false; btn.textContent='Send Code to My Email'; }
    }

    async function verifyOtp() {
        const code=document.getElementById('otpInput').value.trim();
        if (code.length!==6) { document.getElementById('otpVerifyError').textContent='Please enter the 6-digit code.'; document.getElementById('otpVerifyError').classList.remove('hidden'); return; }
        const btn=document.getElementById('verifyOtpBtn'); btn.disabled=true; btn.textContent='Verifying...';
        document.getElementById('otpVerifyError').classList.add('hidden');
        try {
            const res=await fetch('/otp/verify',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},body:JSON.stringify({type:otpPending.type,target_id:otpPending.id,code})});
            const data=await res.json();
            if (!res.ok) { document.getElementById('otpVerifyError').textContent=data.error||'Invalid code.'; document.getElementById('otpVerifyError').classList.remove('hidden'); return; }
            if (otpPending.type==='vehicle') {
                const start=new Date(otpPending.tripDate+'T00:00:00'), end=new Date((otpPending.returnDate||otpPending.tripDate)+'T00:00:00'), cur=new Date(start);
                while(cur<=end){const k=localDateKey(cur);if(tripsCache[k]){tripsCache[k]=tripsCache[k].filter(t=>t.id!==otpPending.id);if(!tripsCache[k].length)delete tripsCache[k];}cur.setDate(cur.getDate()+1);}
                renderCalendar(); renderTripList();
            } else {
                if(roomsCache[selectedDate]){roomsCache[selectedDate]=roomsCache[selectedDate].filter(b=>b.id!==otpPending.id);if(!roomsCache[selectedDate].length)delete roomsCache[selectedDate];}
                renderCalendar(); renderRoomBookingList();
            }
            closeOtpModal();
        } catch(e) { document.getElementById('otpVerifyError').textContent='Network error. Please try again.'; document.getElementById('otpVerifyError').classList.remove('hidden'); }
        finally { btn.disabled=false; btn.textContent='Confirm Cancel'; }
    }
fetchAll();
loadResources();
</script>

@endsection
