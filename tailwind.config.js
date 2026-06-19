/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/View/Components/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
            },
            boxShadow: {
                soft: '0 8px 30px rgb(0 0 0 / 0.04)',
                card: '0 18px 60px rgb(15 23 42 / 0.08)',
            },
        },
    },
    plugins: [],
};
