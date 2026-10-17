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

