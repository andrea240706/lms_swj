<script setup>
import { computed } from "vue";

const props = defineProps({
    url: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        default: "Video Pembelajaran",
    },
    aspectRatio: {
        type: String,
        default: "aspect-video", // 16:9
    },
});

const videoInfo = computed(() => {
    const rawUrl = String(props.url ?? "").trim();
    if (! rawUrl) return { type: "none", src: "" };

    // YouTube
    const ytMatch = rawUrl.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
    if (ytMatch && ytMatch[1]) {
        return {
            type: "youtube",
            src: `https://www.youtube-nocookie.com/embed/${ytMatch[1]}?rel=0&modestbranding=1`,
            providerName: "YouTube",
            icon: "youtube",
        };
    }

    // Google Drive
    const gDriveMatch = rawUrl.match(/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/);
    if (gDriveMatch && gDriveMatch[1]) {
        return {
            type: "drive",
            src: `https://drive.google.com/file/d/${gDriveMatch[1]}/preview`,
            providerName: "Google Drive",
            icon: "drive",
        };
    }

    // Instagram (Reels or Posts)
    const igMatch = rawUrl.match(/instagram\.com\/(?:p|reel|tv)\/([a-zA-Z0-9_-]+)/);
    if (igMatch && igMatch[1]) {
        return {
            type: "instagram",
            src: `https://www.instagram.com/p/${igMatch[1]}/embed/captioned/`,
            providerName: "Instagram",
            icon: "instagram",
        };
    }

    // TikTok
    const tikTokMatch = rawUrl.match(/tiktok\.com\/@[\w.-]+\/video\/(\d+)/);
    if (tikTokMatch && tikTokMatch[1]) {
        return {
            type: "tiktok",
            src: `https://www.tiktok.com/embed/v2/${tikTokMatch[1]}`,
            providerName: "TikTok",
            icon: "tiktok",
        };
    }

    // Direct MP4 / WebM / OGG
    if (/\.(mp4|webm|ogg)(\?.*)?$/i.test(rawUrl)) {
        return {
            type: "direct",
            src: rawUrl,
            providerName: "Direct Video",
            icon: "video",
        };
    }

    // Default / External
    return {
        type: "external",
        src: rawUrl,
        providerName: "Tautan Eksternal",
        icon: "external-link",
    };
});
</script>

<template>
    <div class="lms-video-embed w-full rounded-2xl overflow-hidden border border-border bg-black shadow-sm">
        <!-- Direct HTML5 Video Player -->
        <div v-if="videoInfo.type === 'direct'" class="w-full">
            <video
                :src="videoInfo.src"
                controls
                class="w-full max-h-[500px] bg-black"
                preload="metadata"
            >
                Browser Anda tidak mendukung pemutar video HTML5.
            </video>
        </div>

        <!-- YouTube / Google Drive / Instagram / TikTok iframe -->
        <div
            v-else-if="['youtube', 'drive', 'instagram', 'tiktok'].includes(videoInfo.type)"
            class="relative w-full overflow-hidden"
            :class="videoInfo.type === 'tiktok' ? 'aspect-[9/16] max-w-[380px] mx-auto' : aspectRatio"
        >
            <iframe
                :src="videoInfo.src"
                :title="title"
                class="absolute inset-0 w-full h-full border-0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
                loading="lazy"
            ></iframe>
        </div>

        <!-- Fallback External Link -->
        <div v-else class="p-6 bg-surface text-center flex flex-col items-center justify-center gap-3">
            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </div>
            <div class="text-sm font-semibold text-charcoal">{{ title || 'Tonton Video di Tab Baru' }}</div>
            <p class="text-xs text-subtext max-w-md">Platform video ini memerlukan pemutaran langsung melalui aplikasi aslinya.</p>
            <a
                :href="videoInfo.src"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors"
            >
                <span>Buka Video di Sumber Asli</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>
</template>

