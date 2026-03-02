<?php $__env->startSection('title', 'Calendar'); ?>

<?php $__env->startSection('content'); ?>

<div class="min-h-screen bg-stone-100 dark:bg-gray-950 py-10 px-4">
    <div class="max-w-4xl mx-auto">

        
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Driver Allocation</h1>
            <button onclick="goToday()"
                class="px-4 py-1.5 text-sm font-semibold border border-stone-300 dark:border-gray-700 rounded-full text-stone-600 dark:text-gray-400 hover:bg-gray-900 hover:text-white dark:hover:bg-gray-100 dark:hover:text-gray-900 transition-all duration-200">
                Today
            </button>
        </div>

        
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

        
        <div class="rounded-2xl overflow-hidden shadow-lg border border-stone-200 dark:border-gray-800">

            
            <div class="bg-gray-900 dark:bg-gray-950" style="display:grid; grid-template-columns:repeat(7,minmax(0,1fr))">
                <?php $__currentLoopData = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="text-center py-3 text-xs font-semibold tracking-widest uppercase <?php echo e($day === 'Sun' ? 'text-red-400' : 'text-stone-300'); ?>">
                    <?php echo e($day); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div id="calGrid" class="gap-px bg-stone-200 dark:bg-gray-800" style="display:grid; grid-template-columns:repeat(7,minmax(0,1fr)); grid-auto-rows:1fr;"></div>
        </div>

    </div>
</div>


