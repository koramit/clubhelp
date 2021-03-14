module.exports = {
    purge: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue'
    ],
    darkMode: false, // or 'media' or 'class'
    theme: {
        extend: {
            colors: {
                'soft-theme-light': '#f8ffd7',
                'alt-theme-light': '#c7e5c8',
                'bitter-theme-light': '#c5e1a5',
                'thick-theme-light': '#94af76',
                'dark-theme-light': '#96b397',
            }
        },
    },
    variants: {
        extend: {
            backgroundColor: ['active', 'hover', 'disabled'],
            borderColor: ['hover', 'focus', 'disabled'],
            cursor: ['hover', 'focus', 'disabled'],
            opacity: ['disabled'],
        },
    },
    plugins: [],
};
