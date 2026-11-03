import { computed, onBeforeUnmount, ref, watch } from "vue";

/**
 * Reactive countdown composable.
 *
 * @param {import("vue").Ref<string|Date|null> | string | Date | null} targetDateRef
 */
export function useCountdown(targetDateRef) {
    const now = ref(Date.now());
    let timer = null;

    const startTimer = () => {
        if (timer) clearInterval(timer);
        timer = setInterval(() => {
            now.value = Date.now();
        }, 1000);
    };

    const stopTimer = () => {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    };

    startTimer();
    onBeforeUnmount(stopTimer);

    const targetTimestamp = computed(() => {
        const val = typeof targetDateRef === "object" && targetDateRef !== null && "value" in targetDateRef
            ? targetDateRef.value
            : targetDateRef;

        if (! val) return null;
        const parsed = new Date(val).getTime();
        return Number.isNaN(parsed) ? null : parsed;
    });

    const diffMs = computed(() => {
        if (! targetTimestamp.value) return 0;
        return targetTimestamp.value - now.value;
    });

    const isPending = computed(() => diffMs.value > 0);
    const isExpired = computed(() => targetTimestamp.value !== null && diffMs.value <= 0);

    const totalSeconds = computed(() => Math.max(0, Math.floor(diffMs.value / 1000)));
    const days = computed(() => Math.floor(totalSeconds.value / 86400));
    const hours = computed(() => Math.floor((totalSeconds.value % 86400) / 3600));
    const minutes = computed(() => Math.floor((totalSeconds.value % 3600) / 60));
    const seconds = computed(() => totalSeconds.value % 60);

    const formatted = computed(() => {
        if (! targetTimestamp.value) return "-";
        if (isExpired.value) return "Waktu habis";

        const parts = [];
        if (days.value > 0) parts.push(`${days.value} hari`);
        if (hours.value > 0 || days.value > 0) parts.push(`${hours.value} jam`);
        parts.push(`${minutes.value} mnt`);
        if (days.value === 0 && hours.value === 0) {
            parts.push(`${seconds.value} dtk`);
        }

        return parts.join(" ");
    });

    const formattedCompact = computed(() => {
        if (! targetTimestamp.value) return "-";
        if (isExpired.value) return "00:00:00";

        const pad = (n) => String(n).padStart(2, "0");
        if (days.value > 0) {
            return `${days.value}h ${pad(hours.value)}:${pad(minutes.value)}:${pad(seconds.value)}`;
        }
        return `${pad(hours.value)}:${pad(minutes.value)}:${pad(seconds.value)}`;
    });

    // Urgency level: 'normal' | 'warning' (< 24h) | 'critical' (< 2h) | 'expired'
    const urgency = computed(() => {
        if (isExpired.value) return "expired";
        if (totalSeconds.value <= 7200) return "critical"; // < 2 hours
        if (totalSeconds.value <= 86400) return "warning"; // < 24 hours
        return "normal";
    });

    return {
        now,
        targetTimestamp,
        diffMs,
        isPending,
        isExpired,
        totalSeconds,
        days,
        hours,
        minutes,
        seconds,
        formatted,
        formattedCompact,
        urgency,
    };
}