<div id="eventModal"
    class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center px-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6">

        <h2 id="modalDateTitle" class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4"></h2>

        
        <div id="eventList" class="mb-3 max-h-36 overflow-y-auto space-y-2"></div>

        
        <input type="text" id="eventInput" maxlength="50" placeholder="Add an event..."
            class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-gray-700 bg-stone-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100 mb-3 placeholder-stone-400" />

        
        <div class="flex gap-3 mb-4">
            <button onclick="selectColor(1)" data-color="1"
                class="color-dot w-6 h-6 rounded-full bg-blue-400 transition-transform hover:scale-125 ring-2 ring-blue-400 ring-offset-2"></button>
            <button onclick="selectColor(2)" data-color="2"
                class="color-dot w-6 h-6 rounded-full bg-green-400 transition-transform hover:scale-125"></button>
            <button onclick="selectColor(3)" data-color="3"
                class="color-dot w-6 h-6 rounded-full bg-amber-400 transition-transform hover:scale-125"></button>
            <button onclick="selectColor(4)" data-color="4"
                class="color-dot w-6 h-6 rounded-full bg-pink-400 transition-transform hover:scale-125"></button>
        </div>

        
        <div class="flex gap-3">
            <button onclick="saveEvent()"
                class="flex-1 py-2.5 bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 font-semibold rounded-xl hover:opacity-80 transition text-sm">
                Add Event
            </button>
            <button onclick="closeModal()"
                class="px-5 py-2.5 border border-stone-300 dark:border-gray-700 text-stone-600 dark:text-gray-400 font-medium rounded-xl hover:bg-stone-100 dark:hover:bg-gray-800 transition text-sm">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    const MONTHS = ['January','February','March','April','May','June',
                    'July','August','September','October','November','December'];

    const COLOR_CLASSES = {
        1: { bg: 'bg-blue-100',  text: 'text-blue-700',  ring: 'ring-blue-400'  },
        2: { bg: 'bg-green-100', text: 'text-green-700', ring: 'ring-green-400' },
        3: { bg: 'bg-amber-100', text: 'text-amber-700', ring: 'ring-amber-400' },
        4: { bg: 'bg-pink-100',  text: 'text-pink-700',  ring: 'ring-pink-400'  },
    };

    let currentDate  = new Date();
    let currentYear  = currentDate.getFullYear();
    let currentMonth = currentDate.getMonth();
    let selectedDateKey = null;
    let selectedColor   = 1;
    let events = JSON.parse(localStorage.getItem('cal_events') || '{}');

    function saveEvents() {
        localStorage.setItem('cal_events', JSON.stringify(events));
    }

    function dateKey(year, month, day) {
        return `${year}-${String(month + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
    }

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

        for (let i = firstDay - 1; i >= 0; i--) {
            grid.appendChild(createCell(daysInPrev - i, true, false, currentYear, currentMonth - 1, daysInPrev - i, colIndex++ % 7));
        }

        for (let d = 1; d <= daysInMonth; d++) {
            grid.appendChild(createCell(d, false, isSameMonth && d === today.getDate(), currentYear, currentMonth, d, colIndex++ % 7));
        }

        const remaining = (7 - (grid.children.length % 7)) % 7;
        for (let d = 1; d <= remaining; d++) {
            grid.appendChild(createCell(d, true, false, currentYear, currentMonth + 1, d, colIndex++ % 7));
        }
    }

    function createCell(day, otherMonth, isToday, year, month, d, colIdx) {
        const cell = document.createElement('div');
        const isSunday = colIdx === 0;

        let cellClass = 'border-gray-700 transition-colors duration-150 ' + 'h-40 ';
        if (otherMonth)   cellClass += 'bg-stone-50 dark:bg-gray-900 ';
        else if (isToday) cellClass += 'bg-amber-50 dark:bg-gray-800 cursor-pointer hover:bg-amber-100 dark:hover:bg-gray-700 ';
        else              cellClass += 'bg-white dark:bg-gray-900 cursor-pointer hover:bg-stone-50 dark:hover:bg-gray-800 ';

        cell.className = cellClass;

        // Date number
        const num = document.createElement('div');
        let numClass = 'w-7 h-full flex items-center justify-center text-sm bg-gray-200 font-medium mb-1 ';

        if (isToday)         numClass += 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 font-bold';
        else if (otherMonth) numClass += isSunday ? 'text-red-300 dark:text-red-900' : 'text-stone-300 dark:text-gray-700';
        else if (isSunday)   numClass += 'text-red-500 dark:text-red-400 font-semibold';
        else                 numClass += 'text-gray-700 dark:text-gray-300';

        num.className = numClass;
        num.textContent = day;
        cell.appendChild(num);

        // Events
        const key = dateKey(year, month, d);
        if (events[key]) {
            events[key].slice(0, 2).forEach(ev => {
                const c = COLOR_CLASSES[ev.color] || COLOR_CLASSES[1];
                const tag = document.createElement('div');
                tag.className = `text-xs px-1.5 py-0.5 rounded font-medium truncate mb-0.5 ${c.bg} ${c.text}`;
                tag.textContent = ev.text;
                cell.appendChild(tag);
            });

            if (events[key].length > 2) {
                const more = document.createElement('div');
                more.className = 'text-xs text-stone-400 dark:text-gray-600 px-1';
                more.textContent = `+${events[key].length - 2} more`;
                cell.appendChild(more);
            }
        }

        if (!otherMonth) {
            cell.addEventListener('click', () => openModal(year, month, d));
        }

        return cell;
    }

    function openModal(year, month, day) {
        selectedDateKey = dateKey(year, month, day);
        document.getElementById('modalDateTitle').textContent = `${MONTHS[month]} ${day}, ${year}`;
        document.getElementById('eventInput').value = '';
        renderEventList();
        document.getElementById('eventModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('eventInput').focus(), 50);
    }

    function closeModal() {
        document.getElementById('eventModal').classList.add('hidden');
        selectedDateKey = null;
    }

    function renderEventList() {
        const list = document.getElementById('eventList');
        list.innerHTML = '';
        (events[selectedDateKey] || []).forEach((ev, i) => {
            const c = COLOR_CLASSES[ev.color] || COLOR_CLASSES[1];
            const item = document.createElement('div');
            item.className = `flex items-center justify-between px-3 py-1.5 rounded-lg text-sm font-medium ${c.bg} ${c.text}`;
            item.innerHTML = `
                <span class="truncate">${ev.text}</span>
                <button onclick="deleteEvent(${i})" class="ml-2 opacity-50 hover:opacity-100 transition text-xs font-bold">✕</button>
            `;
            list.appendChild(item);
        });
    }

    function saveEvent() {
        const text = document.getElementById('eventInput').value.trim();
        if (!text) return;
        if (!events[selectedDateKey]) events[selectedDateKey] = [];
        events[selectedDateKey].push({ text, color: selectedColor });
        saveEvents();
        renderCalendar();
        renderEventList();
        document.getElementById('eventInput').value = '';
    }

    function deleteEvent(index) {
        events[selectedDateKey].splice(index, 1);
        if (!events[selectedDateKey].length) delete events[selectedDateKey];
        saveEvents();
        renderCalendar();
        renderEventList();
    }

    function selectColor(c) {
        selectedColor = c;
        document.querySelectorAll('.color-dot').forEach(d => {
            d.classList.remove('ring-2', 'ring-blue-400', 'ring-green-400', 'ring-amber-400', 'ring-pink-400', 'ring-offset-2');
        });
        const btn = document.querySelector(`.color-dot[data-color="${c}"]`);
        btn.classList.add('ring-2', COLOR_CLASSES[c].ring, 'ring-offset-2');
    }

    function changeMonth(dir) {
        currentMonth += dir;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        if (currentMonth < 0)  { currentMonth = 11; currentYear--; }
        renderCalendar();
    }

    function goToday() {
        currentYear  = new Date().getFullYear();
        currentMonth = new Date().getMonth();
        renderCalendar();
    }

    document.getElementById('eventModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.getElementById('eventInput').addEventListener('keydown', e => {
        if (e.key === 'Enter') saveEvent();
    });

    renderCalendar();
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/kmarero/Documents/Projects/lara_home/home/home/resources/views/calendar.blade.php ENDPATH**/ ?>