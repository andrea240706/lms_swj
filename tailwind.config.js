import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                canvas: '#F9F8F6',
                charcoal: '#141416',
                muted: '#F1EFEA',
                subtext: '#6E6E77',
                sage: {
                    light: '#EBF2D5',
                    DEFAULT: '#D4E4D2',
                    dark: '#3D5A45',
                },
                blossom: {
                    light: '#FDF0F5',
                    DEFAULT: '#FBD2E0',
                    dark: '#7A3E53',
                },
                sky: {
                    light: '#EAF5FE',
                    DEFAULT: '#D0E8F8',
                    dark: '#2C5270',
                },
                amberpastel: {
                    light: '#FEF5E7',
                    DEFAULT: '#FDE4C3',
                    dark: '#785021',
                },
                lavender: {
                    light: '#F3EFFB',
                    DEFAULT: '#E2D9F3',
                    dark: '#4A3B69',
                },
                lingua: {
                    primary: '#6C5CE7',
                    'primary-dark': '#5B4BC4',
                    accent: '#EEEDFE',
                    'accent-soft': '#E8E5FF',
                    canvas: '#F8F9FD',
                    surface: '#FFFFFF',
                    dark: '#1E1F24',
                    muted: '#6E717C',
                    divider: '#F0F1F5',
                    streak: '#FF7A00',
                    heart: '#FF4D4D',
                    pastel: {
                        yellow: '#FFF5E5',
                        pink: '#FFEEF0',
                        green: '#E8FAF0',
                        blue: '#EBF3FE',
                    },
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'lingua-card': '0px 4px 16px rgba(0, 0, 0, 0.04)',
                'lingua-primary': '0px 8px 20px rgba(108, 92, 231, 0.25)',
            },
            borderRadius: {
                'card': '20px',
                'hero': '24px',
            },
        },
    },

    plugins: [forms, daisyui],

    daisyui: {
        themes: [
            {
                lingua: {
                    primary: '#6C5CE7',
                    secondary: '#EEEDFE',
                    accent: '#FF7A00',
                    neutral: '#1E1F24',
                    'base-100': '#FFFFFF',
                    'base-200': '#F8F9FD',
                    'base-300': '#F0F1F5',
                    info: '#0984E3',
                    success: '#00B894',
                    warning: '#FF7A00',
                    error: '#FF4D4D',
                },
            },
            'light',
        ],
        darkTheme: 'light',
        base: true,
        styled: true,
        utils: true,
        prefix: '',
        logs: false,
    },
};

