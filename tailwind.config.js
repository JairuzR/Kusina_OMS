import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // Sidebar nav — dynamic ternary classes that JIT cannot detect
        'text-gray-300',
        'text-gray-400',
        'text-gray-500',
        'text-white',
        'bg-gray-700',
        'bg-gray-800',
        'bg-gray-900',
        'bg-orange-500',
        'hover:bg-gray-700',
        'hover:text-white',
        // Badge colour pairs used in audit logs, users, orders
        'bg-green-100', 'text-green-800',
        'bg-green-50',  'text-green-700', 'border-green-200',
        'bg-yellow-100','text-yellow-800',
        'bg-yellow-50', 'text-yellow-700','border-yellow-200',
        'bg-red-100',   'text-red-800',
        'bg-red-50',    'text-red-700',   'border-red-200',
        'bg-blue-100',  'text-blue-800',
        'bg-blue-50',   'text-blue-700',  'border-blue-200',
        'bg-gray-100',  'text-gray-800',
        'bg-orange-50', 'border-orange-300',
        // Status dots
        'bg-green-500', 'bg-yellow-500', 'bg-red-500', 'bg-gray-400',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
