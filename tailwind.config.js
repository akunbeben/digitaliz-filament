import preset from './vendor/filament/support/tailwind.config.preset'

const colors = require('tailwindcss/colors');
const defaultTheme = require('tailwindcss/defaultTheme');

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/jaocero/activity-timeline/resources/views/**/*.blade.php',
    ],
    theme: {
        colors: {
            custom: colors.amber,
            danger: colors.rose,
            info: colors.blue,
            primary: colors.amber,
            success: colors.emerald,
            warning: colors.orange,
            ...colors
        },
        height: theme => ({
            auto: 'auto',
            ...theme('spacing'),
            full: '100%',
            screen: 'calc(100dvh)',
        }),
        minHeight: theme => ({
            '0': '0',
            ...theme('spacing'),
            full: '100%',
            screen: 'calc(100dvh)',
        }),
        extend: {
            fontFamily: {
                'sans': ['Be Vietnam Pro', ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
            },
        },
    },
}