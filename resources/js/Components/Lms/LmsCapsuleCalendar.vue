<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Jadwal Pelajaran',
    },
    days: {
        type: Array,
        default: () => [
            { d: 'Sen', n: 18, full: 'Senin' },
            { d: 'Sel', n: 19, full: 'Selasa' },
            { d: 'Rab', n: 20, full: 'Rabu' },
            { d: 'Kam', n: 21, full: 'Kamis' },
            { d: 'Jum', n: 22, full: 'Jumat' },
            { d: 'Sab', n: 23, full: 'Sabtu' },
        ],
    },
    selectedDay: {
        type: Number,
        default: 18,
    },
    events: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['selectDay']);

// Real-Time Clock State
const now = ref(new Date());
let timer = null;

onMounted(() => {
    timer = setInterval(() => {
        now.value = new Date();
    }, 1000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});

// Modal State & Calendar Navigation State
const isModalOpen = ref(false);
const viewMonth = ref(now.value.getMonth());
const viewYear = ref(now.value.getFullYear());
const selectedDate = ref(new Date(viewYear.value, viewMonth.value, props.selectedDay || now.value.getDate()));

const monthNames = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];

const monthShortNames = [
    'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
    'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
];

const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

const currentPillLabel = computed(() => {
    return `${monthShortNames[viewMonth.value]} ${viewYear.value}`;
});

const activeMonthName = computed(() => {
    return monthNames[viewMonth.value];
});

const liveTimeFormatted = computed(() => {
    const d = now.value;
    const dayName = dayNames[d.getDay()];
    const date = d.getDate();
    const month = monthShortNames[d.getMonth()];
    const year = d.getFullYear();
    const hh = String(d.getHours()).padStart(2, '0');
    const mm = String(d.getMinutes()).padStart(2, '0');
    const ss = String(d.getSeconds()).padStart(2, '0');
    return `${dayName}, ${date} ${month} ${year} • ${hh}:${mm}:${ss} WIB`;
});

const selectedDateFormatted = computed(() => {
    const d = selectedDate.value;
    const dayName = dayNames[d.getDay()];
    const date = d.getDate();
    const month = monthNames[d.getMonth()];
    const year = d.getFullYear();
    return `${dayName}, ${date} ${month} ${year}`;
});

const selectedDateString = computed(() => {
    const d = selectedDate.value;
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
});

const selectedEvent = computed(() => {
    return props.events[selectedDateString.value] || null;
});

const getCellDateString = (cell) => {
    const m = String(cell.month + 1).padStart(2, '0');
    const d = String(cell.day).padStart(2, '0');
    return `${cell.year}-${m}-${d}`;
};

const getEventForCell = (cell) => {
    const str = getCellDateString(cell);
    return props.events[str] || null;
};

// Calculate Calendar Day Matrix (6 rows x 7 days = 42 cells)
const calendarCells = computed(() => {
    const year = viewYear.value;
    const month = viewMonth.value;
    const cells = [];

    const firstDayIndex = new Date(year, month, 1).getDay(); // 0 = Sunday
    const daysInCurrentMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();

    const realToday = now.value;

    // Previous Month Trailing Days
    for (let i = firstDayIndex - 1; i >= 0; i--) {
        const d = daysInPrevMonth - i;
        const prevMonthVal = month === 0 ? 11 : month - 1;
        const prevYearVal = month === 0 ? year - 1 : year;
        cells.push({
            day: d,
            month: prevMonthVal,
            year: prevYearVal,
            isCurrentMonth: false,
            isToday: false,
        });
    }

    // Current Month Days
    for (let i = 1; i <= daysInCurrentMonth; i++) {
        const isToday =
            year === realToday.getFullYear() &&
            month === realToday.getMonth() &&
            i === realToday.getDate();
        cells.push({
            day: i,
            month: month,
            year: year,
            isCurrentMonth: true,
            isToday,
        });
    }

    // Next Month Leading Days to reach 42 cells
    const remaining = 42 - cells.length;
    for (let i = 1; i <= remaining; i++) {
        const nextMonthVal = month === 11 ? 0 : month + 1;
        const nextYearVal = month === 11 ? year + 1 : year;
        cells.push({
            day: i,
            month: nextMonthVal,
            year: nextYearVal,
            isCurrentMonth: false,
            isToday: false,
        });
    }

    return cells;
});

