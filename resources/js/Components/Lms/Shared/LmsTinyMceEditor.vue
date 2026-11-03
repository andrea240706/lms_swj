<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "Tuliskan konten atau instruksi di sini...",
    },
    height: {
        type: [Number, String],
        default: 260,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const editorId = `tinymce-editor-${Math.random().toString(36).substring(2, 9)}`;
const isLoaded = ref(false);
let editorInstance = null;

const loadScript = (src) => {
    return new Promise((resolve, reject) => {
        if (window.tinymce) {
            resolve(window.tinymce);
            return;
        }

        const existingScript = document.querySelector(`script[src="${src}"]`);
        if (existingScript) {
            existingScript.addEventListener("load", () => resolve(window.tinymce));
            return;
        }

        const script = document.createElement("script");
        script.src = src;
        script.referrerPolicy = "origin";
        script.onload = () => resolve(window.tinymce);
        script.onerror = (err) => reject(err);
        document.head.appendChild(script);
    });
};

const initEditor = () => {
    if (! window.tinymce) return;

    window.tinymce.init({
        selector: `#${editorId}`,
        height: props.height,
        menubar: false,
        branding: false,
        promotion: false,
        statusbar: true,
        plugins: "advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount",
        toolbar: "undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link media | code fullscreen",
        content_style: `
            body {
                font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                font-size: 14px;
                color: #2D3748;
                line-height: 1.6;
                padding: 12px;
            }
            p { margin-bottom: 0.75rem; }
            h1, h2, h3, h4 { color: #1A202C; font-weight: 700; margin-top: 1rem; margin-bottom: 0.5rem; }
            a { color: #3182CE; text-decoration: underline; }
            table { border-collapse: collapse; width: 100%; margin: 1rem 0; }
            table, th, td { border: 1px solid #CBD5E0; padding: 8px; }
            th { background-color: #EDF2F7; }
            blockquote { border-left: 4px solid #48BB78; padding-left: 1rem; color: #4A5568; margin-left: 0; }
            code { background: #EDF2F7; padding: 2px 5px; border-radius: 4px; font-family: monospace; }
        `,
        setup: (editor) => {
            editorInstance = editor;

            editor.on("init", () => {
                isLoaded.value = true;
                if (props.modelValue) {
                    editor.setContent(props.modelValue);
                }
                if (props.disabled) {
                    editor.mode.set("readonly");
                }
            });

            const updateValue = () => {
                const content = editor.getContent();
                emit("update:modelValue", content);
            };

            editor.on("change keyup undo redo", updateValue);
        },
    });
};

watch(
    () => props.modelValue,
    (newVal) => {
        if (editorInstance && editorInstance.initialized && newVal !== editorInstance.getContent()) {
            editorInstance.setContent(newVal || "");
        }
    }
);

watch(
    () => props.disabled,
    (disabled) => {
        if (editorInstance && editorInstance.initialized) {
            editorInstance.mode.set(disabled ? "readonly" : "design");
        }
    }
);

onMounted(async () => {
    try {
        // Use CDN official TinyMCE 6 (Free / Community build)
        await loadScript("https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js");
        initEditor();
    } catch (e) {
        console.error("Gagal memuat TinyMCE:", e);
    }
});

onBeforeUnmount(() => {
    if (editorInstance) {
        editorInstance.destroy();
        editorInstance = null;
    }
});
</script>

<template>
    <div class="lms-tinymce-container relative rounded-xl border border-border overflow-hidden bg-white">
        <div v-if="!isLoaded" class="p-6 text-center text-subtext text-xs flex items-center justify-center gap-2">
            <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Memuat editor TinyMCE...</span>
        </div>
        <textarea :id="editorId" class="hidden" :placeholder="placeholder"></textarea>
    </div>
</template>

<style scoped>
.lms-tinymce-container :deep(.tox-tinymce) {
    border: none !important;
    border-radius: 0.75rem;
}
.lms-tinymce-container :deep(.tox-toolbar-overlord) {
    background-color: #F8FAFC !important;
    border-bottom: 1px solid #E2E8F0 !important;
}
.lms-tinymce-container :deep(.tox-statusbar) {
    border-top: 1px solid #E2E8F0 !important;
    font-size: 11px;
}
</style>

