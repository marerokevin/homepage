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
                <select id="bookedFor"
                    class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100">
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
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">🚗 Vehicle Make / Model</label>
                    <input type="text" id="tripVehicle" placeholder="e.g. Toyota Innova"
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">🔢 Plate Number</label>
                    <input type="text" id="tripPlate" placeholder="e.g. ABC 1234"
                        class="w-full px-3 py-2 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
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

            {{-- Time picker --}}
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
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function todayStr() {
        return localDateKey(new Date());
    }

    // ── Tab switching ──────────────────────────────────────────────
    function switchTab(tab) {
        activeTab = tab;
        const vBtn   = document.getElementById('tab-vehicle');
        const rBtn   = document.getElementById('tab-rooms');
        const legend = document.getElementById('roomLegend');
        const active   = 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900';
        const inactive = 'border border-stone-300 dark:border-gray-700 text-stone-600 dark:text-gray-400 hover:bg-gray-900 hover:text-white dark:hover:bg-gray-100 dark:hover:text-gray-900';
        if (tab === 'vehicle') {
            vBtn.className = `px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 ${active}`;
            rBtn.className = `px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 ${inactive}`;
            legend.classList.add('hidden');
        } else {
            rBtn.className = `px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 ${active}`;
            vBtn.className = `px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 ${inactive}`;
            legend.classList.remove('hidden');
        }
        fetchAll();
    }

    // ── Data fetching ──────────────────────────────────────────────
    async function fetchAll() {
        await Promise.all([fetchTrips(), fetchRooms()]);
        renderCalendar();
    }

    async function fetchTrips() {
        try {
            const res  = await fetch(`/vehicle-requests?month=${currentMonth + 1}&year=${currentYear}`);
            const data = await res.json();
            tripsCache = {};
            data.forEach(trip => {
                const start   = new Date(trip.trip_date + 'T00:00:00');
                const end     = new Date((trip.return_date || trip.trip_date) + 'T00:00:00');
                const current = new Date(start);
                while (current <= end) {
                    const key = localDateKey(current);
                    if (!tripsCache[key]) tripsCache[key] = [];
                    if (!tripsCache[key].find(t => t.id === trip.id)) tripsCache[key].push(trip);
                    current.setDate(current.getDate() + 1);
                }
            });
        } catch (e) { console.error('Failed to load trips', e); }
    }

    async function fetchRooms() {
        try {
            const res  = await fetch(`/room-bookings?month=${currentMonth + 1}&year=${currentYear}`);
            const data = await res.json();
            roomsCache = {};
            data.forEach(b => {
                if (!roomsCache[b.booking_date]) roomsCache[b.booking_date] = [];
                roomsCache[b.booking_date].push(b);
            });
        } catch (e) { console.error('Failed to load room bookings', e); }
    }

    // ── Calendar rendering ─────────────────────────────────────────
    function renderCalendar() {
        document.getElementById('monthLabel').textContent = `${MONTHS[currentMonth]} ${currentYear}`;
        const grid = document.getElementById('calGrid');
        grid.innerHTML = '';
        const firstDay    = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const daysInPrev  = new Date(currentYear, currentMonth, 0).getDate();
        const today       = new Date();
        const isSameMonth = today.getFullYear() === currentYear && today.getMonth() === currentMonth;
        let colIndex = 0;
        for (let i = firstDay - 1; i >= 0; i--)
            grid.appendChild(createCell(daysInPrev - i, true, false, currentYear, currentMonth - 1, daysInPrev - i, colIndex++ % 7));
        for (let d = 1; d <= daysInMonth; d++)
            grid.appendChild(createCell(d, false, isSameMonth && d === today.getDate(), currentYear, currentMonth, d, colIndex++ % 7));
        const remaining = (7 - (grid.children.length % 7)) % 7;
        for (let d = 1; d <= remaining; d++)
            grid.appendChild(createCell(d, true, false, currentYear, currentMonth + 1, d, colIndex++ % 7));
    }

    function createCell(day, otherMonth, isToday, year, month, d, colIdx) {
        const cell     = document.createElement('div');
        const isSunday = colIdx === 0;
        let cellClass  = 'p-2 transition-colors duration-150 h-32 ';
        if (otherMonth)   cellClass += 'bg-stone-50 dark:bg-gray-900 ';
        else if (isToday) cellClass += 'bg-amber-50 dark:bg-gray-800 cursor-pointer hover:bg-amber-100 dark:hover:bg-gray-700 ';
        else              cellClass += 'bg-white dark:bg-gray-900 cursor-pointer hover:bg-stone-50 dark:hover:bg-gray-800 ';
        cell.className = cellClass;
        const num = document.createElement('div');
        let numClass = 'w-7 h-7 flex items-center justify-center rounded-full text-sm font-medium mb-1 ';
        if (isToday)         numClass += 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 font-bold';
        else if (otherMonth) numClass += isSunday ? 'text-red-300 dark:text-red-900' : 'text-stone-300 dark:text-gray-700';
        else if (isSunday)   numClass += 'text-red-500 dark:text-red-400 font-semibold';
        else                 numClass += 'text-gray-700 dark:text-gray-300';
        num.className = numClass;
        num.textContent = day;
        cell.appendChild(num);
        const key = dateKey(year, month, d);
        if (activeTab === 'vehicle' && tripsCache[key]) {
            tripsCache[key].slice(0, 2).forEach(trip => {
                const isON = trip.return_date && trip.return_date !== trip.trip_date;
                const tag  = document.createElement('div');
                tag.className   = `text-xs px-1.5 py-0.5 rounded font-medium truncate mb-0.5 ${isON ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300'}`;
                tag.textContent = `🚗 ${trip.destination}`;
                cell.appendChild(tag);
            });
            if (tripsCache[key].length > 2) {
                const more = document.createElement('div');
                more.className   = 'text-xs text-stone-400 dark:text-gray-600 px-1';
                more.textContent = `+${tripsCache[key].length - 2} more`;
                cell.appendChild(more);
            }
        }
        if (activeTab === 'rooms' && roomsCache[key]) {
            roomsCache[key].slice(0, 3).forEach(b => {
                const tag = document.createElement('div');
                tag.className   = `text-xs px-1.5 py-0.5 rounded font-medium truncate mb-0.5 ${ROOM_COLORS[b.room] || 'bg-gray-100 text-gray-700'}`;
                tag.textContent = `🏢 ${b.room.replace('Conference Room','').trim()}`;
                cell.appendChild(tag);
            });
            if (roomsCache[key].length > 3) {
                const more = document.createElement('div');
                more.className   = 'text-xs text-stone-400 dark:text-gray-600 px-1';
                more.textContent = `+${roomsCache[key].length - 3} more`;
                cell.appendChild(more);
            }
        }
        if (!otherMonth) {
            cell.addEventListener('click', () => {
                if (activeTab === 'vehicle') openModal(year, month, d);
                else openRoomModal(year, month, d);
            });
        }
        return cell;
    }

    // ── Vehicle Trip Modal ─────────────────────────────────────────
    function openModal(year, month, day) {
        selectedDate = dateKey(year, month, day);
        document.getElementById('modalDateTitle').textContent = `${MONTHS[month]} ${day}, ${year}`;
        document.getElementById('tripPickup').value      = DEFAULT_PICKUP;
        document.getElementById('tripDestination').value = '';
        document.getElementById('tripVehicle').value     = '';
        document.getElementById('tripPlate').value       = '';
        document.getElementById('tripDeparture').value   = '';
        document.getElementById('tripETA').value         = '';
        document.getElementById('tripReturn').value      = '';
        document.getElementById('tripReturnDate').value  = '';
        document.getElementById('overnightNotice').classList.add('hidden');
        document.getElementById('tripError').classList.add('hidden');
        if (IS_VEHICLE_ADMIN) {
            document.getElementById('bookedForSection').classList.remove('hidden');
            loadUsersDropdown();
        }
        renderTripList();
        document.getElementById('eventModal').classList.remove('hidden');
    }

    async function loadUsersDropdown() {
        const select = document.getElementById('bookedFor');
        if (select.children.length > 1) return;
        try {
            const res   = await fetch('/vehicle-requests/users', { headers: { 'Accept': 'application/json' } });
            const users = await res.json();
            users.forEach(u => {
                const opt = document.createElement('option');
                opt.value = u.name; opt.textContent = u.name;
                select.appendChild(opt);
            });
        } catch (e) { console.error('Failed to load users', e); }
    }

    function closeModal() {
        document.getElementById('eventModal').classList.add('hidden');
        selectedDate = null;
    }

    function renderTripList() {
        const list     = document.getElementById('tripList');
        list.innerHTML = '';
        const dayTrips = tripsCache[selectedDate] || [];
        if (dayTrips.length === 0) return;
        const heading = document.createElement('p');
        heading.className   = 'text-xs font-semibold uppercase tracking-widest text-stone-400 dark:text-gray-500 mb-2';
        heading.textContent = 'Scheduled Trips';
        list.appendChild(heading);
        dayTrips.forEach(trip => {
            const canCancel = trip.user_id === CURRENT_USER_ID;
            const card = document.createElement('div');
            card.className = 'rounded-xl border border-stone-200 dark:border-gray-700 p-3 text-sm bg-stone-50 dark:bg-gray-800 space-y-1';
            card.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="font-semibold text-gray-800 dark:text-gray-100">
                        🚗 ${trip.vehicle} <span class="text-xs font-normal text-stone-400 ml-1">${trip.plate}</span>
                    </div>
                    ${canCancel
                        ? `<button onclick="deleteTrip(${trip.id},'${trip.trip_date}','${trip.return_date ?? trip.trip_date}')" class="text-xs text-stone-300 hover:text-red-500 transition font-bold ml-2" title="Cancel">✕</button>`
                        : `<span class="text-xs text-stone-300 ml-2">🔒</span>`}
                </div>
                <div class="text-stone-500 dark:text-gray-400 text-xs">👤 ${trip.user_name}${trip.booked_for ? ` <span class="text-purple-500">(for ${trip.booked_for})</span>` : ''}</div>
                <div class="text-stone-500 dark:text-gray-400 text-xs">📍 ${trip.pickup}</div>
                <div class="text-stone-500 dark:text-gray-400 text-xs">🏁 ${trip.destination}</div>
                <div class="flex flex-wrap gap-4 text-xs text-stone-400 dark:text-gray-500 pt-1">
                    <span>🕐 Dep: <strong>${trip.departure}</strong></span>
                    <span>📥 ETA: <strong>${trip.eta ?? '—'}</strong></span>
                    <span>🔁 Return: <strong>${trip.return_time ?? '—'}${trip.return_date && trip.return_date !== trip.trip_date ? ' <span class="text-amber-500">+1 day</span>' : ''}</strong></span>
                </div>`;
            list.appendChild(card);
        });
    }

    function detectOvernight() {
        const departure  = document.getElementById('tripDeparture').value;
        const returnTime = document.getElementById('tripReturn').value;
        const notice     = document.getElementById('overnightNotice');
        if (departure && returnTime && returnTime < departure) {
            const nextDay = new Date(selectedDate + 'T00:00:00');
            nextDay.setDate(nextDay.getDate() + 1);
            document.getElementById('tripReturnDate').value = localDateKey(nextDay);
            document.getElementById('overnightReturnDate').textContent =
                nextDay.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            notice.classList.remove('hidden');
        } else {
            document.getElementById('tripReturnDate').value = '';
            notice.classList.add('hidden');
        }
    }

    async function saveTrip() {
        const pickup      = document.getElementById('tripPickup').value.trim();
        const destination = document.getElementById('tripDestination').value.trim();
        const vehicle     = document.getElementById('tripVehicle').value.trim();
        const plate       = document.getElementById('tripPlate').value.trim();
        const departure   = document.getElementById('tripDeparture').value;
        const eta         = document.getElementById('tripETA').value;
        const returnTime  = document.getElementById('tripReturn').value;
        const returnDate  = document.getElementById('tripReturnDate').value || null;
        if (!destination || !vehicle || !plate || !departure) {
            showTripError('Please fill in: Destination, Vehicle, Plate Number, and Departure time.');
            return;
        }
        const btn = document.getElementById('saveTripBtn');
        btn.disabled = true; btn.textContent = 'Saving...';
        const bookedFor = IS_VEHICLE_ADMIN ? (document.getElementById('bookedFor').value || null) : null;
        try {
            const res  = await fetch('/vehicle-requests', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ pickup, destination, vehicle, plate, trip_date: selectedDate, departure, eta: eta || null, return_time: returnTime || null, return_date: returnDate, booked_for: bookedFor }),
            });
            const data = await res.json();
            if (!res.ok) { showTripError(data.error || 'Failed to save request.'); return; }
            const start = new Date(data.trip_date + 'T00:00:00');
            const end   = new Date((data.return_date || data.trip_date) + 'T00:00:00');
            const cur   = new Date(start);
            while (cur <= end) {
                const key = localDateKey(cur);
                if (!tripsCache[key]) tripsCache[key] = [];
                if (!tripsCache[key].find(t => t.id === data.id)) tripsCache[key].push(data);
                cur.setDate(cur.getDate() + 1);
            }
            renderCalendar(); renderTripList();
            ['tripDestination','tripVehicle','tripPlate','tripDeparture','tripETA','tripReturn','tripReturnDate'].forEach(id => document.getElementById(id).value = '');
            document.getElementById('overnightNotice').classList.add('hidden');
            document.getElementById('tripError').classList.add('hidden');
        } catch (e) { showTripError('Network error. Please try again.'); }
        finally { btn.disabled = false; btn.textContent = 'Save Trip Request'; }
    }

    async function deleteTrip(id, tripDate, returnDate) {
        if (!confirm('Cancel this trip request?')) return;
        const res  = await fetch(`/vehicle-requests/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' } });
        const data = await res.json();
        if (!res.ok) { alert(data.error || 'Could not cancel.'); return; }
        const start = new Date(tripDate + 'T00:00:00');
        const end   = new Date((returnDate || tripDate) + 'T00:00:00');
        const cur   = new Date(start);
        while (cur <= end) {
            const key = localDateKey(cur);
            if (tripsCache[key]) { tripsCache[key] = tripsCache[key].filter(t => t.id !== id); if (!tripsCache[key].length) delete tripsCache[key]; }
            cur.setDate(cur.getDate() + 1);
        }
        renderCalendar(); renderTripList();
    }

    function showTripError(msg) {
        const el = document.getElementById('tripError');
        el.textContent = msg; el.classList.remove('hidden');
    }

    // ── Conference Room Modal ──────────────────────────────────────
    function openRoomModal(year, month, day) {
        selectedDate = dateKey(year, month, day);
        document.getElementById('roomModalDateTitle').textContent = `${MONTHS[month]} ${day}, ${year}`;
        document.getElementById('roomName').value      = '';
        document.getElementById('roomTitle').value     = '';
        document.getElementById('roomAttendees').value = '1';
        document.getElementById('roomError').classList.add('hidden');
        resetRoomTimePicker();
        renderRoomBookingList();
        document.getElementById('roomModal').classList.remove('hidden');
    }

    function closeRoomModal() {
        document.getElementById('roomModal').classList.add('hidden');
        selectedDate = null;
    }

    function renderRoomBookingList() {
        const list     = document.getElementById('roomBookingList');
        list.innerHTML = '';
        const bookings = roomsCache[selectedDate] || [];
        if (bookings.length === 0) return;
        const heading = document.createElement('p');
        heading.className   = 'text-xs font-semibold uppercase tracking-widest text-stone-400 dark:text-gray-500 mb-2';
        heading.textContent = 'Room Bookings';
        list.appendChild(heading);
        bookings.forEach(b => {
            const canCancel  = b.user_id === CURRENT_USER_ID || IS_ADMIN;
            const colorClass = ROOM_COLORS[b.room] || 'bg-gray-100 text-gray-700';
            const card = document.createElement('div');
            card.className = 'rounded-xl border border-stone-200 dark:border-gray-700 p-3 text-sm bg-stone-50 dark:bg-gray-800 space-y-1';
            card.innerHTML = `
                <div class="flex justify-between items-start">
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full ${colorClass}">🏢 ${b.room}</span>
                    ${canCancel
                        ? `<button onclick="deleteRoomBooking(${b.id})" class="text-xs text-stone-300 hover:text-red-500 transition font-bold ml-2">✕</button>`
                        : `<span class="text-xs text-stone-300 ml-2">🔒</span>`}
                </div>
                <div class="font-semibold text-gray-800 dark:text-gray-100 text-sm">📝 ${b.title}</div>
                <div class="text-stone-500 dark:text-gray-400 text-xs">👤 ${b.user_name}</div>
                <div class="text-stone-500 dark:text-gray-400 text-xs">🕐 ${b.start_time} – ${b.end_time} &nbsp;|&nbsp; 👥 ${b.attendees} attendee${b.attendees > 1 ? 's' : ''}</div>`;
            list.appendChild(card);
        });
    }

    async function saveRoomBooking() {
        const room      = document.getElementById('roomName').value;
        const title     = document.getElementById('roomTitle').value.trim();
        const startTime = document.getElementById('roomStartTime').value;
        const endTime   = document.getElementById('roomEndTime').value;
        const attendees = document.getElementById('roomAttendees').value;
        if (!room || !title || !startTime || !endTime) { showRoomError('Please fill in all fields.'); return; }
        if (endTime <= startTime) { showRoomError('End time must be after start time.'); return; }
        const btn = document.getElementById('saveRoomBtn');
        btn.disabled = true; btn.textContent = 'Saving...';
        try {
            const res  = await fetch('/room-bookings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ room, title, booking_date: selectedDate, start_time: startTime, end_time: endTime, attendees: parseInt(attendees) }),
            });
            const data = await res.json();
            if (!res.ok) { showRoomError(data.error || 'Failed to save booking.'); return; }
            if (!roomsCache[selectedDate]) roomsCache[selectedDate] = [];
            roomsCache[selectedDate].push(data);
            renderCalendar(); renderRoomBookingList();
            document.getElementById('roomName').value      = '';
            document.getElementById('roomTitle').value     = '';
            document.getElementById('roomAttendees').value = '1';
            document.getElementById('roomError').classList.add('hidden');
            resetRoomTimePicker();
        } catch (e) { showRoomError('Network error. Please try again.'); }
        finally { btn.disabled = false; btn.textContent = 'Save Booking'; }
    }

    async function deleteRoomBooking(id) {
        if (!confirm('Cancel this room booking?')) return;
        const res  = await fetch(`/room-bookings/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' } });
        const data = await res.json();
        if (!res.ok) { alert(data.error || 'Could not cancel.'); return; }
        if (roomsCache[selectedDate]) {
            roomsCache[selectedDate] = roomsCache[selectedDate].filter(b => b.id !== id);
            if (!roomsCache[selectedDate].length) delete roomsCache[selectedDate];
        }
        renderCalendar(); renderRoomBookingList();
    }

    // ── Room Time Picker ───────────────────────────────────────────
    function buildTimeOptions(fromMins = 0) {
        // Generate all 15-min slots from fromMins onwards
        const opts = [];
        for (let t = fromMins; t < 24 * 60; t += 15) {
            const th    = Math.floor(t / 60);
            const tm    = t % 60;
            const val   = `${String(th).padStart(2,'0')}:${String(tm).padStart(2,'0')}`;
            const h12   = th % 12 || 12;
            const ampm  = th < 12 ? 'AM' : 'PM';
            const label = `${h12}:${String(tm).padStart(2,'0')} ${ampm}`;
            opts.push({ val, label });
        }
        return opts;
    }

    function resetRoomTimePicker() {
        const now    = new Date();
        const isToday = selectedDate === todayStr();

        let startFromMins = 0;
        let defaultStartH, defaultStartM;

        if (isToday) {
            // Today: start from current time rounded up to nearest 15
            let h = now.getHours();
            let m = Math.ceil(now.getMinutes() / 15) * 15;
            if (m === 60) { m = 0; h += 1; }
            if (h >= 24) h = 0;
            defaultStartH  = h;
            defaultStartM  = m;
            startFromMins  = h * 60 + m;
        } else {
            // Future date: all times available, default to 8:00 AM
            defaultStartH = 8;
            defaultStartM = 0;
            startFromMins = 0;
        }

        const startSel = document.getElementById('roomStartTime');
        startSel.innerHTML = '';
        buildTimeOptions(startFromMins).forEach(o => startSel.appendChild(new Option(o.label, o.val)));

        const startStr = `${String(defaultStartH).padStart(2,'0')}:${String(defaultStartM).padStart(2,'0')}`;
        startSel.value = startStr;

        // Build end time options (start + 15 min onwards)
        const endFromMins = defaultStartH * 60 + defaultStartM + 15;
        const endSel = document.getElementById('roomEndTime');
        endSel.innerHTML = '';
        buildTimeOptions(endFromMins).forEach(o => endSel.appendChild(new Option(o.label, o.val)));

        // Default end = start + 1 hr
        const endTotal = defaultStartH * 60 + defaultStartM + 60;
        const eh = Math.floor(endTotal / 60) % 24;
        const em = endTotal % 60;
        endSel.value = `${String(eh).padStart(2,'0')}:${String(em).padStart(2,'0')}`;

        document.getElementById('roomDurationLabel').textContent = '⏱ 1 hour';
        document.getElementById('roomDurationLabel').className   = 'text-xs text-stone-400 dark:text-gray-500';
    }

    function computeDurationLabel() {
        const startVal = document.getElementById('roomStartTime').value;
        const endSel   = document.getElementById('roomEndTime');

        if (!startVal) { document.getElementById('roomDurationLabel').textContent = ''; return; }

        const [sh, sm]  = startVal.split(':').map(Number);
        const startMins = sh * 60 + sm;
        const prevEnd   = endSel.value;

        // Rebuild end options to only show after selected start
        endSel.innerHTML = '';
        buildTimeOptions(startMins + 15).forEach(o => endSel.appendChild(new Option(o.label, o.val)));

        // Restore or default to +1 hr
        const defaultEnd = startMins + 60;
        const deh  = Math.floor(defaultEnd / 60) % 24;
        const dem  = defaultEnd % 60;
        const dStr = `${String(deh).padStart(2,'0')}:${String(dem).padStart(2,'0')}`;
        endSel.value = (prevEnd && endSel.querySelector(`option[value="${prevEnd}"]`)) ? prevEnd : dStr;

        const [eh, em] = endSel.value.split(':').map(Number);
        const diff = (eh * 60 + em) - startMins;

        if (diff <= 0) {
            document.getElementById('roomDurationLabel').textContent = '⚠ End time must be after start time';
            document.getElementById('roomDurationLabel').className   = 'text-xs text-red-500';
            return;
        }

        const hrs  = Math.floor(diff / 60);
        const mins = diff % 60;
        let label  = '';
        if (hrs > 0)  label += `${hrs} hour${hrs > 1 ? 's' : ''}`;
        if (mins > 0) label += (hrs > 0 ? ' ' : '') + `${mins} minute${mins > 1 ? 's' : ''}`;
        document.getElementById('roomDurationLabel').textContent = `⏱ ${label}`;
        document.getElementById('roomDurationLabel').className   = 'text-xs text-stone-400 dark:text-gray-500';
    }

    function showRoomError(msg) {
        const el = document.getElementById('roomError');
        el.textContent = msg; el.classList.remove('hidden');
    }

    // ── Shared controls ────────────────────────────────────────────
    function changeMonth(dir) {
        currentMonth += dir;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        if (currentMonth < 0)  { currentMonth = 11; currentYear--; }
        fetchAll();
    }

    function goToday() {
        currentYear  = new Date().getFullYear();
        currentMonth = new Date().getMonth();
        fetchAll();
    }

    document.getElementById('eventModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
    document.getElementById('roomModal').addEventListener('click',  function(e) { if (e.target === this) closeRoomModal(); });

    fetchAll();
</script>

@endsection