const isSelectedDate = (cell) => {
    const s = selectedDate.value;
    return (
        s.getFullYear() === cell.year &&
        s.getMonth() === cell.month &&
        s.getDate() === cell.day
    );
};

const openCalendarModal = () => {
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const prevMonth = () => {
    if (viewMonth.value === 0) {
        viewMonth.value = 11;
        viewYear.value--;
    } else {
        viewMonth.value--;
    }
};

const nextMonth = () => {
    if (viewMonth.value === 11) {
        viewMonth.value = 0;
        viewYear.value++;
    } else {
        viewMonth.value++;
    }
};

const goToToday = () => {
    const today = now.value;
    viewMonth.value = today.getMonth();
    viewYear.value = today.getFullYear();
    selectedDate.value = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    const dateStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    emit('selectDay', today.getDate(), dateStr);
};

const selectCalendarDate = (cell) => {
    selectedDate.value = new Date(cell.year, cell.month, cell.day);
    if (!cell.isCurrentMonth) {
        viewMonth.value = cell.month;
        viewYear.value = cell.year;
    }
    const dateStr = getCellDateString(cell);
    emit('selectDay', cell.day, dateStr);
};

const confirmSelection = () => {
    emit('selectDay', selectedDate.value.getDate(), selectedDateString.value);
    closeModal();
};

const gridColsClass = computed(() => {
    const len = props.days?.length || 6;
    if (len === 6) return 'grid-cols-6';
    if (len === 7) return 'grid-cols-7';
    if (len === 5) return 'grid-cols-5';
    return 'grid-cols-6';
});
</script>

<template>
    <div class="mt-5">
        <!-- Header & Real-Time Calendar Trigger Button -->
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-[16px] font-extrabold text-charcoal">
                {{ title }}
            </h3>

            <!-- Pill Button Trigger -->
            <button
                type="button"
                @click="openCalendarModal"
                class="text-xs font-bold text-charcoal bg-white border border-[#ECEAE4] px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-2xs hover:border-charcoal/40 active:scale-95 transition-all cursor-pointer select-none"
                title="Buka Kalender Pendidikan & Real-Time"
            >
                <span class="w-2 h-2 rounded-full bg-[#2E7D32] live-dot"></span>
                <span>{{ currentPillLabel }}</span>
                <svg
                    class="w-3.5 h-3.5 text-charcoal/70 transition-transform duration-200"
                    :class="isModalOpen ? 'rotate-180' : ''"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>
        </div>

        <!-- Weekly Capsule Grid (Senin - Sabtu Dinamis) -->
        <div :class="['grid gap-1.5', gridColsClass]">
            <button
                v-for="item in days"
                :key="item.date || item.n"
                type="button"
                @click="$emit('selectDay', item.n, item.date)"
                class="py-3 px-1 rounded-full flex flex-col items-center justify-center transition-all cursor-pointer select-none relative group"
                :class="
                    selectedDay === item.n
                        ? 'bg-charcoal text-white shadow-md scale-[1.03]'
                        : item.isHoliday
                        ? 'bg-rose-50/80 text-rose-700 border border-rose-200 hover:border-rose-300'
                        : item.isToday
                        ? 'bg-sage-light/60 text-charcoal border border-sage/40 hover:border-charcoal/30'
                        : 'bg-white text-charcoal border border-[#ECEAE4] hover:border-charcoal/30'
                "
                :title="item.holidayName ? `Libur: ${item.holidayName}` : (item.academicEvent || item.full)"
            >
                <!-- Day Name -->
                <span
                    class="text-[10px] font-semibold"
                    :class="
                        selectedDay === item.n
                            ? 'text-white/75'
                            : item.isHoliday
                            ? 'text-rose-600 font-bold'
                            : 'text-subtext'
                    "
                >
                    {{ item.d }}
                </span>

                <!-- Day Number -->
                <span
                    class="text-[15px] font-extrabold mt-0.5"
                    :class="item.isHoliday && selectedDay !== item.n ? 'text-rose-700' : ''"
                >
                    {{ item.n }}
                </span>

                <!-- Indicator Dot -->
                <div class="flex items-center gap-0.5 mt-1">
                    <span
                        v-if="item.isToday"
                        class="w-1.5 h-1.5 rounded-full"
                        :class="selectedDay === item.n ? 'bg-emerald-400' : 'bg-emerald-600 animate-pulse'"
                        title="Hari Ini"
                    ></span>
                    <span
                        v-else-if="item.isHoliday"
                        class="w-1.5 h-1.5 rounded-full"
                        :class="selectedDay === item.n ? 'bg-rose-300' : 'bg-rose-500'"
                        title="Hari Libur"
                    ></span>
                    <span
                        v-else-if="item.eventType === 'exam'"
                        class="w-1.5 h-1.5 rounded-full"
                        :class="selectedDay === item.n ? 'bg-purple-300' : 'bg-purple-600'"
                        title="Ujian / Evaluasi"
                    ></span>
                    <span
                        v-else
                        class="w-1 h-1 rounded-full"
                        :class="selectedDay === item.n ? 'bg-white' : 'bg-charcoal/25'"
                    ></span>
                </div>
            </button>
        </div>

        <!-- ======================================================== -->
        <!-- REAL-TIME KALENDER PENDIDIKAN MODAL                       -->
        <!-- ======================================================== -->
        <Teleport to="body">
            <Transition name="calendar-modal">
                <div
                    v-if="isModalOpen"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/45 backdrop-blur-xs select-none"
                    @click.self="closeModal"
                >
                    <div
                        class="bg-white rounded-[32px] p-5 sm:p-6 w-full max-w-sm sm:max-w-md border border-[#ECEAE4] shadow-2xl relative flex flex-col modal-card"
                    >
                        <!-- Live Time & Kaldik Badge Header -->
                        <div class="flex items-center justify-between pb-3.5 border-b border-[#ECEAE4] mb-3.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#2E7D32] live-dot shrink-0"></span>
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-subtext">
                                        Kalender Pendidikan & Real-Time
                                    </div>
                                    <div class="text-xs font-extrabold text-charcoal">
                                        {{ liveTimeFormatted }}
                                    </div>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="closeModal"
                                class="w-8 h-8 rounded-full bg-canvas text-charcoal flex items-center justify-center text-xs font-bold hover:bg-muted active:scale-95 transition-all cursor-pointer border border-[#E6E4DF]"
                                title="Tutup"
                            >
                                ✕
                            </button>
                        </div>

                        <!-- Month & Year Controls -->
                        <div class="flex items-center justify-between mb-3 px-1">
                            <button
                                type="button"
                                @click="prevMonth"
                                class="w-9 h-9 rounded-full bg-canvas border border-[#ECEAE4] flex items-center justify-center text-charcoal text-base font-extrabold hover:bg-muted active:scale-95 transition-all cursor-pointer"
                                title="Bulan Sebelumnya"
                            >
                                ‹
                            </button>
                            <div class="text-center">
                                <h4 class="text-[16px] font-extrabold text-charcoal leading-tight">
                                    {{ activeMonthName }} {{ viewYear }}
                                </h4>
                                <button
                                    type="button"
                                    @click="goToToday"
                                    class="text-[10px] font-extrabold text-[#2E7D32] bg-sage-light px-2.5 py-0.5 rounded-full mt-1 hover:opacity-85 active:scale-95 transition-all cursor-pointer"
                                >
                                    ● Hari Ini
                                </button>
                            </div>
                            <button
                                type="button"
                                @click="nextMonth"
                                class="w-9 h-9 rounded-full bg-canvas border border-[#ECEAE4] flex items-center justify-center text-charcoal text-base font-extrabold hover:bg-muted active:scale-95 transition-all cursor-pointer"
                                title="Bulan Berikutnya"
                            >
                                ›
                            </button>
                        </div>

                        <!-- Days of Week Row -->
                        <div class="grid grid-cols-7 gap-1 text-center mb-1.5 py-1 bg-canvas/80 rounded-xl">
                            <span
                                v-for="(d, idx) in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']"
                                :key="d"
                                class="text-[11px] font-bold"
                                :class="idx === 0 ? 'text-rose-500' : 'text-subtext'"
                            >
                                {{ d }}
                            </span>
                        </div>

                        <!-- 42-Day Matrix Grid with Holiday & Event Highlights -->
                        <div class="grid grid-cols-7 gap-1 text-center">
                            <button
                                v-for="(cell, i) in calendarCells"
                                :key="i"
                                type="button"
                                @click="selectCalendarDate(cell)"
                                class="h-9 rounded-2xl flex flex-col items-center justify-center text-xs font-bold transition-all relative cursor-pointer active:scale-95"
                                :class="[
                                    cell.isCurrentMonth
                                        ? isSelectedDate(cell)
                                            ? 'bg-charcoal text-white shadow-sm'
                                            : cell.isToday
                                            ? 'bg-sage-light text-sage-dark font-extrabold border border-sage/40'
                                            : getEventForCell(cell)?.type === 'holiday' || getEventForCell(cell)?.type === 'leave'
                                            ? 'text-rose-600 hover:bg-rose-50 font-extrabold'
                                            : 'text-charcoal hover:bg-canvas'
                                        : 'text-subtext/40 hover:bg-canvas/50',
                                ]"
                            >
                                <span>{{ cell.day }}</span>
                                <!-- Event Indicator Dots -->
                                <span
                                    v-if="!isSelectedDate(cell)"
                                    class="w-1 h-1 rounded-full absolute bottom-1"
                                    :class="
                                        cell.isToday
                                            ? 'bg-[#2E7D32]'
                                            : getEventForCell(cell)?.type === 'holiday' || getEventForCell(cell)?.type === 'leave'
                                            ? 'bg-rose-500'
                                            : getEventForCell(cell)?.type === 'exam'
                                            ? 'bg-purple-600'
                                            : getEventForCell(cell)?.type === 'school_event'
                                            ? 'bg-sky-500'
                                            : ''
                                    "
                                ></span>
                            </button>
                        </div>

                        <!-- Legend Mini Bar -->
                        <div class="flex items-center justify-center gap-3 text-[10px] text-subtext mt-3 pt-2 border-t border-[#ECEAE4]/60">
                            <span class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Libur Nasional
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span> PTS / PAS
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Hari Ini
                            </span>
                        </div>

                        <!-- Selected Date Info & Actions -->
                        <div class="mt-3 pt-3 border-t border-[#ECEAE4]">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="text-[10px] text-subtext font-semibold uppercase tracking-wider">
                                        Agenda Kalender Pendidikan
                                    </div>
                                    <div class="text-xs font-extrabold text-charcoal mt-0.5 truncate">
                                        {{ selectedDateFormatted }}
                                    </div>
                                    <!-- Event Badge -->
                                    <div v-if="selectedEvent" class="mt-1.5 space-y-0.5">
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold"
                                            :class="selectedEvent.badgeColor"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            {{ selectedEvent.name }}
                                        </span>
                                        <p class="text-[10px] text-subtext leading-tight truncate">
                                            {{ selectedEvent.description }}
                                        </p>
                                    </div>
                                    <div v-else class="mt-1.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sage-light text-sage-dark">
                                            ● Hari Efektif KBM Reguler
                                        </span>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click="confirmSelection"
                                    class="px-4 py-2 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shadow-sm shrink-0"
                                >
                                    Terapkan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
.calendar-modal-enter-active,
.calendar-modal-leave-active {
    transition: opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}

.calendar-modal-enter-from,
.calendar-modal-leave-to {
    opacity: 0;
}

.calendar-modal-enter-active .modal-card,
.calendar-modal-leave-active .modal-card {
    transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}

.calendar-modal-enter-from .modal-card {
    transform: scale(0.95) translateY(10px);
}

.calendar-modal-leave-to .modal-card {
    transform: scale(0.95) translateY(10px);
}

.live-dot {
    animation: pulse-dot 2s ease-in-out infinite;
}

@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.7); }
}
</style>
